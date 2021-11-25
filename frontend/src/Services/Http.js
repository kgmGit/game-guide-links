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
    store.dispatch("error/setStatus", error.response.status);
    throw error;
  }
);
