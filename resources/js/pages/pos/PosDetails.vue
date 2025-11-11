<template>
    <div>
        <BoxShadow class="p-5">
            <div class="w-full">
                <div v-if="!loadService.request.loading">
                    <div v-if="data">
                        <DataTable :value="data.sale_items" show-gridlines>
                            <template #header>
                                <div class="flex flex-wrap justify-around">
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Purchase Code</p>
                                        <p>{{ data.sale_code }}</p>
                                    </div>
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Date Created</p>
                                        <p>{{ DateUtil.formatToMonthDayYear(data.created_at) }}</p>
                                    </div>
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Payment Method</p>
                                        <p>{{ data.payment_method }}</p>
                                    </div>
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Recorded By</p>
                                        <p>{{ data.user.full_name }}</p>
                                    </div>
                                    <div class="flex items-center justify-center">
                                        <Button type="button" label="Print" icon="pi pi-print" class="primary-bg"
                                            @click="printReport()" />
                                    </div>
                                </div>
                            </template>
                            <Column field="product.product_name" header="Product Name">
                                <template #body="{ data }">
                                    <div class="flex items-center">
                                        <Avatar v-if="data.product.product_image" shape="circle" :image="UrlUtil.getBaseAppUrl(
                                            `storage/images/product/${data.product.product_image}`,
                                        )
                                            " class="aspect-square!" />
                                        <Avatar v-else shape="circle" icon="pi pi-camera" class="aspect-square!" />
                                        <div class="ml-2 shrink">
                                            {{ data.product.product_name }}
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            <Column field="price" header="Price">
                                <template #body="{ data }">
                                    {{ CurrencyUtil.formatCurrency(data.price) }}
                                </template>
                            </Column>
                            <Column field="quantity" header="Quantity" />
                            <Column field="total" header="Total">
                                <template #body="{ data }">
                                    {{ CurrencyUtil.formatCurrency(data.price * data.quantity) }}
                                </template>
                            </Column>
                            <template #footer>
                                <p class="text-end">
                                    <span class="font-semibold">Total:</span> {{
                                        CurrencyUtil.formatCurrency(data.total_amount) }}
                                </p>
                            </template>
                        </DataTable>
                    </div>
                    <div v-else class="flex justify-center p-5">
                        <p>No sales found</p>
                    </div>
                </div>
                <div v-else class="flex justify-center p-5">
                    <PageLoader />
                </div>
            </div>
        </BoxShadow>
        <div v-if="data">
            <div id="sales-report" class="hidden">
                <!-- Header -->
                <div style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    ">
                    <div>
                        <img :src="Logo" alt="ALCESS Logo" style="width: 100px; height: auto; margin-right: 20px" />
                    </div>
                    <div style="text-align: right">
                        <h1 style="color: #00598a; font-size: 24px; margin: 0">
                            RECEIPT
                        </h1>
                        <p style="margin: 5px 0">
                            <strong>Prepared by:</strong> {{ data.user.full_name }}<br />
                            <strong>Date:</strong>
                            {{
                                DateUtil.formatToMonthDayYear(
                                    new Date().toISOString().split("T")[0],
                                )
                            }}<br />
                            <strong>Transaction ID: </strong> {{ data.sale_code }} <br />
                            <strong>Payment Method: </strong> {{ data.payment_method }}
                        </p>
                    </div>
                </div>

                <h2 style="margin-top: 30px; font-size: 16px">Sales Overview</h2>
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px">
                    <thead>
                        <tr>
                            <th style="
                                    border: 1px solid #ccc;
                                    background-color: #00598a;
                                    color: white;
                                    padding: 8px;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                ">
                                Product
                            </th>
                            <th style="
                                    border: 1px solid #ccc;
                                    background-color: #00598a;
                                    color: white;
                                    padding: 8px;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                ">
                                Quantity
                            </th>
                            <th style="
                                    border: 1px solid #ccc;
                                    background-color: #00598a;
                                    color: white;
                                    padding: 8px;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                ">
                                Unit Price
                            </th>
                            <th style="
                                    border: 1px solid #ccc;
                                    background-color: #00598a;
                                    color: white;
                                    padding: 8px;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                ">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sale in data.sale_items">
                            <td style="border: 1px solid #ccc; padding: 8px; vertical-align: middle">
                                {{ sale.product.product_name }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px; vertical-align: middle; text-align: center;">
                                {{ sale.quantity }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px; vertical-align: middle">
                                {{ CurrencyUtil.formatCurrency(sale.price) }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px; vertical-align: middle">
                                {{ CurrencyUtil.formatCurrency(sale.price * sale.quantity) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="border: 1px solid #ccc; padding: 8px; text-align: right">
                                <strong>Total: {{ CurrencyUtil.formatCurrency(data.total_amount) }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ISale } from '@/interfaces/ISale';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import UrlUtil from '@/utils/UrlUtil';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import Logo from "@/../img/logo.jpg";

const route = useRoute();
const router = useRouter();
const loadService = useAxiosUtil<null, ISale>();
const toast = useToast();
const data = ref<ISale | null>(null);

const load = async () => {
    await loadService.get(`admin/sales/${route.params["id"]}`).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load sales");
            router.push({ name: "admin.pos.index" });
        }
    });
};

const printReport = () => {
    const printElement = document.getElementById("sales-report");
    if (!printElement) return;

    const logoUrl = Logo;
    const html = printElement.innerHTML.replace(':src="Logo"', `src="${logoUrl}"`);

    const printWindow = window.open("", "_blank", "width=1000,height=600");
    if (!printWindow) return;

    printWindow.document.write(`
    <html>
      <head>
        <title>Sales Report</title>
        <base href="${window.location.origin}/">
        <style>
          body { font-family: Arial, sans-serif; }
          img { width: 100px; height: auto; display: block; }
        </style>
      </head>
      <body>${html}</body>
    </html>
  `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.onload = () => {
        printWindow.print();
        printWindow.close();
    };
};



onMounted(() => {
    load();
});
</script>