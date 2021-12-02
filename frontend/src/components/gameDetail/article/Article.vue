<template>
  <div v-if="article">
    <b-card>
      <template #header>
        <b-link
          :to="`/games/${article.game_title}/articles/${article.id}${
            article.owner || isAdmin ? '/edit' : ''
          }`"
        >
          <div class="text-center">
            {{ article.title }}
          </div>
        </b-link>
        <div class="text-right text-muted small">
          by {{ article.owner_name }}
        </div>
      </template>

      <div class="content mb-3">
        {{ article.outline }}
      </div>

      <b-row align-h="end" class="mr-1">
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
    </b-card>
  </div>
</template>

<script>
import Favorite from "@/components/Favorite.vue";
import Like from "@/components/Like.vue";
import Report from "@/components/Report.vue";
import { mapGetters } from "vuex";

export default {
  components: { Favorite, Like, Report },
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  computed: {
    ...mapGetters({
      isAdmin: "auth/isAdmin",
    }),
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
.content {
  height: 8em;
  overflow-y: auto;
  white-space: pre-wrap;
}
</style>