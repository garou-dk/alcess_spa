<template>
    <form @submit.prevent="sendCode()">
        <div class="p-2">
            <InputForm
                :errors="errors.email"
                id="email"
                labelName="Email*"
                tag="label"
            >
                <InputText
                    type="email"
                    v-model="form.email"
                    :invalid="errors.email.length > 0"
                    placeholder="Email"
                    fluid
                    id="email"
                    autocomplete="off"
                />
            </InputForm>
        </div>
        
        <div v-if="!forgotMode" class="p-2">
            <InputForm
                :errors="errors.current_password"
                id="current_password"
                labelName="Current Password*"
                tag="label"
            >
                <Password
                    v-model="form.current_password"
                    :invalid="errors.current_password.length > 0"
                    placeholder="Current Password"
                    fluid
                    :feedback="false"
                    :toggleMask="true"
                    input-id="current_password"
                />
            </InputForm>
            <div class="flex justify-end mt-1">
                <Button 
                    type="button" 
                    label="Forgot password? Try other way" 
                    variant="link" 
                    class="p-0! text-xs! text-blue-600! font-normal!" 
                    @click="forgotMode = true" 
                />
            </div>
        </div>
        <div v-else class="p-2 space-y-4">
            <div class="flex items-center gap-2 p-3 mb-2 rounded-lg bg-blue-50 text-blue-700 text-xs">
                <i class="pi pi-info-circle" />
                <span>Choose a method to verify your identity and reset your password.</span>
            </div>
            
            <div class="space-y-2">
                <Button 
                    type="button" 
                    label="Send Code to Email" 
                    icon="pi pi-envelope" 
                    fluid 
                    @click="chooseMethod('email')" 
                    outlined
                    pt:root:class="!border-blue-200 !text-blue-600 hover:!bg-blue-50"
                />
                <Button 
                    type="button" 
                    label="Use Recovery Code" 
                    icon="pi pi-id-card" 
                    fluid 
                    @click="chooseMethod('recovery_key')" 
                    outlined
                    pt:root:class="!border-blue-200 !text-blue-600 hover:!bg-blue-50"
                />
                <Button 
                    type="button" 
                    label="Answer Security Question" 
                    icon="pi pi-question-circle" 
                    fluid 
                    @click="chooseMethod('security_question')" 
                    outlined
                    pt:root:class="!border-blue-200 !text-blue-600 hover:!bg-blue-50"
                />
            </div>

            <div class="flex justify-center">
                <Button 
                    type="button" 
                    label="Back to standard reset" 
                    variant="link" 
                    class="p-0! text-xs! text-gray-500! font-normal!" 
                    @click="forgotMode = false" 
                />
            </div>
        </div>

        <div v-if="!forgotMode" class="flex justify-center p-2 mt-4">
            <Button
                type="submit"
                label="Send Code"
                fluid
                :loading="sendCodeService.request.loading"
                pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!"
            />
        </div>
    </form>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";

interface ResetPasswordForm {
    email: string | null;
    current_password: string | null;
}

interface ResetPasswordErrors {
    email: string[];
    current_password: string[];
}

const emit = defineEmits(['code-sent']);
const toast = useToast();
const sendCodeService = useAxiosUtil<any, any>();
const forgotMode = ref(false);

const form: ResetPasswordForm = reactive({
    email: null,
    current_password: null,
});

const errors: ResetPasswordErrors = reactive({
    email: [],
    current_password: [],
});

const clearErrors = () => {
    errors.email = [];
    errors.current_password = [];
};

const validateSendCode = () => {
    clearErrors();
    if (!form.email) {
        errors.email.push("Email is required");
    }
    if (!forgotMode.value && !form.current_password) {
        errors.current_password.push("Current password is required");
    }

    const hasErrors = [
        errors.email.length > 0,
        !forgotMode.value && errors.current_password.length > 0,
    ];

    return hasErrors.includes(true) ? false : true;
};

const chooseMethod = async (method: 'email' | 'recovery_key' | 'security_question') => {
    clearErrors();
    if (!form.email) {
        errors.email.push("Email is required to proceed");
        toast.error("Please enter your email first");
        return;
    }

    if (method === 'email') {
        await sendCode();
        return;
    }

    // For other methods, we just emit and move to the next screen
    emit('code-sent', {
        email: form.email!,
        current_password: null,
        method: method
    });
};

const sendCode = async () => {
    if (!validateSendCode()) {
        toast.error("Please fill in all required fields correctly.");
        return;
    }

    const data = {
        email: form.email,
        current_password: forgotMode.value ? null : form.current_password,
    };

    await sendCodeService.post("password/send-code", data).then(() => {
        if (sendCodeService.request.status === 200) {
            toast.success("Verification code sent to your email!");
            emit('code-sent', {
                email: form.email!,
                current_password: data.current_password,
                method: 'email'
            });
        } else {
            toast.error(sendCodeService.request.message ?? "Failed to send code.");
            if (sendCodeService.request.errors) {
                Object.keys(sendCodeService.request.errors).forEach((key) => {
                    errors[key] = sendCodeService.request.errors[key];
                });
            }
        }
    });
};
</script>
