<template>
    <div>
        <div :class="responsive.getResponsiveClasses({
            mobile: 'flex justify-center mb-3',
            tablet: 'flex justify-end mb-4',
            desktop: 'flex justify-end'
        })">
            <Button
                label="Print"
                icon="pi pi-print"
                :class="[
                    '!bg-blue-600 hover:!bg-blue-700 !text-white',
                    responsive.getResponsiveClasses({
                        mobile: 'w-full text-sm',
                        tablet: 'text-base',
                        desktop: 'text-base'
                    })
                ]"
                @click="printReport()"
            />
        </div>
        <div id="inventory-report" v-if="!loadService.request.loading" :class="responsive.getResponsiveClasses({
            mobile: 'overflow-x-auto',
            tablet: 'overflow-x-auto',
            desktop: ''
        })">
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
                </div>
            </div>

            <h2 style="margin-top: 30px; font-size: 16px">
                Inventory Overview
            </h2>
            <div :class="responsive.getResponsiveClasses({
                mobile: 'overflow-x-auto',
                tablet: 'overflow-x-auto',
                desktop: ''
            })">
                <table
                    style="width: 100%; border-collapse: collapse; margin-top: 10px; min-width: 600px;"
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
                                Batch ID
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
                                {{ product.batch_number || '-' }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px">
                                {{ product.product_quantity }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="position: relative; margin-top: 30px">
                <div style="float: left; width: 45%">
                    <h2 style="font-size: 16px">Prepared by:</h2>
                    <div style="text-align: center">
                        <span style="display: block; margin-top: 5px"
                            >Blademier S. Canalita</span
                        >
                        <small>{{
                            DateUtil.formatToMonthDayYear(
                                DateUtil.formatYYYYMMDD(new Date()),
                            )
                        }}</small>
                    </div>
                </div>
                <div style="float: right; width: 45%">
                    <h2 style="font-size: 16px">Approved by:</h2>
                    <div style="text-align: center">
                        <span style="display: block; margin-top: 5px"
                            >Albert Von Daligdigan</span
                        >
                        <small>{{
                            DateUtil.formatToMonthDayYear(
                                DateUtil.formatYYYYMMDD(new Date()),
                            )
                        }}</small>
                    </div>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
        <div :class="responsive.getResponsiveClasses({
            mobile: 'flex justify-center py-8',
            tablet: 'flex justify-center py-12',
            desktop: 'flex justify-center'
        })" v-else>
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
import { useResponsive } from "@/composables/useResponsive";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    startDate: string | null;
    endDate: string | null;
}

const props = defineProps<Props>();
const responsive = useResponsive();

const products = ref<ProductInterface[]>([]);

const printReport = () => {
    const printContents =
        document.getElementById("inventory-report")?.innerHTML;
    const printWindow = window.open("", "", "width=1000,height=600");

    if (printContents && printWindow) {
        printWindow.document.write(`
            <html>
                <head>
                    <title>Inventory Report</title>
                    <style>
                        @media print {
                            img { display: block !important; }
                        }
                    </style>
                </head>
                <body style="font-family: Arial, sans-serif;">
                    ${printContents}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        
        // Wait for images to load before printing
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }
};

const loadService = useAxiosUtil<null, ProductInterface[]>();
const toast = useToast();

const load = async () => {
    const params = (props.startDate && props.endDate) 
        ? `?start_date=${props.startDate}&end_date=${props.endDate}` 
        : '';
    
    await loadService.get(`admin/reports/inventory${params}`).then(() => {
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
