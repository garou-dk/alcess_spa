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
        <div v-if="!loadService.request.loading" id="delivery-report">
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
                        Delivery Report
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

            <h2 style="margin-top: 30px; font-size: 16px">Delivery Overview</h2>
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
                            Customer
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
                            Invoice Number
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
                            Date of Delivery
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
                            Date Received
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
                            Tracking Number
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
                            Address
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
                            Total
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(product, index) in data" :key="index">
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ product.customer_name }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ product.order_public_id }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            <span v-if="product.estimated_delivery_start && product.estimated_delivery_end">
                                {{ DateUtil.formatToMonthDayYear(product.estimated_delivery_start) }} - {{ DateUtil.formatToMonthDayYear(product.estimated_delivery_end) }}
                            </span>
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            <span v-if="product.customer_received_date">
                                {{ DateUtil.formatToMonthDayYear(product.customer_received_date) }}
                            </span>
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ product.tracking_number }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ product.address }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ CurrencyUtil.formatCurrency(product.total_amount) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <h2 style="margin-top: 30px; font-size: 16px">Approval</h2>
            <div style="display: flex; justify-content: space-between">
                <div style="width: 45%; text-align: center">
                    <span style="display: block; margin-top: 5px"
                        >Blademier S. Canalita</span
                    >
                    <small>{{
                        DateUtil.formatToMonthDayYear(
                            new Date().toISOString().split("T")[0],
                        )
                    }}</small>
                </div>
            </div>
        </div>
        <div v-else class="flex justify-center p-2">
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
    startDate: string;
    endDate: string;
}


const data = ref<IDeliveryReportData[]>([]);

const loadService = useAxiosUtil<IForm, IDeliveryReportData[]>();

const toast = useToast();

const props = defineProps<Props>();

const form : IForm = reactive({
    start_date: props.startDate,
    end_date: props.endDate,
});

const load = async () => {
    await loadService.get("admin/dashboard/delivery-report", form).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "No data found");
        }
    });
}

const printReport = () => {
    const printContents = document.getElementById("delivery-report")?.innerHTML;
    const printWindow = window.open("", "", "width=1000,height=600");

    if (printContents && printWindow) {
        printWindow.document.write(printContents);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
};

onMounted(() => {
    load();
});
</script>
