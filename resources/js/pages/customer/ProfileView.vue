<template>
    <div :class="getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-4 px-3',
        tablet: 'min-h-screen bg-gray-50 py-6 px-4',
        desktop: 'min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8'
    })">
        <div :class="getResponsiveClasses({
            mobile: 'max-w-full mx-auto',
            tablet: 'max-w-4xl mx-auto',
            desktop: 'max-w-5xl mx-auto'
        })">
            <!-- Header -->
            <div :class="getResponsiveClasses({
                mobile: 'mb-4',
                tablet: 'mb-6',
                desktop: 'mb-6'
            })">
                <h1 :class="getResponsiveClasses({
                    mobile: 'text-xl font-bold text-gray-900',
                    tablet: 'text-2xl font-bold text-gray-900',
                    desktop: 'text-2xl font-bold text-gray-900'
                })">Account Security</h1>
                <p :class="getResponsiveClasses({
                    mobile: 'mt-1 text-sm text-gray-600',
                    tablet: 'mt-1 text-sm text-gray-600',
                    desktop: 'mt-1 text-sm text-gray-600'
                })">Manage your security settings and recovery options</p>
            </div>

            <!-- User Info Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-4">
                <div :class="getResponsiveClasses({
                    mobile: 'p-4',
                    tablet: 'p-5',
                    desktop: 'p-6'
                })">
                    <div :class="getResponsiveClasses({
                        mobile: 'flex flex-col items-center text-center gap-3',
                        tablet: 'flex items-center gap-4',
                        desktop: 'flex items-center gap-4'
                    })">
                        <Avatar 
                            v-if="Page.user?.image" 
                            :image="UrlUtil.getBaseApiUrl(`profile/${Page.user.image}`)"
                            size="xlarge"
                            shape="circle"
                        />
                        <Avatar 
                            v-else 
                            :label="Page.user?.full_name?.[0]?.toUpperCase()" 
                            size="xlarge"
                            shape="circle"
                            class="!bg-blue-600 !text-white"
                        />
                        <div :class="getResponsiveClasses({
                            mobile: 'text-center',
                            tablet: 'flex-1',
                            desktop: 'flex-1'
                        })">
                            <h2 :class="getResponsiveClasses({
                                mobile: 'text-lg font-semibold text-gray-900',
                                tablet: 'text-xl font-semibold text-gray-900',
                                desktop: 'text-xl font-semibold text-gray-900'
                            })">{{ Page.user?.full_name }}</h2>
                            <p class="text-sm text-gray-600">{{ Page.user?.email }}</p>
                            <Tag :value="Page.user?.role?.role_name" severity="info" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings Grid -->
            <div :class="getResponsiveClasses({
                mobile: 'space-y-4',
                tablet: 'grid grid-cols-2 gap-4',
                desktop: 'grid grid-cols-2 gap-4'
            })">
                <!-- Security Question Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div :class="getResponsiveClasses({
                        mobile: 'p-4',
                        tablet: 'p-5',
                        desktop: 'p-6'
                    })">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="pi pi-question-circle text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">Security Question</h3>
                                <p class="text-xs text-gray-500">Account recovery verification</p>
                            </div>
                        </div>

                        <div v-if="Page.user?.security_question" class="p-3 bg-green-50 rounded-lg border border-green-200 mb-4">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-check-circle text-green-600"></i>
                                <span class="text-sm font-medium text-green-800">Configured</span>
                            </div>
                            <p class="text-xs text-green-700 mt-1">Your security question is active and ready for account recovery.</p>
                        </div>
                        <div v-else class="p-3 bg-yellow-50 rounded-lg border border-yellow-200 mb-4">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-exclamation-circle text-yellow-600"></i>
                                <span class="text-sm font-medium text-yellow-800">Not Configured</span>
                            </div>
                            <p class="text-xs text-yellow-700 mt-1">Set up a security question to enable account recovery.</p>
                        </div>

                        <Button 
                            :label="Page.user?.security_question ? 'Update Question' : 'Configure'" 
                            :icon="Page.user?.security_question ? 'pi pi-pencil' : 'pi pi-plus'"
                            :severity="Page.user?.security_question ? 'secondary' : 'primary'"
                            :class="getResponsiveClasses({
                                mobile: 'w-full',
                                tablet: 'w-full',
                                desktop: 'w-full'
                            })"
                            @click="showQuestionDialog = true" 
                        />
                    </div>
                </div>

                <!-- Recovery Codes Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div :class="getResponsiveClasses({
                        mobile: 'p-4',
                        tablet: 'p-5',
                        desktop: 'p-6'
                    })">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <i class="pi pi-key text-purple-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">Recovery Codes</h3>
                                <p class="text-xs text-gray-500">One-time use backup codes</p>
                            </div>
                        </div>

                        <div v-if="recoveryCodes.length > 0">
                            <div :class="getResponsiveClasses({
                                mobile: 'grid grid-cols-1 gap-2 mb-4',
                                tablet: 'grid grid-cols-2 gap-2 mb-4',
                                desktop: 'grid grid-cols-2 gap-2 mb-4'
                            })">
                                <div 
                                    v-for="(code, index) in recoveryCodes" 
                                    :key="index" 
                                    class="flex items-center justify-between p-2 bg-gray-50 rounded border border-gray-200"
                                >
                                    <code class="text-xs font-mono font-semibold text-gray-700 tracking-wide">{{ code }}</code>
                                    <Button 
                                        icon="pi pi-copy" 
                                        text 
                                        rounded 
                                        size="small"
                                        v-tooltip.top="'Copy'"
                                        @click="copyToClipboard(code)" 
                                    />
                                </div>
                            </div>
                            <div class="p-2 bg-amber-50 rounded border border-amber-200 mb-4">
                                <p class="text-xs text-amber-800">
                                    <i class="pi pi-info-circle mr-1"></i>
                                    Store these codes securely. Each code can only be used once.
                                </p>
                            </div>
                            <div :class="getResponsiveClasses({
                                mobile: 'flex flex-col gap-2',
                                tablet: 'flex gap-2',
                                desktop: 'flex gap-2'
                            })">
                                <Button 
                                    label="Download" 
                                    icon="pi pi-download" 
                                    severity="secondary"
                                    outlined
                                    size="small"
                                    :class="getResponsiveClasses({
                                        mobile: 'flex-1',
                                        tablet: 'flex-1',
                                        desktop: 'flex-1'
                                    })"
                                    @click="downloadCodes()" 
                                />
                                <Button 
                                    label="Regenerate" 
                                    icon="pi pi-refresh" 
                                    severity="danger"
                                    outlined
                                    size="small"
                                    :class="getResponsiveClasses({
                                        mobile: 'flex-1',
                                        tablet: 'flex-1',
                                        desktop: 'flex-1'
                                    })"
                                    @click="confirmRegenerate()" 
                                />
                            </div>
                        </div>
                        <div v-else class="text-center py-4">
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                <i class="pi pi-lock text-xl text-gray-400"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700 mb-1">No Recovery Codes</p>
                            <p class="text-xs text-gray-500 mb-4">Generate backup codes for emergency access</p>
                            <Button 
                                label="Generate Codes" 
                                icon="pi pi-key"
                                severity="help"
                                :class="getResponsiveClasses({
                                    mobile: 'w-full',
                                    tablet: 'w-full',
                                    desktop: 'w-full'
                                })"
                                @click="generateCodes()" 
                                :loading="generatingCodes"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Question Dialog -->
        <Dialog 
            v-model:visible="showQuestionDialog" 
            modal 
            header="Security Question Setup" 
            :style="{ width: '28rem' }"
            :breakpoints="{ '575px': '90vw' }"
        >
            <div class="flex flex-col gap-4 pt-2">
                <div class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                    <p class="text-sm text-blue-800">
                        Your security question will be used to verify your identity during account recovery.
                    </p>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700">Select a Question</label>
                    <Select 
                        v-model="questionForm.question" 
                        :options="predefinedQuestions" 
                        placeholder="Choose a security question" 
                        fluid 
                    />
                </div>

                <div class="relative">
                    <Divider align="center">
                        <span class="text-xs text-gray-400 bg-white px-2">OR</span>
                    </Divider>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700">Custom Question</label>
                    <InputText 
                        v-model="questionForm.customQuestion" 
                        placeholder="Enter your own question" 
                        fluid 
                    />
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700">Your Answer <span class="text-red-500">*</span></label>
                    <Password 
                        v-model="questionForm.answer" 
                        placeholder="Enter your secret answer" 
                        :feedback="false" 
                        toggleMask 
                        fluid 
                    />
                    <p class="text-xs text-gray-500">
                        <i class="pi pi-info-circle mr-1"></i>
                        Answers are case-insensitive and securely encrypted.
                    </p>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-2">
                    <Button 
                        label="Cancel" 
                        severity="secondary" 
                        outlined
                        @click="showQuestionDialog = false" 
                    />
                    <Button 
                        label="Save" 
                        icon="pi pi-check"
                        :loading="savingQuestion" 
                        @click="saveQuestion()" 
                    />
                </div>
            </template>
        </Dialog>

        <ConfirmDialog />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import Page from '@/stores/Page';
