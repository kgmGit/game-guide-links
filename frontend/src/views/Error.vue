<template>
  <div>
    <b-card header="エラー">
      <b-card-body>
        <div>ログインし直してください。</div>
        <router-link to="/login" class="btn btn-primary">ログイン</router-link>
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
export default {
  async created() {
    await this.$store.dispatch("auth/logout");
  },
};
</script>
