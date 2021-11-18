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
                rules="required|max:200"
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

            <b-button
              v-if="isEdit"
              :disabled="processing"
              v-b-modal.confirm
              size="lg"
              block
              type="button"
              variant="danger"
              class="mt-5"
              >削除</b-button
            >
          </b-form>
        </ValidationObserver>

        <b-modal title="確認" @ok="deleteSite" id="confirm"
          >攻略サイトを削除しますか？</b-modal
        >
      </b-card-body>
    </b-card>
  </div>
</template>

<script>
import { http } from "@/Services/Http";

export default {
  props: {
    id: {
      default: false,
    },
  },
  data() {
    return {
      processing: false,
      gameTitle: "",
      form: {
        title: "",
        url: "",
        description: "",
      },
    };
  },
  computed: {
    isEdit: function () {
      return !!this.id;
    },
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
      await http
        .post(`/api/games/${this.gameTitle}/sites/`, this.form)
        .then(() => {
          this.$store.dispatch(
            "message/setContent",
            "攻略サイトを登録しました"
          );
          this.$router.push("/games/" + this.gameTitle);
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
    async update() {
      console.log("更新");
    },
    async deleteSite() {
      console.log("削除");
    },
    async fetchSite() {
      await http
        .get(`/api/games/${this.gameTitle}/sites/${this.id}`)
        .then((response) => {
          this.form = response.data.data;
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
  },
  async created() {
    try {
      this.processing = true;

      this.gameTitle = this.$route.params.game_title;
      if (this.isEdit) {
        await this.fetchSite();
      }
    } finally {
      this.processing = false;
    }
  },
};
</script>
