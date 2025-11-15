<template>
    <BoxShadow class="mb-3">
        <div class="w-full">
            <div class="flex items-center justify-between p-3">
                <p class="font-semibold text-gray-700">
                    Recent Pending Orders
                </p>
                <RouterLink :to="{ name: 'admin.order.index' }">
                    <button type="button" class="text-sm text-blue-600 hover:underline cursor-pointer">
                        View All
                    </button>
                </RouterLink>
            </div>
            <DataTable
                class="w-full"
                :value="users"
                :loading="loadService.request.loading"
                columnResizeMode="expand"
            >
                <Column field="full_name" header="Customer">
                    <template #body="{ data }">
                        <div class="flex items-center">
                            <Avatar shape="circle" icon="pi pi-user" />
                            <div class="ml-2 shrink">
                                {{ data.user.full_name }}
                            </div>
                        </div>
                    </template>
                </Column>
                <Column field="created_at" header="Date Ordered">
                    <template #body="{ data }">
                        {{
                            DateUtil.formatToMonthDayYear(data.created_at)
                        }}
                    </template>
                </Column>
                <template #empty>
                    <p>No pending orders</p>
                </template>
            </DataTable>
        </div>
    </BoxShadow>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import { onMounted, ref } from 'vue';
import { useToast } from 'vue-toastification';

const loadService = useAxiosUtil<null, null>();
const toast = useToast();
const users = ref<IOrder[]>([]);

const load = async () => {
    await loadService.get('admin/dashboard/pending').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            users.value = loadService.request.data;
        }
        else {
            toast.error(loadService.request.message ?? 'Failed to load data');
        }
    });
}

onMounted(() => {
    load();
});
</script>