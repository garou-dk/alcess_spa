import { getStoreRoles } from "@/enums/RoleEnum";
import ProductView from "@/pages/product/ProductView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "product",
    name: "product",
    redirect: { name: "admin.product.index" },
    children: [
        {
            path: "",
            name: "admin.product.index",
            component: ProductView,
            meta: {
                access: getStoreRoles(),
                pageName: "Product",
                pageSubName: "Manage your product and stock level",
            },
        },
    ],
};

export default route;
