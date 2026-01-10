import { getStoreRoles } from "@/enums/RoleEnum";
import PosDetails from "@/pages/pos/PosDetails.vue";
import PosView from "@/pages/pos/PosView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "pos",
    name: "pos",
    redirect: { name: "admin.pos.index" },
    children: [
        {
            path: "",
            name: "admin.pos.index",
            component: PosView,
            meta: {
                access: getStoreRoles(),
                pageName: "Orders",
                pageSubName: "Streamline Orders, Boost Efficiency",
            },
        },
        {
            path: ":id",
            name: "admin.pos.find",
            component: PosDetails,
            meta: {
                access: getStoreRoles(),
                pageName: "Orders",
                pageSubName: "Streamline Orders, Boost Efficiency",
            },
        }
    ],
};

export default route;
