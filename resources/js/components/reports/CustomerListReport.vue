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

            <!-- Table -->
            <h2 style="margin-top: 30px; font-size: 16px">Customer Overview</h2>
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

            <!-- Approval -->
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
import { IAddress } from "@/interfaces/IAddress";
import { UserInterface } from "@/interfaces/UserInterface";
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import DateUtil from "@/utils/DateUtil";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";

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
        </head>
        <body style="font-family: Arial, sans-serif;">${printContents}</body>
      </html>
    `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
};

const loadService = useAxiosUtil<null, UserInterface[]>();
const toast = useToast();

const load = async () => {
    await loadService.get('admin/reports/customers').then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        }
        else {
            toast.error(loadService.request.message ?? "Failed to load settings");
        }
    });
}

const showCompleteAddress = (value: IAddress) => {
    return `${value.other_details}, ${value.barangay.barangay_name}, ${value.barangay.municity.municity_name}, ${value.barangay.municity.province.province_name}, ${value.barangay.municity.province.region.region_name}, ${value.barangay.municity.province.region.island_group.island_group_name}, ${value.postal_code}`
}

onMounted(() => {
    load();
});
</script>
