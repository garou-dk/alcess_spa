import { getStoreRoles } from "@/enums/RoleEnum";
import SalesWalkInView from "@/pages/sales/SalesWalkInView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "sales-walkin",
    name: "sales-walkin",
    redirect: { name: "admin.sales.walkin" },
    children: [
        {
            path: "",
            name: "admin.sales.walkin",
            component: SalesWalkInView,
            meta: {
                access: getStoreRoles(),
                pageName: "Walk-In Sales",
                pageSubName: "View All Walk-In Transactions",
            },
        },
    ],
};

export default route;
