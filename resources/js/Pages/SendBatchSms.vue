<template>
  <app-layout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Send Batch Sms
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow sm:rounded-lg">
          <div class="flex flex-wrap justify-end p-2">
            <span>Balance</span><span>{{ balance }}</span>
          </div>
          <div class="p-3">
            <div>
              <p>New Message</p>
            </div>

            <new-message :isBatch="true" v-on:add-list="pushElement"></new-message>
          </div>
          <div v-if="list.length > 0">
                <h1 class="text-center font-bold">Messages in batch</h1>
                <div class="flex flex-wrap">
                    <div class="w-1/4 bg-white rounded p-2 shadow-sm" v-for="(element, index) in list" :key="index">
                            <p><span>Phone</span> <span>{{element.phone}}</span> </p>
                            <p><span>Message</span> <span>{{element.message}}</span> </p>
                    </div>
                </div>
          <div>
            <button
                class="ml-4 mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                :class="{ 'opacity-25': processing }"
                :disabled="processing"
                @click="submit"
            >
                Send Messages
            </button>
          </div>
                    </div>

        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import axios from "axios";
import NewMessage from "../Components/NewMessage.vue";

export default defineComponent({
  components: {
    AppLayout,
    NewMessage
  },
  props: {
    balance: {
      type: Number,
      default: null,
    },
  },
  mounted() {
    // this.getBalance();
  },
  data() {
    return {
      list: [],
      processing: false

    };
  },

  methods: {
    pushElement(data) {
      this.list.push(data);
    },
    submit() {
        this.processing = true;
        axios
        .post(this.route("send-batch-sms"), {list: this.list}).then(() => {
            this.list = [];
            this.processing = false;
        }).catch(()=>{
            this.processing = false
        });
    },
  },
});
</script>

<style>
</style>