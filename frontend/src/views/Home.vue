<template>
  <div>
    <b-form-input
      v-model="searchStr"
      :placeholder="'ゲーム名を入力' + (isVerified ? '・Enterで新規登録' : '')"
      @keypress.prevent.enter="pressEnter"
      @input="onInput"
      autocomplete="off"
      maxlength="30"
      autofocus
    ></b-form-input>
    <div class="mt-2 mb-5">
      <transition-group name="games">
        <div v-for="game in games" :key="game.id">
          <game :game="game" />
        </div>
        <b-card v-if="isShowMoreGameBtn" key="more-btn">
          <b-button
            @click="moreGames"
            :disabled="processing"
            size="lg"
            block
            type="button"
            variant="primary"
            >さらに表示</b-button
          >
        </b-card>
      </transition-group>
    </div>
    <b-modal title="確認" hide-header @ok="addGame" id="confirm">
      <div class="text-center">{{ searchStr }}を追加しますか？</div>
    </b-modal>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Game from "@/components/home/Game";
import { http } from "@/Services/Http";
import { SEARCH_GAMES_PER_PAGE } from "@/utils/const";

export default {
  components: {
    Game,
  },
  data() {
    return {
      processing: false,
      searchStr: "",
      games: [],
      timeOutId: null,
      page: 1,
      isNoMoreGames: true,
    };
  },
  computed: {
    ...mapGetters({
      isVerified: "auth/isVerified",
    }),
    SEARCH_INTERVAL() {
      return 500;
    },
    isShowMoreGameBtn() {
      return (
        this.games &&
        this.games.length > 0 &&
        this.games.length % SEARCH_GAMES_PER_PAGE === 0 &&
        !this.isNoMoreGames
      );
    },
  },
  methods: {
    pressEnter() {
      if (this.processing || !this.isVerified || !this.searchStr) return;
      this.$bvModal.show("confirm");
    },
    async addGame() {
      try {
        this.processing = true;

        const body = {
          title: this.searchStr,
        };

        await http
          .post("/api/games", body)
          .then((response) => {
            const game = response.data.data;

            this.$store.dispatch("message/setContent", "ゲームを登録しました");
            this.$router.push({
              name: "GameDetail",
              params: {
                title: game.title,
              },
            });
          })
          .catch((e) => {
            if (e.response.status === 422) {
              this.$store.dispatch(
                "message/setContent",
                "すでに登録されています"
              );

              return;
            }
            this.$router.replace({ name: "Error" });
          });
      } finally {
        this.processing = false;
      }
    },

    onInput() {
      this.page = 1;
      this.isNoMoreGames = false;

      if (this.timeOutId) {
        clearTimeout(this.timeOutId);
      }
      if (!this.searchStr) {
        this.games = [];
        return;
      }

      this.timeOutId = setTimeout(
        this.fetchGames.bind(this),
        this.SEARCH_INTERVAL
      );
    },
    async fetchGames() {
      await http
        .get(`/api/games/?title=${this.searchStr}`)
        .then((response) => {
          this.games = response.data.data;
        })
        .catch(() => {});

      if (!this.searchStr) {
        this.games = [];
      }
    },
    async moreGames() {
      this.page++;
      try {
        this.processing = true;
        await http
          .get(`/api/games/?title=${this.searchStr}&page=${this.page}`)
          .then((response) => {
            const moreGames = response.data.data;
            this.games = this.games.concat(moreGames);
            this.isNoMoreGames = moreGames.length === 0;
          })
          .catch(() => {});
      } finally {
        this.processing = false;
      }
    },
  },
};
</script>

<style scoped>
.games-leave-active,
.games-enter-active {
  transition: opacity 0.2s, transform 0.2s ease;
}

.games-leave-active {
  position: absolute;
  width: 100%;
}

.games-leave-to,
.games-enter {
  opacity: 0;
  transform: translateY(50px);
}
.games-leave,
.games-enter-to {
  opacity: 1;
}
.games-move {
  transition: transform 0.2s;
}
</style>