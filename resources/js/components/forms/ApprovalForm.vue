<template>
    <div>
        <div>
            <button
                type="button"
                class="hover:bg-green-200 hover:cursor-pointer p-2 w-full text-start flex items-center gap-2"
                @click="openModal($event, 'Confirmed')"
            >
                <i class="pi pi-thumbs-up" /> Approve
            </button>
        </div>
        <div>
            <button
                type="button"
                class="hover:bg-red-200 hover:cursor-pointer p-2 w-full text-start flex items-center gap-2"
                @click="openModal($event, 'Rejected')"
            >
                <i class="pi pi-thumbs-down" /> Decline
            </button>
        </div>
        <Dialog
            v-model:visible="modalVisible"
            header="Change Status"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <p>Are you sure you want to change the status to <b>{{ form.status }}</b>?</p>
            <template #footer>
                <Button
                    label="No"
                    icon="pi pi-times"
                    class="p-button-text"
                    @click="modalVisible = false"
                />
                <Button
                    label="Yes"
                    icon="pi pi-check"
                    class="primary-bg"
                    @click="handleSubmit()"
                />
            </template>
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import { reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';

interface Props {
    data: IOrder;
}

interface IForm {
    status: string | null;
}

const modalVisible = ref<boolean>(false);
const props = defineProps<Props>();
const submitService = useAxiosUtil<IForm, IOrder>();
const form = reactive<IForm>({
    status: null,
});
const toast = useToast();
const emit = defineEmits(["cb", "closePopup"]);

const openModal = (event: Event, value: string) => {
    form.status = value;
    modalVisible.value = true;
};

const handleSubmit = async () => {
    await submitService.patch(`admin/orders/approval/${props.data.order_id}`, form).then(() => {
        if (submitService.request.status === 200 && submitService.request.data) {
            toast.success(submitService.request.message);
            emit("cb", submitService.request.data);
            modalVisible.value = false;
        }
        else {
            toast.error(submitService.request.message ?? "Please try again.");
        }
    });
}
</script>