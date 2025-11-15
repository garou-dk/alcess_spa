import { getStoreCustomers, getStoreRoles } from "@/enums/RoleEnum";
import ProductSearch from "@/pages/product/ProductSearch.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "search-product",
    name: "search-product",
    redirect: { name: "home" },
    children: [
        {
            path: "",
            name: "customer.search-product",
            component: ProductSearch,
            meta: {
                access: getStoreCustomers(),
                pageName: "Search Product",
                pageSubName: "Search Product",
            },
        },
    ],
};

export default route;
