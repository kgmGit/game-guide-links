<template>
  <div>
    <div v-for="report in reports" :key="report.id">
      <report-item class="mb-4" :report="report" @click-complite="complite" />
    </div>
  </div>
</template>

<script>
import ReportItem from "@/components/ReportItem.vue";
import { http } from "@/Services/Http";

export default {
  components: {
    ReportItem,
  },
  data() {
    return {
      processiong: false,
      reports: [],
    };
  },
  methods: {
    async complite(id) {
      if (this.processiong) return;

      if (
        !(await this.$bvModal
          .msgBoxConfirm("通報内容を削除します。\nよろしいですか？")
          .then((value) => value))
      )
        return;

      try {
        this.processiong = true;
        await this.deleteReport(id);
        this.reports = this.reports.filter((report) => report.id !== id);
      } finally {
        this.processiong = false;
      }
    },
    async deleteReport(id) {
      await http
        .delete("/api/reports/" + id)
        .then(() => {
          this.$store.dispatch("message/setContent", "通報内容を削除しました");
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
    async fetchReports() {
      await http
        .get("/api/reports/")
        .then((response) => {
          this.reports = response.data.data;
        })
        .catch(() => {
          this.$router.replace({ name: "Error" });
        });
    },
  },
  async created() {
    this.fetchReports();
  },
};
</script>