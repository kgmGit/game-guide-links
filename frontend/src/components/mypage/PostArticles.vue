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
import { http } from "@/Services/Http";

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
      await http
        .get("/api/posts/articles")
        .then((response) => {
          this.articles = response.data.data;
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
  },
  async created() {
    await this.fetchArticle();
    if (this.articles) {
      const titles = this.articles.map((article) => article.game_title);
      this.gameTitles = [...new Set(titles)];
    }
  },
};
</script>