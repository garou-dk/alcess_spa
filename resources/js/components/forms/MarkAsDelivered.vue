<template>
    <Button
        label="Mark as Received"
        type="button"
        icon="pi pi-check"
        @click="handleSubmit()"
        :loading="submitService.request.loading"
    />
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useToast } from 'vue-toastification';

interface Props {
    data: IOrder;
}

const props = defineProps<Props>();

const submitService = useAxiosUtil<null, IOrder>();

const toast = useToast();
const emit = defineEmits(["cb", "closePopup"]);

const handleSubmit = async () => {
    await submitService.patch(`customer/orders/mark-delivered/${props.data.order_id}`, null).then(() => {
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