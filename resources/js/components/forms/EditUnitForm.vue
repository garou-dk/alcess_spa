<template>
    <div>
        <form @submit.prevent="handleSubmit">
            <div class="p-2">
                <InputForm
                    :errors="errors.unit_name"
                    id="unit_name"
                    labelName="Unit name*"
                    tag="label"
                >
                    <InputText
                        type="text"
                        v-model="form.unit_name"
                        :invalid="errors.unit_name.length > 0"
                        placeholder="Unit name"
                        fluid
                        id="unit_name"
                        autocomplete="off"
                    />
                </InputForm>
            </div>
            <div class="flex justify-center p-2">
                <Button
                    type="submit"
                    label="Save"
                    icon="pi pi-save"
                    class="primary-bg"
                    :loading="submitService.request.loading"
                />
            </div>
        </form>
    </div>
</template>
<script setup lang="ts">
import {
    UnitFormErrorInterface,
    UnitFormInterface,
    UnitInterface,
} from "@/interfaces/UnitInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    data: UnitInterface;
}

const emit = defineEmits(["cb"]);
const toast = useToast();
const submitService = useAxiosUtil<UnitFormInterface, null>();
const props = defineProps<Props>();

const form: UnitFormInterface = reactive({
    unit_name: props.data.unit_name,
});
const errors: UnitFormErrorInterface = reactive({
    unit_name: [],
});

const clearError = () => {
    errors.unit_name = [];
};

const validate = () => {
    clearError();
    if (!form.unit_name) {
        errors.unit_name.push("Unit name is required");
    }
    const hasErrors = [errors.unit_name.length > 0];
    return hasErrors.includes(true) ? false : form;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .patch(`admin/units/${props.data.unit_id}`, data)
            .then(() => {
                if (
                    submitService.request.status === 200 &&
                    submitService.request.data
                ) {
                    toast.success(submitService.request.message);
                    emit("cb");
                } else {
                    toast.error(
                        submitService.request.message ?? "Please try again",
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
