import { createRouter, createWebHistory } from "vue-router";
import HomeView from "@/pages/HomeView.vue";
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import EmailVerificationView from "@/pages/EmailVerificationView.vue";
import { UserInterface } from "@/interfaces/UserInterface";
import { getStoreCustomers, getStoreRoles, RoleEnum } from "@/enums/RoleEnum";
import AdminLoginView from "@/pages/AdminLoginView.vue";
import AdminIndex from "@/pages/AdminIndex.vue";

const authService = useAxiosUtil<null, UserInterface>();

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            name: "home",
            component: HomeView,
            meta: {
                access: getStoreCustomers(),
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
                    },
                },
                {
                    path: "app",
                    name: "admin.app",
                    component: AdminIndex,
                    meta: {
                        access: getStoreRoles(),
                    },
                }
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
            next();
        } else {
            // To-do: Add more role redirection
            if (
                getStoreCustomers().includes(
                    Page.user.role.role_name as RoleEnum,
                )
            ) {
                next({ name: "home" });
            }
        }
    } else {
        if (Array.isArray(to.meta.access) && to.meta.access.includes(null)) {
            next();
        } else {
            next({ name: "home" });
        }
    }
});

export default router;
