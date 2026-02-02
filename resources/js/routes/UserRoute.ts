import { RoleEnum } from "@/enums/RoleEnum";
import { RouteRecordRaw } from "vue-router";
const route: RouteRecordRaw = {
    path: "users",
    name: "users",
    redirect: { name: "admin.users.index" },
    children: [
        {
            path: "",
            name: "admin.users.index",
            component: () => import("@/pages/user/UserView.vue"),
            meta: {
                access: [RoleEnum.ADMIN],
                pageName: "User",
                pageSubName: "Manage user efficiently",
            },
        },
    ],
};

export default route;
