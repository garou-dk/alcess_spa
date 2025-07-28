import { getStoreRoles } from "@/enums/RoleEnum";
import OrderView from "@/pages/order/OrderView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "order",
    name: "order",
    redirect: { name: "admin.order.index" },
    children: [
        {
            path: "",
            name: "admin.order.index",
            component: OrderView,
            meta: {
                access: getStoreRoles(),
                pageName: "Order",
                pageSubName: "Quick Orders, Zero Hassle",
            },
        },
    ],
};

export default route;
