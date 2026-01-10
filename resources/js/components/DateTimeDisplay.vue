<template>
    <div class="flex items-center justify-end gap-4 mb-5">
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
            <i class="pi pi-calendar text-blue-600"></i>
            <span class="text-sm font-semibold text-gray-700 select-none">{{ currentDate }}</span>
        </div>
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
            <i class="pi pi-clock text-blue-600"></i>
            <span class="text-sm font-semibold text-gray-700 select-none">{{ currentTime }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from "vue";

const currentDate = ref<string>("");
const currentTime = ref<string>("");
let intervalId: number | null = null;

const updateDateTime = () => {
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

onMounted(() => {
    updateDateTime();
    intervalId = window.setInterval(updateDateTime, 1000);
});

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
    }
});
</script>
