<template>
  <div>
    <b-card>
      <template #header>
        <b-link :to="'/games/' + $route.params.game_title">
          <h4 class="text-center">
            {{ $route.params.game_title }}
          </h4>
        </b-link>
      </template>

      <b-card-body>
        <ValidationObserver
          ref="form"
          v-slot="{ invalid: voInvalid, handleSubmit }"
        >
          <b-form @submit.prevent="handleSubmit(registerOrUpdate)">
            <b-form-group label="タイトル" label-for="title">
              <ValidationProvider
                immediate
                vid="title"
                rules="required"
                v-slot="{ errors, valid }"
              >
                <b-form-input
                  id="title"
                  :state="valid"
                  maxlength="20"
                  v-model="form.title"
                ></b-form-input>
                <b-form-invalid-feedback :state="valid">
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </ValidationProvider>
            </b-form-group>

            <b-form-group label="URL" label-for="url">
              <ValidationProvider
                immediate
                vid="url"
                rules="required|url: {require_protocol: true }"
                v-slot="{ errors, valid }"
              >
                <b-form-input
                  id="url"
                  maxlength="255"
                  :state="valid"
                  v-model="form.url"
                ></b-form-input>
                <b-form-invalid-feedback :state="valid">
                  {{ errors[0] }}
                </b-form-invalid-feedback>
              </ValidationProvider>
            </b-form-group>

            <b-form-group label="説明" label-for="description">
              <ValidationProvider
                immediate
                vid="description"
                rules="required|max:30"
                v-slot="{ errors, valid }"
              >
                <b-form-textarea
                  id="description"
                  max-rows="3"
                  rows="3"
                  :state="valid"
                  v-model="form.description"
                ></b-form-textarea>
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
              >{{ isEdit ? "更新" : "登録" }}</b-button
            >
          </b-form>
        </ValidationObserver>
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
export default {
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      processing: false,
      form: {
        title: "",
        url: "",
        description: "",
      },
    };
  },
  methods: {
    async registerOrUpdate() {
      if (this.processing) return;

      try {
        this.processing = true;

        if (this.isEdit) {
          await this.update();
        } else {
          await this.register();
        }
      } finally {
        this.processing = false;
      }
    },
    async register() {
      console.log("登録");
    },
    async update() {
      console.log("更新");
    },
  },
};
</script>
