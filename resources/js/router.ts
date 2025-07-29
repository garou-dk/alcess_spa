import { createRouter, createWebHistory } from "vue-router";
import HomeView from "@/pages/HomeView.vue";
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import EmailVerificationView from "@/pages/EmailVerificationView.vue";
import { UserInterface } from "@/interfaces/UserInterface";
import { getStoreCustomers, getStoreRoles, RoleEnum } from "@/enums/RoleEnum";
import AdminLoginView from "@/pages/AdminLoginView.vue";
import AdminIndex from "@/pages/AdminIndex.vue";
import DashboardRoute from "@/routes/DashboardRoute";
import UserRoute from "@/routes/UserRoute";
import UnitRoute from "@/routes/UnitRoute";
import CategoryRoute from "@/routes/CategoryRoute";
import ProductRoute from "@/routes/ProductRoute";
import OrderRoute from "@/routes/OrderRoute";
import PosRoute from "@/routes/PosRoute";
import ReportRoute from "@/routes/ReportRoute";
import CustomerIndex from "@/pages/CustomerIndex.vue";
import HomeRoute from "@/routes/HomeRoute";
import ProductInfoRoute from "@/routes/ProductInfoRoute";
import CartRoute from "@/routes/CartRoute";

const authService = useAxiosUtil<null, UserInterface>();

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            name: "home",
            component: HomeView,
            meta: {
                access: [null],
                pageName: "Home",
                pageSubName: "Home",
            },
        },
        {
            path: "/admin",
            redirect: { name: "admin.login" },
            children: [
                {
                    path: "login",
                    name: "admin.login",
                    component: AdminLoginView,
                    meta: {
                        access: [null],
                        pageName: "Administrator Login",
                        pageSubName: "Administrator Login",
                    },
                },
                {
                    path: "app",
                    name: "admin.app",
                    component: AdminIndex,
                    redirect: { name: "admin.dashboard.index" },
                    meta: {
                        access: getStoreRoles(),
                        pageName: "App",
                        pageSubName: "App",
                    },
                    children: [
                        { ...DashboardRoute },
                        { ...UserRoute },
                        { ...UnitRoute },
                        { ...CategoryRoute },
                        { ...ProductRoute },
                        { ...OrderRoute },
                        { ...PosRoute },
                        { ...ReportRoute },
                    ],
                }
            ],
        },
        {
            path: "/customer",
            name: "customer.app",
            component: CustomerIndex,
            redirect: { name: "customer.home.index" },
            meta: {
                access: [RoleEnum.CUSTOMER],
                pageName: "Customer",
                pageSubName: "Customer",
            },
            children: [
                { ...HomeRoute },
                { ...ProductInfoRoute },
                { ...CartRoute },
            ]
        },
        {
            path: "/verify",
            name: "verify",
            component: EmailVerificationView,
            meta: {
                access: [null],
            },
        },
    ],
});

router.beforeEach(async (to, from, next) => {
    if (!Page.loaded) {
        await authService
            .get("check", null)
            .then(() => {
                if (
                    authService.request.status === 200 &&
                    authService.request.data
                ) {
                    Page.user = authService.request.data;
                } else {
                    Page.user = null;
                }
            })
            .finally(() => {
                Page.loaded = true;
            });
    }
    if (Page.user) {
        if (
            Array.isArray(to.meta.access) &&
            to.meta.access.includes(Page.user.role.role_name)
        ) {
            console.log("User has access, proceeding to route");
            next();
        } else {
            // To-do: Add more role redirection
            if (
                getStoreCustomers().includes(
                    Page.user.role.role_name as RoleEnum,
                )
            ) {
                next({ name: "customer.home.index" });
            }
        }
    } else {
        console.log("User not logged in, checking access for unauthenticated users");
        
        if (Array.isArray(to.meta.access) && to.meta.access.includes(null)) {
            next();
        } else {
            next({ name: "home" });
        }
    }
});

export default router;
