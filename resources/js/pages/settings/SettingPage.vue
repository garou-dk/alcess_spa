<template>
    <div :class="responsive.getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-3 px-3',
        tablet: 'min-h-screen bg-gray-50 py-4 px-4',
        desktop: 'min-h-screen bg-gray-50 py-4 sm:py-6 px-3 sm:px-6'
    })">
        <!-- Date and Time Display -->
        <DateTimeDisplay />

        <!-- Settings Container -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            tablet: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            desktop: 'rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden'
        })">
            <div
                v-if="errorSettings"
                :class="responsive.getResponsiveClasses({
                    mobile: 'p-6 text-center',
                    tablet: 'p-8 text-center',
                    desktop: 'p-8 text-center'
                })"
            >
                <div class="mb-4">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center',
                        tablet: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center',
                        desktop: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-exclamation-triangle text-2xl text-red-600',
                            tablet: 'pi pi-exclamation-triangle text-3xl text-red-600',
                            desktop: 'pi pi-exclamation-triangle text-3xl text-red-600'
                        })"></i>
                    </div>
                </div>
                <h3 :class="responsive.getResponsiveClasses({
                    mobile: 'mb-2 text-base font-semibold text-red-800',
                    tablet: 'mb-2 text-lg font-semibold text-red-800',
                    desktop: 'mb-2 text-lg font-semibold text-red-800'
                })">Failed to Load Settings</h3>
                <p :class="responsive.getResponsiveClasses({
                    mobile: 'mb-4 text-sm text-red-600',
                    tablet: 'mb-4 text-sm text-red-600',
                    desktop: 'mb-4 text-sm text-red-600'
                })">{{ errorSettings }}</p>
                <Button
                    label="Retry"
                    icon="pi pi-refresh"
                    @click="retrySettings"
                    class="!bg-red-600 hover:!bg-red-700"
                />
            </div>
            <div v-else-if="!loadingSettings" class="w-full">
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'flex items-center justify-between px-3 py-3 border-b border-gray-200 bg-gray-50',
                    tablet: 'flex items-center justify-between px-4 py-4 border-b border-gray-200 bg-gray-50',
                    desktop: 'flex items-center justify-between px-5 py-4 border-b border-gray-200'
                })">
                    <div>
                        <h1 :class="responsive.getResponsiveClasses({
                            mobile: 'text-base font-bold text-gray-800',
                            tablet: 'text-lg font-bold text-gray-800',
                            desktop: 'text-lg font-semibold text-gray-800'
                        })">Payment Settings</h1>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">Configure online banking payment details</p>
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'flex flex-col items-end',
                        tablet: 'flex flex-col items-end',
                        desktop: 'flex items-center gap-2 text-sm text-gray-600'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'hidden',
                            tablet: 'hidden',
                            desktop: 'pi pi-cog text-blue-600'
                        })"></i>
                        <span :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-600 font-medium',
                            tablet: 'text-xs text-gray-600 font-medium',
                            desktop: 'font-medium'
                        })">Bank Configuration</span>
                    </div>
                </div>
                <form @submit.prevent="handleSubmit" :class="responsive.getResponsiveClasses({
                    mobile: 'p-3',
                    tablet: 'p-4',
                    desktop: 'p-5'
                })">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'space-y-4',
                        tablet: 'space-y-4',
                        desktop: 'space-y-4'
                    })">
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
                                    :class="responsive.getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })"
                                />
                            </InputForm>
                        </div>
                        <div v-if="defaultValues && defaultValues.screenshot.value" :class="responsive.getResponsiveClasses({
                            mobile: 'rounded-lg border border-gray-200 p-3 bg-gray-50',
                            tablet: 'rounded-lg border border-gray-200 p-4 bg-gray-50',
                            desktop: 'rounded-lg border border-gray-200 p-4 bg-gray-50'
                        })">
                            <div class="mb-2">
                                <span :class="responsive.getResponsiveClasses({
                                    mobile: 'text-xs font-medium text-gray-700',
                                    tablet: 'text-sm font-medium text-gray-700',
                                    desktop: 'text-sm font-medium text-gray-700'
                                })">Current QR Code Screenshot</span>
                            </div>
                            <img 
                                :src="UrlUtil.getBaseApiUrl(ssLink)" 
                                alt="Screenshot of QR Code" 
                                :class="responsive.getResponsiveClasses({
                                    mobile: 'w-full rounded-lg shadow-sm max-h-64 object-contain',
                                    tablet: 'w-full rounded-lg shadow-sm max-h-80 object-contain',
                                    desktop: 'w-full rounded-lg shadow-sm'
                                })"
                            />
                        </div>
                        <div>
                            <InputForm
                                :errors="errors.screenshot"
                                id="screenshot"
                                labelName="Screenshot of QR Code*"
                                tag="span"
                            >
                                <div v-if="selectedFile">
                                    <InputGroup>
                                        <InputText
                                            :model-value="selectedFile.name"
                                            readonly
                                            :class="responsive.getResponsiveClasses({
                                                mobile: 'text-sm',
                                                tablet: 'text-base',
                                                desktop: 'text-base'
                                            })"
                                        />
                                        <InputGroupAddon>
                                            <Button
                                                type="button"
                                                icon="pi pi-times"
                                                @click="selectedFile = null"
                                                severity="danger"
                                                :class="responsive.getResponsiveClasses({
                                                    mobile: 'text-sm',
                                                    tablet: 'text-base',
                                                    desktop: 'text-base'
                                                })"
                                            />
                                        </InputGroupAddon>
                                    </InputGroup>
                                </div>
                                <Button 
                                    v-else 
                                    label="Upload Screenshot" 
                                    icon="pi pi-upload" 
                                    @click="fileElement.click()"
                                    :class="[
                                        '!bg-blue-600 hover:!bg-blue-700 !text-white',
                                        responsive.getResponsiveClasses({
                                            mobile: 'w-full text-sm',
                                            tablet: 'text-base',
                                            desktop: 'text-base'
                                        })
                                    ]"
                                />
                            </InputForm>
                        </div>
                        <div :class="responsive.getResponsiveClasses({
                            mobile: 'flex justify-center pt-4',
                            tablet: 'flex justify-center pt-4',
                            desktop: 'flex justify-center pt-4'
                        })">
                            <Button 
                                type="submit" 
                                label="Save Settings" 
                                icon="pi pi-save" 
                                :loading="submitService.request.loading"
                                :class="[
                                    '!bg-blue-600 hover:!bg-blue-700 !text-white',
                                    responsive.getResponsiveClasses({
                                        mobile: 'w-full text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })
                                ]"
                            />
                        </div>
                    </div>
                </form>
            </div>
            <div v-else :class="responsive.getResponsiveClasses({
                mobile: 'p-6 flex justify-center w-full',
                tablet: 'p-8 flex justify-center w-full',
                desktop: 'p-8 flex justify-center w-full'
            })">
                <PageLoader />
            </div>
        </div>
        <input type="file" ref="fileElement" @change="onFileChange" class="hidden" id="screenshot" accept="image/png, image/jpeg, image/jpg" />
    </div>
