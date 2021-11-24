<template>
  <div>
    <b-navbar toggleable="sm" type="dark" variant="dark" class="px-5">
      <b-navbar-brand custom to="/">ゲーム攻略リンク</b-navbar-brand>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav>
          <b-nav-item v-show="!isAuth" to="/register">登録</b-nav-item>
          <b-nav-item v-show="!isAuth" to="/login">ログイン</b-nav-item>
          <b-nav-item v-show="isAuth" @click.prevent="logout"
            >ログアウト</b-nav-item
          >
        </b-navbar-nav>

        <b-navbar-nav class="ml-auto">
          <b-nav-item v-show="isAdmin" to="/reports">通報内容確認</b-nav-item>
          <b-nav-item v-show="isAuth" to="/mypage/favorites/games"
            >マイページ</b-nav-item
          >
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  computed: {
    ...mapGetters({
      isAuth: "auth/isAuth",
      isVerified: "auth/isVerified",
      isAdmin: "auth/isAdmin",
      user: "auth/user",
    }),
  },
  methods: {
    async logout() {
      await this.$store.dispatch("auth/logout");
      this.$store.dispatch("message/setContent", "ログアウトしました");
      this.$router.replace({ name: "Login" });
    },
  },
};
</script>