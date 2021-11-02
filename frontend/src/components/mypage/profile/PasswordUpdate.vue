<template>
  <div>
    <b-card header="パスワード更新">
      <b-card-body>
        <ValidationObserver
          ref="form"
          v-slot="{ invalid: voInvalid, handleSubmit }"
        >
          <b-form @submit.prevent="handleSubmit(update)">
            <b-form-group label="現在のパスワード" label-for="current_password">
              <ValidationProvider
                immediate
                rules="required|min:5|max:255"
                v-slot="{ errors, valid }"
                vid="current_password"
              >
                <b-form-input
                  id="current_password"
                  type="password"
                  maxlength="255"
                  :state="valid"
                  v-model="form.current_password"
                ></b-form-input>
                <b-form-invalid-feedback :state="valid">
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </ValidationProvider>
            </b-form-group>

            <b-form-group label="新しいパスワード" label-for="password">
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
              >更新</b-button
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
        current_password: "",
        password: "",
        password_confirmation: "",
      },
    };
  },
  methods: {
    async update() {
      try {
        this.processing = true;

        await this.$store
          .dispatch("auth/updatePassword", this.form)
          .then(() => {
            this.$store.dispatch(
              "message/setContent",
              "パスワードを更新しました"
            );
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
