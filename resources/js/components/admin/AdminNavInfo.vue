<template>
    <div v-if="route.name !== 'admin.dashboard.index'" class="flex flex-wrap items-center px-5 py-3">
        <div class="grow p-2">
            <h1 class="text-2xl font-semibold text-gray-600">
                {{ route.meta["pageName"] }}
            </h1>
            <h2 class="text-base font-medium text-gray-500">
                {{ route.meta["pageSubName"] }}
            </h2>
        </div>
        <div class="p-2">
            <div class="flex items-center gap-1 p-2 text-sm text-gray-600">
                <i class="pi pi-calendar text-xl text-blue-600"></i>
                <span class="ml-1 font-semibold select-none">{{
                    currentDate
                }}</span>
            </div>
        </div>
        <div class="p-2">
            <div class="flex items-center gap-1 p-2 text-sm text-gray-600">
                <i class="pi pi-clock text-xl text-blue-600"></i>
                <span class="ml-1 font-semibold select-none">{{
                    currentTime
                }}</span>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
import { onMounted } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const currentDate = ref<string>("");
const currentTime = ref<string>("");

const updateDateTime = () => {
    const update = () => {
        const now = new Date();
        const gmt8 = new Date(
            now.getUTCFullYear(),
            now.getUTCMonth(),
            now.getUTCDate(),
            now.getUTCHours() + 8,
            now.getUTCMinutes(),
            now.getUTCSeconds(),
        );

        const dateFormatter = new Intl.DateTimeFormat("en-US", {
            weekday: "short",
            day: "2-digit",
            month: "short",
            year: "numeric",
            timeZone: "Asia/Singapore",
        });

        const dateParts = dateFormatter.formatToParts(gmt8);
        const weekday = dateParts.find((p) => p.type === "weekday")?.value;
        const day = dateParts.find((p) => p.type === "day")?.value;
        const month = dateParts.find((p) => p.type === "month")?.value;
        const year = dateParts.find((p) => p.type === "year")?.value;
        currentDate.value = `${weekday}, ${day} ${month} ${year}`;

        const timeFormatter = new Intl.DateTimeFormat("en-US", {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true,
            timeZone: "Asia/Singapore",
        });

        const timeParts = timeFormatter.formatToParts(gmt8);
        const hour = timeParts.find((p) => p.type === "hour")?.value;
        const minute = timeParts.find((p) => p.type === "minute")?.value;
        const second = timeParts.find((p) => p.type === "second")?.value;
        const dayPeriod = timeParts.find((p) => p.type === "dayPeriod")?.value;
        currentTime.value = `${hour} : ${minute} : ${second} ${dayPeriod}`;
    };

    update();
    setInterval(update, 1000);
};

onMounted(() => {
    updateDateTime();
});
</script>
