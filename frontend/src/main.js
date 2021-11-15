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

import { library } from "@fortawesome/fontawesome-svg-core";
import {
  faHeart as fasHeart,
  faThumbsUp as fasThumbsUp,
  faHeartBroken,
  faPhoneSquareAlt,
  faTrashAlt,
} from "@fortawesome/free-solid-svg-icons";
import {
  faHeart as farHeart,
  faThumbsUp as farThumbsUp,
} from "@fortawesome/free-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

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
extend("url", {
  validate(value) {
    if (value) {
      const re = /https?:\/\/[\w/:%#$&?()~.=+-]+/;
      return re.test(value);
    }

    return false;
  },
  message: "有効なURLではありません",
});
localize("ja", ja);
Vue.component("ValidationProvider", ValidationProvider);
Vue.component("ValidationObserver", ValidationObserver);

library.add(
  fasHeart,
  fasThumbsUp,
  faHeartBroken,
  faPhoneSquareAlt,
  farHeart,
  farThumbsUp,
  faTrashAlt
);
Vue.component("font-awesome-icon", FontAwesomeIcon);

const createApp = async () => {
  await store.dispatch("auth/me");

  new Vue({
    router,
    store,
    render: (h) => h(App),
  }).$mount("#app");
};

createApp();
