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
import { getStoreRoles, RoleEnum } from "@/enums/RoleEnum";

const unitState = useUnitStore();
const roleState = useRoleStore();
const categoryState = useCategoryStore();

watch(
    () => Page.user,
    () => {
        if (Page.user) {
            if (
                getStoreRoles().includes(Page.user.role.role_name as RoleEnum)
            ) {
                unitState.fetchUnits();
                roleState.fetchRoles();
            }
            categoryState.fetchCategories();
        }
    },
);
</script>
