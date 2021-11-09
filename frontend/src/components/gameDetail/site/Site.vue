<template>
  <div v-if="site">
    <b-card class="item">
      <template #header>
        <b-link :to="$route.path + '/sites/' + site.id">
          <div class="text-center">
            {{ site.title }}
          </div>
        </b-link>
      </template>

      <b-card-body>
        <b-link :href="site.url">{{ site.url }}</b-link>
        <div class="mt-2 description">
          {{ site.description }}
        </div>

        <b-row align-h="end" class="mt-2">
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
    site: {
      type: Object,
      required: true,
    },
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
.item {
  height: 17em;
}
.description {
  height: 5em;
  overflow-y: auto;
  white-space: pre-wrap;
}
</style>