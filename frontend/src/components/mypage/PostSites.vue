<template>
  <div v-if="gameTitles">
    <div v-for="title in gameTitles" :key="title">
      <b-card class="mb-5">
        <template #header>
          <div class="text-center">{{ title }}</div>
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
      this.sites = [];
      for (let i = 0; i < 30; i++) {
        this.sites.push({
          id: i,
          title: "title" + i,
          url: "url" + i,
          favorites_count: 0,
          favorited: false,
          likes_count: 0,
          liked: false,
          owner: true,
          owner_name: "owner",
          game_title: "game1",
        });
      }
      for (let i = 0; i < 6; i++) {
        this.sites.push({
          id: i,
          title: "other_title" + i,
          url: "other_url" + i,
          favorites_count: 0,
          favorited: false,
          likes_count: 0,
          liked: false,
          owner: true,
          owner_name: "owner",
          game_title: "other_game",
        });
      }
    },
  },
  async created() {
    await this.fetchSites();
    const titles = this.sites.map((site) => site.game_title);
    this.gameTitles = [...new Set(titles)];
  },
};
</script>