import UrlUtil from '@/utils/UrlUtil';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useToast } from 'vue-toastification';
import { useConfirm } from "primevue/useconfirm";
import { useResponsive } from '@/composables/useResponsive';
import { 
    Button, Dialog, Select, InputText, Password, 
    ConfirmDialog, Avatar, Tag, Divider 
} from 'primevue';

const toast = useToast();
const confirm = useConfirm();
const securityService = useAxiosUtil<any, any>();

const { getResponsiveClasses } = useResponsive();

const recoveryCodes = ref<string[]>([]);
const showQuestionDialog = ref(false);
const savingQuestion = ref(false);
const generatingCodes = ref(false);

const predefinedQuestions = [
    "What was the name of your first pet?",
    "In what city were you born?",
    "What is your mother's maiden name?",
    "What was the name of your first school?",
    "What is the name of your favorite childhood friend?",
    "What was the make of your first car?",
    "What is your favorite book?",
];

const questionForm = reactive({
    question: null as string | null,
    customQuestion: '',
    answer: '',
});

const generateCodes = async () => {
    generatingCodes.value = true;
    await securityService.post('security/settings', { generate_codes: true }).then(() => {
        generatingCodes.value = false;
        if (securityService.request.status === 200) {
            recoveryCodes.value = securityService.request.data.recovery_codes;
            toast.success("Recovery codes generated successfully.");
        } else {
            toast.error(securityService.request.message || "Failed to generate codes.");
        }
    });
};

