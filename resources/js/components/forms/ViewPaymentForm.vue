<template>
    <div>
        <div class="p-2">
            <!-- Cancel Payment form (reason) - shown when admin clicks Cancel Payment -->
            <div v-if="showCancelForm" class="flex flex-col gap-4">
                <p class="text-sm text-gray-600">Reject this payment and cancel the order (e.g. suspected scam). The customer will be notified with the reason below.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for cancelling payment*</label>
                    <Textarea
                        v-model="cancelReason"
                        placeholder="e.g. Invalid or fraudulent payment proof"
                        rows="4"
                        class="w-full"
                        :invalid="cancelReasonError.length > 0"
                    />
                    <p v-if="cancelReasonError.length > 0" class="text-sm text-red-600 mt-1">{{ cancelReasonError }}</p>
                </div>
                <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200">
                    <Button
                        type="button"
                        label="Back"
                        icon="pi pi-arrow-left"
                        severity="secondary"
                        outlined
                        @click="showCancelForm = false; cancelReason = ''; cancelReasonError = ''"
                    />
                    <Button
                        type="button"
                        label="Submit Cancel Payment"
                        icon="pi pi-times-circle"
                        severity="danger"
                        @click="handleCancelPayment()"
                        :loading="cancelPaymentService.request.loading"
                    />
                </div>
            </div>

            <!-- View Payment Details + Confirm / Cancel Payment buttons -->
            <template v-else>
                <div v-if="props.data.payment_method === 'Online Payment' && props.data.date_payment_processed" class="flex flex-col gap-4">
                    <!-- Payment Proof -->
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-3">Proof of Payment (Screenshot/Image)</p>
                        <div class="flex justify-center border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                            <img :src="UrlUtil.getBaseApiUrl(`admin/orders/payment-image/${props.data.order_id}`)" alt="Payment Image" class="max-w-full h-auto" />
                        </div>
                    </div>

                    <!-- Customer Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <InputText
                            :model-value="props.data.user?.full_name || 'N/A'"
                            readonly
                            disabled
                            fluid
                            class="!bg-gray-100"
                        />
                    </div>

                    <!-- Contact Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                        <InputText
                            :model-value="props.data.contact_number || 'N/A'"
                            readonly
                            disabled
                            fluid
                            class="!bg-gray-100"
                        />
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <InputText
                            :model-value="CurrencyUtil.formatCurrency(props.data.total_amount || 0)"
                            readonly
                            disabled
                            fluid
                            class="!bg-gray-100 !font-bold !text-lg"
                        />
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <InputText
                            :model-value="DateUtil.formatToMonthDayYear(props.data.date_payment_processed)"
                            readonly
                            disabled
                            fluid
                            class="!bg-gray-100"
                        />
                    </div>

                    <!-- E-Wallet -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">E-Wallet</label>
                        <InputText
                            :model-value="props.data.bank_name"
                            readonly
                            disabled
                            fluid
                            class="!bg-gray-100"
                        />
                    </div>
                </div>
                <div v-if="!props.data.date_paid_confirmed" class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-200">
                    <Button
                        type="button"
                        label="Cancel Payment"
                        icon="pi pi-times-circle"
                        severity="danger"
                        outlined
                        @click="showCancelForm = true"
                    />
                    <Button
                        label="Confirm Payment"
                        icon="pi pi-check"
                        class="!bg-blue-600 hover:!bg-blue-700"
                        @click="handleSubmit()"
                        :loading="submitService.request.loading"
                    />
                </div>
            </template>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import { IOrder } from '@/interfaces/IOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import { useToast } from 'vue-toastification';

interface Props {
    data: IOrder;
}

const props = defineProps<Props>();

const submitService = useAxiosUtil<null, IOrder>();
const cancelPaymentService = useAxiosUtil<{ remarks: string }, IOrder>();

const toast = useToast();
const emit = defineEmits(["cb", "closePopup"]);

const showCancelForm = ref(false);
const cancelReason = ref('');
const cancelReasonError = ref('');

const handleSubmit = async () => {
    await submitService.patch(`admin/orders/confirm-payment-status/${props.data.order_id}`, null).then(() => {
        if (submitService.request.status === 200 && submitService.request.data) {
            toast.success(submitService.request.message);
            emit("cb", submitService.request.data);
        }
        else {
            toast.error(submitService.request.message ?? "Please try again.");
        }
    });
};

const handleCancelPayment = async () => {
    const trimmed = cancelReason.value?.trim() ?? '';
    cancelReasonError.value = '';
    if (!trimmed) {
        cancelReasonError.value = 'Reason is required.';
        return;
    }
    if (trimmed.length > 500) {
        cancelReasonError.value = 'Reason must be at most 500 characters.';
        return;
    }
    await cancelPaymentService.patch(`admin/orders/cancel-payment/${props.data.order_id}`, { remarks: trimmed }).then(() => {
        if (cancelPaymentService.request.status === 200 && cancelPaymentService.request.data) {
            toast.success(cancelPaymentService.request.message ?? 'Payment cancelled. Order has been cancelled.');
            emit("cb", cancelPaymentService.request.data);
            emit("closePopup");
        }
        else {
            toast.error(cancelPaymentService.request.message ?? "Failed to cancel payment.");
        }
    });
};
</script>