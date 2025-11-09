<template>
    <form @submit.prevent="handleSubmit()">
        <p>Are you sure you want to change the status to <b>{{ form.status }}</b>?</p>
        <div class="my-2">
            <InputForm
                :errors="errors.shipping_fee"
                id="shipping_fee"
                labelName="Shipping Fee*"
                tag="span"
            >
                <InputNumber
                    type="number"
                    v-model="form.shipping_fee"
                    :invalid="errors.shipping_fee.length > 0"
                    placeholder="Shipping Fee"
                    fluid
                    id="shipping_fee"
                    autocomplete="off"
                    :min="0"
                    currency="PHP"
                    mode="currency"
                />
            </InputForm>
        </div>
        <div class="flex justify-center">
            <div class="flex">
                <div class="p-2">
                    <Button label="No" @click="closeModal" type="button" severity="danger" />
                </div>
                <div class="p-2">
                    <Button label="Yes" type="submit" severity="success" :loading="submitService.request.loading" />
                </div>
            </div>
        </div>
    </form>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import { reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';

interface Props {
    data: IOrder;
    status: string | null;
}

interface IForm {
    status: string | null;
    shipping_fee: number | null;
}

interface IFormError {
    status: string[];
    shipping_fee: string[];
}

const props = defineProps<Props>();
const submitService = useAxiosUtil<IForm, IOrder>();
const form = reactive<IForm>({
    status: props.status,
    shipping_fee: null
});
const errors = reactive<IFormError>({
    status: [],
    shipping_fee: []
})
const toast = useToast();
const emit = defineEmits(["cb", "closePopup"]);

const validate = () => {
    if (props.data.order_type === 'Delivery' && form.shipping_fee === null) {
        errors.shipping_fee.push("Shipping fee is required.");
    }
    if (form.status === null) {
        errors.status.push("Status is required.");
    }

    const hasErrors = [errors.shipping_fee.length > 0, errors.status.length > 0];
    return hasErrors.includes(true) ? false : form;
}

const handleSubmit = async () => {
    if (validate()) {
        if (props.data.order_type !== 'Delivery') {
            form.shipping_fee = 0;
        }
        await submitService.patch(`admin/orders/approval/${props.data.order_id}`, form).then(() => {
            if (submitService.request.status === 200 && submitService.request.data) {
                toast.success(submitService.request.message);
                emit("cb", submitService.request.data);
            }
            else {
                toast.error(submitService.request.message ?? "Please try again.");
            }
        });
    }
    else {
        toast.error("Please fill in the required fields.");
    }
}

const closeModal = () => {
    emit("closePopup");
}
</script>