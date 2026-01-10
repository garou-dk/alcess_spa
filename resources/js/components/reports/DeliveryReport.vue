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
        <div v-if="!loadService.request.loading" id="delivery-report" :class="responsive.getResponsiveClasses({
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
                        ORDER STATUS REPORT
                    </h1>
                </div>
            </div>

            <h2 style="margin-top: 30px; font-size: 16px">Order Status Overview</h2>
            <div :class="responsive.getResponsiveClasses({
                mobile: 'overflow-x-auto',
                tablet: 'overflow-x-auto',
                desktop: ''
            })">
                <table
                    style="width: 100%; border-collapse: collapse; margin-top: 10px; min-width: 800px;"
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
                            Order ID
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
                            Product Name
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
                            Customer Name
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
                            Quantity
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
                            Payment Method
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
                            Time of Purchase
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(order, index) in data" :key="index">
                        <tr v-for="(product, pIndex) in order.products" :key="`${index}-${pIndex}`">
                            <td style="border: 1px solid #ccc; padding: 8px">
                                {{ order.order_public_id }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px">
                                {{ product.product_name }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px">
                                {{ order.customer_name }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px; text-align: center">
                                {{ product.quantity }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px">
                                {{ order.payment_method }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px">
                                {{ DateUtil.formatToMonthDayYear(order.created_at) }}
                            </td>
                        </tr>
                    </template>
                </tbody>
                </table>
            </div>

            <div style="position: relative; margin-top: 30px">
                <div style="float: left; width: 45%">
                    <h2 style="font-size: 16px">Approval</h2>
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
                    <h2 style="font-size: 16px">Prepared by:</h2>
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
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import CurrencyUtil from "@/utils/CurrencyUtil";
import DateUtil from "@/utils/DateUtil";
import { useResponsive } from "@/composables/useResponsive";
import { onMounted, reactive, ref } from "vue";
import { useToast } from "vue-toastification";

interface IDeliveryReportData {
    order_id: number;
    order_public_id: string;
    customer_name: string;
    contact_number: string;
    barangay_id: number;
    other_details: string;
    postal_code: string;
    delivery_courier: string;
    tracking_number: string;
    estimated_delivery_start: string;
    estimated_delivery_end: string;
    shipping_fee: number;
    total_amount: number;
    payment_method: string;
    date_paid_confirmed?: string;
    products: Array<{
        product_name: string;
        quantity: number;
        price: number;
        subtotal: number;
    }>;
    created_at: string;
    days_in_delivery: number;
    customer_received_date: string | null;
    address: string;
}

interface IForm {
    start_date: string | null;
    end_date: string | null;
}

interface Props {
    startDate: string | null;
    endDate: string | null;
}


const props = defineProps<Props>();
const responsive = useResponsive();

const data = ref<IDeliveryReportData[]>([]);
const loadService = useAxiosUtil<IForm, IDeliveryReportData[]>();
const toast = useToast();

const form : IForm = reactive({
    start_date: props.startDate || null,
    end_date: props.endDate || null,
});

const load = async () => {
    console.log('Loading order status report with dates:', form);
    await loadService.get("admin/dashboard/delivery-report", form).then(() => {
        console.log('Order status report response:', loadService.request);
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
            console.log('Order status data loaded:', data.value.length, 'orders');
        }
        // No toast notification for empty data - just show empty table
    });
}

const printReport = () => {
    const printContents = document.getElementById("delivery-report")?.innerHTML;
    const printWindow = window.open("", "", "width=1000,height=600");

    if (printContents && printWindow) {
        printWindow.document.write(`
            <html>
                <head>
                    <title>Order Status Report</title>
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

onMounted(() => {
    load();
});
</script>
