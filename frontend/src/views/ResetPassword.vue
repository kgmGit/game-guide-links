<template>
  <div>
    <b-card header="パスワードのリセット">
      <b-card-body>
        <ValidationObserver
          ref="form"
          v-slot="{ invalid: voInvalid, handleSubmit }"
        >
          <b-form @submit.prevent="handleSubmit(resetPassword)">
            <b-form-group label="メールアドレス" label-for="email">
              <ValidationProvider
                immediate
                vid="email"
                rules="required|max:255|email"
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

            <b-form-group label="パスワード" label-for="password">
              <ValidationProvider
                immediate
                rules="required|min:5|max:255"
                v-slot="{ errors, valid }"
                vid="パスワード"
              >
                <b-form-input
                  id="password"
                  type="password"
                  maxlength="255"
                  :state="valid"
                  v-model="form.password"
                ></b-form-input>
                <b-form-invalid-feedback :state="valid">
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </ValidationProvider>
            </b-form-group>

            <b-form-group
              label="パスワード確認"
              label-for="password_confirmation"
            >
              <ValidationProvider
                immediate
                rules="required|confirmed:パスワード"
                v-slot="{ errors, valid }"
              >
                <b-form-input
                  id="password_confirmation"
                  type="password"
                  maxlength="255"
                  :state="valid"
                  v-model="form.password_confirmation"
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
              >パスワードリセット</b-button
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
        password: "",
        password_confirmation: "",
      },
    };
  },
  methods: {
    async resetPassword() {
      try {
        this.processing = true;

        const body = {
          email: this.form.email,
          password: this.form.password,
          password_confirmation: this.form.password_confirmation,
          token: this.$route.query.token,
        };

        await this.$store
          .dispatch("auth/resetPassword", body)
          .then(() => {
            this.$store.dispatch(
              "message/setContent",
              "パスワードを変更しました"
            );
            this.$router.replace({ name: "Home" });
          })
          .catch((e) => {
            if (e.response.status === 422) {
              this.$refs.form.setErrors({
                email: ["登録されているメールアドレスと異なります"],
              });
              return;
            }
            this.$router.replace({ name: "Error" });
          });
      } finally {
        this.processing = false;
      }
    },
  },
};
</script>
