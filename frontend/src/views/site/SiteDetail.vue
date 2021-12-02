<template>
  <div>
    <div class="border rounded bg-light p-2 mb-4">
      <b-link :to="'/games/' + $route.params.game_title">
        <h4 class="text-center">
          {{ $route.params.game_title }}
        </h4>
      </b-link>
    </div>

    <b-card v-if="site">
      <template #header>
        <div class="text-center">
          {{ site.title }}
        </div>
        <h5 class="d-flex justify-content-between text-muted small mt-2">
          <div>最終更新 : {{ update_at }}</div>
          <div>by {{ site.owner_name || "(退会済みユーザ)" }}</div>
        </h5>
      </template>
      <b-card>
        <template #header>
          <b-link :href="site.url">{{ site.url }}</b-link>
        </template>
        <div class="content">
          {{ site.description }}
        </div>
      </b-card>
    </b-card>
  </div>
</template>

<script>
import { http } from "@/Services/Http";
import { unixTimestampToYmdHis } from "@/utils/formatters";

export default {
  data() {
    return {
      site: null,
    };
  },
  computed: {
    update_at() {
      return unixTimestampToYmdHis(this.site.updated_at);
    },
  },
  methods: {
    async fetchSite(gameTitle, siteId) {
      await http
        .get("/api/games/" + gameTitle + "/sites/" + siteId)
        .then((response) => {
          this.site = response.data.data;
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
  },
  async created() {
    await this.fetchSite(this.$route.params.game_title, this.$route.params.id);
  },
};
</script>

<style scoped>
.item {
  height: 17em;
}
.content {
  white-space: pre-wrap;
}
</style>