<template>
  <div v-if="gameTitles">
    <div v-for="title in gameTitles" :key="title">
      <b-card class="mb-5">
        <template #header>
          <div class="text-center">{{ title }}</div>
        </template>
        <b-card-body>
          <articles
            :propArticles="
              articles.filter((article) => article.game_title === title)
            "
          />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import Articles from "@/components/gameDetail/article/Articles.vue";

export default {
  components: {
    Articles,
  },
  data() {
    return {
      articles: null,
      gameTitles: null,
    };
  },
  methods: {
    async fetchArticle() {
      this.articles = [];
      for (let i = 0; i < 30; i++) {
        this.articles.push({
          id: i,
          title: "title" + i,
          outline: "outline" + i,
          favorites_count: 1,
          favorited: true,
          likes_count: 0,
          liked: false,
          owner: false,
          owner_name: "owner",
          game_title: "game1",
        });
      }
      for (let i = 0; i < 6; i++) {
        this.articles.push({
          id: i,
          title: "other_title" + i,
          outline: "other_outline" + i,
          favorites_count: 1,
          favorited: true,
          likes_count: 0,
          liked: false,
          owner: false,
          owner_name: "owner",
          game_title: "other_game",
        });
      }
    },
  },
  async created() {
    await this.fetchArticle();
    const titles = this.articles.map((article) => article.game_title);
    this.gameTitles = [...new Set(titles)];
  },
};
</script>