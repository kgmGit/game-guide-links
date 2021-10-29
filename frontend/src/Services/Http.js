import axios from "axios";
import store from "@/store";

export const http = axios.create({
  withCredentials: true,
});

http.interceptors.request.use((request) => {
  return request;
});

http.interceptors.response.use(
  (response) => {
    return response;
  },
  async (error) => {
    if (
      error.response &&
      [401, 419].includes(error.response.status) &&
      store.getters["auth/user"]
    ) {
      await store.dispatch("auth/logout");
    }
    throw error;
  }
);
