<template>
  <div v-if="site">
    <b-card>
      <template #header>
        <b-link
          :to="`/games/${site.game_title}/sites/${site.id}${
            site.owner || isAdmin ? '/edit' : ''
          }`"
        >
          <div class="text-center">
            {{ site.title }}
          </div>
        </b-link>
        <div class="text-right text-muted small">by {{ site.owner_name }}</div>
      </template>

      <div class="content mb-3">
        <b-link :href="site.url">{{ site.url }}</b-link>
        <hr />
        <div class="mt-2">
          {{ site.description }}
        </div>
      </div>

      <b-row align-h="end" class="mr-1">
        <like
          :count="site.likes_count"
          :liked="site.liked"
          @click="clickLike"
        />
        <favorite
          :count="site.favorites_count"
          :favorited="site.favorited"
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
    site: {
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
      this.$emit("click-like", this.site.id);
    },
    clickFavorite() {
      this.$emit("click-favorite", this.site.id);
    },
    clickReport() {
      this.$emit("click-report", this.site.id);
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
hr {
  margin: 8px 0px;
}
</style>