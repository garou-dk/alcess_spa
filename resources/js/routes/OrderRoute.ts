import { getStoreRoles } from "@/enums/RoleEnum";
import { RouteRecordRaw } from "vue-router";
const route: RouteRecordRaw = {
    path: "order",
    name: "order",
    redirect: { name: "admin.order.index" },
    children: [
        {
            path: "",
            name: "admin.order.index",
            component: () => import("@/pages/order/OrderView.vue"),
            meta: {
                access: getStoreRoles(),
                pageName: "Order",
                pageSubName: "Quick Orders, Zero Hassle",
            },
        },
        {
            path: ":orderId/invoice/:saleId",
            name: "admin.order.invoice",
            component: () => import("@/pages/order/OrderInvoiceView.vue"),
            meta: {
                access: getStoreRoles(),
                pageName: "Order Invoice",
                pageSubName: "Sales Invoice",
            },
        },
    ],
};

export default route;
