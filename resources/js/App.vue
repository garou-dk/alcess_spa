<template>
    <div>
        <router-view></router-view>
    </div>
</template>
<script setup lang="ts">
import { watch } from "vue";
import Page from "@/stores/Page";
import { useUnitStore } from "@/stores/UnitState";
import { useRoleStore } from "@/stores/RoleState";
import { useCategoryStore } from "./stores/CategoryState";

const unitState = useUnitStore();
const roleState = useRoleStore();
const categoryState = useCategoryStore();

watch(
    () => Page.user,
    () => {
        if (Page.user) {
            roleState.fetchRoles();
            unitState.fetchUnits();
            categoryState.fetchCategories();
        }
    },
);
</script>
