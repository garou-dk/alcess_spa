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
        <div id="inventory-report">
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
                    <p style="margin: 5px 0">
                        <strong>Prepared by:</strong> Carlos Mendez<br />
                        <strong>Date:</strong>
                        {{
                            DateUtil.formatToMonthDayYear(
                                new Date().toISOString().split("T")[0],
                            )
                        }}
                    </p>
                </div>
            </div>

            <h2 style="margin-top: 30px; font-size: 16px">
                Inventory Overview
            </h2>
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
                            Quantity Shipped
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
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            Dell Laptop
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            LAP123
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">15</td>
                        <td style="border: 1px solid #ccc; padding: 8px">55</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            Ergonomic Chair
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            OCH456
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">5</td>
                        <td style="border: 1px solid #ccc; padding: 8px">35</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            HP Laser Printer
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            PRI789
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">3</td>
                        <td style="border: 1px solid #ccc; padding: 8px">12</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            Samsung Monitor
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            MON012
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">10</td>
                        <td style="border: 1px solid #ccc; padding: 8px">30</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            Lenovo Desktop
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">
                            DES345
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px">8</td>
                        <td style="border: 1px solid #ccc; padding: 8px">42</td>
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
import DateUtil from "@/utils/DateUtil";

const printReport = () => {
    const printContents =
        document.getElementById("inventory-report")?.innerHTML;
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
