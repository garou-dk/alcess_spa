<template>
    <form
        class="rounded-lg border-2 p-4 transition-all duration-200"
        :class="props.isSelected ? 'border-blue-400 bg-blue-50 shadow-md' : 'border-gray-300 bg-white hover:border-gray-400'"
        @submit.prevent="handleSubmit()"
    >
        <div class="flex flex-wrap items-end">
            <div class="p-2 max-lg:w-full lg:grow">
                <InputForm
                    :errors="errors.specification_name"
                    id="specification_name"
                    label-name="Specification Name"
                    tag="label"
                >
                    <InputText
                        v-model="form.specification_name"
                        id="specification_name"
                        type="text"
                        placeholder="Enter specification name"
                        fluid
                        :invalid="errors.specification_name.length > 0"
                        :disabled="!props.isSelected"
                        class="transition-all duration-200"
                    />
                </InputForm>
            </div>
            <div class="p-2 max-lg:w-full lg:grow">
                <InputForm
                    :errors="errors.specification_value"
                    id="specification_value"
                    label-name="Specification Value"
                    tag="label"
                >
                    <InputText
                        v-model="form.specification_value"
                        id="specification_value"
                        type="text"
                        placeholder="Enter specification value"
                        fluid
                        :invalid="errors.specification_value.length > 0"
                        :disabled="!props.isSelected"
                        class="transition-all duration-200"
                    />
                </InputForm>
            </div>
            <div
                class="flex flex-wrap gap-2 p-2 max-lg:w-full max-lg:justify-center"
            >
                <div v-if="props.isSelected">
                    <Button
                        type="submit"
                        icon="pi pi-save"
                        :loading="submitService.request.loading"
                        class="h-10 w-10 flex items-center justify-center"
                        severity="success"
                    />
                </div>
                <div v-else>
                    <slot name="select" />
                </div>
                <div>
                    <DeleteProductSpecification
                        :data="props.data"
                        @cb="deleteEmit"
                    />
                </div>
            </div>
        </div>
        
        <!-- Selected Indicator -->
        <div v-if="props.isSelected" class="mt-2 flex items-center gap-2 text-sm text-blue-700">
            <i class="pi pi-pencil"></i>
            <span class="font-medium">Editing mode - Make changes and click save</span>
        </div>
    </form>
</template>

<script setup lang="ts">
import { ProductInterface } from "@/interfaces/ProductInterface";
import {
    ProductSpecificationFormErrorInterface,
    ProductSpecificationFormInterface,
    ProductSpecificationInterface,
} from "@/interfaces/ProductSpecificationInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { inject, onMounted, reactive, Ref } from "vue";
import { useToast } from "vue-toastification";
import DeleteProductSpecification from "@/components/forms/DeleteProductSpecification.vue";

interface Props {
    data: ProductSpecificationInterface;
    isSelected: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits(["cb"]);
const toast = useToast();
const submitService = useAxiosUtil<
    ProductSpecificationFormInterface,
    ProductInterface
>();
const selectedProductSpecification = inject<
    Ref<ProductSpecificationInterface | null>
>("selectedProductSpecification");

const form: ProductSpecificationFormInterface = reactive({
    specification_name: props.data.specification_name,
    specification_value: props.data.specification_value,
});

const errors: ProductSpecificationFormErrorInterface = reactive({
    specification_name: [],
    specification_value: [],
});

const clearErrors = () => {
    errors.specification_name = [];
    errors.specification_value = [];
};

const deleteEmit = (data: ProductInterface) => {
    emit("cb", data);
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

const initState = () => {
    form.specification_name = props.data.specification_name;
    form.specification_value = props.data.specification_value;
    clearErrors();
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        if (
            props.data.specification_name !== form.specification_name ||
            props.data.specification_value !== form.specification_value
        ) {
            await submitService
                .patch(
                    `admin/specifications/${props.data.specification_id}`,
                    data,
                )
                .then(() => {
                    if (
                        submitService.request.status === 200 &&
                        submitService.request.data
                    ) {
                        toast.success(submitService.request.message);
                        emit("cb", submitService.request.data);
                    } else {
                        toast.error(
                            submitService.request.message ?? "Please try again",
                        );
                        if (submitService.request.errors) {
                            Object.keys(submitService.request.errors).forEach(
                                (key) => {
                                    errors[key] =
                                        submitService.request.errors[key];
                                },
                            );
                        }
                    }
                });
        } else {
            selectedProductSpecification.value = null;
        }
    } else {
        toast.error("Please fill in the required fields.");
    }
};

onMounted(() => {
    initState();
});
</script>