<template>
  <b-card header="ユーザ情報">
    <b-card-body>
      <verifi-email-send v-if="!isVerified" />
      <profile-update class="mt-3" />
      <password-update class="mt-3" />
      <user-delete class="mt-3" />
    </b-card-body>
  </b-card>
</template>

<script>
import { mapGetters } from "vuex";
import VerifiEmailSend from "@/components/mypage/profile/VerifiEmailSend.vue";
import ProfileUpdate from "@/components/mypage/profile/ProfileUpdate.vue";
import PasswordUpdate from "@/components/mypage/profile/PasswordUpdate.vue";
import UserDelete from "@/components/mypage/profile/UserDelete.vue";

export default {
  computed: {
    ...mapGetters({
      isVerified: "auth/isVerified",
    }),
  },
  components: {
    VerifiEmailSend,
    ProfileUpdate,
    PasswordUpdate,
    UserDelete,
  },
};
</script>