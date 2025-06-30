<template>
    <div class="flex h-svh">
        <div
            v-if="!mobileMode"
            class="shadow-left-float z-10 overflow-hidden transition-all duration-300"
            :class="{
                'w-72 opacity-100': sideBar,
                'w-0 opacity-0': !sideBar,
            }"
        >
            <AdminSideBar />
        </div>
        <Drawer v-if="mobileMode" v-model:visible="sideBar">
            <AdminSideBar />
        </Drawer>
        <div class="grow bg-gray-100 h-full overflow-auto">
            <AdminNavBar />
            <AdminNavInfo />
            <div class="p-5">
                <RouterView />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import AdminNavBar from "@/components/admin/AdminNavBar.vue";
import AdminNavInfo from "@/components/admin/AdminNavInfo.vue";
import AdminSideBar from "@/components/admin/AdminSideBar.vue";
import { onMounted, provide, ref } from "vue";

const sideBar = ref<boolean>(true);
provide("sideBar", sideBar);
const mobileMode = ref<boolean>(false);

const setSideBar = () => {
    if (window.innerWidth < 1024) {
        sideBar.value = false;
        mobileMode.value = true;
    } else {
        sideBar.value = true;
        mobileMode.value = false;
    }
};

onMounted(() => {
    window.addEventListener("resize", setSideBar);
    setSideBar();
});
</script>
