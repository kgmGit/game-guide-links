<template>
  <div>
    <div class="border rounded bg-light p-2 mb-4 text-center">
      お気に入り - ゲーム
    </div>
    <div v-if="games">
      <div v-for="game in games" :key="game.id">
        <b-card class="mb-3">
          <b-row>
            <b-col sm="10">
              <b-link :to="'/games/' + game.title">
                <div class="text-center">{{ game.title }}</div>
              </b-link>
            </b-col>
            <b-col sm="2" class="text-right">
              <favorite
                :count="game.favorites_count"
                :favorited="game.favorited"
                @click="clickFavorite(game.title)"
              />
            </b-col>
          </b-row>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
import { http } from "@/Services/Http";
import Favorite from "@/components/Favorite.vue";

export default {
  components: {
    Favorite,
  },
  data() {
    return {
      games: null,
      reportTitle: null,
    };
  },
  methods: {
    async fetchGames() {
      await http
        .get("/api/favorites/games")
        .then((response) => {
          this.games = response.data.data;
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
    async clickFavorite(title) {
      if (this.processing) return;

      try {
        this.processing = true;

        const index = this.games.findIndex((game) => game.title === title);
        const isAdd = !this.games[index].favorited;
        this.games[index].favorited = isAdd;
        this.games[index].favorites_count += isAdd ? 1 : -1;

        if (isAdd) {
          await this.favorite(title);
        } else {
          await this.unfavorite(title);
        }
      } finally {
        this.processing = false;
      }
    },
    async favorite(title) {
      await http.put(`/api/games/${title}/favorite`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },
    async unfavorite(title) {
      await http.delete(`/api/games/${title}/favorite`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },
  },
  async created() {
    this.fetchGames();
  },
};
</script>