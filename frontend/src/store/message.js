const DISPLAY_TIME = 3000;

const state = {
  content: null,
};

const getters = {
  content(state) {
    return state.content;
  },
};

const mutations = {
  setContent(state, value) {
    state.content = value;
  },
};

const actions = {
  setContent({ commit }, value) {
    commit("setContent", value);

    setTimeout(() => {
      commit("setContent", null);
    }, DISPLAY_TIME);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
