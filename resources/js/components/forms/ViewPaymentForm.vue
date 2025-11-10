<template>
    <div>
        <div class="p-2">
            <div v-if="props.data.payment_method === 'Online Payment' && props.data.date_payment_processed">
                <div class="p-2">
                    <InputForm
                        :errors="[]"
                        id="bank_name"
                        labelName="E-Wallet Name"
                        tag="label"
                    >
                        <InputText
                            :model-value="props.data.bank_name"
                            id="bank_name"
                            type="text"
                            fluid
                            readonly
                        />
                    </InputForm>
                </div>
                <div class="p-2">
                    <InputForm
                        :errors="[]"
                        id="transaction_number"
                        labelName="Transaction Number"
                        tag="label"
                    >
                        <InputText
                            :model-value="props.data.transaction_number"
                            id="transaction_id"
                            type="text"
                            fluid
                            readonly
                        />
                    </InputForm>
                </div>
                <div>
                    <p class="font-semibold">Payment Image</p>
                </div>
                <div class="flex justify-center">
                    <img :src="UrlUtil.getBaseApiUrl(`admin/orders/payment-image/${props.data.order_id}`)" alt="Payment Image" class="max-lg:w-full lg:max-w-full" />
                </div>
            </div>
            <div v-if="!props.data.date_paid_confirmed" class="flex justify-center mt-3">
                <Button
                    label="Confirm Payment"
                    icon="pi pi-check"
                    class="primary-bg"
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
import { useToast } from 'vue-toastification';

interface Props {
    data: IOrder;
}

const props = defineProps<Props>();

const submitService = useAxiosUtil<null, IOrder>();

const toast = useToast();
const emit = defineEmits(["cb", "closePopup"]);

const handleSubmit = async () => {
    await submitService.patch(`admin/orders/confirm-payment/${props.data.order_id}`, null).then(() => {
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