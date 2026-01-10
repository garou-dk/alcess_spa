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
import { configureEcho } from "@laravel/echo-vue";

// Fix for ApexCharts passive event listener warnings on mobile
// This suppresses the console warnings about non-passive touch event listeners
if (typeof window !== 'undefined') {
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    EventTarget.prototype.addEventListener = function(type, listener, options) {
        // Make touch events passive by default to improve mobile performance
        if (type === 'touchstart' || type === 'touchmove' || type === 'touchend') {
            if (typeof options === 'boolean') {
                options = { capture: options, passive: true };
            } else if (typeof options === 'object' && options !== null) {
                options = { ...options, passive: true };
            } else {
                options = { passive: true };
            }
        }
        return originalAddEventListener.call(this, type, listener, options);
    };
}

configureEcho({
    broadcaster: "pusher",
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    enabledTransports:
        (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https"
            ? ["ws", "wss"]
            : ["ws"],
});
import Vue3Barcode from "vue3-barcode";

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
