import { RouteRecordRaw } from "vue-router";
import DashboardView from "@/pages/dashboard/DashboardView.vue";
import { getStoreRoles } from "@/enums/RoleEnum";

const route: RouteRecordRaw = {
    path: "dashboard",
    name: "dashboard",
    redirect: { name: "admin.dashboard.index" },
    children: [
        {
            path: "",
            name: "admin.dashboard.index",
            component: DashboardView,
            meta: {
                access: getStoreRoles(),
                pageName: "Dashboard",
                pageSubName: "Welcome back, Here's your overview!",
            },
        },
    ],
};

export default route;
