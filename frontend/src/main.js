import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import { BootstrapVue, IconsPlugin, ModalPlugin } from "bootstrap-vue";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";
import {
  ValidationProvider,
  ValidationObserver,
  extend,
  localize,
} from "vee-validate";
import { required, min, max, email, confirmed } from "vee-validate/dist/rules";
import ja from "vee-validate/dist/locale/ja.json";

Vue.config.productionTip = false;

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(ModalPlugin);

required.message = "必須項目です";
min.message = "{length}文字以上で入力してください";
max.message = "{length}文字以内で入力してください";
email.message = "メール形式ではありません";
confirmed.message = "{target}と一致しません";
extend("required", required);
extend("min", min);
extend("max", max);
extend("email", email);
extend("confirmed", confirmed);
localize("ja", ja);
Vue.component("ValidationProvider", ValidationProvider);
Vue.component("ValidationObserver", ValidationObserver);

const createApp = async () => {
  store.dispatch("auth/me");

  new Vue({
    router,
    store,
    render: (h) => h(App),
  }).$mount("#app");
};

createApp();
