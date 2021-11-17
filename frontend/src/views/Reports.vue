<template>
  <div>
    <div v-for="report in reports" :key="report.id">
      <report-item class="mb-4" :report="report" @click-complite="complite" />
    </div>
  </div>
</template>

<script>
import { TYPE_ARTICLE, TYPE_GAME, TYPE_SITE } from "@/utils/const.js";
import ReportItem from "@/components/ReportItem.vue";

export default {
  components: {
    ReportItem,
  },
  data() {
    return {
      reports: [],
    };
  },
  methods: {
    async complite(id) {
      if (
        !(await this.$bvModal
          .msgBoxConfirm("通報内容を削除します。\nよろしいですか？")
          .then((value) => value))
      )
        return;

      console.log("complite:" + id);
      this.reports = this.reports.filter((report) => report.id !== id);
    },
  },
  async created() {
    this.reports = [];

    for (let i = 1; i < 10; i++) {
      this.reports.push({
        id: i,
        reportable_id: i + 1,
        reportable_type: [TYPE_GAME, TYPE_SITE, TYPE_ARTICLE][i % 3],
        content: "a".repeat(i * 3),
        user_name: "user",
        game_title: "game_title",
      });
    }
  },
};
</script>