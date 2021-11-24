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