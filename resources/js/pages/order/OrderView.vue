<template>
    <div>
        <BoxShadow class="mb-3">
            <div class="flex items-center p-2">
                <span class="text-gray-700 mr-2">Payment Status:</span>
                <div class="flex items-center">
                    <img :src="GCashIcon" alt="Gcash" class="w-6 h-6 mr-1" />
                    <span class="text-gray-700">Gcash</span>
                </div>
                <div class="flex items-center ml-4">
                    <img :src="CashIcon" alt="Cash" class="w-6 h-6 mr-1" />
                    <span class="text-gray-700">Cash</span>
                </div>
            </div>
        </BoxShadow>

        <BoxShadow class="mb-3">
            <div class="flex w-full">
                <div class="grow p-2">
                    <InputForm :errors="[]" id="search" label-name="Search" tag="label">
                        <InputGroup>
                            <InputGroupAddon>
                                <i class="pi pi-search" />
                            </InputGroupAddon>
                            <InputText id="search" placeholder="Search by Order ID or Customer Name" fluid />
                        </InputGroup>
                    </InputForm>
                </div>
                <div class="p-2">
                    <InputForm :errors="[]" id="status" label-name="Status" tag="label">
                        <Select placeholder="Select Status" />
                    </InputForm>
                </div>
                <div class="p-2">
                    <InputForm :errors="[]" id="limit" label-name="Limit" tag="label">
                        <Select placeholder="Select Status" />
                    </InputForm>
                </div>
                <div class="flex items-end p-2">
                    <Button label="Filter" icon="pi pi-filter" class="primary-bg" />
                </div>
            </div>
        </BoxShadow>

        <BoxShadow>
            <div class="w-full">
                <DataTable :value="list">
                    <Column field="customer_name" header="Customer Name" />
                    <Column field="order_date" header="Order Date" />
                    <Column field="order_type" header="Order Type" />
                    <Column field="payment_status" header="Payment Status" />
                    <Column field="amount" header="Amount" />
                    <Column field="payment_method" header="Payment Method">
                        <template #body="{ data }">
                            <div class="flex items-center">
                                <img v-if="data.payment_method === 'Gcash'" :src="GCashIcon" alt="Gcash"
                                    class="w-6 h-6 mr-1" />
                                <img v-else-if="data.payment_method === 'Cash'" :src="CashIcon" alt="Cash"
                                    class="w-6 h-6 mr-1" />
                                <span class="text-gray-700">{{ data.payment_method }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="{ data }">
                            <Button label="View" icon="pi pi-eye" class="primary-bg" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </BoxShadow>
    </div>
</template>
<script setup lang="ts">
import GCashIcon from "@/../img/gcash.png";
import CashIcon from "@/../img/cash.png";
import { Button } from "primevue";
import { ref } from "vue";
import { OrderInterface } from "@/interfaces/OrderInterface";

const list = ref<OrderInterface[]>([
    {
        customer_name: "Juan Dela Cruz",
        order_date: "2025-05-30",
        order_type: "For Pick Up",
        payment_status: "Pending",
        amount: 200,
        payment_method: "Gcash"
    },
]);
</script>