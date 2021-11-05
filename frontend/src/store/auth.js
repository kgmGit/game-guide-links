import { http } from "@/Services/Http";

const state = {
  isAuth: false,
  user: null,
};

const getters = {
  isAuth(state) {
    return state.isAuth;
  },
  isVerified(state) {
    return !!state.user && !!state.user.email_verified_at;
  },
  user(state) {
    return state.user;
  },
};

const mutations = {
  setIsAuth(state, value) {
    state.isAuth = value;
  },
  setUser(state, value) {
    state.user = value;
  },
};

const actions = {
  async register({ dispatch }, user) {
    await http.get("/sanctum/csrf-cookie");
    await http.post("/register", user);
    await dispatch("me");
  },
  async login({ dispatch }, user) {
    await http.get("/sanctum/csrf-cookie");
    await http.post("/login", user);
    await dispatch("me");
  },
  async logout({ commit }) {
    await http.get("/sanctum/csrf-cookie");
    await http.post("/logout");

    commit("setIsAuth", false);
    commit("setUser", null);
  },
  async me({ commit }) {
    await http
      .get("/api/user")
      .then((response) => {
        commit("setIsAuth", true);
        commit("setUser", response.data.data);
      })
      .catch(() => {
        commit("setIsAuth", false);
        commit("setUser", null);
      });
  },
  async sendVerifyMail() {
    await http.post("/email/verification-notification");
  },
  async sendResetPasswordMail(context, email) {
    await http.post("/forgot-password", email);
  },
  async resetPassword(context, credentials) {
    await http.post("/reset-password", credentials);
  },
  async updateProfile({ dispatch }, credentials) {
    await http.put("/user/profile-information", credentials);

    await dispatch("me");
  },
  async updatePassword(context, credentials) {
    await http.put("/user/password", credentials);
  },
  async deleteUser({ commit }) {
    await http.delete("/user");
    commit("setIsAuth", false);
    commit("setUser", null);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
