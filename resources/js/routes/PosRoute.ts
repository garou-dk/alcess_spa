import { getStoreRoles } from "@/enums/RoleEnum";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "pos",
    name: "pos",
    redirect: { name: "admin.pos.index" },
    children: [
        {
            path: "",
            name: "admin.pos.index",
            component: () => import("@/pages/pos/PosView.vue"),
            meta: {
                access: getStoreRoles(),
                pageName: "Orders",
                pageSubName: "Streamline Orders, Boost Efficiency",
            },
        },
        {
            path: ":id",
            name: "admin.pos.find",
            component: () => import("@/pages/pos/PosDetails.vue"),
            meta: {
                access: getStoreRoles(),
                pageName: "Orders",
                pageSubName: "Streamline Orders, Boost Efficiency",
            },
        }
    ],
};

export default route;
