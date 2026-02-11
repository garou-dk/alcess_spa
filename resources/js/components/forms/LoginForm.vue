<template>
    <form @submit.prevent="handleSubmit()">
        <!-- Unverified Account Notice -->
        <transition name="notice-slide">
            <div v-if="showUnverifiedNotice" class="mx-2 mb-4 rounded-xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center">
                            <i class="pi pi-envelope text-amber-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-amber-800 mb-1">Account Not Verified</h4>
                        <p class="text-xs text-amber-700 leading-relaxed">
                            {{ unverifiedMessage }}
                        </p>
                    </div>
                    <button 
                        type="button" 
                        @click="showUnverifiedNotice = false" 
                        class="flex-shrink-0 text-amber-400 hover:text-amber-600 transition-colors"
                    >
                        <i class="pi pi-times text-sm"></i>
                    </button>
                </div>
            </div>
        </transition>

        <div class="p-2">
            <InputForm
                :errors="errors.email"
                id="email"
                labelName="Email"
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
                :errors="errors.password"
                id="password"
                labelName="Password"
                tag="label"
            >
                <Password
                    v-model="form.password"
                    :invalid="errors.password.length > 0"
                    placeholder="Password"
                    fluid
                    :feedback="false"
                    :toggleMask="true"
                    input-id="password"
                />
            </InputForm>
        </div>
        <div class="flex justify-center p-2">
            <Button
                type="submit"
                label="Login"
                :loading="authService.request.loading"
                fluid
                pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!"
            />
        </div>
        <slot name="footer" />
    </form>
</template>
<script setup lang="ts">
import { getStoreRoles, RoleEnum } from "@/enums/RoleEnum";
import {
    LoginFormErrorInterface,
    LoginFormInterface,
} from "@/interfaces/LoginFormInterface";
import { UserInterface } from "@/interfaces/UserInterface";
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";

interface Props {
    admin?: boolean;
}

const router = useRouter();
const props = defineProps<Props>();
const toast = useToast();
const authService = useAxiosUtil<LoginFormInterface, UserInterface>();
const form: LoginFormInterface = reactive({
    email: null,
    password: null,
});
const errors: LoginFormErrorInterface = reactive({
    email: [],
    password: [],
});

const showUnverifiedNotice = ref(false);
const unverifiedMessage = ref('');

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearErrors();
    if (!form.email) {
        errors.email.push("Email is required");
    }
    if (!form.password) {
        errors.password.push("Password is required");
    }

    const hasErrors = [errors.email.length > 0, errors.password.length > 0];

    return hasErrors.includes(true) ? false : form;
};

const emit = defineEmits(['success']);

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        // Reset notice on new attempt
        showUnverifiedNotice.value = false;

        await authService
            .post(props.admin ? "admin/login" : "login", data)
            .then(async () => {
                if (
                    authService.request.status === 200 &&
                    authService.request.data
                ) {
                    Page.user = authService.request.data;
                    
                    // Navigate first
                    if (
                        getStoreRoles().includes(
                            Page.user.role.role_name as RoleEnum,
                        )
                    ) {
                        await router.push({ name: "admin.app" });
                    } else {
                        await router.push({ name: "customer.home.index" });
                    }
                    
                    // Then close modal after navigation completes
                    emit('success');
                } else {
                    const message = authService.request.message ?? "Please try again.";
                    
                    // Check if it's an email verification issue
                    if (
                        authService.request.status === 401 &&
                        message.toLowerCase().includes('verify')
                    ) {
                        showUnverifiedNotice.value = true;
                        unverifiedMessage.value = message;
                    } else {
                        toast.error(message);
                    }
                    
                    if (authService.request.errors) {
                        Object.keys(authService.request.errors).forEach(
                            (key) => {
                                errors[key] = authService.request.errors[key];
                            },
                        );
                    }
                }
            });
    } else {
        toast.error("Please fill in the required fields.");
    }
};
</script>
<style scoped>
.notice-slide-enter-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.notice-slide-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.notice-slide-enter-from {
    opacity: 0;
    transform: translateY(-12px);
    max-height: 0;
}
.notice-slide-enter-to {
    opacity: 1;
    transform: translateY(0);
    max-height: 200px;
}
.notice-slide-leave-from {
    opacity: 1;
    transform: translateY(0);
    max-height: 200px;
}
.notice-slide-leave-to {
    opacity: 0;
    transform: translateY(-8px);
    max-height: 0;
}
</style>
