import { getStoreRoles } from "@/enums/RoleEnum";
import OrderView from "@/pages/order/OrderView.vue";
import OrderInvoiceView from "@/pages/order/OrderInvoiceView.vue";
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
        {
            path: ":orderId/invoice/:saleId",
            name: "admin.order.invoice",
            component: OrderInvoiceView,
            meta: {
                access: getStoreRoles(),
                pageName: "Order Invoice",
                pageSubName: "Sales Invoice",
            },
        },
    ],
};

export default route;
