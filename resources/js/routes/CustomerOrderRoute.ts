import { RoleEnum } from "@/enums/RoleEnum";
import CustomerOrderPage from "@/pages/customer_order/CustomerOrderPage.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "order",
    name: "customer-order",
    redirect: { name: "customer.order.index" },
    children: [
        {
            path: "",
            name: "customer.order.index",
            component: CustomerOrderPage,
            meta: {
                access: [
                    RoleEnum.CUSTOMER
                ],
                pageName: "Order",
                pageSubName: "Manage your order efficiently",
            },
        },
    ],
};

export default route;
