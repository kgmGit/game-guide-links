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
      v-if="sites.length > 0"
      :total-rows="sites.length"
      v-model="currentPage"
      :per-page="perPage"
      class="mt-3"
    />

    <b-modal id="auth-site-modal" hide-header hide-footer>
      <auth-modal />
    </b-modal>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Site from "@/components/gameDetail/site/Site.vue";
import AuthModal from "@/components/AuthModal.vue";

export default {
  components: { Site, AuthModal },
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
        this.$bvModal.show("auth-site-modal");
        return;
      }

      try {
        this.processing = true;

        const index = this.sites.findIndex((site) => site.id === id);
        const isAdd = !this.sites[index].liked;

        if (isAdd) {
          await this.like(id);
        } else {
          await this.unlike(id);
        }

        this.sites[index].liked = isAdd;
        this.sites[index].likes_count += isAdd ? 1 : -1;
      } finally {
        this.processing = false;
      }
    },
    async like(id) {
      // todo: サイトいいね登録API呼び出し
      console.log("site like" + id);
    },
    async unlike(id) {
      // todo: サイトいいね解除API呼び出し
      console.log("site unlike" + id);
    },

    async clickFavorite(id) {
      if (this.processing) return;

      if (!this.isVerified) {
        this.$bvModal.show("auth-site-modal");
        return;
      }

      try {
        this.processing = true;

        const index = this.sites.findIndex((site) => site.id === id);
        const isAdd = !this.sites[index].favorited;

        if (isAdd) {
          await this.favorite(id);
        } else {
          await this.unfavorite(id);
        }

        this.sites[index].favorited = isAdd;
        this.sites[index].favorites_count += isAdd ? 1 : -1;
      } finally {
        this.processing = false;
      }
    },
    async favorite(id) {
      // todo: サイトお気に入り登録API呼び出し
      console.log("site favorite" + id);
    },
    async unfavorite(id) {
      // todo: サイトお気に入り解除API呼び出し
      console.log("site unfavorite" + id);
    },

    clickReport(id) {
      // todo: サイト通報小画面表示
      console.log(id + "report");
    },
  },

  async created() {
    this.sites = JSON.parse(JSON.stringify(this.propSites));
  },
};
</script>