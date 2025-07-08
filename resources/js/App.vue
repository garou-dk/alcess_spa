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

const unitState = useUnitStore();
const roleState = useRoleStore();

watch(
    () => Page.user,
    () => {
        if (Page.user) {
            unitState.fetchUnits();
            roleState.fetchRoles();
        }
    },
);
</script>
