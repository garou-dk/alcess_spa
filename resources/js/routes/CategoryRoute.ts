import { getStoreRoles } from "@/enums/RoleEnum";
import CategoryView from "@/pages/category/CategoryView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "category",
    name: "category",
    redirect: { name: "admin.category.index" },
    children: [
        {
            path: "",
            name: "admin.category.index",
            component: CategoryView,
            meta: {
                access: getStoreRoles(),
                pageName: "Category",
                pageSubName: "Manage stock category",
            },
        },
    ],
};

export default route;
