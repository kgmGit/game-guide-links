<template>
  <div v-if="articles">
    <div
      v-for="article in getPaginateItems"
      :key="'article' + article.id"
      class="mt-3"
    >
      <article-component
        :article="article"
        @click-like="clickLike"
        @click-favorite="clickFavorite"
        @click-report="clickReport"
      />
    </div>
    <b-pagination
      v-if="articles.length > perPage"
      :total-rows="articles.length"
      v-model="currentPage"
      :per-page="perPage"
      class="mt-3"
    />

    <b-modal ref="auth-modal" hide-header hide-footer>
      <auth-modal />
    </b-modal>

    <b-modal ref="report-modal" size="lg" hide-header hide-footer>
      <report-modal
        :gameTitle="targetArticle && targetArticle.game_title"
        :article="targetArticle"
        @reported="$refs['report-modal'].hide('report-modal')"
      />
    </b-modal>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import ArticleComponent from "../article/Article.vue";
import AuthModal from "@/components/AuthModal.vue";
import ReportModal from "@/components/ReportModal.vue";
import { http } from "@/Services/Http";

export default {
  components: { ArticleComponent, AuthModal, ReportModal },
  props: {
    propArticles: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      processing: false,
      articles: [],
      targetArticle: null,

      perPage: 10,
      currentPage: 1,
    };
  },

  computed: {
    ...mapGetters({
      isVerified: "auth/isVerified",
    }),

    getPaginateItems: function () {
      const start = (this.currentPage - 1) * this.perPage;
      const end = this.currentPage * this.perPage;
      return this.articles.slice(start, end);
    },
  },

  methods: {
    async clickLike(id) {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$refs["auth-modal"].show();
        return;
      }

      try {
        this.processing = true;

        const index = this.articles.findIndex((article) => article.id === id);
        const isAdd = !this.articles[index].liked;
        this.articles[index].liked = isAdd;
        this.articles[index].likes_count += isAdd ? 1 : -1;

        if (isAdd) {
          await this.like(id);
        } else {
          await this.unlike(id);
        }
      } finally {
        this.processing = false;
      }
    },
    async like(id) {
      await http.put(`/api/articles/${id}/like`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },
    async unlike(id) {
      await http.delete(`/api/articles/${id}/like`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },

    async clickFavorite(id) {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$refs["auth-modal"].show();
        return;
      }

      try {
        this.processing = true;

        const index = this.articles.findIndex((article) => article.id === id);
        const isAdd = !this.articles[index].favorited;
        this.articles[index].favorited = isAdd;
        this.articles[index].favorites_count += isAdd ? 1 : -1;

        if (isAdd) {
          await this.favorite(id);
        } else {
          await this.unfavorite(id);
        }
      } finally {
        this.processing = false;
      }
    },
    async favorite(id) {
      await http.put(`/api/articles/${id}/favorite`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },
    async unfavorite(id) {
      await http.delete(`/api/articles/${id}/favorite`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },

    clickReport(id) {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$refs["auth-modal"].show();
        return;
      }

      this.targetArticle = this.getTargetArticle(id);

      this.$refs["report-modal"].show();
    },
    getTargetArticle(id) {
      return this.articles.find((article) => article.id === id);
    },
  },

  async created() {
    this.articles = JSON.parse(JSON.stringify(this.propArticles));
  },
};
</script>