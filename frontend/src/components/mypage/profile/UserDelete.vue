<template>
  <div>
    <b-card header="アカウントの削除">
      <b-card-body>
        <b-button
          :disabled="processing"
          v-b-modal.confirm
          size="lg"
          block
          type="button"
          variant="danger"
          >削除</b-button
        >
        <b-modal title="確認" @ok="deleteUser" id="confirm"
          >アカウントを削除しますか？</b-modal
        >
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      processing: false,
    };
  },
  methods: {
    async deleteUser() {
      try {
        this.processing = true;

        await this.$store
          .dispatch("auth/deleteUser", this.form)
          .then(() => {
            this.$store.dispatch(
              "message/setContent",
              "アカウントを削除しました"
            );
            this.$router.replace({ name: "Home" });
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
