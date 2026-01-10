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
        <div class="p-2">
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
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.new_password"
                id="new_password"
                labelName="New Password*"
                tag="label"
            >
                <Password
                    v-model="form.new_password"
                    :invalid="errors.new_password.length > 0"
                    placeholder="New Password"
                    fluid
                    :toggleMask="true"
                    input-id="new_password"
                />
            </InputForm>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.new_password_confirmation"
                id="new_password_confirmation"
                labelName="Confirm New Password*"
                tag="label"
            >
                <Password
                    v-model="form.new_password_confirmation"
                    :invalid="errors.new_password_confirmation.length > 0"
                    placeholder="Confirm New Password"
                    fluid
                    :feedback="false"
                    :toggleMask="true"
                    input-id="new_password_confirmation"
                />
            </InputForm>
        </div>
        
        <div class="flex justify-center p-2">
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
import { reactive } from "vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";

interface ResetPasswordForm {
    email: string | null;
    current_password: string | null;
    new_password: string | null;
    new_password_confirmation: string | null;
}

interface ResetPasswordErrors {
    email: string[];
    current_password: string[];
    new_password: string[];
    new_password_confirmation: string[];
}

const emit = defineEmits(['code-sent']);
const toast = useToast();
const sendCodeService = useAxiosUtil<any, any>();

const form: ResetPasswordForm = reactive({
    email: null,
    current_password: null,
    new_password: null,
    new_password_confirmation: null,
});

const errors: ResetPasswordErrors = reactive({
    email: [],
    current_password: [],
    new_password: [],
    new_password_confirmation: [],
});

const clearErrors = () => {
    errors.email = [];
    errors.current_password = [];
    errors.new_password = [];
    errors.new_password_confirmation = [];
};

const validateSendCode = () => {
    clearErrors();
    if (!form.email) {
        errors.email.push("Email is required");
    }
    if (!form.current_password) {
        errors.current_password.push("Current password is required");
    }
    if (!form.new_password) {
        errors.new_password.push("New password is required");
    }
    if (!form.new_password_confirmation) {
        errors.new_password_confirmation.push("Password confirmation is required");
    }
    if (form.new_password !== form.new_password_confirmation) {
        errors.new_password_confirmation.push("Passwords do not match");
    }

    const hasErrors = [
        errors.email.length > 0,
        errors.current_password.length > 0,
        errors.new_password.length > 0,
        errors.new_password_confirmation.length > 0,
    ];

    return hasErrors.includes(true) ? false : true;
};

const sendCode = async () => {
    if (!validateSendCode()) {
        toast.error("Please fill in all required fields correctly.");
        return;
    }

    const data = {
        email: form.email,
        current_password: form.current_password,
        new_password: form.new_password,
        new_password_confirmation: form.new_password_confirmation,
    };

    await sendCodeService.post("password/send-code", data).then(() => {
        if (sendCodeService.request.status === 200) {
            toast.success("Verification code sent to your email!");
            emit('code-sent', {
                email: form.email!,
                current_password: form.current_password!,
                new_password: form.new_password!,
                new_password_confirmation: form.new_password_confirmation!,
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
