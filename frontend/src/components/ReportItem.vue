<template>
  <b-card>
    <b-card-body>
      <b-card-sub-title>タイプ</b-card-sub-title>
      <b-card-text>{{ report.reportable_type }}</b-card-text>
      <b-card-sub-title>ユーザ</b-card-sub-title>
      <b-card-text>{{ report.user_name }}</b-card-text>
      <b-card-sub-title>内容</b-card-sub-title>
      <b-card-text>{{ report.content }}</b-card-text>
    </b-card-body>
    <template #footer>
      <b-row align-h="end">
        <b-button class="mr-3" :to="getLinkUrl">確認</b-button>
        <b-button @click="clickComplite">対応完了</b-button>
      </b-row>
    </template>
  </b-card>
</template>

<script>
import { TYPE_GAME, TYPE_SITE, TYPE_ARTICLE } from "@/utils/const.js";

export default {
  props: {
    report: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {};
  },
  computed: {
    getLinkUrl: function () {
      switch (this.report.reportable_type) {
        case TYPE_GAME:
          return `/games/${this.report.game_title}`;
        case TYPE_SITE:
          return `/games/${this.report.game_title}/sites/${this.report.reportable_id}/edit`;
        case TYPE_ARTICLE:
          return `/games/${this.report.game_title}/articles/${this.report.reportable_id}/edit`;
        default:
          return "";
      }
    },
  },
  methods: {
    clickComplite() {
      this.$emit("click-complite", this.report.id);
    },
  },
};
</script>