</template>
<script setup lang="ts">
import { IAppConfiguration } from '@/interfaces/IAppConfiguration';
import { useResponsive } from '@/composables/useResponsive';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import DateTimeDisplay from '@/components/DateTimeDisplay.vue';
import { onMounted, reactive, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';
import CardSkeleton from "@/components/skeletons/CardSkeleton.vue";

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
    screenshot_url: string | null;
}

const ssLink = ref<string | null>('admin/app-configurations/bank-screenshot');
const responsive = useResponsive();

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

const loadingSettings = ref(false);
const errorSettings = ref<string | null>(null);

const retrySettings = async () => {
    errorSettings.value = null;
    await load();
};

const load = async () => {
    loadingSettings.value = true;
    errorSettings.value = null;
    try {
        await loadService.get("admin/app-configurations/online-bank").then(() => {
            if (loadService.request.status === 200 && loadService.request.data) {
                defaultValues.value = loadService.request.data;
                form.account_number = defaultValues.value.bank_account.value;
                reloadLink();
            }
            else {
                toast.error(loadService.request.message ?? "Failed to load settings");
                errorSettings.value = loadService.request.message ?? "Failed to load settings";
            }
        });
    } catch (error) {
        errorSettings.value = 'Failed to load settings. Please try again.';
    } finally {
        loadingSettings.value = false;
    }
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
    // Screenshot is only required if there's no existing screenshot
    if (!form.screenshot && (!defaultValues.value || !defaultValues.value.screenshot?.value)) {
        errors.screenshot.push("Screenshot is required");
    }
    return Object.keys(errors).every((key) => errors[key].length === 0);
}

const submitService = useAxiosUtil<IForm, null>();

const handleSubmit = async () => {
    if (validate()) {
        // Build FormData manually to properly handle file uploads
        const formData = new FormData();
        formData.append('account_number', form.account_number || '');
        
        // Only include screenshot if a new file was selected
        if (form.screenshot) {
            formData.append('screenshot', form.screenshot);
        }
        
        await submitService.postFormData("admin/app-configurations/online-bank", formData as unknown as IForm).then(() => {
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