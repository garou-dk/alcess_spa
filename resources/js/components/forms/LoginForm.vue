<template>
    <form @submit.prevent="handleSubmit()">
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
                pt:root:class="bg-sky-800! rounded-3xl!"
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
import { reactive } from "vue";
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

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await authService
            .post(props.admin ? "admin/login" : "login", data)
            .then(() => {
                if (
                    authService.request.status === 200 &&
                    authService.request.data
                ) {
                    Page.user = authService.request.data;
                    if (
                        getStoreRoles().includes(
                            Page.user.role.role_name as RoleEnum,
                        )
                    ) {
                        router.push({ name: "admin.app" });
                    }
                } else {
                    toast.error(
                        authService.request.message ?? "Please try again.",
                    );
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
