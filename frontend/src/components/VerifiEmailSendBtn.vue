<template>
  <b-button
    :disabled="processing"
    size="lg"
    block
    type="button"
    variant="primary"
    @click.prevent="onClick"
    >確認メール送信</b-button
  >
</template>

<script>
export default {
  data() {
    return {
      processing: false,
    };
  },
  methods: {
    async onClick() {
      try {
        this.processing = true;

        await this.$store
          .dispatch("auth/sendVerifyMail")
          .then(() => {
            this.$store.dispatch(
              "message/setContent",
              "確認メールを送信しました"
            );
          })
          .catch(() => {
            this.$router.replace({ name: "Error" });
          });
      } finally {
        this.processing = false;
      }
    },
  },
};
</script>