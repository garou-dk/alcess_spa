<template>
    <div>
        <BoxShadow class="p-5">
            <form v-if="!loadService.request.loading" @submit.prevent="handleSubmit" class="w-full flex flex-col gap-2">
                <div>
                    <InputForm
                        :errors="errors.account_number"
                        id="account_number"
                        labelName="Account Number*"
                        tag="label"
                    >
                        <InputText
                            v-model="form.account_number"
                            id="account_number"
                            type="text"
                            placeholder="Enter account number"
                            fluid
                            :invalid="errors.account_number.length > 0"
                        />
                    </InputForm>
                </div>
                <div v-if="defaultValues && defaultValues.screenshot.value">
                    <div>
                        <span>Current Screenshot of QR Code</span>
                    </div>
                    <img :src="UrlUtil.getBaseApiUrl(ssLink)" alt="Screenshot of QR Code" class="w-full" />
                </div>
                <div>
                    <InputForm
                        :errors="errors.screenshot"
                        id="screenshot"
                        labelName="Screenshot of QR Code*"
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
                    <Button type="submit" label="Save" icon="pi pi-save" raised :loading="submitService.request.loading" />
                </div>
            </form>
            <div v-else class="p-2 flex justify-center w-full">
                <PageLoader />
            </div>
        </BoxShadow>
        <input type="file" ref="fileElement" @change="onFileChange" class="hidden" id="screenshot" accept="image/png, image/jpeg, image/jpg" />
    </div>
</template>
<script setup lang="ts">
import { IAppConfiguration } from '@/interfaces/IAppConfiguration';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import { onMounted, reactive, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';

interface IForm {
    account_number: string | null;
    screenshot: File | null;
}

interface IFormError {
    account_number: string[];
    screenshot: string[];
}

interface IReceivedData {
    bank_account: IAppConfiguration;
    screenshot: IAppConfiguration;
}

const ssLink = ref<string | null>('admin/app-configurations/bank-screenshot');

const reloadLink = () => {
    ssLink.value = `admin/app-configurations/bank-screenshot?${new Date().getTime()}`
}

const form = reactive<IForm>({
    account_number: null,
    screenshot: null
});

const errors = reactive<IFormError>({
    account_number: [],
    screenshot: []
});

const defaultValues = ref<IReceivedData | null>(null);

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
        form.screenshot = selectedFile.value;
    }
    else {
        form.screenshot = null;
    }
});

const loadService = useAxiosUtil<null, IReceivedData>();
const toast = useToast();

const load = async () => {
    await loadService.get("admin/app-configurations/online-bank").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            defaultValues.value = loadService.request.data;
            form.account_number = defaultValues.value.bank_account.value;
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
    form.account_number = null;
    selectedFile.value = null;
}

const validate = () => {
    clearErrors();
    if (!form.account_number) {
        errors.account_number.push("Account number is required");
    }
    if (!form.screenshot) {
        errors.screenshot.push("Screenshot is required");
    }
    return Object.keys(errors).every((key) => errors[key].length === 0);
}

const submitService = useAxiosUtil<IForm, null>();

const handleSubmit = async () => {
    if (validate()) {
        await submitService.postFormData("admin/app-configurations/online-bank", form).then(() => {
            if (submitService.request.status === 200) {
                load();
                clearForm();
                toast.success(submitService.request.message ?? "Settings updated successfully");
            }
            else {
                toast.error(submitService.request.message ?? "Failed to update settings");
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