<template>
    <div>
        <div class="p-2">
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
            <div v-if="!props.data.date_paid_confirmed" class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                <Button
                    label="Confirm Payment"
                    icon="pi pi-check"
                    class="!bg-blue-600 hover:!bg-blue-700"
                    @click="handleSubmit()"
                    :loading="submitService.request.loading"
                />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
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

const toast = useToast();
const emit = defineEmits(["cb", "closePopup"]);

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
}
</script>