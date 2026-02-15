<template>
    <form @submit.prevent="handleSubmit()">
        <div class="space-y-4">
            <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                <i class="pi pi-exclamation-triangle text-amber-600 text-lg"></i>
                <p class="text-sm text-amber-800 font-medium">Are you sure you want to cancel this order?</p>
            </div>

            <!-- Cancel Reason -->
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">
                    Reason for cancellation <span class="text-red-500">*</span>
                </label>
                <Select
                    v-model="cancelReason"
                    :options="cancelReasons"
                    placeholder="Select a reason"
                    class="w-full"
                    :invalid="submitted && !cancelReason"
                />
            </div>

            <!-- Other Reason (shown when "Other" is selected) -->
            <div v-if="cancelReason === 'Other'" class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">
                    Please specify <span class="text-red-500">*</span>
                </label>
                <Textarea
                    v-model="otherReason"
                    rows="3"
                    placeholder="Enter your reason for cancellation..."
                    class="w-full"
                    :invalid="submitted && cancelReason === 'Other' && !otherReason?.trim()"
                    autoResize
                />
            </div>

            <p v-if="submitted && !cancelReason" class="text-xs text-red-500 font-medium">
                <i class="pi pi-info-circle text-xs mr-1"></i>
                Please select a cancellation reason.
            </p>
            <p v-if="submitted && cancelReason === 'Other' && !otherReason?.trim()" class="text-xs text-red-500 font-medium">
                <i class="pi pi-info-circle text-xs mr-1"></i>
                Please specify your reason.
            </p>
        </div>

        <div class="flex justify-end gap-3 mt-5 pt-4 border-t border-gray-200">
            <Button label="Keep Order" @click="closeModal" type="button" severity="secondary" outlined icon="pi pi-arrow-left" />
            <Button label="Cancel Order" type="submit" severity="danger" :loading="submitService.request.loading" icon="pi pi-times" />
        </div>
    </form>
</template>
<script setup lang="ts">
import { ref } from 'vue';
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

const cancelReason = ref<string | null>(null);
const otherReason = ref('');
const submitted = ref(false);

const cancelReasons = [
    'Changed my mind',
    'Found a better price elsewhere',
    'Ordered by mistake',
    'Delivery takes too long',
    'Want to change order details',
    'Financial reasons',
    'Duplicate order',
    'Other',
];

const closeModal = () => {
    emit("closePopup");
}

const handleSubmit = async () => {
    submitted.value = true;

    if (!cancelReason.value) {
        toast.error("Please select a reason for cancellation.");
        return;
    }

    if (cancelReason.value === 'Other' && !otherReason.value?.trim()) {
        toast.error("Please specify your reason for cancellation.");
        return;
    }

    const reason = cancelReason.value === 'Other' 
        ? otherReason.value.trim() 
        : cancelReason.value;

    await submitService.patch(`customer/orders/cancel/${props.order.order_id}`, {
        cancel_reason: reason
    }).then(() => {
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