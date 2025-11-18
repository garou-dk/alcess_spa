<template>
    <div class="h-full overflow-auto pb-2">
        <div class="flex items-center justify-center gap-2 p-5">
            <div class="rounded-full">
                <img
                    :src="Logo"
                    alt="Store Icon"
                    class="h-16 w-16 object-contain"
                />
            </div>
            <h1 class="text-3xl font-bold text-sky-800 select-none">ALCESS</h1>
        </div>
        <SideBarButton
            to="admin.dashboard.index"
            icon="pi pi-objects-column"
            label="Dashboard"
        />
        <SideBarButton
            to="admin.order.index"
            icon="pi pi-shopping-cart"
            label="Order"
        />
        <SideBarButton to="admin.pos.index" icon="pi pi-my-pos" label="Sales" />
        <SideBarButton
            to="admin.product.index"
            icon="pi pi-book"
            label="Inventory"
        />
        <SideBarButton
            v-if="Page.user && Page.user.role.role_name === RoleEnum.ADMIN"
            to="admin.users.index"
            icon="pi pi-users"
            label="Users"
        />
        <SideBarAccordion
            icon="pi pi-cog"
            label="Settings"
            :active="['admin.unit.index'].includes(route.name as string)"
        >
            <SideBarButton
                to="admin.unit.index"
                icon="pi pi-unit-ruler"
                label="Units"
            />
            <SideBarButton
                to="admin.category.index"
                icon="pi pi pi-tag"
                label="Categories"
            />
            <SideBarButton
                to="admin.setting.index"
                icon="pi pi pi-cog"
                label="Settings"
            />
        </SideBarAccordion>
        <SideBarButton
            to="admin.report.index"
            icon="pi pi-chart-bar"
            label="Reports"
        />
        <SideBarButton
            to="admin.rate.index"
            icon="pi pi-star"
            label="Ratings"
        />
    </div>
</template>
<script setup lang="ts">
import Logo from "@/../img/logo.png";
import SideBarButton from "@/components/SideBarButton.vue";
import SideBarAccordion from "@/components/SideBarAccordion.vue";
import { useRoute } from "vue-router";
import Page from "@/stores/Page";
import { RoleEnum } from "@/enums/RoleEnum";

const route = useRoute();
</script>
