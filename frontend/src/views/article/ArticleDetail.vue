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

      <b-card v-if="article">
        <template #header>
          <div class="text-center">
            {{ article.title }}
          </div>
          <h5 class="text-right text-muted small">
            by {{ article.owner_name }}
          </h5>
        </template>
        <b-card-body>
          <b-card header="概要">
            {{ article.outline }}
          </b-card>
          <b-card class="mt-3">
            <div v-html="article.content" class="ql-editor"></div>
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
      article: null,
    };
  },
  methods: {
    async fetchArticle(gameTitle, articleId) {
      await http
        .get("/api/games/" + gameTitle + "/articles/" + articleId)
        .then((response) => {
          this.article = response.data.data;
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
  },
  async created() {
    await this.fetchArticle(
      this.$route.params.game_title,
      this.$route.params.id
    );
  },
};
</script>