import { getStoreRoles } from "@/enums/RoleEnum";
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
                pageName: "Sales",
                pageSubName: "Streamline Sales, Boost Efficiency",
            },
        },
    ],
};

export default route;
