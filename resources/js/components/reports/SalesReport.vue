<template>
    <div>
        <div class="flex justify-end">
            <Button label="Print" icon="pi pi-print" class="primary-bg" @click="printReport()" />
        </div>

        <div id="sales-report">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <img :src="Logo" alt="ALCESS Logo" style="width: 100px; height: auto; margin-right: 20px;">
                </div>
                <div style="text-align: right;">
                    <h1 style="color: #00598a; font-size: 24px; margin: 0;">SALES REPORT</h1>
                    <p style="margin: 5px 0;">
                        <strong>Prepared by:</strong> Carlos Mendez<br>
                        <strong>Date:</strong> {{ DateUtil.formatToMonthDayYear(new Date().toISOString().split('T')[0])
                        }}
                    </p>
                </div>
            </div>

            <!-- Table -->
            <h2 style="margin-top: 30px; font-size: 16px;">Sales Overview</h2>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr>
                        <th
                            style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            Date</th>
                        <th
                            style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            Invoice Number</th>
                        <th
                            style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            Customer Name</th>
                        <th
                            style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            Product</th>
                        <th
                            style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            Quantity</th>
                        <th
                            style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            Unit Price</th>
                        <th
                            style="border: 1px solid #ccc; background-color: #00598a; color: white; padding: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                            Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px;">2025-07-29</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">INV-1001</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">John Doe</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">Laptop</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">2</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">₱35,000</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">₱70,000</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px;">2025-07-29</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">INV-1002</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">Jane Smith</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">Printer</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">1</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">₱5,000</td>
                        <td style="border: 1px solid #ccc; padding: 8px;">₱5,000</td>
                    </tr>
                    <!-- Add more sales rows here -->
                </tbody>
            </table>

            <!-- Approval -->
            <h2 style="margin-top: 30px; font-size: 16px;">Approval</h2>
            <div style="display: flex; justify-content: space-between;">
                <div style="width: 45%; text-align: center;">
                    <span style="display: block; margin-top: 5px;">Henry Bennet</span>
                    <small>{{ DateUtil.formatToMonthDayYear(new Date().toISOString().split('T')[0]) }}</small>
                </div>
                <div style="width: 45%; text-align: center;">
                    <span style="display: block; margin-top: 5px;">Penelope Smith</span>
                    <small>{{ DateUtil.formatToMonthDayYear(new Date().toISOString().split('T')[0]) }}</small>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Logo from "@/../img/logo.jpg";
import DateUtil from "@/utils/DateUtil";

const printReport = () => {
    const printContents = document.getElementById("sales-report")?.innerHTML;
    const printWindow = window.open("", "", "width=1000,height=600");

    if (printContents && printWindow) {
        printWindow.document.write(`
      <html>
        <head>
          <title>Sales Report</title>
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
</script>
