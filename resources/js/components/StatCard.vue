<template>
    <div class="bg-white rounded-xl shadow-md p-4 border border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-600 mb-1">{{ label }}</p>
                <p class="text-xl font-bold truncate" :class="valueColor">{{ value }}</p>
            </div>
            <div v-if="icon" class="p-2.5 rounded-lg flex-shrink-0 ml-3" :class="iconBgColor">
                <i :class="icon" class="text-lg" :style="{ color: iconColor }"></i>
            </div>
        </div>
        <div v-if="subtitle" class="mt-2 text-xs text-gray-500">
            {{ subtitle }}
        </div>
    </div>
</template>

<script setup lang="ts">
interface Props {
    label: string;
    value: string | number;
    valueColor?: string;
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
