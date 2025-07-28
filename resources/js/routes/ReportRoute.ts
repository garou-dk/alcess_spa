import { getStoreRoles } from "@/enums/RoleEnum";
import ReportView from "@/pages/report/ReportView.vue";
import { RouteRecordRaw } from "vue-router";

const route: RouteRecordRaw = {
    path: "report",
    name: "report",
    redirect: { name: "admin.report.index" },
    children: [
        {
            path: "",
            name: "admin.report.index",
            component: ReportView,
            meta: {
                access: getStoreRoles(),
                pageName: "Reports",
                pageSubName: "Your Data, Clearly Delivered",
            },
        },
    ],
};

export default route;
