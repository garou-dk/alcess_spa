<template>
    <div class="profile-page">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <Button 
                    icon="pi pi-arrow-left" 
                    text 
                    rounded 
                    class="back-btn"
                    @click="$router.back()" 
                />
                <div class="header-info">
                    <h1 class="page-title">Account Security</h1>
                    <p class="page-subtitle">Manage your security settings and recovery options</p>
                </div>
            </div>
        </div>

        <!-- User Information Card -->
        <div class="content-section">
            <div class="section-card user-card">
                <div class="user-info">
                    <div class="user-avatar">
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
                            class="default-avatar"
                        />
                    </div>
                    <div class="user-details">
                        <h2 class="user-name">{{ Page.user?.full_name }}</h2>
                        <p class="user-email">{{ Page.user?.email }}</p>
                        <Tag :value="Page.user?.role?.role_name" severity="info" class="user-role" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings Grid -->
        <div class="content-section">
            <h2 class="section-title">
                <i class="pi pi-shield"></i>
                Security Settings
            </h2>
            
            <div class="settings-grid">
                <!-- Security Question Card -->
                <div class="section-card">
                    <div class="card-header">
                        <div class="card-icon security-question-icon">
                            <i class="pi pi-question-circle"></i>
                        </div>
                        <div class="card-title-group">
                            <h3 class="card-title">Security Question</h3>
                            <p class="card-description">Account recovery verification method</p>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div v-if="Page.user?.security_question" class="status-box status-active">
                            <div class="status-indicator">
                                <i class="pi pi-check-circle"></i>
                                <span class="status-text">Configured</span>
                            </div>
                            <p class="status-detail">Your security question is active and can be used for account recovery.</p>
                        </div>
                        <div v-else class="status-box status-warning">
                            <div class="status-indicator">
                                <i class="pi pi-exclamation-circle"></i>
                                <span class="status-text">Not Configured</span>
                            </div>
                            <p class="status-detail">Set up a security question to enable account recovery via verification.</p>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <Button 
                            :label="Page.user?.security_question ? 'Update Question' : 'Configure'" 
                            :icon="Page.user?.security_question ? 'pi pi-pencil' : 'pi pi-plus'"
                            :severity="Page.user?.security_question ? 'secondary' : 'primary'"
                            @click="showQuestionDialog = true" 
                        />
                    </div>
                </div>

                <!-- Recovery Codes Card -->
                <div class="section-card">
                    <div class="card-header">
                        <div class="card-icon recovery-codes-icon">
                            <i class="pi pi-key"></i>
                        </div>
                        <div class="card-title-group">
                            <h3 class="card-title">Backup Recovery Codes</h3>
                            <p class="card-description">One-time use emergency access codes</p>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div v-if="recoveryCodes.length > 0" class="codes-container">
                            <div class="codes-grid">
                                <div v-for="(code, index) in recoveryCodes" :key="index" class="code-item">
                                    <span class="code-number">{{ index + 1 }}.</span>
                                    <code class="code-value">{{ code }}</code>
                                    <Button 
                                        icon="pi pi-copy" 
                                        text 
                                        rounded 
                                        size="small"
                                        @click="copyToClipboard(code)" 
                                    />
                                </div>
                            </div>
                            <div class="codes-warning">
                                <i class="pi pi-info-circle"></i>
                                <span>Store these codes securely. Each code can only be used once.</span>
                            </div>
                        </div>
                        <div v-else class="no-codes">
                            <div class="no-codes-icon">
                                <i class="pi pi-lock"></i>
                            </div>
                            <p class="no-codes-title">No Recovery Codes Generated</p>
                            <p class="no-codes-description">Generate backup codes to ensure you can recover your account if you lose access to your primary authentication method.</p>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div v-if="recoveryCodes.length > 0" class="footer-actions">
                            <Button 
                                label="Download" 
                                icon="pi pi-download" 
                                severity="secondary"
                                outlined
                                @click="downloadCodes()" 
                            />
                            <Button 
                                label="Regenerate" 
                                icon="pi pi-refresh" 
                                severity="danger"
                                outlined
                                @click="confirmRegenerate()" 
                            />
                        </div>
                        <Button 
                            v-else
                            label="Generate Recovery Codes" 
                            icon="pi pi-key"
                            severity="primary"
                            @click="generateCodes()" 
                            :loading="generatingCodes"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Question Dialog -->
        <Dialog 
            v-model:visible="showQuestionDialog" 
            modal 
            header="Security Question Configuration" 
            :style="{ width: '32rem' }"
            :breakpoints="{ '575px': '90vw' }"
        >
            <div class="dialog-content">
                <p class="dialog-info">
                    Your security question will be used to verify your identity during account recovery. 
                    Choose a question only you can answer.
                </p>
                
                <div class="form-group">
                    <label class="form-label">Select a Predefined Question</label>
                    <Select 
                        v-model="questionForm.question" 
                        :options="predefinedQuestions" 
                        placeholder="Choose a security question" 
                        fluid 
                    />
                </div>

                <div class="divider-container">
                    <div class="divider-line"></div>
                    <span class="divider-text">OR</span>
                    <div class="divider-line"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Create a Custom Question</label>
                    <InputText 
                        v-model="questionForm.customQuestion" 
                        placeholder="Enter your own security question" 
                        fluid 
                    />
                </div>
                
                <div class="form-group">
                    <label class="form-label">Your Answer <span class="required">*</span></label>
                    <Password 
                        v-model="questionForm.answer" 
                        placeholder="Enter your secret answer" 
                        :feedback="false" 
                        toggleMask 
                        fluid 
                    />
                    <small class="form-hint">
                        <i class="pi pi-info-circle"></i>
                        Answers are case-insensitive and securely encrypted.
                    </small>
                </div>
            </div>

            <template #footer>
                <div class="dialog-footer">
                    <Button 
                        label="Cancel" 
                        severity="secondary" 
                        outlined
                        @click="showQuestionDialog = false" 
                    />
                    <Button 
                        label="Save Configuration" 
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

