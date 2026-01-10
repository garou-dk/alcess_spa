import { getStoreCustomers, getStoreRoles } from "@/enums/RoleEnum";
import ProductSearch from "@/pages/product/ProductSearch.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "products",
    name: "products",
    redirect: { name: "home" },
    children: [
        {
            path: "search",
            name: "customer.search-product",
            component: ProductSearch,
            meta: {
                access: getStoreCustomers(),
                pageName: "Search Product",
                pageSubName: "Search Product",
            },
        },
        {
            path: "browse",
            name: "customer.browse-products",
            component: ProductSearch,
            meta: {
                access: getStoreCustomers(),
                pageName: "Browse Products",
                pageSubName: "Browse Products",
            },
        },
    ],
};

export default route;
