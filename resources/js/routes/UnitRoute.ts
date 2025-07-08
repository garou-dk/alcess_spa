import { getStoreRoles } from "@/enums/RoleEnum";
import UnitView from "@/pages/unit/UnitView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "unit",
    name: "unit",
    redirect: { name: "admin.unit.index" },
    children: [
        {
            path: "",
            name: "admin.unit.index",
            component: UnitView,
            meta: {
                access: getStoreRoles(),
                pageName: "Unit",
                pageSubName: "Manage stock unit measure",
            },
        },
    ],
};

export default route;
