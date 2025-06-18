import { createRouter, createWebHistory } from "vue-router";
import HomeView from "@/pages/HomeView.vue";
import Page from "@/stores/Page";
import useAxiosUtil from "./utils/AxiosUtil";

const authService = useAxiosUtil<null, Object>();

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            name: "home",
            component: HomeView
        }
    ]
});

router.beforeEach(async(to, from, next) => {
    if (!Page.loaded) {
        await authService.get("check", null).then(() => {
            if (authService.request.status === 200 && authService.request.data) {
                Page.user = authService.request.data;
            }
            else {
                Page.user = null;
            }
        }).finally(() => {
            Page.loaded = true;
        });
    }
    next();
});

export default router;