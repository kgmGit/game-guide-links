<template>
  <b-alert :show="!!message" variant="primary" class="text-center">{{
    message
  }}</b-alert>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  computed: {
    ...mapGetters({
      message: "message/content",
    }),
  },
};
</script>