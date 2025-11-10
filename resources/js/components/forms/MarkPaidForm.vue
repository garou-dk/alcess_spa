<template>
    <Button
        type="button"
        class="hover:bg-blue-200! hover:cursor-pointer p-2 w-full text-start flex items-center gap-2 bg-white! text-black! border-none!"
        @click="handleSubmit($event)"
        label="Mark as Paid"
        icon="pi pi-check"
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

const handleSubmit = async (event: Event) => {
    await submitService.patch(`admin/orders/confirm-payment/${props.data.order_id}`, null).then(() => {
        if (submitService.request.status === 200 && submitService.request.data) {
            toast.success(submitService.request.message);
            emit("cb", event);
        }
        else {
            toast.error(submitService.request.message ?? "Please try again.");
        }
    });
}
</script>