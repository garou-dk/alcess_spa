import { createApp } from "vue";
import Toast from "vue-toastification";
import App from "@/App.vue";
import Primevue from "@/configs/primevue";
import router from "@/router";
import InputForm from "@/components/InputForm.vue";
import PageLoader from "@/components/PageLoader.vue";
import VueApexCharts from "vue3-apexcharts";
import BoxShadow from "@/components/BoxShadow.vue";
import { createPinia } from "pinia";
import Vue3Barcode from 'vue3-barcode'

const pinia = createPinia();

const app = createApp(App);
app.use(pinia);
app.use(Primevue.primevue, Primevue.config);
app.use(Toast);
app.use(router);
app.use(VueApexCharts);
app.component("Vue3Barcode", Vue3Barcode);
app.component("InputForm", InputForm);
app.component("PageLoader", PageLoader);
app.component("BoxShadow", BoxShadow);
app.mount("#app");
