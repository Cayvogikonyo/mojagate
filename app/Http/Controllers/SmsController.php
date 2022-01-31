<?php

namespace App\Http\Controllers;

use App\Models\SmsSent;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use stdClass;

class SmsController extends Controller
{
    /**
     * Get session token for api requests
     */
    public function getToken(){

        try{
            $email = config('sms_api.email');
            $pass = config('sms_api.password');

            $client = new \GuzzleHttp\Client();
            $response = $client->post(
                config('sms_api.api_url').'login',
                [
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'email' => $email,
                        'password' => $pass,
                    ],
                ]
            );
            $body = json_decode($response->getBody());

            Cache::put('sms_api_token', $body->data);

        } catch (RequestException $e) {

            $message = 'unknown';
            if ($e->hasResponse()) {
                $message = $e->getResponse()->getReasonPhrase();
            }
            return ['message' => 'error', 'reason' => $message, 'code' => $e->getResponse()->getStatusCode()];
        }
        
        return ['message' => 'success'];
    }
    
    /**
     * Get sms account balancee
     */
    public function getBalance(){

        $tokenData = $this->getTokenData();
        try{
            $client = new \GuzzleHttp\Client();
            $response = $client->get(
                config('sms_api.api_url').'balance',
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$tokenData->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $body = json_decode($response->getBody());

        } catch (RequestException $e) {
            dd($e->getResponse());
            $message = 'unknown';
            if ($e->hasResponse()) {
                $message = $e->getResponse()->getReasonPhrase();
            }
            return ['message' => 'error', 'reason' => $message, 'code' => $e->getResponse()->getStatusCode()];
        }

        return ['message' => 'success', 'balance' => $body->data ? $body->data->balance : null];
    }
    
    
    /**
     * Send an sms
     */
    public function sendSms(Request $request){

        $tokenData = $this->getTokenData();

        $data = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string'
        ]);

        $sms =  SmsSent::create([
            'user_id' => Auth::user()->id,
            'message' => $data['message'],
            'to' => $data['phone'],
            'message_id' => 'sms_id'.Auth::user()->id.date('YmdHis'),
        ]);

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post(
                config('sms_api.api_url').'sendsms',
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$tokenData->token,
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'from' => 'MOJAGATE',
                        'phone' => $data['phone'],
                        'message' => $data['message'],
                        'message_id' => 'sms_id'.Auth::user()->id.date('YmdHis'),
                        'webhook_url' => 'https://mojagate.com/sms-webhook',
                    ],
                ]
            );

            $sms->sent = true;
            $sms->update();
    
            $body = json_decode($response->getBody());
    
            return ['message' => 'success', 'balance' => $body->data ? $body->data->balance : null];
    
        } catch (RequestException $e) {


            $message = 'unknown';
            if ($e->hasResponse()) {
                $message = $e->getResponse()->getReasonPhrase();
            }
            return ['message' => 'error', 'reason' => $message, 'code' => $e->getResponse()->getStatusCode()];
        }

       
    }    


    /**
     * Send an sms
     */
    public function sendBatchSms(Request $request){


        $data = $request->validate([
            'list' => 'required|array',
            'list.*.phone' => 'required|string',
            'list.*.message' => 'required|string'
        ]);

        $to_send = [];
        $sms_ids = [];
        foreach ($request->list as $key => $value) {

            //Add message id
            $value['message_id'] = mt_rand(0, 99999999).date('YmdHis');

            array_push($to_send, $value);

            $sms =  SmsSent::create([
                'user_id' => Auth::user()->id,
                'message' => $value['message'],
                'to' => $value['phone'],
                'message_id' => $value['message_id']
            ]);

            array_push($sms_ids, $sms->id);

        }

        $tokenData = $this->getTokenData();

        
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post(
                config('sms_api.api_url').'batch-sms',
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$tokenData->token,
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'from' => 'MOJAGATE',
                        'messages' => $to_send,
                        'webhook_url' => 'https://mojagate.com/sms-webhook',
                    ],
                ]
            );

    
            $body = json_decode($response->getBody());

            SmsSent::whereIn('id', $sms_ids)->update(['sent' => true]);
    
            return ['message' => 'success'];
    
        } catch (RequestException $e) {


            $message = 'unknown';
            if ($e->hasResponse()) {
                $message = $e->getResponse()->getReasonPhrase();
            }
            return ['message' => 'error', 'reason' => $message, 'code' => $e->getResponse()->getStatusCode()];
        }

       
    }

    /**
     * Helper to access cache for api token key
     */
    public function getTokenData(){

        $tokenData = null;//Cache::get('sms_api_token');

        $count = 0;

        //Incase cache reset or token not set get token
        while($tokenData == null){
            $this->getToken();
            $tokenData = Cache::get('sms_api_token');

            //Break loop after 3 retrials
            if($count == 3){
                break;
            }
        }
        
        if(empty($tokenData)){
            // abort(500, "Token Data Cannot be generated! Check you configurations");
            abort(response(['message' => 'Token Data Cannot be generated! Check you configurations', 'status_code' => 500], 500));
        }

        return $tokenData;
    }


    /**
     * Helper to access cache for api token key
     */
    public function getSuccessRates(){

        
        return [
            [
                'name' => "Success Rate",
                'data' => [
                    [ 'name' => "Sent", 'y' => SmsSent::where('sent', true)->count() ],
                    [ 'name' => "Not Sent", 'y' =>  SmsSent::where('sent', false)->count() ],
                ],
            ],
        ];
    }
}
