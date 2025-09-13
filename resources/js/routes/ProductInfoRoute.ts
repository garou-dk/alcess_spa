import { getStoreCustomers } from "@/enums/RoleEnum";
import ProductInfoView from "@/pages/product/ProductInfo.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "product-info",
    name: "product-info",
    children: [
        {
            path: "",
            name: "customer.product-info",
            redirect: { name: "customer.home.index" },
        },
        {
            path: ":id",
            name: "customer.product-info.index",
            component: ProductInfoView,
            meta: {
                access: getStoreCustomers(),
                pageName: "Product Info",
                pageSubName: "Product Info",
            },
        },
    ],
};

export default route;
