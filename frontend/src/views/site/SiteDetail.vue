<template>
  <div>
    <b-card>
      <template #header>
        <b-link :to="'/games/' + $route.params.game_title">
          <h4 class="text-center">
            {{ $route.params.game_title }}
          </h4>
        </b-link>
      </template>

      <b-card v-if="site">
        <template #header>
          <div class="text-center">
            {{ site.title }}
          </div>
          <h5 class="text-right text-muted small">by {{ site.owner_name }}</h5>
        </template>
        <b-card-body>
          <b-card>
            <template #header>
              <b-link :href="site.url">{{ site.url }}</b-link>
            </template>
            <b-card-body class="content">
              {{ site.description }}
            </b-card-body>
          </b-card>
        </b-card-body>
      </b-card>
    </b-card>
  </div>
</template>

<script>
import { http } from "@/Services/Http";

export default {
  data() {
    return {
      site: null,
    };
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