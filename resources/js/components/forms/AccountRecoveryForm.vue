<template>
    <div class="account-recovery-container">
        <!-- Step Selection -->
        <div v-if="step === 'choice'" class="space-y-4">
            <p class="text-sm text-gray-600 mb-6">
                If you lost access to your email or completely forgot your credentials, try one of these recovery methods:
            </p>
            
            <Button 
                label="Use Backup Code" 
                icon="pi pi-shield" 
                fluid 
                severity="help" 
                outlined 
                class="justify-start! p-4!"
                @click="step = 'code'"
            />
            
            <Button 
                label="Answer Security Question" 
                icon="pi pi-question-circle" 
                fluid 
                severity="info" 
                outlined 
                class="justify-start! p-4!"
                @click="step = 'question_start'"
            />

            <Button 
                label="Back to Reset" 
                variant="link" 
                fluid
                class="text-xs! text-gray-400!"
                @click="$emit('back')"
            />
        </div>

        <!-- recovery by code -->
        <div v-else-if="step === 'code'" class="space-y-4">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="pi pi-shield text-purple-600" />
                Backup Code
            </h3>
            <p class="text-xs text-gray-500">Enter one of your 10-character recovery codes.</p>
            
            <div class="p-2">
                <InputText 
                    v-model="recoveryForm.code" 
                    placeholder="E.g. ABC123DEF4" 
                    class="w-full text-center font-mono tracking-widest uppercase"
                    maxlength="10"
                />
            </div>

            <Button 
                label="Verify & Recover" 
                fluid 
                :loading="recovering" 
                @click="recoverByCode()" 
            />
            
            <Button label="Back" variant="link" class="text-xs! text-gray-400!" fluid @click="step = 'choice'" />
        </div>

        <!-- recovery by question - start -->
        <div v-else-if="step === 'question_start'" class="space-y-4">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="pi pi-question-circle text-blue-600" />
                Security Question
            </h3>
            <p class="text-xs text-gray-500">Enter your full name or email to find your security question.</p>
            
            <div class="p-2">
                <InputText v-model="recoveryForm.identifier" placeholder="Email or Full Name" fluid />
            </div>

            <Button label="Find Question" fluid :loading="recovering" @click="findQuestion()" />
            <Button label="Back" variant="link" class="text-xs! text-gray-400!" fluid @click="step = 'choice'" />
        </div>

        <!-- recovery by question - answer -->
        <div v-else-if="step === 'question_answer'" class="space-y-4">
            <h3 class="text-lg font-semibold">Security Question</h3>
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-100 italic text-blue-800">
                "{{ foundQuestion }}"
            </div>
            
            <div class="p-2">
                <InputText v-model="recoveryForm.answer" placeholder="Your Answer" fluid />
            </div>

            <Button label="Verify Answer" fluid :loading="recovering" @click="verifyAnswer()" />
        </div>

        <!-- Final Step: Success Info -->
        <div v-else-if="step === 'success'" class="text-center space-y-6">
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="pi pi-check text-2xl text-green-600 font-bold" />
                </div>
            </div>
            <h3 class="text-xl font-bold text-green-700">Account Found!</h3>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Your registered email is:</p>
                <p class="text-lg font-bold text-gray-800 tracking-wide">{{ foundEmail }}</p>
            </div>
            <p class="text-xs text-gray-600">
                You can now proceed to reset your password using the "Forgot Password" flow with this email.
            </p>
            <Button label="Proceed to Password Reset" fluid severity="success" @click="$emit('recovered', foundEmail)" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useToast } from 'vue-toastification';

const emit = defineEmits(['back', 'recovered']);
const toast = useToast();
const recoveryService = useAxiosUtil<any, any>();

const step = ref<'choice' | 'code' | 'question_start' | 'question_answer' | 'success'>('choice');
const recovering = ref(false);
const foundQuestion = ref('');
const foundEmail = ref('');

const recoveryForm = reactive({
    code: '',
    identifier: '',
    answer: '',
});

const recoverByCode = async () => {
    if (!recoveryForm.code) return;
    recovering.value = true;
    await recoveryService.post('recovery/by-code', { recovery_code: recoveryForm.code.toUpperCase() }).then(() => {
        recovering.value = false;
        if (recoveryService.request.status === 200) {
            foundEmail.value = recoveryService.request.data.email;
            step.value = 'success';
        } else {
            toast.error(recoveryService.request.message);
        }
    });
};

const findQuestion = async () => {
    if (!recoveryForm.identifier) return;
    recovering.value = true;
    await recoveryService.post('recovery/question', { identifier: recoveryForm.identifier }).then(() => {
        recovering.value = false;
        if (recoveryService.request.status === 200) {
            foundQuestion.value = recoveryService.request.data.security_question;
            step.value = 'question_answer';
        } else {
            toast.error(recoveryService.request.message);
        }
    });
};

const verifyAnswer = async () => {
    if (!recoveryForm.answer) return;
    recovering.value = true;
    await recoveryService.post('recovery/verify-answer', { 
        identifier: recoveryForm.identifier, 
        security_answer: recoveryForm.answer 
    }).then(() => {
        recovering.value = false;
        if (recoveryService.request.status === 200) {
            foundEmail.value = recoveryService.request.data.email;
            step.value = 'success';
        } else {
            toast.error(recoveryService.request.message);
        }
    });
};
</script>
