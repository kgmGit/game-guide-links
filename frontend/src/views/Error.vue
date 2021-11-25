<template>
  <div>
    <b-card>
      <template #header>
        <h5 class="text-center">{{ status }}</h5>
      </template>
      <b-card-body>
        <div v-if="[403, 404].includes(status)">
          <div>ページが存在しません。</div>
          <router-link to="/" class="mt-2 btn btn-primary">ホーム</router-link>
        </div>
        <div v-else-if="[401, 419].includes(status)">
          <div>ログインし直してください。</div>
          <router-link to="/login" class="mt-2 btn btn-primary"
            >ログイン</router-link
          >
        </div>
        <div v-else>
          <div>エラーが発生しました。</div>
          <router-link to="/" class="mt-2 btn btn-primary">ホーム</router-link>
        </div>
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  computed: {
    ...mapGetters({
      status: "error/status",
    }),
  },
  async created() {
    if ([401, 419].includes(this.status)) {
      await this.$store.dispatch("auth/logout");
    }
  },
};
</script>
