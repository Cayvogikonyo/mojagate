<template>
  <app-layout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow sm:rounded-lg">
          <div class="flex flex-wrap justify-end p-2">
            <span>Balance</span><span>{{ balance }}</span>
          </div>
          <div class="p-3">
            <p>Statistics</p>

            <div class="flex p-4">
        

              <div class="w-1/2">
                <p class="text-center">Success Rates</p>
                <highcharts :options="successOptions"></highcharts>
              </div>
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
import Welcome from "@/Jetstream/Welcome.vue";
import { Chart } from "highcharts-vue";
import axios from "axios";

export default defineComponent({
  components: {
    AppLayout,
    Welcome,
    highcharts: Chart,
  },
  props: {
    balance: {
      type: Number,
      default: null,
    },
  },
  mounted(){
      this.getStats();
  },
  data() {
    return {
      
      successOptions: {
        chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: "pie",
        },
        title: {
          text: "Success Rates",
        },
        tooltip: {
          pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>",
        },
        accessibility: {
          point: {
            valueSuffix: "%",
          },
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: "pointer",
            dataLabels: {
              enabled: true,
              format: "<b>{point.name}</b><br>{point.percentage:.1f} %",
              distance: -50,
              filter: {
                property: "percentage",
                operator: ">",
                value: 4,
              },
            },
          },
        },
        series: [
          {
            name: "Success Rate",
            data: [
              { name: "Sent", y: 61 },
              { name: "Not Sent", y: 39 },
            ],
          },
        ],
      },
    };
  },
  methods: {
      getStats(){
          axios.get('get-success-data').then(response => {
            console.log(response);
            this.successOptions.series = response.data;

          }).catch((e) => {
            console.log(e);
          })
      }
  }
});
</script>
