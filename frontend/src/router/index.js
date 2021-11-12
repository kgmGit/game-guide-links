import Vue from "vue";
import VueRouter from "vue-router";
import store from "@/store";
import Home from "@/views/Home.vue";
import Register from "@/views/Register.vue";
import Login from "@/views/Login.vue";
import ForgotPassword from "@/views/ForgotPassword.vue";
import ResetPassword from "@/views/ResetPassword.vue";
import MyPage from "@/views/MyPage.vue";
import MyPageProfile from "@/components/mypage/Profile.vue";
import GameDetail from "@/views/GameDetail.vue";
import ArticleDetail from "@/views/article/ArticleDetail.vue";

import Error from "@/views/Error.vue";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },

  {
    path: "/register",
    name: "Register",
    component: Register,
    beforeEnter(to, from, next) {
      if (store.getters["auth/isAuth"]) {
        next(false);
      } else {
        next();
      }
    },
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
    beforeEnter(to, from, next) {
      if (store.getters["auth/isAuth"]) {
        next(false);
      } else {
        next();
      }
    },
  },
  {
    path: "/forgot-password",
    name: "ForgotPassword",
    component: ForgotPassword,
  },
  {
    path: "/reset-password",
    name: "ResetPassword",
    component: ResetPassword,
  },

  {
    path: "/mypage",
    name: "MyPage",
    component: MyPage,
    children: [
      {
        path: "profile",
        component: MyPageProfile,
      },
    ],
    beforeEnter(to, from, next) {
      if (store.getters["auth/isAuth"]) {
        next();
      } else {
        next(false);
      }
    },
  },

  {
    path: "/games/:title",
    name: "GameDetail",
    component: GameDetail,
  },

  {
    path: "/games/:game_title/articles/:id",
    name: "ArticleDetail",
    component: ArticleDetail,
  },

  {
    path: "/error",
    name: "Error",
    component: Error,
  },
  // {
  //   path: "/about",
  //   name: "About",
  //   // route level code-splitting
  //   // this generates a separate chunk (about.[hash].js) for this route
  //   // which is lazy-loaded when the route is visited.
  //   component: () =>
  //     import(/* webpackChunkName: "about" */ "../views/About.vue"),
  // },
];

const router = new VueRouter({
  mode: "history",
  base: "/",
  routes,
});

export default router;
