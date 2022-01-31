<template>
  <app-layout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Send Sms
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

            <new-message></new-message>
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
    NewMessage,
  },
  props: {
    balance: {
      type: Number,
      default: null,
    },
  },
  mounted() {
    // this.getBalance();
  }
});
</script>

<style>
</style>