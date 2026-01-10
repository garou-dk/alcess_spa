import { RoleEnum } from "@/enums/RoleEnum";
import CustomerInvoicePage from "@/pages/customer_invoice/CustomerInvoicePage.vue";
import { RouteRecordRaw } from "vue-router";

export const CustomerInvoiceRoute: RouteRecordRaw = {
    path: "invoice",
    name: "customer-invoice",
    component: CustomerInvoicePage,
    meta: {
        access: [RoleEnum.CUSTOMER],
    },
};
