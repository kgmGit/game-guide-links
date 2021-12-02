<template>
  <div>
    <div class="border rounded bg-light p-2 mb-4 text-center">
      投稿 - 攻略記事
    </div>
    <div v-if="gameTitles">
      <div v-for="title in gameTitles" :key="title">
        <b-card class="mb-5">
          <template #header>
            <b-link :to="'/games/' + title">
              <div class="text-center">{{ title }}</div>
            </b-link>
          </template>
          <articles
            :propArticles="
              articles.filter((article) => article.game_title === title)
            "
          />
        </b-card>
      </div>
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