import { RoleEnum } from "@/enums/RoleEnum";
import { RouteRecordRaw } from "vue-router";
import SettingPage from "@/pages/settings/SettingPage.vue";

const route: RouteRecordRaw = {
    path: "setting",
    name: "setting",
    redirect: { name: "admin.setting.index" },
    children: [
        {
            path: "",
            name: "admin.setting.index",
            component: SettingPage,
            meta: {
                access: [RoleEnum.ADMIN],
                pageName: "Settings",
                pageSubName: "Your Website Setting",
            },
        },
    ],
};

export default route;
