<template>
    <form @submit.prevent="handleSubmit()">
        <div class="p-2">
            <InputForm
                :errors="errors.specification_name"
                id="specification_name"
                label-name="Specification Name*"
                tag="label"
            >
                <InputText
                    v-model="form.specification_name"
                    id="specification_name"
                    type="text"
                    placeholder="Enter specification name"
                    fluid
                    :invalid="errors.specification_name.length > 0"
                />
            </InputForm>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.specification_value"
                id="specification_value"
                label-name="Specification Value*"
                tag="label"
            >
                <InputText
                    v-model="form.specification_value"
                    id="specification_value"
                    type="text"
                    placeholder="Enter specification value"
                    fluid
                    :invalid="errors.specification_value.length > 0"
                />
            </InputForm>
        </div>
        <div class="flex justify-center p-2">
            <Button
                :loading="submitService.request.loading"
                type="submit"
                label="Save"
                icon="pi pi-save"
                class="primary-bg"
            />
        </div>
    </form>
</template>
<script setup lang="ts">
import { ProductInterface } from "@/interfaces/ProductInterface";
import {
    ProductSpecificationFormErrorInterface,
    ProductSpecificationFormInterface,
} from "@/interfaces/ProductSpecificationInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    data: ProductInterface;
}

const emit = defineEmits(["cb"]);
const toast = useToast();
const submitService = useAxiosUtil<
    ProductSpecificationFormInterface,
    ProductInterface
>();
const props = defineProps<Props>();

const form: ProductSpecificationFormInterface = reactive({
    specification_name: null,
    specification_value: null,
});

const errors: ProductSpecificationFormErrorInterface = reactive({
    specification_name: [],
    specification_value: [],
});

const clearErrors = () => {
    errors.specification_name = [];
    errors.specification_value = [];
};

const validate = () => {
    clearErrors();

    if (!form.specification_name) {
        errors.specification_name.push("Specification name is required.");
    }
    if (!form.specification_value) {
        errors.specification_value.push("Specification value is required.");
    }

    const hasErrors = [
        errors.specification_name.length > 0,
        errors.specification_value.length > 0,
    ];

    return !hasErrors.includes(true) ? form : false;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .post(`admin/specifications/${props.data.product_id}`, data)
            .then(() => {
                if (
                    submitService.request.status === 200 &&
                    submitService.request.data
                ) {
                    toast.success("Specification added successfully.");
                    emit("cb", submitService.request.data);
                } else {
                    toast.error(
                        submitService.request.message ||
                            "An error occurred while adding the specification.",
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
