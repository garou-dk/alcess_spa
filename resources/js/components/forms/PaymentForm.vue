<template>
    <div>
        <form @submit.prevent="handleSubmit()" v-if="!loadService.request.loading" class="flex flex-col gap-2">
            <div v-if="defaultValues && defaultValues.screenshot.value">
                <div>
                    <p class="font-semibold text-center">Store QR Code</p>
                </div>
                <img :src="UrlUtil.getBaseApiUrl(ssLink)" alt="Screenshot of QR Code" class="w-full" />
                <div>
                    <p class="text-center">E-Wallet Account Number : {{ defaultValues.bank_account.value }}</p>
                </div>
            </div>
            <div>
                <InputForm
                    :errors="errors.bank_name"
                    id="bank_name"
                    labelName="Bank Name/E-Wallet*"
                    tag="label"
                >
                    <InputText
                        v-model="form.bank_name"
                        id="bank_name"
                        type="text"
                        placeholder="Enter bank/e-wallet name"
                        fluid
                        :invalid="errors.bank_name.length > 0"
                    />
                </InputForm>
            </div>
            <div>
                <InputForm
                    :errors="errors.transaction_number"
                    id="transaction_number"
                    labelName="Transaction Number*"
                    tag="label"
                >
                    <InputText
                        v-model="form.transaction_number"
                        id="transaction_number"
                        type="text"
                        placeholder="Enter transaction number"
                        fluid
                        :invalid="errors.transaction_number.length > 0"
                    />
                </InputForm>
            </div>
            <div>
                <InputForm
                    :errors="errors.payment_proof"
                    id="payment_proof"
                    labelName="Proof of Payment (Screenshot/Image)*"
                    tag="span"
                >
                    <div
                        v-if="selectedFile"
                    >
                        <InputGroup>
                            <InputText
                                :model-value="selectedFile.name"
                                readonly
                            />
                            <InputGroupAddon>
                                <Button
                                    type="button"
                                    icon="pi pi-times"
                                    @click="selectedFile = null"
                                    severity="danger"
                                />
                            </InputGroupAddon>
                        </InputGroup>
                    </div>
                    <Button v-else label="Upload Screenshot" icon="pi pi-upload" @click="fileElement.click()" />
                </InputForm>
            </div>
            <div class="flex justify-center mt-3">
                <Button
                    type="submit"
                    label="Submit"
                    icon="pi pi-save"
                    class="primary-bg"
                    :loading="submitService.request.loading"
                />
            </div>
        </form>
        <div v-else class="p-5 flex justify-center">
            <PageLoader />
        </div>
        <input type="file" ref="fileElement" @change="onFileChange" class="hidden" id="screenshot" accept="image/png, image/jpeg, image/jpg" />
    </div>
</template>
<script setup lang="ts">
import { IAppConfiguration } from '@/interfaces/IAppConfiguration';
import { IOrder } from '@/interfaces/IOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import { onMounted, reactive, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';

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
    transaction_number: string | null;
}

interface IFormError {
    payment_proof: string[];
    bank_name: string[];
    transaction_number: string[];
}

const props = defineProps<Props>();
const emit = defineEmits(["cb", "closePopup"]);
const toast = useToast();

const fileElement = ref<HTMLInputElement>();

const selectedFile = ref<File | null>(null);

const onFileChange = (event: Event) => {
    const files = (event.target as HTMLInputElement).files;
    if (files && files.length > 0) {
        selectedFile.value = files[0];
    }
    else {
        selectedFile.value = null;
    }
    fileElement.value!.value = "";
}

watch(selectedFile, () => {
    if (selectedFile.value) {
        form.payment_proof = selectedFile.value;
    }
    else {
        form.payment_proof = null;
    }
});

const form = reactive<IForm>({
    payment_proof: null,
    bank_name: null,
    transaction_number: null
});

const errors = reactive<IFormError>({
    payment_proof: [],
    bank_name: [],
    transaction_number: []
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
}

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const clearForm = () => {
    form.transaction_number = null;
    selectedFile.value = null;
}

const validate = () => {
    clearErrors();
    if (!form.transaction_number) {
        errors.transaction_number.push("Transaction number is required");
    }
    if (!form.payment_proof) {
        errors.payment_proof.push("Payment proof is required");
    }
    if (!form.bank_name) {
        errors.bank_name.push("Bank name is required");
    }
    return Object.keys(errors).every((key) => errors[key].length === 0);
}

const submitService = useAxiosUtil<FormData, IOrder>();

const handleSubmit = async () => {
    if (validate()) {
        const newForm = new FormData();
        newForm.append("transaction_number", form.transaction_number ?? "");
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