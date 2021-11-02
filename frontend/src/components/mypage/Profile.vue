<template>
  <b-card header="ユーザ情報">
    <b-card-body>
      <profile-update />
      <password-update class="mt-3" />
      <user-delete class="mt-3" />
    </b-card-body>
  </b-card>
</template>

<script>
import ProfileUpdate from "@/components/mypage/profile/ProfileUpdate.vue";
import PasswordUpdate from "@/components/mypage/profile/PasswordUpdate.vue";
import UserDelete from "@/components/mypage/profile/UserDelete.vue";

export default {
  components: {
    ProfileUpdate,
    PasswordUpdate,
    UserDelete,
  },
};
</script>