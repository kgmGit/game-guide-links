<template>
  <div>
    <b-form-input
      v-model="searchStr"
      placeholder="ゲーム名を入力"
      @keypress.prevent.enter="pressEnter"
    ></b-form-input>
    <div class="mt-2">
      <div v-for="game in games" :key="game.id">
        <game :game="game" />
      </div>
    </div>
    <b-modal title="確認" @ok="addGame" id="confirm"
      >{{ searchStr }}を追加しますか？</b-modal
    >
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Game from "@/components/home/Game";

export default {
  components: {
    Game,
  },
  data() {
    return {
      searchStr: "",
      games: [
        { name: "noita" },
        { name: "テイルズオブアライズ" },
        { name: "New World" },
        { name: "ドラゴンクエストⅪ" },
        { name: "Back 4 Blood" },
      ],
    };
  },
  computed: {
    ...mapGetters({
      isVerified: "auth/isVerified",
    }),
  },
  methods: {
    pressEnter() {
      if (this.isVerified && !this.searchStr) return;
      this.$bvModal.show("confirm");
    },
    addGame() {
      console.log("add game!");
    },
  },
};
</script>
