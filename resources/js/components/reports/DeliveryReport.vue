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
        <div id="delivery-report">
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
                            Item Price
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
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            Alice Bennett
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            INV-100239
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            2025-07-20
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            2025-07-23
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            TRK582143
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            ₱4,785.00
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            742 Aspen Drive, Denver, CO
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            ₱5,933.40
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            David Nguyen
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            INV-100240
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            2025-07-21
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            2025-07-24
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            TRK613892
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            ₱2,610.00
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            19 Westchester Ave, White Plains, NY
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            ₱3,427.80
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            Samantha Torres
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            INV-100241
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            2025-07-22
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            2025-07-25
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            TRK904312
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            ₱7,003.50
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            88 Lakeside Blvd, Austin, TX
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            ₱8,166.40
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            Michael Chan
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            INV-100242
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            2025-07-23
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            2025-07-26
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            TRK123578
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            ₱1,971.42
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            456 Ocean View, Santa Monica, CA
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            ₱2,842.00
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            Natalie Brooks
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            INV-100243
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            2025-07-24
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            2025-07-27
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            TRK779002
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            ₱3,387.20
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            101 Elm Street, Portland, OR
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            ₱4,389.70
                        </td>
                    </tr>
                </tbody>
            </table>

            <h2 style="margin-top: 30px; font-size: 16px">Approval</h2>
            <div style="display: flex; justify-content: space-between">
                <div style="width: 45%; text-align: center">
                    <span style="display: block; margin-top: 5px"
                        >Henry Bennet</span
                    >
                    <small>{{
                        DateUtil.formatToMonthDayYear(
                            new Date().toISOString().split("T")[0],
                        )
                    }}</small>
                </div>
                <div style="width: 45%; text-align: center">
                    <span style="display: block; margin-top: 5px"
                        >Penelope Smith</span
                    >
                    <small>{{
                        DateUtil.formatToMonthDayYear(
                            new Date().toISOString().split("T")[0],
                        )
                    }}</small>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import Logo from "@/../img/logo.jpg";
import Page from "@/stores/Page";
import DateUtil from "@/utils/DateUtil";

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
</script>
