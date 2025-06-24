<template>
    <div>
        <div class="flex items-center justify-center p-5 gap-2">
            <div class="rounded-full">
                <img :src="Logo" alt="Store Icon" class="h-16 w-16">
            </div>
            <h1 class="text-3xl font-bold text-sky-800 select-none">ALCESS</h1>
        </div>
        <SideBarButton
            to=""
            icon="pi pi-objects-column"
            label="Dashboard"
        />
        <SideBarButton
            to=""
            icon="pi pi-shopping-cart"
            label="Order"
        />
        <SideBarButton
            to=""
            icon="pi pi-my-pos"
            label="POS"
        />
        <SideBarButton
            to=""
            icon="pi pi-book"
            label="Inventory"
        />
        <SideBarButton
            to=""
            icon="pi pi-users"
            label="Users"
        />
        <SideBarAccordion
            icon="pi pi-cog"
            label="Settings"
        >
            <SideBarButton
                to=""
                icon="pi pi-id-card"
                label="Profile"
            />
            <SideBarButton
                to=""
                icon="pi pi-key"
                label="Password"
            />
            <SideBarButton
                to=""
                icon="pi pi-unit-ruler"
                label="Units"
            />
            <SideBarButton
                to=""
                icon="pi pi pi-tag"
                label="Categories"
            />
        </SideBarAccordion>
        <SideBarButton
            to=""
            icon="pi pi-chart-bar"
            label="Reports"
        />
    </div>
</template>
<script setup lang="ts">
import Logo from "@/../img/logo.png";
import SideBarButton from "@/components/SideBarButton.vue";
import SideBarAccordion from "@/components/SideBarAccordion.vue";
</script>