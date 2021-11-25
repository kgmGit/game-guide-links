const state = {
  status: null,
};

const getters = {
  status(state) {
    return state.status;
  },
};

const mutations = {
  setStatus(state, value) {
    state.status = value;
  },
};

const actions = {
  setStatus({ commit }, value) {
    commit("setStatus", value);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
