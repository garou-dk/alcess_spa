<template>
    <div>
        <form @submit.prevent="handleSubmit()" v-if="!loadService.request.loading" class="flex flex-col gap-4">
            <!-- Store QR Code -->
            <div v-if="defaultValues && defaultValues.screenshot.value" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <p class="font-semibold text-center mb-3">Store QR Code</p>
                <div class="flex justify-center mb-3">
                    <img :src="UrlUtil.getBaseApiUrl(ssLink)" alt="Store QR Code" class="w-64 h-64 object-contain" />
                </div>
                <p class="text-center text-sm text-gray-700">E-Wallet Account Number: <span class="font-semibold">{{ defaultValues.bank_account.value }}</span></p>
            </div>

            <!-- Customer Name -->
            <div>
                <InputForm
                    :errors="errors.customer_name"
                    id="customer_name"
                    labelName="Name*"
                    tag="label"
                >
                    <InputText
                        v-model="form.customer_name"
                        id="customer_name"
                        type="text"
                        placeholder="Enter your name"
                        fluid
                        :invalid="errors.customer_name.length > 0"
                    />
                </InputForm>
            </div>

            <!-- Contact Number -->
            <div>
                <InputForm
                    :errors="errors.contact_number"
                    id="contact_number"
                    labelName="Contact Number*"
                    tag="label"
                >
                    <InputText
                        v-model="form.contact_number"
                        id="contact_number"
                        type="text"
                        placeholder="Enter your contact number"
                        fluid
                        :invalid="errors.contact_number.length > 0"
                    />
                </InputForm>
            </div>

            <!-- Amount (View Only) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                <InputText
                    :model-value="CurrencyUtil.formatCurrency(order.total_amount || 0)"
                    readonly
                    disabled
                    fluid
                    class="!bg-gray-100 !font-bold !text-lg"
                />
            </div>

            <!-- Date (View Only - Current Date) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <InputText
                    :model-value="currentDate"
                    readonly
                    disabled
                    fluid
                    class="!bg-gray-100"
                />
            </div>

            <!-- E-Wallet -->
            <div>
                <InputForm
                    :errors="errors.bank_name"
                    id="bank_name"
                    labelName="E-Wallet*"
                    tag="label"
                >
                    <Select
                        v-model="form.bank_name"
                        :options="eWalletOptions"
                        id="bank_name"
                        placeholder="Select e-wallet"
                        option-label="label"
                        option-value="value"
                        class="w-full"
                        :invalid="errors.bank_name.length > 0"
                    />
                </InputForm>
            </div>

            <!-- Upload Screenshot -->
            <div>
                <InputForm
                    :errors="errors.payment_proof"
                    id="payment_proof"
                    labelName="Proof of Payment (Screenshot/Image)*"
                    tag="span"
                >
                    <MediaUploader
                        v-model="form.payment_proof"
                        :allow-crop="false"
                        label="Upload Screenshot"
                        accept="image/*"
                         @update:modelValue="errors.payment_proof = []"
                    />
                </InputForm>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-gray-200">
                <Button
                    type="button"
                    label="Cancel"
                    icon="pi pi-times"
                    @click="emit('closePopup')"
                    severity="secondary"
                    outlined
                />
                <Button
                    type="submit"
                    label="Submit"
                    icon="pi pi-save"
                    class="!bg-blue-600 hover:!bg-blue-700"
                    :loading="submitService.request.loading"
                    :disabled="!isFormValid"
                />
            </div>
        </form>
        <div v-else class="p-5 flex justify-center">
            <PageLoader />
        </div>
    </div>
</template>
<script setup lang="ts">
import { IAppConfiguration } from '@/interfaces/IAppConfiguration';
import { IOrder } from '@/interfaces/IOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import { onMounted, reactive, ref, watch, computed } from 'vue';
import { useToast } from 'vue-toastification';
import MediaUploader from "@/components/common/MediaUploader.vue";

interface IReceivedData {
    bank_account: IAppConfiguration;
    screenshot: IAppConfiguration;
}

interface Props {
    order: IOrder;
}

interface IForm {
    payment_proof: File | null;
    bank_name: string | null;
    customer_name: string | null;
    contact_number: string | null;
}

interface IFormError {
    payment_proof: string[];
    bank_name: string[];
    customer_name: string[];
    contact_number: string[];
}

const props = defineProps<Props>();
const emit = defineEmits(["cb", "closePopup"]);
const toast = useToast();

const eWalletOptions = [
    { label: 'Gcash', value: 'Gcash' },
    { label: 'Paymaya', value: 'Paymaya' },
];

const currentDate = computed(() => {
    return DateUtil.formatToMonthDayYear(new Date().toISOString());
});

const isFormValid = computed(() => {
    return !!(
        form.customer_name && 
        form.customer_name.trim() !== '' &&
        form.contact_number && 
        form.contact_number.trim() !== '' &&
        form.bank_name && 
        form.bank_name.trim() !== '' &&
        form.payment_proof
    );
});

const form = reactive<IForm>({
    payment_proof: null,
    bank_name: null,
    customer_name: null,
    contact_number: null
});

const errors = reactive<IFormError>({
    payment_proof: [],
    bank_name: [],
    customer_name: [],
    contact_number: []
});

const loadService = useAxiosUtil<null, IReceivedData>();
const defaultValues = ref<IReceivedData | null>(null);
const ssLink = ref<string | null>('customer/bank-screenshot');

const reloadLink = () => {
    ssLink.value = `customer/bank-screenshot?${new Date().getTime()}`
}

const load = async () => {
    await loadService.get("customer/owner-online-bank").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            defaultValues.value = loadService.request.data;
            reloadLink();
        }
        else {
            toast.error(loadService.request.message ?? "Failed to load settings");
        }
    });
    
    // Pre-fill customer name and contact number from order
    form.customer_name = props.order.user?.full_name || '';
    form.contact_number = props.order.contact_number || '';
}

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const clearForm = () => {
    form.bank_name = null;
    form.customer_name = null;
    form.contact_number = null;
    selectedFile.value = null;
}

const validate = () => {
    clearErrors();
    if (!form.customer_name || form.customer_name.trim() === '') {
        errors.customer_name.push("Name is required");
    }
    if (!form.contact_number || form.contact_number.trim() === '') {
        errors.contact_number.push("Contact number is required");
    }
    if (!form.bank_name || form.bank_name.trim() === '') {
        errors.bank_name.push("E-wallet is required");
    }
    if (!form.payment_proof) {
        errors.payment_proof.push("Payment proof is required");
    }
    return Object.keys(errors).every((key) => errors[key].length === 0);
}

const submitService = useAxiosUtil<FormData, IOrder>();

const handleSubmit = async () => {
    if (validate()) {
        const newForm = new FormData();
        newForm.append("customer_name", form.customer_name ?? "");
        newForm.append("contact_number", form.contact_number ?? "");
        newForm.append("payment_proof", form.payment_proof ?? "");
        newForm.append("bank_name", form.bank_name ?? "");
        await submitService.patchFormData(`customer/orders/set-payment/${props.order.order_id}`, newForm).then(() => {
            if (submitService.request.status === 200) {
                clearForm();
                emit("cb", submitService.request.data);
                toast.success(submitService.request.message ?? "Payment saved successfully");
            }
            else {
                toast.error(submitService.request.message ?? "Failed to setup payment");
            }
        });
    }
    else {
        toast.error("Please fill in the required fields.");
    }
}

onMounted(() => {
    load();
});
</script>