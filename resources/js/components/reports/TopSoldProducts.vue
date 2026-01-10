<template>
    <BoxShadow>
        <div class="w-full">
            <div 
                :class="[
                    'flex items-center justify-between border-b border-gray-200',
                    responsive.getResponsiveClasses({
                        mobile: 'px-3 py-3',
                        tablet: 'px-4 py-3',
                        desktop: 'px-5 py-4' // Original desktop padding
                    })
                ]"
            >
                <div>
                    <h1 :class="responsive.getResponsiveTextSize('lg') + ' font-semibold text-gray-800'">
                        Best Selling Products
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">Top performing items by sales volume</p>
                </div>
                <div :class="'flex items-center gap-2 text-gray-600 ' + responsive.getResponsiveTextSize('sm')">
                    <i class="pi pi-chart-line text-blue-600"></i>
                    <span class="font-medium">{{ data.length }} Products</span>
                </div>
            </div>

            <!-- Desktop Table Layout - Hidden on mobile/tablet -->
            <div class="hidden lg:block">
                <DataTable
                    class="w-full"
                    :value="data"
                    columnResizeMode="expand"
                    :paginator="data.length > 5"
                    :rows="5"
                    stripedRows
                >
                    <Column field="product_name" header="Product Name" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3 py-1">
                                <Avatar
                                    v-if="data.product_image"
                                    shape="circle"
                                    :image="UrlUtil.getBaseAppUrl(`storage/images/product/${data.product_image}`)"
                                    class="aspect-square! w-10 h-10"
                                    size="large"
                                />
                                <Avatar
                                    v-else
                                    shape="circle"
                                    icon="pi pi-camera"
                                    class="bg-gray-100 text-gray-400"
                                    size="large"
                                />
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">
                                        {{ data.product_name }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="category.category_name" header="Category" :sortable="true">
                        <template #body="{ data }">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                {{ data.category.category_name }}
                            </span>
                        </template>
                    </Column>
                    <Column field="units_sold" header="Units Sold" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-900">{{ data.units_sold }}</span>
                                <span class="text-xs text-gray-500">units</span>
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-8">
                            <i class="pi pi-inbox text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">No products found</p>
                            <p class="text-xs text-gray-400 mt-1">Start selling to see your top products here</p>
                        </div>
                    </template>
                </DataTable>
            </div>

            <!-- Mobile/Tablet Card Layout - Shown on mobile/tablet -->
            <div class="block lg:hidden" :class="responsive.getResponsiveClasses({ mobile: 'p-3', tablet: 'p-4' })">
                <div v-if="data.length === 0" class="text-center py-8">
                    <i class="pi pi-inbox text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 font-medium">No products found</p>
                    <p class="text-xs text-gray-400 mt-1">Start selling to see your top products here</p>
                </div>
                <div v-else :class="'space-y-3 ' + (data.length > 5 ? 'max-h-96 overflow-y-auto' : '')">
                    <div 
                        v-for="item in data.slice(0, 5)" 
                        :key="item.product_id"
                        class="bg-gray-50 rounded-lg p-3 border border-gray-200 hover:bg-gray-100 transition-colors"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <Avatar
                                    v-if="item.product_image"
                                    shape="circle"
                                    :image="UrlUtil.getBaseAppUrl(`storage/images/product/${item.product_image}`)"
                                    :class="responsive.getResponsiveClasses({
                                        mobile: 'w-10 h-10',
                                        tablet: 'w-12 h-12'
                                    }) + ' aspect-square!'"
                                    size="large"
                                />
                                <Avatar
                                    v-else
                                    shape="circle"
                                    icon="pi pi-camera"
                                    :class="responsive.getResponsiveClasses({
                                        mobile: 'w-10 h-10',
                                        tablet: 'w-12 h-12'
                                    }) + ' bg-gray-100 text-gray-400'"
                                    size="large"
                                />
                                <div class="flex-1 min-w-0">
                                    <div :class="responsive.getResponsiveTextSize('base') + ' font-medium text-gray-900 truncate'">
                                        {{ item.product_name }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                            {{ item.category.category_name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-3">
                                <div :class="responsive.getResponsiveTextSize('lg') + ' font-bold text-gray-900'">
                                    {{ item.units_sold }}
                                </div>
                                <div class="text-xs text-gray-500">units sold</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Show more indicator if there are more than 5 items -->
                <div v-if="data.length > 5" class="text-center mt-3 pt-3 border-t border-gray-200">
                    <span class="text-xs text-gray-500">Showing top 5 of {{ data.length }} products</span>
                </div>
            </div>
        </div>
    </BoxShadow>
</template>
<script setup lang="ts">
import UrlUtil from '@/utils/UrlUtil';
import { useResponsive } from '@/composables/useResponsive';

export interface TopSellingProductInterface {
    product_id: number;
    product_name: string;
    product_image: string | null;
    units_sold: number;
    unit: {
        unit_id: number;
        unit_name: string;
        deleted_at: string | null;
        created_at: string;
        updated_at: string;
    };
    category: {
        category_id: number;
        category_name: string;
        category_slug: string;
        category_image: string | null;
        deleted_at: string | null;
        editable: number;
        created_at: string;
        updated_at: string;
    };
}

import { useDashboardData } from '@/composables/useDashboardData';
import { computed } from 'vue';

const { dashboardData } = useDashboardData();
const responsive = useResponsive();

const data = computed(() => dashboardData.top_sellers || []);
</script>