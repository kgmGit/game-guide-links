<template>
  <div>
    <b-card header="登録">
      <b-card-body>
        <ValidationObserver
          ref="form"
          v-slot="{ invalid: voInvalid, handleSubmit }"
        >
          <b-form @submit.prevent="handleSubmit(register)">
            <b-form-group label="ユーザ名" label-for="name">
              <ValidationProvider
                immediate
                vid="name"
                rules="required|max:5"
                v-slot="{ errors, valid }"
              >
                <b-form-input
                  id="name"
                  v-model="form.name"
                  maxlength="20"
                  :state="valid"
                ></b-form-input>
                <b-form-invalid-feedback :state="valid">
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </ValidationProvider>
            </b-form-group>

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
              >登録</b-button
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
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
      },
    };
  },
  methods: {
    async register() {
      try {
        this.processing = true;

        await this.$store
          .dispatch("auth/register", this.form)
          .then(() => {
            this.$store.dispatch("message/setContent", "登録しました");
            this.$router.replace({ name: "Home" });
          })
          .catch((e) => {
            if (e.response.status === 422) {
              this.$refs.form.setErrors(e.response.data.errors);
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
