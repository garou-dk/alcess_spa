import { getStoreRoles } from "@/enums/RoleEnum";
import SalesOnlineView from "@/pages/sales/SalesOnlineView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "sales-online",
    name: "sales-online",
    redirect: { name: "admin.sales.online" },
    children: [
        {
            path: "",
            name: "admin.sales.online",
            component: SalesOnlineView,
            meta: {
                access: getStoreRoles(),
                pageName: "Online Sales",
                pageSubName: "View All Online Orders",
            },
        },
    ],
};

export default route;
