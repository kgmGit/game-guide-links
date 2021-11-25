<template>
  <div v-if="game">
    <b-card>
      <template #header>
        <h4 class="text-center">
          {{ game.title }}
          <b-button
            v-if="game.owner || isAdmin"
            variant="outline-dark"
            @click="clickDelete"
          >
            <font-awesome-icon :icon="['fas', 'trash-alt']" />
          </b-button>
        </h4>
      </template>
      <b-card-body>
        <b-row align-h="end">
          <favorite
            :count="game.favorites_count"
            :favorited="game.favorited"
            @click="clickFavorite"
          />
          <report @click="clickReport" class="ml-3" />
        </b-row>

        <b-modal ref="auth-modal" hide-header hide-footer>
          <auth-modal />
        </b-modal>

        <b-modal ref="report-modal" size="lg" hide-header hide-footer>
          <report-modal
            :gameTitle="game.title"
            @reported="$refs['report-modal'].hide()"
          />
        </b-modal>

        <b-row class="mt-5">
          <b-col sm="6">
            <b-card>
              <template #header>
                <div class="text-center">攻略サイト</div>
              </template>

              <b-card-body>
                <div v-if="isVerified">
                  <b-button
                    :to="$route.path + '/sites/add'"
                    size="lg"
                    block
                    variant="primary"
                    >新規登録</b-button
                  >
                  <hr v-if="hasSites" class="my-4" />
                </div>
                <div v-if="hasSites">
                  <sites :propSites="sites" />
                </div>
              </b-card-body>
            </b-card>
          </b-col>

          <b-col sm="6">
            <b-card>
              <template #header>
                <div class="text-center">攻略記事</div>
              </template>

              <b-card-body>
                <div v-if="isVerified">
                  <b-button
                    :to="$route.path + '/articles/add'"
                    size="lg"
                    block
                    variant="primary"
                    >新規登録</b-button
                  >
                  <hr v-if="hasArticles" class="my-4" />
                </div>
                <div v-if="hasArticles">
                  <articles :propArticles="articles" />
                </div>
              </b-card-body>
            </b-card>
          </b-col>
        </b-row>
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Favorite from "@/components/Favorite.vue";
import AuthModal from "@/components/AuthModal.vue";
import Report from "@/components/Report.vue";
import { http } from "@/Services/Http";
import Sites from "@/components/gameDetail/site/Sites.vue";
import Articles from "@/components/gameDetail/article/Articles.vue";
import ReportModal from "@/components/ReportModal.vue";

export default {
  components: {
    Favorite,
    AuthModal,
    Report,
    Sites,
    Articles,
    ReportModal,
  },
  data() {
    return {
      game: null,
      processing: false,
      sites: null,
      articles: null,
    };
  },
  computed: {
    ...mapGetters({
      isVerified: "auth/isVerified",
      isAuth: "auth/isAuth",
      isAdmin: "auth/isAdmin",
    }),
    hasSites: function () {
      return this.sites !== null && this.sites.length > 0;
    },
    hasArticles: function () {
      return this.articles !== null && this.articles.length > 0;
    },
  },
  methods: {
    async clickDelete() {
      if (this.processing) return;

      if (this.hasSites || this.hasArticle) {
        this.$bvModal.msgBoxOk(
          "攻略サイト、攻略情報が追加されているゲームは削除できません"
        );
        return;
      }

      if (
        !(await this.$bvModal.msgBoxConfirm(
          "ゲームを削除します。\nよろしいですか？"
        ))
      ) {
        return;
      }

      try {
        this.processing = true;

        this.deleteGame();
      } finally {
        this.processing = false;
      }
    },
    async deleteGame() {
      await http
        .delete("/api/games/" + this.game.title)
        .then(() => {
          this.$store.dispatch("message/setContent", "ゲームを削除しました");
          this.$router.replace({ name: "Home" });
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },

    async clickFavorite() {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$refs["auth-modal"].show();
        return;
      }

      try {
        this.processing = true;

        const isAdd = !this.game.favorited;
        this.game.favorited = isAdd;
        this.game.favorites_count += isAdd ? 1 : -1;

        if (isAdd) {
          await this.favorite();
        } else {
          await this.unfavorite();
        }
      } finally {
        this.processing = false;
      }
    },
    async favorite() {
      await http.put(`/api/games/${this.game.title}/favorite`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },
    async unfavorite() {
      await http.delete(`/api/games/${this.game.title}/favorite`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },

    clickReport() {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$refs["auth-modal"].show();
        return;
      }

      this.$refs["report-modal"].show();
    },

    async fetchSites() {
      await http
        .get("/api/games/" + this.game.title + "/sites")
        .then((response) => {
          this.sites = response.data.data;
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
    async fetchArticles() {
      await http
        .get("/api/games/" + this.game.title + "/articles")
        .then((response) => {
          this.articles = response.data.data;
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
  },
  async created() {
    try {
      this.processing = true;

      const title = this.$route.params.title;
      await http
        .get("/api/games/" + title)
        .then((response) => {
          this.game = response.data.data;
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    } finally {
      this.processing = false;
    }
    this.fetchSites();
    this.fetchArticles();
  },
};
</script>