const toast = useToast();
const confirm = useConfirm();
const securityService = useAxiosUtil<any, any>();

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
        message: 'Regenerating codes will permanently invalidate all existing codes. This action cannot be undone. Do you wish to proceed?',
        header: 'Confirm Code Regeneration',
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
        toast.error("Please provide an answer to your security question.");
        return;
    }

    const finalQuestion = questionForm.customQuestion || questionForm.question;
    if (!finalQuestion) {
        toast.error("Please select or enter a security question.");
        return;
    }

    savingQuestion.value = true;
    await securityService.post('security/settings', {
        security_question: finalQuestion,
        security_answer: questionForm.answer
    }).then(() => {
        savingQuestion.value = false;
        if (securityService.request.status === 200) {
            toast.success("Security question configured successfully.");
            showQuestionDialog.value = false;
            questionForm.question = null;
            questionForm.customQuestion = '';
            questionForm.answer = '';
            if (Page.user) {
                Page.user.security_question = finalQuestion;
            }
        } else {
            toast.error(securityService.request.message || "Failed to save security question.");
        }
    });
};

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    toast.info("Code copied to clipboard.");
};

const downloadCodes = () => {
    const content = [
        '═══════════════════════════════════════════════════════════════',
        '                    ACCOUNT RECOVERY CODES',
        '═══════════════════════════════════════════════════════════════',
        '',
        `Account: ${Page.user?.email}`,
        `Generated: ${new Date().toLocaleString()}`,
        '',
        '───────────────────────────────────────────────────────────────',
        '',
        ...recoveryCodes.value.map((code, index) => `  ${String(index + 1).padStart(2, '0')}.  ${code}`),
        '',
        '───────────────────────────────────────────────────────────────',
        '',
        'IMPORTANT SECURITY NOTICE:',
        '• Each code can only be used once.',
        '• Store these codes in a secure location.',
        '• Do not share these codes with anyone.',
        '• Generating new codes will invalidate these codes.',
        '',
        '═══════════════════════════════════════════════════════════════',
    ].join('\n');
    
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `recovery-codes-${new Date().toISOString().split('T')[0]}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
    toast.success("Recovery codes downloaded.");
};
</script>

<style scoped>
.profile-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1.5rem;
}

/* Page Header */
.page-header {
    background: white;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.back-btn {
    color: #64748b !important;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.2;
}

.page-subtitle {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0.25rem 0 0 0;
}

/* Content Sections */
.content-section {
    margin-bottom: 1.5rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 1rem 0;
}

.section-title i {
    color: #0ea5e9;
}

/* Section Cards */
.section-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

/* User Card */
.user-card {
    padding: 1.5rem;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.default-avatar {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
    color: white !important;
    font-weight: 600 !important;
}

.user-details {
    flex: 1;
}

.user-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 0.25rem 0;
}

.user-email {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0 0 0.5rem 0;
}

.user-role {
    font-size: 0.75rem;
}

/* Settings Grid */
.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 1.25rem;
}

@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
}

/* Card Header */
.card-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.card-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.card-icon i {
    font-size: 1.25rem;
    color: white;
}

.security-question-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.recovery-codes-icon {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
}

.card-title-group {
    flex: 1;
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 0.25rem 0;
}

.card-description {
    font-size: 0.8125rem;
    color: #64748b;
    margin: 0;
}

/* Card Body */
.card-body {
    padding: 1.25rem 1.5rem;
}

/* Status Box */
.status-box {
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid;
}

.status-active {
    background: #f0fdf4;
    border-color: #bbf7d0;
}

.status-warning {
    background: #fefce8;
    border-color: #fef08a;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.status-active .status-indicator i {
    color: #16a34a;
}

.status-warning .status-indicator i {
    color: #ca8a04;
}

.status-text {
    font-size: 0.875rem;
    font-weight: 600;
}

.status-active .status-text {
    color: #166534;
}

.status-warning .status-text {
    color: #854d0e;
}

.status-detail {
    font-size: 0.8125rem;
    margin: 0;
}

.status-active .status-detail {
    color: #15803d;
}

.status-warning .status-detail {
    color: #a16207;
}

/* Codes Container */
.codes-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.codes-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
}

@media (max-width: 480px) {
    .codes-grid {
        grid-template-columns: 1fr;
    }
}

.code-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 0.75rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
}

.code-number {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 500;
    min-width: 1.25rem;
}

.code-value {
    flex: 1;
    font-family: 'SF Mono', 'Consolas', monospace;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #334155;
    letter-spacing: 0.05em;
}

.codes-warning {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #fef3c7;
    border: 1px solid #fcd34d;
    border-radius: 6px;
    font-size: 0.8125rem;
    color: #92400e;
}

.codes-warning i {
    color: #d97706;
}

/* No Codes State */
.no-codes {
    text-align: center;
    padding: 1.5rem 1rem;
}

.no-codes-icon {
    width: 64px;
    height: 64px;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.no-codes-icon i {
    font-size: 1.5rem;
    color: #94a3b8;
}

.no-codes-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #334155;
    margin: 0 0 0.5rem 0;
}

.no-codes-description {
    font-size: 0.8125rem;
    color: #64748b;
    margin: 0;
    max-width: 320px;
    margin: 0 auto;
    line-height: 1.5;
}

/* Card Footer */
.card-footer {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
}

.footer-actions {
    display: flex;
    gap: 0.75rem;
}

/* Dialog Styles */
.dialog-content {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.dialog-info {
    font-size: 0.875rem;
    color: #64748b;
    line-height: 1.6;
    margin: 0;
    padding: 0.75rem 1rem;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 3px solid #0ea5e9;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #334155;
}

.required {
    color: #ef4444;
}

.form-hint {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    color: #64748b;
}

.form-hint i {
    font-size: 0.75rem;
}

.divider-container {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.divider-line {
    flex: 1;
    height: 1px;
    background: #e2e8f0;
}

.divider-text {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 500;
}

.dialog-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

/* Responsive */
@media (max-width: 640px) {
    .profile-page {
        padding: 1rem;
    }
    
    .page-header {
        padding: 1rem;
    }
    
    .page-title {
        font-size: 1.25rem;
    }
    
    .user-info {
        flex-direction: column;
        text-align: center;
    }
    
    .card-header {
        padding: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .card-footer {
        padding: 0.875rem 1rem;
    }
    
    .footer-actions {
        flex-direction: column;
    }
    
    .footer-actions :deep(.p-button) {
        width: 100%;
    }
}
</style>
