<template>
  <div class="w-full md:w-1/4 md:mx-auto">
    <form @submit.prevent="submit">
      <div>
        <jet-label for="phone" value="Phone" />
        <jet-input
          id="phine"
          type="number"
          class="mt-1 block w-full"
          v-model="form.phone"
          required
          autofocus
        />
      </div>
      <div>
        <jet-label for="test" value="Text" />
        <jet-input
          id="message"
          type="text"
          class="mt-1 block w-full"
          v-model="form.message"
          required
          autofocus
        />
      </div>

      <jet-button
        class="ml-4 mt-4"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        {{isBatch ? 'Add To List' :'Send Message' }}
      </jet-button>
    </form>
  </div>
</template>

<script>
import { defineComponent } from "@vue/runtime-core";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetLabel from "@/Jetstream/Label.vue";

export default defineComponent({
  components: {
    JetButton,
    JetInput,
    JetLabel,
  },
  props: {
    isBatch: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      form: this.$inertia.form({
        phone: "",
        message: "",
      }),
    };
  },

  methods: {
    submit() {
      if (this.isBatch) {
          this.$emit('add-list', {phone: this.form.phone, message: this.form.message})
          this.form.reset()
      } else {
        this.form
          .transform((data) => ({
            ...data,
          }))
          .post(this.route("send-sms"), {
            onFinish: () => this.form.reset(),
          });
      }
    },
  },
});
</script>

<style>
</style>