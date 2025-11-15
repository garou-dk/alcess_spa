import { getStoreRoles } from "@/enums/RoleEnum";
import RateView from "@/pages/rate/RateView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "rate",
    name: "rate",
    redirect: { name: "admin.rate.index" },
    children: [
        {
            path: "",
            name: "admin.rate.index",
            component: RateView,
            meta: {
                access: getStoreRoles(),
                pageName: "Rate",
                pageSubName: "Reply Customer Comments",
            },
        },
    ],
};

export default route;
