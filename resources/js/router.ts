import { createRouter, createWebHistory } from "vue-router";
import HomeView from "@/pages/HomeView.vue";
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import EmailVerificationView from "@/pages/EmailVerificationView.vue";
import { UserInterface } from "@/interfaces/UserInterface";
import { getStoreCustomers, getStoreRoles, RoleEnum } from "@/enums/RoleEnum";

import LoginView from "@/pages/auth/LoginView.vue";
import RegisterView from "@/pages/auth/RegisterView.vue";
import ForgotPasswordView from "@/pages/auth/ForgotPasswordView.vue";
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
import CustomerOrderRoute from "@/routes/CustomerOrderRoute";
import { CustomerInvoiceRoute } from "@/routes/CustomerInvoiceRoute";
import SettingRoute from "@/routes/SettingRoute";
import ProductCategoryRoute from "@/routes/ProductCategoryRoute";
import ProductSearchRoute from "@/routes/ProductSearchRoute";
import RateRoute from "@/routes/RateRoute";
import SalesWalkInRoute from "@/routes/SalesWalkInRoute";
import SalesOnlineRoute from "@/routes/SalesOnlineRoute";
import ProfileRoute from "@/routes/ProfileRoute";

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
        // Unified Auth Routes
        {
            path: "/login",
            name: "auth.login",
            component: LoginView,
            meta: {
                access: [null],
                pageName: "Login",
            },
        },
        {
            path: "/register",
            name: "auth.register",
            component: RegisterView,
            meta: {
                access: [null],
                pageName: "Register",
            },
        },
        {
            path: "/forgot-password",
            name: "auth.forgot-password",
            component: ForgotPasswordView,
            meta: {
                access: [null],
                pageName: "Forgot Password",
            },
        },
        {
            path: "/admin",
            redirect: { name: "admin.login" }, // Redirect to admin login route to ensure props are passed
            children: [
                {
                    path: "login",
                    name: "admin.login",
                    component: LoginView,
                    props: { admin: true },
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
                        { ...SalesWalkInRoute },
                        { ...SalesOnlineRoute },
                        { ...ReportRoute },
                        { ...SettingRoute },
                        { ...RateRoute },
                    ],
                },
            ],
        },
        {
            path: "/customer",
            name: "customer.app",
            component: CustomerIndex,
            redirect: { name: "customer.home.index" },
            meta: {
                access: getStoreCustomers(),
                pageName: "Customer",
                pageSubName: "Customer",
            },
            children: [
                { ...HomeRoute },
                { ...ProductInfoRoute },
                { ...CartRoute },
                { ...CustomerOrderRoute },
                { ...CustomerInvoiceRoute },
                { ...ProductCategoryRoute },
                { ...ProductSearchRoute },
                { ...ProfileRoute },
            ],
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

router.beforeEach(async (to, _from, next) => {
    if (!Page.loaded) {
        // Silently check authentication status
        // The 401 error is expected for non-logged-in users and should not be treated as an error
        try {
            await authService.axios({
                url: "/api/check",
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                // Suppress console errors for this request
                validateStatus: (status) => status < 500,
            }).then((response) => {
                if (response.status === 200 && response.data?.data) {
                    Page.user = response.data.data;
                } else {
                    Page.user = null;
                }
            });
        } catch (error) {
            // Silently handle any errors - user is simply not authenticated
            Page.user = null;
        } finally {
            Page.loaded = true;
        }
    }
    if (Page.user) {
        // If user is logged in and trying to access login/register page, redirect to appropriate dashboard
        if (to.name === "auth.login" || to.name === "auth.register" || to.name === "admin.login") {
            if (getStoreRoles().includes(Page.user.role.role_name as RoleEnum)) {
                next({ name: "admin.dashboard.index" });
                return;
            } else if (getStoreCustomers().includes(Page.user.role.role_name as RoleEnum)) {
                next({ name: "customer.home.index" });
                return;
            }
        }

        if (
            Array.isArray(to.meta.access) &&
            to.meta.access.includes(Page.user.role.role_name)
        ) {
            // console.log("User has access, proceeding to route");
            next();
        } else {
            // To-do: Add more role redirection
            if (
                getStoreCustomers().includes(
                    Page.user.role.role_name as RoleEnum,
                )
            ) {
                next({ name: "customer.home.index" });
            } else if (getStoreRoles().includes(Page.user.role.role_name as RoleEnum)) {
                next({ name: "admin.dashboard.index" });
            } else {
                // User doesn't have access
                next({ name: "home" });
            }
        }
    } else {
        // console.log("User not logged in, checking access for unauthenticated users");

        if (Array.isArray(to.meta.access) && to.meta.access.includes(null)) {
            next();
        } else {
            next({ name: "auth.login" });
        }
    }
});

export default router;
