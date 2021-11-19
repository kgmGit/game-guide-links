<template>
  <div v-if="gameTitles">
    <div v-for="title in gameTitles" :key="title">
      <b-card class="mb-5">
        <template #header>
          <b-link :to="'/games/' + title">
            <div class="text-center">{{ title }}</div>
          </b-link>
        </template>
        <b-card-body>
          <sites
            :propSites="sites.filter((site) => site.game_title === title)"
          />
        </b-card-body>
      </b-card>
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
        .get("/api/favorites/sites")
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
    const titles = this.sites.map((site) => site.game_title);
    this.gameTitles = [...new Set(titles)];
  },
};
</script>