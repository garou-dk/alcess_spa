import { RoleEnum } from "@/enums/RoleEnum";
import CartView from "@/pages/cart/CartView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "cart",
    name: "cart",
    redirect: { name: "customer.cart.index" },
    children: [
        {
            path: "",
            name: "customer.cart.index",
            component: CartView,
            meta: {
                access: [RoleEnum.CUSTOMER],
                pageName: "Cart",
                pageSubName: "Manage your selected items here.",
            },
        },
    ],
};

export default route;
