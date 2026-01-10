<template>
    <div>
        <form @submit.prevent="handleSubmit">
            <div class="p-2">
                <InputForm
                    :errors="errors.category_name"
                    id="category_name"
                    labelName="Category name*"
                    tag="label"
                >
                    <InputText
                        type="text"
                        v-model="form.category_name"
                        :invalid="errors.category_name.length > 0"
                        placeholder="Category name"
                        fluid
                        id="category_name"
                        autocomplete="off"
                    />
                </InputForm>
            </div>
            <div class="flex justify-center p-2">
                <Button
                    type="submit"
                    label="Save"
                    icon="pi pi-save"
                    class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                    :loading="submitService.request.loading"
                />
            </div>
        </form>
    </div>
</template>
<script setup lang="ts">
import {
    CategoryChangeNameFormErrorInterface,
    CategoryChangeNameFormInterface,
    CategoryInterface,
} from "@/interfaces/CategoryInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    data: CategoryInterface;
}

const emit = defineEmits(["cb"]);
const toast = useToast();
const submitService = useAxiosUtil<CategoryChangeNameFormInterface, null>();
const props = defineProps<Props>();

const form: CategoryChangeNameFormInterface = reactive({
    category_name: props.data.category_name,
});
const errors: CategoryChangeNameFormErrorInterface = reactive({
    category_name: [],
});

const clearError = () => {
    errors.category_name = [];
};

const validate = () => {
    clearError();
    if (!form.category_name) {
        errors.category_name.push("Category name is required");
    }
    const hasErrors = [errors.category_name.length > 0];
    return hasErrors.includes(true) ? false : form;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .patch(
                `admin/categories/update-name/${props.data.category_id}`,
                data,
            )
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
