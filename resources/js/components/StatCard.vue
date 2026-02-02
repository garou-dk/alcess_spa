<template>
    <div class="bg-white rounded-xl shadow-md p-5 border border-gray-100 kpi-card dashboard-card h-full">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ label }}</p>
                <p class="font-bold stat-value leading-tight" :class="[valueColor, valueClass || 'text-2xl']">{{ value }}</p>
            </div>
            <div v-if="icon" class="p-3 rounded-xl flex-shrink-0 ml-3 shadow-sm" :class="iconBgColor">
                <i :class="icon" class="text-xl" :style="{ color: iconColor }"></i>
            </div>
        </div>
        <div v-if="subtitle" class="mt-3 flex items-center gap-1.5">
            <span class="inline-block w-1 h-1 rounded-full bg-blue-400"></span>
            <span class="text-xs text-gray-500 font-medium whitespace-normal">{{ subtitle }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
interface Props {
    label: string;
    value: string | number;
    valueColor?: string;
    valueClass?: string;
    icon?: string;
    iconColor?: string;
    iconBgColor?: string;
    subtitle?: string;
}

withDefaults(defineProps<Props>(), {
    valueColor: 'text-gray-900',
    iconBgColor: 'bg-blue-50',
    iconColor: '#2563EB',
});
</script>
