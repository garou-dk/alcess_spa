<template>
    <div>
        <div class="flex justify-end">
            <Button
                label="Print"
                icon="pi pi-print"
                class="primary-bg"
                @click="printReport()"
            />
        </div>
        <div id="inventory-report" v-if="!loadService.request.loading">
            <div
                style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                "
            >
                <div>
                    <img
                        :src="Logo"
                        alt="ALCESS Logo"
                        style="width: 100px; height: auto; margin-right: 20px"
                    />
                </div>
                <div style="text-align: right">
                    <h1 style="color: #00598a; font-size: 24px; margin: 0">
                        INVENTORY REPORT
                    </h1>
                    <p style="margin: 5px 0">
                        <strong>Prepared by:</strong> {{ Page.user ? Page.user.full_name : '' }}<br />
                        <strong>Date:</strong>
                        {{
                            DateUtil.formatToMonthDayYear(
                                new Date().toISOString().split("T")[0],
                            )
                        }}
                    </p>
                </div>
            </div>

            <h2 style="margin-top: 30px; font-size: 16px">
                Inventory Overview
            </h2>
            <table
                style="width: 100%; border-collapse: collapse; margin-top: 10px"
            >
                <thead>
                    <tr>
                        <th
                            style="
                                border: 1px solid #ccc;
                                background-color: #00598a;
                                color: white;
                                padding: 8px;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            "
                        >
                            Item Description
                        </th>
                        <th
                            style="
                                border: 1px solid #ccc;
                                background-color: #00598a;
                                color: white;
                                padding: 8px;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            "
                        >
                            Item SKU
                        </th>
                        <th
                            style="
                                border: 1px solid #ccc;
                                background-color: #00598a;
                                color: white;
                                padding: 8px;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            "
                        >
                            Current Quantity
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(product, index) in products" :key="index">
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ product.product_name }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ product.sku }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ product.product_quantity }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <h2 style="margin-top: 30px; font-size: 16px">Approval</h2>
            <div style="display: flex; justify-content: space-between">
                <div style="width: 45%; text-align: center">
                    <span style="display: block; margin-top: 5px"
                        >Albert Daligdigan</span
                    >
                    <small>{{
                        DateUtil.formatToMonthDayYear(
                            new Date().toISOString().split("T")[0],
                        )
                    }}</small>
                </div>
            </div>
        </div>
        <div class="flex justify-center" v-else>
            <PageLoader />
        </div>
    </div>
</template>
<script setup lang="ts">
import Logo from "@/../img/logo.jpg";
import { ProductInterface } from "@/interfaces/ProductInterface";
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import DateUtil from "@/utils/DateUtil";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";

const products = ref<ProductInterface[]>([]);

const printReport = () => {
    const printContents =
        document.getElementById("inventory-report")?.innerHTML;
    const printWindow = window.open("", "", "width=1000,height=600");

    if (printContents && printWindow) {
        printWindow.document.write(printContents);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
};

const loadService = useAxiosUtil<null, ProductInterface[]>();
const toast = useToast();

const load = async () => {
    await loadService.get('admin/reports/inventory').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            products.value = loadService.request.data;
        }
        else {
            toast.error(loadService.request.message ?? "Failed to load settings");
        }
    });
}

onMounted(() => {
    load();
});
</script>
