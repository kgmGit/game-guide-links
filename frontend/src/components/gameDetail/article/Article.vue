<template>
  <div v-if="article">
    <b-card class="item">
      <template #header>
        <b-link
          :to="'/games/' + article.game_title + '/articles/' + article.id"
        >
          <div class="text-center">
            {{ article.title }}
          </div>
        </b-link>
        <div class="text-right text-muted small">
          by {{ article.owner_name }}
        </div>
      </template>

      <b-card-body>
        <div class="content">
          {{ article.outline }}
        </div>

        <b-row align-h="end" class="mt-2">
          <like
            :count="article.likes_count"
            :liked="article.liked"
            @click="clickLike"
          />
          <favorite
            :count="article.favorites_count"
            :favorited="article.favorited"
            @click="clickFavorite"
            class="ml-3"
          />
          <report @click="clickReport" class="ml-3" />
        </b-row>
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
import Favorite from "@/components/Favorite.vue";
import Like from "@/components/Like.vue";
import Report from "@/components/Report.vue";

export default {
  components: { Favorite, Like, Report },
  props: {
    article: {
      type: Object,
      required: true,
    },
  },

  methods: {
    clickLike() {
      this.$emit("click-like", this.article.id);
    },
    clickFavorite() {
      this.$emit("click-favorite", this.article.id);
    },
    clickReport() {
      this.$emit("click-report", this.article.id);
    },
  },
};
</script>

<style scoped>
.item {
  height: 17em;
}
.content {
  height: 6em;
  overflow-y: auto;
  white-space: pre-wrap;
}
</style>