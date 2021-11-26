<template>
  <b-card>
    <template #header>
      <div class="text-center">
        {{ gameTitle }}
      </div>
    </template>

    <b-card-body>
      <div v-if="site" class="mb-5">
        <b-card-sub-title>タイトル</b-card-sub-title>
        <b-card-text>{{ site.title }}</b-card-text>
        <b-card-sub-title>URL</b-card-sub-title>
        <b-card-text>{{ site.url }}</b-card-text>
        <b-card-sub-title>説明</b-card-sub-title>
        <b-card-text>{{ site.description }}</b-card-text>
      </div>

      <div v-else-if="article" class="mb-5">
        <b-card-sub-title>タイトル</b-card-sub-title>
        <b-card-text>{{ article.title }}</b-card-text>
        <b-card-sub-title>概要</b-card-sub-title>
        <b-card-text>{{ article.outline }}</b-card-text>
      </div>

      <div>
        <ValidationObserver
          ref="form"
          v-slot="{ invalid: voInvalid, handleSubmit }"
        >
          <b-form @submit.prevent="handleSubmit(report)">
            <b-form-group label="通報内容" label-for="content">
              <ValidationProvider
                immediate
                vid="content"
                rules="required|max:255"
                v-slot="{ errors, valid }"
              >
                <b-form-textarea
                  id="content"
                  max-rows="3"
                  rows="3"
                  :state="valid"
                  v-model="content"
                  autofocus
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
              variant="warning"
              class="mt-5"
              >通報</b-button
            >
          </b-form>
        </ValidationObserver>
      </div>
    </b-card-body>
  </b-card>
</template>

<script>
import { http } from "@/Services/Http";
export default {
  props: {
    gameTitle: {
      type: String,
    },
    site: {
      type: Object,
      default: null,
    },
    article: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      processing: false,
      content: "",
    };
  },
  computed: {
    reportUrl: function () {
      if (this.site) {
        return `/api/sites/${this.site.id}/report`;
      } else if (this.article) {
        return `/api/articles/${this.article.id}/report`;
      } else {
        return `/api/games/${this.gameTitle}/report`;
      }
    },
  },
  methods: {
    async report() {
      if (this.processing) return;
      try {
        this.processing = true;

        const body = {
          content: this.content,
        };
        await http
          .post(this.reportUrl, body)
          .then(() => {
            this.$store.dispatch("message/setContent", "通報が完了しました");
            this.$emit("reported");
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