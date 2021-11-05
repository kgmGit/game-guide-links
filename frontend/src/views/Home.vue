<template>
  <div>
    <b-form-input
      v-model="searchStr"
      placeholder="ゲーム名を入力"
      @keypress.prevent.enter="pressEnter"
      autocomplete="off"
    ></b-form-input>
    <div class="mt-2">
      <transition-group name="games">
        <div v-for="game in games" :key="game.id">
          <game :game="game" />
        </div>
      </transition-group>
    </div>
    <b-modal title="確認" @ok="addGame" id="confirm"
      >{{ searchStr }}を追加しますか？</b-modal
    >
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Game from "@/components/home/Game";
import { http } from "@/Services/Http";

export default {
  components: {
    Game,
  },
  data() {
    return {
      processing: false,
      searchStr: "",
      searchedStr: "",
      games: null,
      searchIntervalId: null,
    };
  },
  computed: {
    ...mapGetters({
      isVerified: "auth/isVerified",
    }),
    SEARCH_INTERVAL() {
      return 500;
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
                game: game,
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
    async getGames() {
      if (!this.searchStr) {
        this.searchedStr = this.searchStr;
        this.games = null;
        return;
      }

      if (this.searchStr === this.searchedStr) {
        return;
      }

      this.searchedStr = this.searchStr;
      await http
        .get("/api/games/?title=" + this.searchedStr)
        .then((response) => {
          this.games = response.data.data;
        })
        .catch(() => {});
    },
  },
  created() {
    this.searchIntervalId = setInterval(
      this.getGames.bind(this),
      this.SEARCH_INTERVAL
    );
  },
  beforeDestroy() {
    clearInterval(this.searchIntervalId);
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