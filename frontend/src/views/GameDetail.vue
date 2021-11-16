<template>
  <div v-if="game">
    <b-card>
      <template #header>
        <h4 class="text-center">
          {{ game.title }}
          <b-button
            v-if="game.owner"
            variant="outline-dark"
            @click="deleteGame"
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

        <b-modal id="auth-game-modal" hide-header hide-footer>
          <auth-modal />
        </b-modal>

        <b-modal id="report-modal" size="lg" hide-header hide-footer>
          <report-modal
            :gameTitle="game.title"
            @reported="$bvModal.hide('report-modal')"
          />
        </b-modal>

        <b-row class="mt-5">
          <b-col sm>
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

                  <div v-if="hasSites">
                    <hr class="my-4" />
                    <sites :propSites="sites" />
                  </div>
                </div>
              </b-card-body>
            </b-card>
          </b-col>

          <b-col sm>
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

                  <div v-if="hasArticles">
                    <hr class="my-4" />
                    <articles :propArticles="articles" />
                  </div>
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
    }),
    hasSites: function () {
      return this.sites !== null && this.sites.length > 0;
    },
    hasArticles: function () {
      return this.articles !== null && this.articles.length > 0;
    },
  },
  methods: {
    async deleteGame() {
      if (this.hasSites || this.hasArticle) {
        this.$bvModal.msgBoxOk(
          "攻略サイト、攻略情報が追加されているゲームは削除できません"
        );
        return;
      }

      try {
        this.processing = true;
        // todo: ゲーム削除API呼び出し
        console.log("delete game");
      } finally {
        this.processing = false;
      }
    },
    async clickFavorite() {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$bvModal.show("auth-game-modal");
        return;
      }

      try {
        this.processing = true;

        const isAdd = !this.game.favorited;
        if (isAdd) {
          await this.favorite();
        } else {
          await this.unfavorite();
        }

        this.game.favorited = isAdd;
        this.game.favorites_count += isAdd ? 1 : -1;
      } finally {
        this.processing = false;
      }
    },
    async favorite() {
      // todo:ゲームお気に入り登録API呼び出し
      console.log("favorite");
    },
    async unfavorite() {
      // todo:ゲームお気に入り登録API呼び出し
      console.log("unfavorite");
    },

    clickReport() {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$bvModal.show("auth-game-modal");
        return;
      }

      this.$bvModal.show("report-modal");
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
    const title = this.$route.params.title;
    await http
      .get("/api/games/" + title)
      .then((response) => {
        this.game = response.data.data;
      })
      .catch(() => {
        this.$router.replace({ name: "Error" });
      });

    this.fetchSites();
    this.fetchArticles();
  },
};
</script>