import { getStoreRoles } from "@/enums/RoleEnum";
import UserView from "@/pages/user/UserView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "users",
    name: "users",
    redirect: { name: "admin.users.index" },
    children: [
        {
            path: "",
            name: "admin.users.index",
            component: UserView,
            meta: {
                access: getStoreRoles(),
                pageName: "User",
                pageSubName: "Manage user efficiently",
            },
        },
    ],
};

export default route;
