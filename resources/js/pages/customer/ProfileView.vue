<template>
    <div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Account Security</h1>
            <p class="text-gray-600">Manage your recovery options and security settings.</p>
        </div>

        <div class="grid grid-cols-1 gap-8">
            <!-- Security Question Section -->
            <Card class="shadow-sm border border-gray-200 overflow-hidden">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="pi pi-question-circle text-blue-600 text-xl" />
                        </div>
                        <span class="text-xl font-semibold">Security Question</span>
                    </div>
                </template>
                <template #content>
                    <p class="text-gray-600 mb-6">
                        Security questions help you recover your account if you forget your email and password.
                    </p>
                    
                    <div class="space-y-4">
                        <div v-if="Page.user?.security_question" class="p-4 bg-green-50 rounded-lg border border-green-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="pi pi-check-circle text-green-600" />
                                <div>
                                    <p class="text-sm font-medium text-green-800">Security question is active</p>
                                    <p class="text-xs text-green-600">You can use this to recover your account.</p>
                                </div>
                            </div>
                            <Button label="Change" icon="pi pi-pencil" text size="small" @click="showQuestionDialog = true" />
                        </div>
                        <div v-else class="p-4 bg-orange-50 rounded-lg border border-orange-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="pi pi-exclamation-triangle text-orange-600" />
                                <div>
                                    <p class="text-sm font-medium text-orange-800">Security question not set</p>
                                    <p class="text-xs text-orange-600">Setting this up is highly recommended.</p>
                                </div>
                            </div>
                            <Button label="Setup Now" icon="pi pi-plus" severity="warning" size="small" @click="showQuestionDialog = true" />
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Backup Codes Section -->
            <Card class="shadow-sm border border-gray-200 overflow-hidden">
                <template #title>
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="pi pi-shield text-purple-600 text-xl" />
                        </div>
                        <span class="text-xl font-semibold">Backup Recovery Codes</span>
                    </div>
                </template>
                <template #content>
                    <p class="text-gray-600 mb-6">
                        Recovery codes are a one-time use code that can restore access to your account if you lose everything else. Keep them in a safe place.
                    </p>

                    <div class="flex flex-col items-center justify-center p-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                        <div v-if="recoveryCodes.length > 0" class="w-full">
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div v-for="(code, index) in recoveryCodes" :key="index" class="bg-white p-3 rounded border border-gray-200 flex items-center justify-between">
                                    <span class="font-mono text-sm font-bold tracking-widest text-gray-700">{{ code }}</span>
                                    <i class="pi pi-copy text-gray-400 cursor-pointer hover:text-blue-500" @click="copyToClipboard(code)" />
                                </div>
                            </div>
                            <div class="flex gap-3 justify-center">
                                <Button label="Download PDF" icon="pi pi-download" outlined @click="downloadCodes()" />
                                <Button label="Generate New Codes" icon="pi pi-refresh" severity="danger" outlined @click="confirmRegenerate()" />
                            </div>
                        </div>
                        <div v-else class="text-center">
                            <i class="pi pi-lock text-4xl text-gray-300 mb-4" />
                            <p class="text-gray-500 mb-6 italic">No active backup codes visible.</p>
                            <Button label="Generate Backup Codes" icon="pi pi-key" severity="help" @click="generateCodes()" />
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Security Question Dialog -->
        <Dialog v-model:visible="showQuestionDialog" modal header="Security Question Setup" :style="{ width: '30rem' }">
            <div class="space-y-6 pt-4">
                <div class="flex flex-col gap-2">
                    <label for="question" class="font-semibold">Select a Question</label>
                    <Select v-model="questionForm.question" :options="predefinedQuestions" placeholder="Select a question" fluid />
                    <div class="mt-2 text-xs text-gray-500">Or write your own:</div>
                    <InputText v-model="questionForm.customQuestion" placeholder="Enter your own question" fluid />
                </div>
                
                <div class="flex flex-col gap-2">
                    <label for="answer" class="font-semibold">Your Answer</label>
                    <Password v-model="questionForm.answer" placeholder="Your secret answer" :feedback="false" toggleMask fluid />
                    <p class="text-xs text-gray-500 italic">Answers are case-insensitive and hashed for security.</p>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <Button type="button" label="Cancel" severity="secondary" @click="showQuestionDialog = false" />
                    <Button type="button" label="Save Security Question" :loading="savingQuestion" @click="saveQuestion()" />
                </div>
            </div>
        </Dialog>

        <ConfirmDialog />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import Page from '@/stores/Page';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useToast } from 'vue-toastification';
import { useConfirm } from "primevue/useconfirm";

const toast = useToast();
const confirm = useConfirm();
const securityService = useAxiosUtil<any, any>();

const recoveryCodes = ref<string[]>([]);
const showQuestionDialog = ref(false);
const savingQuestion = ref(false);

const predefinedQuestions = [
    "What was the name of your first pet?",
    "What city were you born in?",
    "What was your maternal grandmother's maiden name?",
    "What was the name of your elementary school?",
    "What is your favorite book?",
];

const questionForm = reactive({
    question: null,
    customQuestion: '',
    answer: '',
});

const generateCodes = async () => {
    await securityService.post('security/settings', { generate_codes: true }).then(() => {
        if (securityService.request.status === 200) {
            recoveryCodes.value = securityService.request.data.recovery_codes;
            toast.success("New recovery codes generated!");
        }
    });
};

const confirmRegenerate = () => {
    confirm.require({
        message: 'Regenerating codes will invalidate your old ones. Are you sure?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
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
            toast.success("Security question updated successfully!");
            showQuestionDialog.value = false;
            // Update Page.user store
            if (Page.user) {
                Page.user.security_question = finalQuestion;
            }
        }
    });
};

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    toast.info("Code copied to clipboard", { timeout: 1000 });
};

const downloadCodes = () => {
    const text = `Alcess SPA - Account Recovery Codes\nGenerated on: ${new Date().toLocaleString()}\n\n${recoveryCodes.value.join('\n')}\n\nKeep these codes in a safe place.`;
    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'alcess-recovery-codes.txt';
    a.click();
    window.URL.revokeObjectURL(url);
};
</script>

<style scoped>
:deep(.p-card) {
    border-radius: 1rem;
}
:deep(.p-card-title) {
    border-bottom: 1px solid #f3f4f6;
    padding-bottom: 1rem;
}
</style>
