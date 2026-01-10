<template>
    <form @submit.prevent="logoutUser">
        <Button
            type="submit"
            label="Logout"
            icon="pi pi-sign-out"
            fluid
            :loading="logoutService.request.loading"
            class="!bg-blue-600 !text-white !border-none hover:!bg-blue-700 !transition-colors"
        />
    </form>
</template>
<script setup lang="ts">
import { getStoreRoles, RoleEnum } from "@/enums/RoleEnum";
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useRouter } from "vue-router";

const logoutService = useAxiosUtil();
const router = useRouter();

const logoutUser = async () => {
    await logoutService.post("logout", null).then(() => {
        const role = Page.user.role.role_name as RoleEnum;
        Page.user = null;
        if (logoutService.request.status === 200) {
            if (getStoreRoles().includes(role)) {
                router.push({ name: "admin.login" });
            } else {
                router.push({ name: "home" });
            }
        } else {
            router.push({ name: "home" });
        }
    });
};
</script>
