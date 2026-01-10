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
                        LIST OF CUSTOMER
                    </h1>
                </div>
            </div>

            <!-- Table -->
            <h2 style="margin-top: 30px; font-size: 16px">Customer Overview</h2>
            <div :class="responsive.getResponsiveClasses({
                mobile: 'overflow-x-auto',
                tablet: 'overflow-x-auto',
                desktop: ''
            })">
                <table
                    style="width: 100%; border-collapse: collapse; margin-top: 10px; min-width: 700px;"
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
                                Account Name
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
                                Contact Number
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(person, index) in data" :key="index">
                            <td style="border: 1px solid #ccc; padding: 8px">
                                {{ DateUtil.formatToMonthDayYear(person.created_at) }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px">
                                {{ person.full_name }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px">
                                {{
                                    person.address ? showCompleteAddress(person.address) : "N/A"
                                }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px">
                                {{ person.address ? person.address.contact_number : 'N/A' }}
                            </td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>

            <!-- Approval -->
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
import { IAddress } from "@/interfaces/IAddress";
import { UserInterface } from "@/interfaces/UserInterface";
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

const data = ref<UserInterface[]>([]);

const printReport = () => {
    const printContents =
        document.getElementById("inventory-report")?.innerHTML;
    const printWindow = window.open("", "", "width=1000,height=600");

    if (printContents && printWindow) {
        printWindow.document.write(`
      <html>
        <head>
          <title>List of Customer</title>
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

const loadService = useAxiosUtil<null, UserInterface[]>();
const toast = useToast();

const load = async () => {
    const params = (props.startDate && props.endDate) 
        ? `?start_date=${props.startDate}&end_date=${props.endDate}` 
        : '';
    
    await loadService.get(`admin/reports/customers${params}`).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        }
        // No toast notification for empty data - just show empty table
    });
}

const showCompleteAddress = (value: IAddress) => {
    return `${value.other_details}, ${value.barangay.barangay_name}, ${value.barangay.municity.municity_name}, ${value.barangay.municity.province.province_name}, ${value.barangay.municity.province.region.region_name}, ${value.barangay.municity.province.region.island_group.island_group_name}, ${value.postal_code}`
}

onMounted(() => {
    load();
});
</script>
