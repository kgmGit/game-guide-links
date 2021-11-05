<template>
  <div>
    <b-card header="プロフィール更新">
      <b-card-body>
        <ValidationObserver
          ref="form"
          v-slot="{ invalid: voInvalid, handleSubmit }"
        >
          <b-form @submit.prevent="handleSubmit(update)">
            <b-form-group label="ユーザ名" label-for="name">
              <ValidationProvider
                immediate
                vid="name"
                rules="required|max:20"
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
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      processing: false,
      form: {
        name: "",
        email: "",
      },
    };
  },
  computed: {
    ...mapGetters({
      user: "auth/user",
    }),
  },
  methods: {
    async update() {
      try {
        this.processing = true;

        await this.$store
          .dispatch("auth/updateProfile", this.form)
          .then(() => {
            this.$store.dispatch(
              "message/setContent",
              "プロフィールを更新しました"
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
  created() {
    (this.form.name = this.user.name), (this.form.email = this.user.email);
  },
};
</script>
