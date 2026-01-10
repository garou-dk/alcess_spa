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
        <div class="flex justify-center p-2">
            <Button
                :loading="submitService.request.loading"
                type="submit"
                label="Save"
                icon="pi pi-save"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
            />
        </div>
    </form>
</template>
<script setup lang="ts">
import {
    ChangeNameFormErrorInterface,
    ChangeNameFormInterface,
} from "@/interfaces/ChangeNameFormInterface";
import { UserInterface } from "@/interfaces/UserInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    data: UserInterface;
}

const props = defineProps<Props>();
const toast = useToast();
const emit = defineEmits(["cb"]);
const submitService = useAxiosUtil<ChangeNameFormInterface, null>();

const form: ChangeNameFormInterface = reactive({
    full_name: props.data.full_name,
});

const errors: ChangeNameFormErrorInterface = reactive({
    full_name: [],
});

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearErrors();
    if (!form.full_name) {
        errors.full_name.push("Full name is required");
    }

    const hasErrors = [errors.full_name.length > 0];
    return hasErrors.includes(true) ? false : form;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .patch(`admin/users/change-name/${props.data.user_id}`, data)
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
    } else {
        toast.error("Please fill in the required fields.");
    }
};
</script>
