<template>
    <Button
        type="button"
        icon="pi pi-check"
        label="Confirm Cash Delivery"
        severity="success"
        :loading="submitService.request.loading"
        @click="handleSubmit()"
    />
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useToast } from 'vue-toastification';

interface Props {
    order: IOrder;
}
const props = defineProps<Props>();

const submitService = useAxiosUtil();

const toast = useToast();
const emit = defineEmits(["cb", "closePopup"]);

const handleSubmit = async () => {
    await submitService.patch(`customer/orders/confirm-cash-delivery/${props.order.order_id}`, null).then(() => {
        if (submitService.request.status === 200 && submitService.request.data) {
            toast.success(submitService.request.message);
            emit("cb", submitService.request.data);
        }
        else {
            toast.error(submitService.request.message ?? "Please try again.");
        }
    })
}
</script>