<template>
  <div>
    <b-card header="メール認証が完了していません">
      <b-card-body>
        <verifi-email-send-btn />
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
import VerifiEmailSendBtn from "@/components/VerifiEmailSendBtn.vue";

export default {
  components: {
    VerifiEmailSendBtn,
  },
};
</script>
