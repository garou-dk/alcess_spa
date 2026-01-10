import { getStoreCustomers, getStoreRoles } from "@/enums/RoleEnum";
import ProductSearch from "@/pages/product/ProductSearch.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "category",
    name: "category.product-category-index",
    redirect: { name: "home" },
    children: [
        {
            path: ":id",
            name: "customer.product-category",
            component: ProductSearch,
            meta: {
                access: getStoreCustomers(),
                pageName: "Category",
                pageSubName: "Category",
            },
        },
    ],
};

export default route;
