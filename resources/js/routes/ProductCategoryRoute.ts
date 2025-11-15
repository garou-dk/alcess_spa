import { getStoreCustomers, getStoreRoles } from "@/enums/RoleEnum";
import ProductCategory from "@/pages/product/ProductCategory.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "category",
    name: "category",
    redirect: { name: "home" },
    children: [
        {
            path: ":id",
            name: "customer.product-category",
            component: ProductCategory,
            meta: {
                access: getStoreCustomers(),
                pageName: "Category",
                pageSubName: "Category",
            },
        },
    ],
};

export default route;
