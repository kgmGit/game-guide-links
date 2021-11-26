<template>
  <b-row align-h="center">
    <b-col xl="3" lg="4">
      <b-card header="メニュー" class="mb-5">
        <b-card-body>
          <b-list-group>
            <div v-if="isVerified">
              <b-list-group-item>
                <b-dropdown block variant="outline-dark" text="お気に入り">
                  <b-dropdown-item to="/mypage/favorites/games"
                    >ゲーム</b-dropdown-item
                  >
                  <b-dropdown-item to="/mypage/favorites/sites"
                    >攻略サイト</b-dropdown-item
                  >
                  <b-dropdown-item to="/mypage/favorites/articles"
                    >攻略記事</b-dropdown-item
                  >
                </b-dropdown>
              </b-list-group-item>

              <b-list-group-item>
                <b-dropdown block variant="outline-dark" text="投稿">
                  <b-dropdown-item to="/mypage/posts/games"
                    >ゲーム</b-dropdown-item
                  >
                  <b-dropdown-item to="/mypage/posts/sites"
                    >攻略サイト</b-dropdown-item
                  >
                  <b-dropdown-item to="/mypage/posts/articles"
                    >攻略記事</b-dropdown-item
                  >
                </b-dropdown>
              </b-list-group-item>
            </div>
            <b-list-group-item>
              <b-button to="/mypage/profile" block variant="outline-dark"
                >ユーザ情報</b-button
              ></b-list-group-item
            >
          </b-list-group>
        </b-card-body>
      </b-card>
    </b-col>

    <b-col xl="7" lg="8">
      <router-view></router-view>
    </b-col>
  </b-row>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  computed: {
    ...mapGetters({
      isVerified: "auth/isVerified",
    }),
  },
};
</script>