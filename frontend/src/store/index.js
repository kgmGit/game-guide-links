import Vue from "vue";
import Vuex from "vuex";
import auth from "@/store/auth";
import message from "@/store/message";
import error from "@/store/error";

Vue.use(Vuex);

export default new Vuex.Store({
  strict: true,
  modules: {
    auth,
    message,
    error,
  },
});
