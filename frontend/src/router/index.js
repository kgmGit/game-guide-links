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
import ArticleEdit from "@/views/article/ArticleEdit.vue";
import SiteEdit from "@/views/site/SiteEdit.vue";
import FavoriteSites from "@/components/mypage/FavoriteSites.vue";
import PostSites from "@/components/mypage/PostSites.vue";
import FavoriteArticles from "@/components/mypage/FavoriteArticles.vue";
import PostArticles from "@/components/mypage/PostArticles.vue";

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
        path: "favorites/sites",
        component: FavoriteSites,
      },
      {
        path: "favorites/articles",
        component: FavoriteArticles,
      },
      {
        path: "posts/sites",
        component: PostSites,
      },
      {
        path: "posts/articles",
        component: PostArticles,
      },
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
    path: "/games/:game_title/sites/add",
    name: "SiteAdd",
    component: SiteEdit,
  },
  {
    path: "/games/:game_title/sites/:id/edit",
    name: "SiteEdit",
    component: SiteEdit,
    props: true,
  },

  {
    path: "/games/:game_title/articles/add",
    name: "ArticleAdd",
    component: ArticleEdit,
  },
  {
    path: "/games/:game_title/articles/:id",
    name: "ArticleDetail",
    component: ArticleDetail,
  },
  {
    path: "/games/:game_title/articles/:id/edit",
    name: "ArticleEdit",
    component: ArticleEdit,
    props: true,
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
