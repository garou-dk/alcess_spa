<template>
    <form @submit.prevent="handleSubmit()">
        <div class="p-2">
            <InputForm
                :errors="errors.full_name"
                id="full_name"
                labelName="Full name*"
                tag="label"
            >
                <InputText 
                    type="text"
                    v-model="form.full_name"
                    :invalid="errors.full_name.length > 0"
                    placeholder="Full name"
                    fluid
                    id="full_name"
                    autocomplete="off"
                />
            </InputForm>
        </div>
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
                            <span v-if="!hasLowercase" class="text-sm text-red-500">
                                At least one lowercase letter
                            </span>
                            <span v-if="!hasUppercase" class="text-sm text-red-500">
                                At least one uppercase letter
                            </span>
                            <span v-if="!hasNumber" class="text-sm text-red-500">
                                At least one number
                            </span>
                            <span v-if="!hasSymbol" class="text-sm text-red-500">
                                At least one special character
                            </span>
                            <span v-if="!hasEightLength" class="text-sm text-red-500">
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
                        <span v-if="!mustSamePassword" class="text-sm text-red-500">
                            Password does not match
                        </span>
                    </template>
                </Password>
            </InputForm>
        </div>
        <div class="p-2 flex justify-center">
            <Button
                type="submit"
                label="Sign Up"
                fluid
            />
        </div>
        <slot name="footer" />
    </form>
</template>
<script setup lang="ts">
import { RegisterFormErrorInterface, RegisterFormInterface } from '@/interfaces/RegisterFormInterface';
import { computed, reactive, ref } from 'vue';

const strongRegex = ref(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/);
const mediumRegex = ref(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/);

const form : RegisterFormInterface = reactive({
    full_name: null,
    email: null,
    password: null,
    password_confirmation: null
});

const errors : RegisterFormErrorInterface = reactive({
    full_name: [],
    email: [],
    password: [],
    password_confirmation: []
});

const hasLowercase = computed(() => /[a-z]/.test(form.password));
const hasUppercase = computed(() => /[A-Z]/.test(form.password));
const hasNumber = computed(() => /\d/.test(form.password));
const hasSymbol = computed(() => /[\W_]/.test(form.password));
const hasEightLength = computed(() => form.password && form.password.length >= 8);
const mustSamePassword = computed(() => form.password === form.password_confirmation);

const clearErrors = () => {
    errors.full_name = [];
    errors.email = [];
    errors.password = [];
    errors.password_confirmation = [];
}

const validate = () => {
    clearErrors();
    if (!form.full_name) {
        errors.full_name.push('Full name is required');
    }
    if (!form.email) {
        errors.email.push('Email is required');
    }
    if (!form.password) {
        errors.password.push('Password is required');
    }
    if (!form.password_confirmation) {
        errors.password_confirmation.push('Password confirmation is required');
    }

    const hasErrors = [
        errors.full_name.length > 0,
        errors.email.length > 0,
        errors.password.length > 0,
        errors.password_confirmation.length > 0
    ];

    return hasErrors.includes(true) ? false : form;
}

const handleSubmit = async() => {
    const data = validate();
}
</script>