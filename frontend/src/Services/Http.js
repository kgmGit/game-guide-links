import axios from "axios";

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
    throw error;
  }
);
