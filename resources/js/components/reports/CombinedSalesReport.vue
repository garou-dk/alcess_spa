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

        <div v-if="!loadService.request.loading" id="combined-sales-report-data" :class="responsive.getResponsiveClasses({
            mobile: 'overflow-x-auto',
            tablet: 'overflow-x-auto',
            desktop: ''
        })">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center">
                <div>
                    <img
                        :src="Logo"
                        alt="ALCESS Logo"
                        style="width: 100px; height: auto; margin-right: 20px"
                    />
                </div>
                <div style="text-align: right">
                    <h1 style="color: #00598a; font-size: 24px; margin: 0">
                        SALES REPORT
                    </h1>
                </div>
            </div>

            <!-- Table -->
            <h2 style="margin-top: 30px; font-size: 16px">Combined Sales Overview</h2>
            <div :class="responsive.getResponsiveClasses({
                mobile: 'overflow-x-auto',
                tablet: 'overflow-x-auto',
                desktop: ''
            })">
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px; min-width: 900px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                Date
                            </th>
                            <th style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                Customer Name
                            </th>
                            <th style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                Type
                            </th>
                            <th style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                Product Name
                            </th>
                            <th style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                Quantity
                            </th>
                            <th style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                Delivery Fee
                            </th>
                            <th style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                Total Amount
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(val, index) in combinedData" :key="index">
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ DateUtil.formatToMonthDayYear(val.date) }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.customer_name }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.type }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.product_name }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: center">
                            {{ val.quantity }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: right">
                            {{ CurrencyUtil.formatCurrency(val.delivery_fee) }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: right">
                            {{ CurrencyUtil.formatCurrency(val.total_amount) }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="border: 1px solid #ccc; padding: 8px; text-align: right; font-weight: bold; background-color: #f3f4f6">
                            GRAND TOTAL:
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: right; font-weight: bold; background-color: #f3f4f6; color: #000; font-size: 16px">
                            {{ CurrencyUtil.formatCurrency(grandTotal) }}
                        </td>
                    </tr>
                </tfoot>
                </table>
            </div>

            <!-- Approval -->
            <div style="position: relative; margin-top: 30px">
                <div style="float: left; width: 45%">
                    <h2 style="font-size: 16px">Prepared by:</h2>
                    <div style="text-align: center">
                        <span style="display: block; margin-top: 5px">Blademier S. Canalita</span>
                        <small>{{ DateUtil.formatToMonthDayYear(DateUtil.formatYYYYMMDD(new Date())) }}</small>
                    </div>
                </div>
                <div style="float: right; width: 45%">
                    <h2 style="font-size: 16px">Approved by:</h2>
                    <div style="text-align: center">
                        <span style="display: block; margin-top: 5px">Albert Von Daligdigan</span>
                        <small>{{ DateUtil.formatToMonthDayYear(DateUtil.formatYYYYMMDD(new Date())) }}</small>
                    </div>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
        <div v-else :class="responsive.getResponsiveClasses({
            mobile: 'flex justify-center py-8',
            tablet: 'flex justify-center py-12',
            desktop: 'flex justify-center p-2'
        })">
            <PageLoader />
        </div>
    </div>
</template>

<script setup lang="ts">
import Logo from "@/../img/logo.jpg";
import useAxiosUtil from "@/utils/AxiosUtil";
import CurrencyUtil from "@/utils/CurrencyUtil";
import DateUtil from "@/utils/DateUtil";
import { useResponsive } from "@/composables/useResponsive";
import { computed, onMounted, ref } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    startDate: string | null;
    endDate: string | null;
    salesType?: string;
}

const props = withDefaults(defineProps<Props>(), {
    salesType: 'all'
});

const responsive = useResponsive();

interface ICombinedSalesData {
    date: string;
    type: 'Walk-In' | 'Online';
    customer_name: string;
    product_name: string;
    quantity: number;
    delivery_fee: number;
    total_amount: number;
}

const toast = useToast();
const loadService = useAxiosUtil<null, any>();

const walkInData = ref<any[]>([]);
const onlineData = ref<any[]>([]);

const combinedData = computed<ICombinedSalesData[]>(() => {
    const combined: ICombinedSalesData[] = [];
    
    // Add walk-in sales if type is 'all' or 'walk-in'
    if (props.salesType === 'all' || props.salesType === 'walk-in') {
        walkInData.value.forEach(sale => {
            combined.push({
                date: sale.transaction_date,
                type: 'Walk-In',
                customer_name: sale.customer_name,
                product_name: sale.product_name,
                quantity: sale.quantity,
                delivery_fee: 0,
                total_amount: sale.total_amount
            });
        });
    }
    
    // Add online sales if type is 'all' or 'online'
    if (props.salesType === 'all' || props.salesType === 'online') {
        onlineData.value.forEach(order => {
            combined.push({
                date: order.order_date,
                type: 'Online',
                customer_name: order.customer_name,
                product_name: order.product_name,
                quantity: order.quantity,
                delivery_fee: order.delivery_fee || 0,
                total_amount: order.total_amount
            });
        });
    }
    
    // Sort by date (newest first)
    return combined.sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime());
});

const walkInTotal = computed(() => {
    if (props.salesType === 'online') return 0;
    return walkInData.value.reduce((sum, sale) => sum + parseFloat(sale.total_amount), 0);
});

const onlineTotal = computed(() => {
    if (props.salesType === 'walk-in') return 0;
    return onlineData.value.reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
});

const grandTotal = computed(() => {
    return walkInTotal.value + onlineTotal.value;
});

const walkInCount = computed(() => walkInData.value.length);
const onlineCount = computed(() => onlineData.value.length);
const totalCount = computed(() => walkInCount.value + onlineCount.value);

const load = async () => {
    try {
        // Build query params - only include dates if both are provided
        const walkInParams = (props.startDate && props.endDate) 
            ? `?start_date=${props.startDate}&end_date=${props.endDate}` 
            : '';
        const onlineParams = (props.startDate && props.endDate) 
            ? `?start_date=${props.startDate}&end_date=${props.endDate}` 
            : '';
        
        // Load walk-in sales
        await loadService.get(`admin/reports/walkin-sales${walkInParams}`);
        if (loadService.request.status === 200 && loadService.request.data) {
            walkInData.value = loadService.request.data;
        }
        
        // Load online sales
        await loadService.get(`admin/reports/online-sales${onlineParams}`);
        if (loadService.request.status === 200 && loadService.request.data) {
            onlineData.value = loadService.request.data;
        }
    } catch (error) {
        console.error('Error loading sales data:', error);
        toast.error("Failed to load sales data");
    }
};

const printReport = () => {
    const printContents = document.getElementById("combined-sales-report-data")?.innerHTML;
    
    if (!printContents) {
        console.error("Could not find report content to print");
        toast.error("Unable to print report");
        return;
    }
    
    const printWindow = window.open("", "", "width=1000,height=600");

    if (printWindow) {
        printWindow.document.write(`
            <html>
                <head>
                    <title>Sales Report</title>
                    <style>
                        @media print {
                            img { display: block !important; }
                        }
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                        }
                    </style>
                </head>
                <body>
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
    } else {
        toast.error("Unable to open print window");
    }
};

onMounted(() => {
    load();
});
</script>
