<template>
    <form @submit.prevent="handleSubmit()">
        <p>Are you sure you want to cancel this order?</p>
        <div class="flex justify-center">
            <div class="flex">
                <div class="p-2">
                    <Button label="No" @click="closeModal" type="button" severity="danger" />
                </div>
                <div class="p-2">
                    <Button label="Yes" @click="" type="submit" severity="success" :loading="submitService.request.loading" />
                </div>
            </div>
        </div>
    </form>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useToast } from 'vue-toastification';

interface Props {
    order: IOrder;
}

const submitService = useAxiosUtil<null, IOrder>();
const emit = defineEmits(["cb", "closePopup"]);
const toast = useToast();
const props = defineProps<Props>();

const closeModal = () => {
    emit("closePopup");
}

const handleSubmit = async () => {
    await submitService.patch(`customer/orders/cancel/${props.order.order_id}`, null).then(() => {
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