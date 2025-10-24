<template>
    <OverlayBadge :value="count" pt:pcBadge:root:class="bg-sky-800! text-white">
        <RouterLink :to="{ name: 'customer.cart.index' }">
            <Button
                type="button"
                icon="pi pi-shopping-cart"
                class="bg-white!"
                :class="{
                    'text-sky-800!': count > 0,
                    'text-black!': !count,
                }"
            />
        </RouterLink>
    </OverlayBadge>
</template>
<script setup lang="ts">
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useEcho } from "@laravel/echo-vue";
import { onMounted, ref } from "vue";

const count = ref<number>(0);
const loadService = useAxiosUtil<null, number>();

useEcho(
    `cart.${Page.user.user_id}`,
    [".cart.count"],
    (value: { count: number }) => {
        count.value = value.count;
    },
);

const load = async () => {
    await loadService.get("customer/carts/count").then(() => {
        if (loadService.request.status === 200) {
            count.value = loadService.request.data;
        }
    });
};

onMounted(() => {
    load();
});
</script>
