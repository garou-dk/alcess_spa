<template>
    <div :class="responsive.getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-3 px-3',
        tablet: 'min-h-screen bg-gray-50 py-4 px-4',
        desktop: 'min-h-screen bg-gray-50 py-6 px-6'
    })">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Account Settings</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sidebar / Tabs -->
                <div class="md:col-span-1 space-y-2">
                    <button 
                        @click="activeTab = 'profile'"
                        :class="[
                            'w-full text-left px-4 py-3 rounded-lg transition-colors flex items-center gap-3',
                            activeTab === 'profile' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100'
                        ]"
                    >
                        <i class="pi pi-user"></i>
                        <span>Basic Information</span>
                    </button>
                    <button 
                        @click="activeTab = 'security'"
                        :class="[
                            'w-full text-left px-4 py-3 rounded-lg transition-colors flex items-center gap-3',
                            activeTab === 'security' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100'
                        ]"
                    >
                        <i class="pi pi-shield"></i>
                        <span>Security Settings</span>
                    </button>
                </div>

                <!-- Content Area -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Profile Tab -->
                    <div v-if="activeTab === 'profile'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h2>
                            <div class="flex items-center gap-6 mb-8">
                                <div class="relative group">
                                    <Avatar
                                        v-if="Page.user?.image"
                                        :image="UrlUtil.getBaseApiUrl(`profile/${Page.user.image}`)"
                                        shape="circle"
                                        style="width: 100px; height: 100px;"
                                        class="border-4 border-gray-100"
                                    />
                                    <Avatar
                                        v-else
                                        :label="Page.user?.full_name[0]"
                                        shape="circle"
                                        style="width: 100px; height: 100px;"
                                        class="bg-blue-100 text-blue-600 text-3xl font-bold border-4 border-gray-100"
                                    />
                                    <button 
                                        @click="showChangeProfileForm = true"
                                        class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full shadow-lg hover:bg-blue-700 transition-colors"
                                    >
                                        <i class="pi pi-camera"></i>
                                    </button>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900">{{ Page.user?.full_name }}</h3>
                                    <p class="text-gray-500">{{ Page.user?.email }}</p>
                                    <div class="mt-2">
                                        <Button 
                                            label="Change Name" 
                                            icon="pi pi-pencil" 
                                            size="small" 
                                            text 
                                            @click="showChangeNameForm = true"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4 border-t border-gray-100 pt-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-500">Email Address</span>
                                    <span class="text-gray-900">{{ Page.user?.email }}</span>
                                    <span v-if="Page.user?.email_verified_at" class="text-xs text-green-600 flex items-center gap-1 mt-1">
                                        <i class="pi pi-check-circle"></i>
                                        Verified
                                    </span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-500">Account Role</span>
                                    <span class="text-gray-900">{{ Page.user?.role?.role_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div v-if="activeTab === 'security'" class="space-y-6">
                        <!-- Password Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="pi pi-lock text-blue-600"></i>
                                Password
                            </h2>
                            <p class="text-gray-600 text-sm mb-4">Regularly changing your password helps keep your account secure.</p>
                            <Button 
                                label="Change Password" 
                                icon="pi pi-key" 
                                severity="secondary" 
                                @click="showChangePasswordForm = true"
                            />
                        </div>

                        <!-- Security Questions Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="pi pi-question-circle text-blue-600"></i>
                                Security Questions
                            </h2>
                            <p class="text-gray-600 text-sm mb-4">Add an extra layer of security by setting up a security question. This can be used to recover your account.</p>
                            
                            <div v-if="Page.user?.security_question" class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-4">
                                <div class="flex items-start gap-3">
                                    <i class="pi pi-check-circle text-blue-600 mt-1"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-blue-900">Security Question Set</p>
                                        <p class="text-sm text-blue-800">{{ Page.user.security_question }}</p>
                                    </div>
                                </div>
                            </div>

                            <Button 
                                :label="Page.user?.security_question ? 'Update Security Question' : 'Setup Security Question'" 
                                icon="pi pi-shield" 
                                @click="showSecurityQuestionForm = true"
                            />
                        </div>

                        <!-- Recovery Key Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="pi pi-id-card text-blue-600"></i>
                                Recovery Code
                            </h2>
                            <p class="text-gray-600 text-sm mb-4">The recovery code is a unique identifier you can use to reset your password if you lose access to your email.</p>
                            
                            <div class="flex flex-col gap-4">
                                <div class="bg-amber-50 border border-amber-100 rounded-lg p-4">
                                    <p class="text-sm text-amber-800">Regenerating the code will invalidate your previous code. The new code will be sent to your email.</p>
                                </div>
                                <Button 
                                    label="Regenerate & Email Code" 
                                    icon="pi pi-refresh" 
                                    severity="warning" 
                                    @click="regenerateRecoveryKey"
                                    :loading="isRegenerating"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <Dialog v-model:visible="showChangeNameForm" modal header="Change Name" :style="{ width: '28rem' }" :breakpoints="{ '575px': '90vw' }">
            <ChangeNameForm v-if="Page.user" :data="Page.user" @cb="onProfileUpdate" />
        </Dialog>

        <Dialog v-model:visible="showChangePasswordForm" modal header="Change Password" :style="{ width: '28rem' }" :breakpoints="{ '575px': '90vw' }">
            <ChangePasswordForm v-if="Page.user" :data="Page.user" @cb="showChangePasswordForm = false" />
        </Dialog>

        <Dialog v-model:visible="showChangeProfileForm" modal header="Change Profile Image" :style="{ width: '28rem' }" :breakpoints="{ '575px': '90vw' }">
            <ChangeProfileForm v-if="Page.user" :data="Page.user" @cb="onProfileUpdate" />
        </Dialog>

        <Dialog v-model:visible="showSecurityQuestionForm" modal header="Security Question" :style="{ width: '32rem' }" :breakpoints="{ '575px': '90vw' }">
            <div class="p-2 space-y-4">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-700">Select a question</label>
                    <Select 
                        v-model="securityForm.security_question" 
                        :options="questions" 
                        placeholder="Choose a question" 
                        class="w-full"
                    />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-700">Your answer</label>
                    <InputText 
                        v-model="securityForm.security_answer" 
                        type="password" 
                        placeholder="Type your answer here..." 
                        class="w-full"
                    />
                    <small class="text-gray-500">Answers are case-insensitive and stored securely (hashed).</small>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <Button label="Cancel" text @click="showSecurityQuestionForm = false" />
                    <Button label="Save Settings" @click="saveSecuritySettings" :loading="isSavingSecurity" />
                </div>
            </div>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import Page from '@/stores/Page';
import { useResponsive } from '@/composables/useResponsive';
import UrlUtil from '@/utils/UrlUtil';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useToast } from 'vue-toastification';
import ChangeNameForm from '@/components/forms/ChangeNameForm.vue';
import ChangePasswordForm from '@/components/forms/ChangePasswordForm.vue';
import ChangeProfileForm from '@/components/forms/ChangeProfileForm.vue';

const responsive = useResponsive();
const toast = useToast();
const axios = useAxiosUtil();

const activeTab = ref('profile');
const showChangeNameForm = ref(false);
const showChangePasswordForm = ref(false);
const showChangeProfileForm = ref(false);
const showSecurityQuestionForm = ref(false);

const isRegenerating = ref(false);
const isSavingSecurity = ref(false);

const securityForm = reactive({
    security_question: Page.user?.security_question || '',
    security_answer: ''
});

const questions = ref<string[]>([]);

const loadQuestions = async () => {
    try {
        const response = await axios.axios.get('/api/user/security-questions');
        questions.value = response.data.data;
    } catch (e) {
        toast.error('Failed to load security questions');
    }
};

const onProfileUpdate = (updatedUser: any) => {
    if (updatedUser) {
        Page.user = updatedUser;
    }
    showChangeNameForm.value = false;
    showChangeProfileForm.value = false;
};

const saveSecuritySettings = async () => {
    if (!securityForm.security_question || !securityForm.security_answer) {
        toast.error('Please fill in both the question and your answer');
        return;
    }

    isSavingSecurity.value = true;
    try {
        const response = await axios.axios.patch(`/api/user/update-security-settings/${Page.user?.user_id}`, {
            security_question: securityForm.security_question,
            security_answer: securityForm.security_answer
        });
        
        if (response.data.success) {
            toast.success('Security settings updated successfully');
            if (Page.user) {
                Page.user.security_question = securityForm.security_question;
            }
            showSecurityQuestionForm.value = false;
            securityForm.security_answer = '';
        }
    } catch (e) {
        toast.error('Failed to update security settings');
    } finally {
        isSavingSecurity.value = false;
    }
};

const regenerateRecoveryKey = async () => {
    isRegenerating.value = true;
    try {
        const response = await axios.axios.patch(`/api/user/update-security-settings/${Page.user?.user_id}`, {
            regenerate_recovery_key: true
        });
        
        if (response.data.success) {
            toast.success('New recovery code has been generated and sent to your email');
        }
    } catch (e) {
        toast.error('Failed to regenerate recovery code');
    } finally {
        isRegenerating.value = false;
    }
};

onMounted(() => {
    loadQuestions();
});
</script>
