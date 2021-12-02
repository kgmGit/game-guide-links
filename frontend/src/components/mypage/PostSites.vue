<template>
  <div>
    <div class="border rounded bg-light p-2 mb-4 text-center">
      投稿 - 攻略サイト
    </div>
    <div v-if="gameTitles">
      <div v-for="title in gameTitles" :key="title">
        <b-card class="mb-5">
          <template #header>
            <b-link :to="'/games/' + title">
              <div class="text-center">{{ title }}</div>
            </b-link>
          </template>
          <sites
            :propSites="sites.filter((site) => site.game_title === title)"
          />
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
import Sites from "@/components/gameDetail/site/Sites.vue";
import { http } from "@/Services/Http";

export default {
  components: {
    Sites,
  },
  data() {
    return {
      sites: null,
      gameTitles: null,
    };
  },
  methods: {
    async fetchSites() {
      await http
        .get("/api/posts/sites")
        .then((response) => {
          this.sites = response.data.data;
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
  },
  async created() {
    await this.fetchSites();
    if (this.sites) {
      const titles = this.sites.map((site) => site.game_title);
      this.gameTitles = [...new Set(titles)];
    }
  },
};
</script>