<template>
  <div>
    <div class="border rounded bg-light p-2 mb-4">
      <b-link :to="'/games/' + $route.params.game_title">
        <h4 class="text-center">
          {{ $route.params.game_title }}
        </h4>
      </b-link>
    </div>

    <b-card v-if="article">
      <template #header>
        <div class="text-center">
          {{ article.title }}
        </div>
        <h5 class="d-flex justify-content-between text-muted small mt-2">
          <div>最終更新 : {{ update_at }}</div>
          <div>by {{ article.owner_name }}</div>
        </h5>
      </template>
      <b-card header="概要">
        {{ article.outline }}
      </b-card>
      <b-card class="mt-3">
        <div v-html="article.content" class="ql-editor"></div>
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
      article: null,
    };
  },
  computed: {
    update_at() {
      return unixTimestampToYmdHis(this.article.updated_at);
    },
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