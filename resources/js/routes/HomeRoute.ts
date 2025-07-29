import { getStoreRoles, RoleEnum } from "@/enums/RoleEnum";
import HomeIndexView from "@/pages/home/HomeIndexView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "home",
    name: "customer.home",
    redirect: { name: "customer.home.index" },
    children: [
        {
            path: "",
            name: "customer.home.index",
            component: HomeIndexView,
            meta: {
                access: [RoleEnum.CUSTOMER],
                pageName: "Home",
                pageSubName: "Search what you needed here.",
            },
        },
    ],
};

export default route;
