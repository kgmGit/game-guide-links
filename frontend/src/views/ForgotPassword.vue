<template>
  <div>
    <b-card header="パスワードリセットメールの送信">
      <b-card-body>
        <ValidationObserver
          ref="form"
          v-slot="{ invalid: voInvalid, handleSubmit }"
        >
          <b-form @submit.prevent="handleSubmit(sendMail)">
            <b-form-group label="メールアドレス" label-for="email">
              <ValidationProvider
                immediate
                vid="email"
                rules="required|email"
                v-slot="{ errors, valid }"
              >
                <b-form-input
                  id="email"
                  :state="valid"
                  maxlength="255"
                  v-model="form.email"
                ></b-form-input>
                <b-form-invalid-feedback :state="valid">
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </ValidationProvider>
            </b-form-group>

            <b-button
              :disabled="voInvalid || processing"
              size="lg"
              block
              type="submit"
              variant="primary"
              class="mt-5"
              >送信</b-button
            >
          </b-form>
        </ValidationObserver>
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      processing: false,
      form: {
        email: "",
      },
    };
  },
  methods: {
    async sendMail() {
      try {
        this.processing = true;

        await this.$store
          .dispatch("auth/sendResetPasswordMail", this.form)
          .then(() => {
            this.$store.dispatch("message/setContent", "メールを送信しました");
            this.$router.replace({ name: "Home" });
          })
          .catch(() => {
            this.$router.replace({ name: "Error" });
          });
      } finally {
        this.processing = false;
      }
    },
  },
};
</script>
