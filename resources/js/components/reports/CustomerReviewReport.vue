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

        <div v-if="!loadService.request.loading" id="customer-review-report-data" :class="responsive.getResponsiveClasses({
            mobile: 'overflow-x-auto',
            tablet: 'overflow-x-auto',
            desktop: ''
        })">
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
                        CUSTOMER REVIEW REPORT
                    </h1>
                </div>
            </div>

            <!-- Table -->
            <h2 style="margin-top: 30px; font-size: 16px">Customer Reviews Overview</h2>
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
                                Review Date
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
                            Rating
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
                            Review Comment
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(val, index) in data" :key="index">
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ DateUtil.formatToMonthDayYear(val.review_date) }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.customer_name }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.product_name }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.rating }}
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            {{ val.comment }}
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>

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

interface ICustomerReviewData {
    review_date: string;          // ISO date string
    customer_name: string;        // Full name from User model
    product_name: string;         // Product name
    rating: number;               // Rating value (1-5)
    comment: string;              // Review comment text
}

const data = ref<ICustomerReviewData[]>([]);

const printReport = () => {
    const printContents = document.getElementById("customer-review-report-data")?.innerHTML;
    const printWindow = window.open("", "", "width=1000,height=600");

    if (printContents && printWindow) {
        printWindow.document.write(`
      <html>
        <head>
          <title>Customer Review Report</title>
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

const loadService = useAxiosUtil<null, ICustomerReviewData[]>();
const toast = useToast();

const load = async () => {
    const params = (props.startDate && props.endDate) 
        ? `?start_date=${props.startDate}&end_date=${props.endDate}` 
        : '';
    
    console.log('Loading customer reviews with dates:', { startDate: props.startDate, endDate: props.endDate, params });
    
    await loadService.get(`admin/reports/customer-reviews${params}`, null).then(() => {
        console.log('Customer reviews response:', loadService.request);
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
            console.log('Customer reviews loaded:', data.value.length, 'reviews');
        }
        // No toast notification for empty data - just show empty table
    });
}

onMounted(() => {
    load();
});
</script>
