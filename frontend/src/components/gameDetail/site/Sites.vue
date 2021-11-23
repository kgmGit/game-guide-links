<template>
  <div v-if="sites">
    <div v-for="site in getPaginateItems" :key="'site' + site.id" class="mt-3">
      <site
        :site="site"
        @click-like="clickLike"
        @click-favorite="clickFavorite"
        @click-report="clickReport"
      />
    </div>
    <b-pagination
      v-if="sites.length > perPage"
      :total-rows="sites.length"
      v-model="currentPage"
      :per-page="perPage"
      class="mt-3"
    />

    <b-modal ref="auth-modal" hide-header hide-footer>
      <auth-modal />
    </b-modal>

    <b-modal ref="report-modal" size="lg" hide-header hide-footer>
      <report-modal
        :gameTitle="targetSite && targetSite.game_title"
        :site="targetSite"
        @reported="$refs['report-modal'].hide()"
      />
    </b-modal>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Site from "@/components/gameDetail/site/Site.vue";
import AuthModal from "@/components/AuthModal.vue";
import ReportModal from "@/components/ReportModal.vue";
import { http } from "@/Services/Http";

export default {
  components: { Site, AuthModal, ReportModal },
  props: {
    propSites: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      processing: false,
      sites: [],
      targetSite: null,

      perPage: 10,
      currentPage: 1,
    };
  },

  computed: {
    ...mapGetters({
      isVerified: "auth/isVerified",
    }),

    getPaginateItems: function () {
      const start = (this.currentPage - 1) * this.perPage;
      const end = this.currentPage * this.perPage;
      return this.sites.slice(start, end);
    },
  },

  methods: {
    async clickLike(id) {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$refs["auth-modal"].show();
        return;
      }

      try {
        this.processing = true;

        const index = this.sites.findIndex((site) => site.id === id);
        const isAdd = !this.sites[index].liked;
        this.sites[index].liked = isAdd;
        this.sites[index].likes_count += isAdd ? 1 : -1;

        if (isAdd) {
          await this.like(id);
        } else {
          await this.unlike(id);
        }
      } finally {
        this.processing = false;
      }
    },
    async like(id) {
      await http.put(`/api/sites/${id}/like`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },
    async unlike(id) {
      await http.delete(`/api/sites/${id}/like`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },

    async clickFavorite(id) {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$refs["auth-modal"].show();
        return;
      }

      try {
        this.processing = true;

        const index = this.sites.findIndex((site) => site.id === id);
        const isAdd = !this.sites[index].favorited;
        this.sites[index].favorited = isAdd;
        this.sites[index].favorites_count += isAdd ? 1 : -1;

        if (isAdd) {
          await this.favorite(id);
        } else {
          await this.unfavorite(id);
        }
      } finally {
        this.processing = false;
      }
    },
    async favorite(id) {
      await http.put(`/api/sites/${id}/favorite`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },
    async unfavorite(id) {
      await http.delete(`/api/sites/${id}/favorite`).catch(() => {
        this.$router.replace({ name: "Error" });
      });
    },

    clickReport(id) {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$refs["auth-modal"].show();
        return;
      }

      this.targetSite = this.getTargetSite(id);

      this.$refs["report-modal"].show();
    },
    getTargetSite(id) {
      return this.sites.find((site) => site.id === id);
    },
  },

  async created() {
    this.sites = JSON.parse(JSON.stringify(this.propSites));
  },
};
</script>