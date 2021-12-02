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

      <ValidationObserver
        ref="form"
        v-slot="{ invalid: voInvalid, handleSubmit }"
      >
        <b-form @submit.prevent="handleSubmit(registerOrUpdate)">
          <b-form-group label="タイトル" label-for="title">
            <ValidationProvider
              immediate
              vid="title"
              rules="required|max:20"
              v-slot="{ errors, valid }"
            >
              <b-form-input
                id="title"
                :state="valid"
                maxlength="20"
                v-model="form.title"
                autofocus
              ></b-form-input>
              <b-form-invalid-feedback :state="valid">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </ValidationProvider>
          </b-form-group>

          <b-form-group label="概要" label-for="outline">
            <ValidationProvider
              immediate
              vid="outline"
              rules="required|max:200"
              v-slot="{ errors, valid }"
            >
              <b-form-textarea
                id="outline"
                max-rows="4"
                rows="4"
                :state="valid"
                v-model="form.outline"
              ></b-form-textarea>
              <b-form-invalid-feedback :state="valid">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </ValidationProvider>
          </b-form-group>

          <b-form-group label="攻略情報" label-for="content">
            <ValidationProvider
              immediate
              vid="content"
              rules="required|max_byte:6000"
              v-slot="{ errors, valid }"
            >
              <vue-editor
                id="content"
                :state="valid"
                v-model="form.content"
                :editor-toolbar="customToolbar"
              />
              <b-form-invalid-feedback :state="valid">
                {{ errors[0] }}
              </b-form-invalid-feedback>
            </ValidationProvider>
          </b-form-group>

          <b-card header="プレビュー" class="mt-5">
            <div v-html="form.content" class="ql-editor"></div>
          </b-card>

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

      <b-modal title="確認" @ok="deleteArticle" id="confirm"
        >攻略記事を削除しますか？</b-modal
      >
    </b-card>
  </div>
</template>

<script>
import { VueEditor } from "vue2-editor";
import { http } from "@/Services/Http";

export default {
  components: {
    VueEditor,
  },
  props: {
    id: {
      default: null,
    },
  },
  data() {
    return {
      processing: false,
      gameTitle: "",
      form: {
        title: "",
        outline: "",
        content: "",
      },
      customToolbar: [
        [
          {
            header: [false, 1, 2, 3, 4, 5, 6],
          },
        ],
        ["bold", "italic", "underline", "strike"],
        [
          {
            align: "",
          },
          {
            align: "center",
          },
          {
            align: "right",
          },
          {
            align: "justify",
          },
        ],
        ["blockquote"],
        [
          {
            list: "ordered",
          },
          {
            list: "bullet",
          },
          {
            list: "check",
          },
        ],
        [
          {
            indent: "-1",
          },
          {
            indent: "+1",
          },
        ],
        [
          {
            color: [],
          },
          {
            background: [],
          },
        ],
        ["clean"],
      ],
    };
  },
  computed: {
    isEdit: function () {
      return !!this.id;
    },
  },
  methods: {
    async registerOrUpdate() {
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
        .post(`/api/games/${this.gameTitle}/articles`, this.form)
        .then(() => {
          this.$store.dispatch("message/setContent", "攻略記事を登録しました");
          this.$router.push("/games/" + this.gameTitle);
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
    async update() {
      await http
        .patch(`/api/games/${this.gameTitle}/articles/${this.id}`, this.form)
        .then(() => {
          this.$store.dispatch("message/setContent", "攻略記事を更新しました");
          this.$router.push("/games/" + this.gameTitle);
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
    async deleteArticle() {
      await http
        .delete(`/api/games/${this.gameTitle}/articles/${this.id}`, this.form)
        .then(() => {
          this.$store.dispatch("message/setContent", "攻略記事を削除しました");
          this.$router.push("/games/" + this.gameTitle);
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
    async fetchArticle() {
      await http
        .get("/api/games/" + this.gameTitle + "/articles/" + this.id)
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
        await this.fetchArticle();
      }
    } finally {
      this.processing = false;
    }
  },
};
</script>
