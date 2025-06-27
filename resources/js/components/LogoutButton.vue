<template>
    <form @submit.prevent="logoutUser">
        <Button
            type="submit"
            label="Logout"
            class="bg-sky-800! text-white"
            :loading="logoutService.request.loading"
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
        if (logoutService.request.status === 200) {
            if (
                getStoreRoles().includes(Page.user.role.role_name as RoleEnum)
            ) {
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
