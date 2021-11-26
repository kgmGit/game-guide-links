<template>
  <div>
    <b-card header="ログイン">
      <b-card-body>
        <ValidationObserver
          ref="form"
          v-slot="{ invalid: voInvalid, handleSubmit }"
        >
          <b-form @submit.prevent="handleSubmit(login)">
            <b-form-group label="メールアドレス" label-for="email">
              <ValidationProvider
                immediate
                vid="email"
                rules="required"
                v-slot="{ errors, valid }"
              >
                <b-form-input
                  id="email"
                  :state="valid"
                  maxlength="255"
                  v-model="form.email"
                  autofocus
                ></b-form-input>
                <b-form-invalid-feedback :state="valid">
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </ValidationProvider>
            </b-form-group>

            <b-form-group label="パスワード" label-for="password">
              <ValidationProvider
                immediate
                vid="password"
                rules="required"
                v-slot="{ errors, valid }"
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

            <div class="mt-2">
              <router-link to="/forgot-password"
                >パスワードを忘れた場合</router-link
              >
            </div>

            <b-button
              :disabled="voInvalid || processing"
              size="lg"
              block
              type="submit"
              variant="primary"
              class="mt-5"
              >ログイン</b-button
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
      },
    };
  },
  methods: {
    async login() {
      try {
        this.processing = true;

        await this.$store
          .dispatch("auth/login", this.form)
          .then(() => {
            this.$store.dispatch("message/setContent", "ログインしました");
            this.$router.replace({ name: "Home" });
          })
          .catch((e) => {
            if (e.response.status === 422) {
              this.$refs.form.setErrors({
                password: ["メールアドレスまたはパスワードが異なります"],
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
