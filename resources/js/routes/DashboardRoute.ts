import { RouteRecordRaw } from "vue-router";
import DashboardView from "@/pages/dashboard/DashboardView.vue";

const route: RouteRecordRaw = {
    path: "dashboard",
    name: "dashboard",
    redirect: { name: "admin.dashboard.index" },
    children: [
        {
            path: "",
            name: "admin.dashboard.index",
            component: DashboardView,
        },
    ],
};

export default route;