const confirmRegenerate = () => {
    confirm.require({
        message: 'Regenerating codes will permanently invalidate all existing codes. This action cannot be undone.',
        header: 'Confirm Regeneration',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Regenerate',
        rejectLabel: 'Cancel',
        accept: () => {
            generateCodes();
        }
    });
};

const saveQuestion = async () => {
    if (!questionForm.answer) {
        toast.error("Please provide an answer.");
        return;
    }

    const finalQuestion = questionForm.customQuestion || questionForm.question;
    if (!finalQuestion) {
        toast.error("Please select or enter a question.");
        return;
    }

    savingQuestion.value = true;
    await securityService.post('security/settings', {
        security_question: finalQuestion,
        security_answer: questionForm.answer
    }).then(() => {
        savingQuestion.value = false;
        if (securityService.request.status === 200) {
            toast.success("Security question saved successfully.");
            showQuestionDialog.value = false;
            questionForm.question = null;
            questionForm.customQuestion = '';
            questionForm.answer = '';
            if (Page.user) {
                Page.user.security_question = finalQuestion;
            }
        } else {
            toast.error(securityService.request.message || "Failed to save.");
        }
    });
};

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    toast.info("Code copied to clipboard.");
};

const downloadCodes = () => {
    const content = [
        '═══════════════════════════════════════════════════════',
        '              ACCOUNT RECOVERY CODES',
        '═══════════════════════════════════════════════════════',
        '',
        `Account: ${Page.user?.email}`,
        `Generated: ${new Date().toLocaleString()}`,
        '',
        '───────────────────────────────────────────────────────',
        '',
        ...recoveryCodes.value.map((code, index) => `  ${String(index + 1).padStart(2, '0')}.  ${code}`),
        '',
        '───────────────────────────────────────────────────────',
        '',
        'IMPORTANT:',
        '• Each code can only be used once.',
        '• Store these codes securely.',
        '• Do not share with anyone.',
        '',
        '═══════════════════════════════════════════════════════',
    ].join('\n');
    
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `recovery-codes-${new Date().toISOString().split('T')[0]}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
    toast.success("Codes downloaded.");
};
</script>
