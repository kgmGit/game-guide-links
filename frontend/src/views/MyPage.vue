<template>
  <b-row align-h="center">
    <b-col cols="3">
      <b-card header="メニュー">
        <b-card-body>
          <b-list-group>
            <b-list-group-item to="/mypage/favorites"
              >お気に入り</b-list-group-item
            >
            <b-list-group-item to="/mypage/posts">投稿</b-list-group-item>
            <b-list-group-item to="/mypage/profile"
              >ユーザ情報</b-list-group-item
            >
          </b-list-group>
        </b-card-body>
      </b-card>
    </b-col>

    <b-col cols="7">
      <router-view></router-view>
    </b-col>
  </b-row>
</template>

<script>
export default {};
</script>