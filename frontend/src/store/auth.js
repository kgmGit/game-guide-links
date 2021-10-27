const state = {
  // isAuth: false,
  // user: null,
  isAuth: true,
  user: {
    name: "test-user",
    email_verified_at: true,
  },
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

const actions = {};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
