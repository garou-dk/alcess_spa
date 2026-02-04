<template>
    <form @submit.prevent="handleSubmit" class="p-6 max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Product Image</h2>
            <p class="text-sm text-gray-600">Upload or update your product image (optional)</p>
        </div>

        <!-- Image Upload Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div v-if="!form.product_image && props.data.product_image" class="flex flex-col items-center mb-4">
                <img 
                    :src="UrlUtil.getBaseAppUrl(`storage/images/product/${props.data.product_image}`)" 
                    alt="Current Product Image" 
                    class="w-64 h-64 object-cover rounded-lg border-2 border-gray-300 mb-2" 
                />
                <p class="text-xs text-gray-500">Current Image</p>
            </div>
            
            <div class="flex flex-col items-center">
                <MediaUploader
                    v-model="form.product_image"
                    label="Change Product Image"
                    :aspect-ratio="1"
                />
            </div>
        </div>

        <!-- Next Button -->
        <div v-if="form.product_image" class="flex justify-end mt-6">
            <Button
                type="submit"
                label="Next"
                icon="pi pi-arrow-right"
                iconPos="right"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                :loading="submitService.request.loading"
            />
        </div>
    </form>
</template>

<script setup lang="ts">
import {
    ProductInterface,
    UpdateProductImageFormErrorInterface,
    UpdateProductImageFormInterface,
} from "@/interfaces/ProductInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import UrlUtil from "@/utils/UrlUtil";
import { reactive } from "vue";
import { useToast } from "vue-toastification";
import MediaUploader from "@/components/common/MediaUploader.vue";

interface Props {
    data: ProductInterface;
}

const submitService = useAxiosUtil<FormData, unknown>();
const toast = useToast();
const emit = defineEmits(["cb"]);
const props = defineProps<Props>();

const form: UpdateProductImageFormInterface = reactive({
    product_image: null,
});

const errors: UpdateProductImageFormErrorInterface = reactive({
    product_image: [],
});

const clearForm = () => {
    form.product_image = null;
};

const clearErrors = () => {
    errors.product_image = [];
};

const validate = () => {
    clearErrors();
    let data = new FormData();
    if (!form.product_image) {
        errors.product_image.push("Product image is required.");
    } else {
        data.append("product_image", form.product_image);
    }

    const isValid = [errors.product_image.length > 0];
    return isValid.includes(true) ? false : data;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .patchFormData(
                `admin/products/update-image/${props.data.product_id}`,
                data,
            )
            .then(() => {
                if (
                    submitService.request.status === 200 &&
                    submitService.request.data
                ) {
                    toast.success("Product image saved. Continue to add specifications.");
                    // Pass false to keep modal open and move to next tab
                    emit("cb", submitService.request.data, false);
                    clearForm();
                } else {
                    toast.error(
                        submitService.request.message ?? "Please try again",
                    );
                    if (submitService.request.errors) {
                        errors.product_image =
                            submitService.request.errors.product_image || [];
                    }
                }
            });
    } else {
        toast.error("Please fill in all required fields and try again.");
    }
};
</script>