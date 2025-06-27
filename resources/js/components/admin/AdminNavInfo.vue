<template>
    <div class="flex flex-wrap items-center p-5">
        <div class="grow p-2">
            <h1 class="text-3xl font-semibold text-gray-600">
                {{ route.meta["pageName"] }}
            </h1>
            <h2 class="text-xl font-semibold text-gray-600">
                {{ route.meta["pageSubName"] }}
            </h2>
        </div>
        <div class="p-2">
            <BoxShadow>
                <div class="flex items-center gap-1 p-1 text-sm text-gray-600">
                    <i class="pi pi-calendar text-2xl! text-blue-600"></i>
                    <span class="ml-1 font-semibold select-none">{{
                        currentDate
                    }}</span>
                </div>
            </BoxShadow>
        </div>
        <div class="p-2">
            <BoxShadow>
                <div class="flex items-center gap-1 p-1 text-sm text-gray-600">
                    <i class="pi pi-clock text-2xl! text-blue-600"></i>
                    <span class="ml-1 font-semibold select-none">{{
                        currentTime
                    }}</span>
                </div>
            </BoxShadow>
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
            hour12: true,
            timeZone: "Asia/Singapore",
        });

        const timeParts = timeFormatter.formatToParts(gmt8);
        const hour = timeParts.find((p) => p.type === "hour")?.value;
        const minute = timeParts.find((p) => p.type === "minute")?.value;
        const dayPeriod = timeParts.find((p) => p.type === "dayPeriod")?.value;
        currentTime.value = `${hour} : ${minute} ${dayPeriod}`;
    };

    update();
    setInterval(update, 1000);
};

onMounted(() => {
    updateDateTime();
});
</script>
