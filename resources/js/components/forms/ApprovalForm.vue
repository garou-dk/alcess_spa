<template>
    <form @submit.prevent="handleSubmit()">
        <div class="mb-4">
            <p class="text-gray-700">Are you sure you want to change the status to <span class="font-semibold text-gray-900">{{ form.status }}</span>?</p>
        </div>
        <div class="my-2" v-if="(form.status === 'Accept' || form.status === 'Confirmed') && props.data.order_type === 'Delivery'" >
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
        <div class="my-2" v-if="form.status === 'Decline' || form.status === 'Rejected'">
            <InputForm
                :errors="errors.remarks"
                id="remarks"
                labelName="Reason for Declining*"
                tag="span"
            >
                <Textarea
                    v-model="form.remarks"
                    id="remarks"
                    placeholder="Enter reason for declining this order"
                    fluid
                    :invalid="errors.remarks.length > 0"
                />
            </InputForm>
        </div>
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
            <Button 
                label="Cancel" 
                @click="closeModal" 
                type="button" 
                severity="secondary"
                outlined
            />
            <Button 
                label="Confirm" 
                type="submit" 
                class="!bg-blue-600 hover:!bg-blue-700"
                :loading="submitService.request.loading" 
            />
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
    remarks: string | null
}

interface IFormError {
    status: string[];
    shipping_fee: string[];
    remarks: string[]
}

const props = defineProps<Props>();
const submitService = useAxiosUtil<IForm, IOrder>();
const form = reactive<IForm>({
    status: props.status,
    shipping_fee: null,
    remarks: null
});
const errors = reactive<IFormError>({
    status: [],
    shipping_fee: [],
    remarks: []
})
const toast = useToast();
const emit = defineEmits(["cb", "closePopup"]);

const clearErrors = () => {
    errors.status = [];
    errors.shipping_fee = [];
    errors.remarks = [];
}

const validate = () => {
    clearErrors();
    
    // Validate shipping fee for Delivery orders when accepting
    if ((form.status === 'Accept' || form.status === 'Confirmed') && props.data.order_type === 'Delivery' && form.shipping_fee === null) {
        errors.shipping_fee.push("Shipping fee is required for delivery orders.");
    }
    
    if (form.status === null) {
        errors.status.push("Status is required.");
    }

    // Validate remarks when declining
    if ((form.status === 'Decline' || form.status === 'Rejected') && !form.remarks) {
        errors.remarks.push("Reason for declining is required.");
    }

    const hasErrors = [errors.shipping_fee.length > 0, errors.status.length > 0, errors.remarks.length > 0];
    return hasErrors.includes(true) ? false : form;
}

const handleSubmit = async () => {
    const result = validate();
    
    if (result) {
        // Determine which endpoint to use based on status
        let endpoint = '';
        let payload: any = {};
        
        if (form.status === 'Accept' || form.status === 'Confirmed') {
            // Accept order endpoint
            endpoint = `admin/orders/accept/${props.data.order_id}`;
            payload = {
                shipping_fee: props.data.order_type === 'Delivery' ? form.shipping_fee : 0
            };
        } else if (form.status === 'Decline' || form.status === 'Rejected') {
            // Decline order endpoint
            endpoint = `admin/orders/decline/${props.data.order_id}`;
            payload = {
                remarks: form.remarks
            };
        }
        
        await submitService.patch(endpoint, payload).then(() => {
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