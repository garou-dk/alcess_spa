<template>
    <div>
        <div class="flex justify-end">
            <Button
                label="Print"
                icon="pi pi-print"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                @click="printReport()"
            />
        </div>

        <div v-if="!loadService.request.loading" id="online-sales-report-data">
            <!-- Header -->
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
                        ONLINE SALES REPORT
                    </h1>
                    <p style="color: #666; font-size: 12px; margin: 5px 0 0 0">
                        {{ DateUtil.formatToMonthDayYear(props.startDate) }} - {{ DateUtil.formatToMonthDayYear(props.endDate) }}
                    </p>
                    <p style="color: #059669; font-size: 11px; margin: 2px 0 0 0; font-weight: 600">
                        ✓ Paid Orders Only
                    </p>
                </div>
            </div>

            <!-- Table -->
            <h2 style="margin-top: 30px; font-size: 16px">Online Sales Overview (Paid Orders)</h2>
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
                            Date
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
                            Total Amount
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(val, index) in data" :key="index">
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ DateUtil.formatToMonthDayYear(val.order_date) }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.order_id }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.customer_name }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.product_name }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.quantity }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ CurrencyUtil.formatCurrency(val.total_amount) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Approval -->
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

interface Props {
    startDate: string;
    endDate: string;
}

interface IForm {
    start_date: string | null;
    end_date: string | null;
}

interface IOnlineSalesData {
    order_date: string;           // ISO date string
    order_id: string;             // Order public ID
    customer_name: string;        // Full name from User model
    product_name: string;         // Product name
    quantity: number;             // Quantity ordered
    total_amount: number;         // Total for this line item
}

const props = defineProps<Props>();

const form: IForm = reactive({
    start_date: props.startDate,
    end_date: props.endDate,
});

const data = ref<IOnlineSalesData[]>([]);

const printReport = () => {
    const printContents = document.getElementById("online-sales-report-data")?.innerHTML;
    const printWindow = window.open("", "", "width=1000,height=600");

    if (printContents && printWindow) {
        printWindow.document.write(`
      <html>
        <head>
          <title>Online Sales Report</title>
          <style>
            @media print {
              img { display: block !important; }
            }
          </style>
        </head>
        <body style="font-family: Arial, sans-serif;">${printContents}</body>
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

const loadService = useAxiosUtil<IForm, IOnlineSalesData[]>();
const toast = useToast();

const load = async () => {
    await loadService.get('admin/reports/online-sales', form).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
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
