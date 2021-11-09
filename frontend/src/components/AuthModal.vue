<template>
  <div class="my-4">
    <div v-if="!isAuth">
      <div class="text-center">ログインが必要です</div>
      <b-button to="/login" size="lg" block variant="primary" class="mt-3"
        >ログイン</b-button
      >
    </div>
    <div v-else-if="!isVerified">
      <div class="text-center">メール認証が必要です</div>
      <verifi-email-send-btn class="mt-3" />
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import VerifiEmailSendBtn from "@/components/VerifiEmailSendBtn.vue";

export default {
  components: {
    VerifiEmailSendBtn,
  },
  computed: {
    ...mapGetters({
      isAuth: "auth/isAuth",
      isVerified: "auth/isVerified",
    }),
  },
};
</script>