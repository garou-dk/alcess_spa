<template>
    <div class="py-3 sm:py-4 px-2 sm:px-0">
        <div class="space-y-4 sm:space-y-5">
            <!-- User Info Card -->
            <div class="rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-3 sm:px-4 py-3">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-user text-blue-600 text-lg"></i>
                        <h2 class="text-base sm:text-lg font-bold text-gray-800">Account Information</h2>
                    </div>
                </div>
                <div class="p-3 sm:p-4">
                    <div class="flex items-center gap-3">
                        <Avatar 
                            v-if="Page.user?.image" 
                            :image="UrlUtil.getBaseApiUrl(`profile/${Page.user.image}`)"
                            size="large"
                            shape="circle"
                        />
                        <Avatar 
                            v-else 
                            :label="Page.user?.full_name?.[0]?.toUpperCase()" 
                            size="large"
                            shape="circle"
                            class="!bg-blue-600 !text-white"
                        />
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ Page.user?.full_name }}</h3>
                            <p class="text-xs text-gray-500">{{ Page.user?.email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Question Card -->
            <div class="rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-3 sm:px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="pi pi-question-circle text-blue-600 text-lg"></i>
                            <h2 class="text-base sm:text-lg font-bold text-gray-800">Security Question</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">{{ isEditingQuestion ? 'Editing' : 'View' }}</span>
                            <ToggleSwitch v-model="isEditingQuestion" />
                        </div>
                    </div>
                </div>
                <div class="p-3 sm:p-4">
                    <p class="text-xs text-gray-500 mb-3">Used for account recovery if you forget your email and password.</p>
                    
                    <!-- Status Badge -->
                    <div v-if="Page.user?.security_question && !isEditingQuestion" class="p-3 bg-green-50 rounded-lg border border-green-200 mb-3">
                        <div class="flex items-center gap-2">
                            <i class="pi pi-check-circle text-green-600"></i>
                            <span class="text-sm font-medium text-green-800">Configured</span>
                        </div>
                        <p class="text-xs text-green-700 mt-1 break-words">{{ Page.user.security_question }}</p>
                    </div>
                    <div v-else-if="!isEditingQuestion" class="p-3 bg-yellow-50 rounded-lg border border-yellow-200 mb-3">
                        <div class="flex items-center gap-2">
                            <i class="pi pi-exclamation-circle text-yellow-600"></i>
                            <span class="text-sm font-medium text-yellow-800">Not Configured</span>
                        </div>
                        <p class="text-xs text-yellow-700 mt-1">Toggle edit mode to set up a security question.</p>
                    </div>

                    <!-- Edit Form -->
                    <div v-if="isEditingQuestion" class="space-y-3">
                        <div class="p-2 bg-blue-50 rounded border-l-4 border-blue-500 mb-3">
                            <p class="text-xs text-blue-800">
                                <i class="pi pi-info-circle mr-1"></i>
                                Choose a question only you can answer.
                            </p>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-gray-700">Select a Question</label>
                            <Select 
                                v-model="questionForm.question" 
                                :options="predefinedQuestions" 
                                placeholder="Choose a question" 
                                class="w-full text-sm"
                                panelClass="security-question-panel"
                            />
                        </div>

                        <div class="relative">
                            <Divider align="center">
                                <span class="text-[10px] text-gray-400 bg-white px-2">OR</span>
                            </Divider>
                        </div>
                        
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-gray-700">Custom Question</label>
                            <InputText 
                                v-model="questionForm.customQuestion" 
                                placeholder="Enter your own question" 
                                class="w-full text-sm"
                            />
                        </div>
                        
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-gray-700">Your Answer <span class="text-red-500">*</span></label>
                            <Password 
                                v-model="questionForm.answer" 
                                placeholder="Enter your secret answer" 
                                :feedback="false" 
                                toggleMask 
                                class="w-full"
                                inputClass="text-sm"
                            />
                            <p class="text-[10px] text-gray-500">
                                <i class="pi pi-info-circle mr-1"></i>
                                Answers are case-insensitive and encrypted.
                            </p>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <Button 
                                label="Cancel" 
                                severity="secondary" 
                                outlined
                                size="small"
                                class="flex-1"
                                @click="cancelEditQuestion()" 
                            />
                            <Button 
                                label="Save Question" 
                                icon="pi pi-check"
                                size="small"
                                class="flex-1"
                                :loading="savingQuestion" 
                                @click="saveQuestion()" 
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recovery Codes Card -->
            <div class="rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-3 sm:px-4 py-3">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-key text-purple-600 text-lg"></i>
                        <h2 class="text-base sm:text-lg font-bold text-gray-800">Recovery Codes</h2>
                    </div>
                </div>
                <div class="p-3 sm:p-4">
                    <p class="text-xs text-gray-500 mb-3">One-time use backup codes for emergency account access.</p>

                    <div v-if="recoveryCodes.length > 0">
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div 
                                v-for="(code, index) in recoveryCodes" 
                                :key="index" 
                                class="flex items-center justify-between p-2 bg-gray-50 rounded border border-gray-200"
                            >
                                <code class="text-[10px] font-mono font-semibold text-gray-700 tracking-wide">{{ code }}</code>
                                <Button 
                                    icon="pi pi-copy" 
                                    text 
                                    rounded 
                                    size="small"
                                    class="!w-6 !h-6"
                                    v-tooltip.top="'Copy'"
                                    @click="copyToClipboard(code)" 
                                />
                            </div>
                        </div>
                        <div class="p-2 bg-amber-50 rounded border border-amber-200 mb-3">
                            <p class="text-[10px] text-amber-800">
                                <i class="pi pi-info-circle mr-1"></i>
                                Store these codes securely. Each code can only be used once.
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <Button 
                                label="Download" 
                                icon="pi pi-download" 
                                severity="secondary"
                                outlined
                                size="small"
                                class="flex-1"
                                @click="downloadCodes()" 
                            />
                            <Button 
                                label="Regenerate" 
                                icon="pi pi-refresh" 
                                severity="danger"
                                outlined
                                size="small"
                                class="flex-1"
                                @click="confirmRegenerate()" 
                            />
                        </div>
                    </div>
                    <div v-else class="text-center py-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-2">
                            <i class="pi pi-lock text-gray-400"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-700 mb-1">No Recovery Codes</p>
                        <p class="text-[10px] text-gray-500 mb-3">Generate backup codes for emergency access</p>
                        <Button 
                            label="Generate Codes" 
                            icon="pi pi-key"
                            severity="help"
                            class="w-full"
                            @click="generateCodes()" 
                            :loading="generatingCodes"
                        />
                    </div>
                </div>
            </div>
        </div>



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
import { 
    Button, Select, InputText, Password, 
    ConfirmDialog, Avatar, Divider, ToggleSwitch 
} from 'primevue';

const toast = useToast();
const confirm = useConfirm();
const securityService = useAxiosUtil<any, any>();

const recoveryCodes = ref<string[]>([]);
const isEditingQuestion = ref(false);
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
        message: 'This will invalidate all existing codes. Continue?',
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
            isEditingQuestion.value = false;
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

const cancelEditQuestion = () => {
    isEditingQuestion.value = false;
    questionForm.question = null;
    questionForm.customQuestion = '';
    questionForm.answer = '';
};

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    toast.info("Code copied.");
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

<style scoped>
:deep(.security-question-panel) {
    max-width: min(20rem, calc(100vw - 3rem)) !important;
    width: auto !important;
}

:deep(.security-question-panel .p-select-option) {
    white-space: normal !important;
    word-break: break-word !important;
    font-size: 0.75rem !important;
    padding: 0.625rem 0.75rem !important;
    line-height: 1.3 !important;
}
</style>
