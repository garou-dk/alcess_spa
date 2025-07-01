<template>
    <form @submit.prevent="handleSubmit()">
        <div class="p-2">
            <InputForm
                :errors="[]"
                id="current_user"
                labelName="Change password for"
                tag="label"
            >
                <InputText
                    :model-value="props.data.full_name"
                    id="current_user"
                    readonly
                />
            </InputForm>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.password"
                id="password"
                labelName="Password*"
                tag="label"
            >
                <Password
                    v-model="form.password"
                    :invalid="errors.password.length > 0"
                    placeholder="Password"
                    fluid
                    input-id="password"
                    toggle-mask
                    :medium-regex="mediumRegex"
                    :strong-regex="strongRegex"
                >
                    <template #footer>
                        <div class="flex flex-col gap-1">
                            <span
                                v-if="!hasLowercase"
                                class="text-sm text-red-500"
                            >
                                At least one lowercase letter
                            </span>
                            <span
                                v-if="!hasUppercase"
                                class="text-sm text-red-500"
                            >
                                At least one uppercase letter
                            </span>
                            <span
                                v-if="!hasNumber"
                                class="text-sm text-red-500"
                            >
                                At least one number
                            </span>
                            <span
                                v-if="!hasSymbol"
                                class="text-sm text-red-500"
                            >
                                At least one special character
                            </span>
                            <span
                                v-if="!hasEightLength"
                                class="text-sm text-red-500"
                            >
                                At least 8 characters
                            </span>
                        </div>
                    </template>
                </Password>
            </InputForm>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.password_confirmation"
                id="password_confirmation"
                labelName="Confirm Password*"
                tag="label"
            >
                <Password
                    v-model="form.password_confirmation"
                    :invalid="errors.password_confirmation.length > 0"
                    placeholder="Confirm Password"
                    fluid
                    input-id="password_confirmation"
                    toggle-mask
                    :medium-regex="mediumRegex"
                    :strong-regex="strongRegex"
                >
                    <template #footer>
                        <span
                            v-if="!mustSamePassword"
                            class="text-sm text-red-500"
                        >
                            Password does not match
                        </span>
                    </template>
                </Password>
            </InputForm>
        </div>
        <div class="flex justify-center p-2">
            <Button
                :loading="submitService.request.loading"
                type="submit"
                label="Save"
                icon="pi pi-save"
            />
        </div>
    </form>
</template>
<script setup lang="ts">
import { ChangePasswordFormErrorInterface, ChangePasswordFormInterface } from "@/interfaces/ChangePasswordFormInterface";
import { UserInterface } from "@/interfaces/UserInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { computed, reactive, ref } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    data: UserInterface;
}

const props = defineProps<Props>();
const toast = useToast();
const emit = defineEmits(["cb"]);
const submitService = useAxiosUtil<ChangePasswordFormInterface, null>();
const strongRegex = ref(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/);
const mediumRegex = ref(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/);
const hasLowercase = computed(() => /[a-z]/.test(form.password));
const hasUppercase = computed(() => /[A-Z]/.test(form.password));
const hasNumber = computed(() => /\d/.test(form.password));
const hasSymbol = computed(() => /[\W_]/.test(form.password));
const hasEightLength = computed(
    () => form.password && form.password.length >= 8,
);
const mustSamePassword = computed(
    () => form.password === form.password_confirmation,
);

const form: ChangePasswordFormInterface = reactive({
    password: null,
    password_confirmation: null,
});

const errors: ChangePasswordFormErrorInterface = reactive({
    password: [],
    password_confirmation: [],
});

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearErrors();
    if (!form.password) {
        errors.password.push("Password is required");
    }
    if (!form.password_confirmation) {
        errors.password_confirmation.push("Password confirmation is required");
    }

    if (form.password !== form.password_confirmation) {
        errors.password_confirmation.push("Passwords do not match");
    }

    const hasErrors = [
        errors.password.length > 0,
        errors.password_confirmation.length > 0,
    ];
    return hasErrors.includes(true) ? false : form;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .patch(`admin/users/change-password/${props.data.user_id}`, data)
            .then(() => {
                if (submitService.request.status === 200) {
                    emit("cb");
                    toast.success(submitService.request.message);
                } else {
                    toast.error(
                        submitService.request.message ?? "Please try again.",
                    );
                    if (submitService.request.errors) {
                        Object.keys(submitService.request.errors).forEach(
                            (key) => {
                                errors[key] = submitService.request.errors[key];
                            },
                        );
                    }
                }
            });
    }
};
</script>
