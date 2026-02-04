<template>
    <div>
        <form @submit.prevent="handleSubmit">
            <div class="p-2">
                <InputForm
                    :errors="[]"
                    id="category_name"
                    labelName="Change image for"
                    tag="label"
                >
                    <InputText
                        type="text"
                        :model-value="props.data.category_name"
                        readonly
                        placeholder="Category name"
                        fluid
                        id="category_name"
                        autocomplete="off"
                    />
                </InputForm>
            </div>
            <div class="p-2">
                <InputForm
                    :errors="errors.category_image"
                    id="category_image"
                    label-name="Image"
                    tag="label"
                >
                    <MediaUploader
                        v-model="form.category_image"
                        :aspect-ratio="1"
                        label="Upload Category Image"
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
    CategoryChangeImageFormErrorInterface,
    CategoryChangeImageFormInterface,
    CategoryInterface,
} from "@/interfaces/CategoryInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import MediaUploader from "@/components/common/MediaUploader.vue";
import { reactive } from "vue";
import { useToast } from "vue-toastification";
import ValidatorUtil from "@/utils/ValidatorUtil";

interface Props {
    data: CategoryInterface;
}

const emit = defineEmits(["cb"]);
const props = defineProps<Props>();
const toast = useToast();
const submitService = useAxiosUtil<FormData, null>();

const form: CategoryChangeImageFormInterface = reactive({
    category_image: null,
});
const errors: CategoryChangeImageFormErrorInterface = reactive({
    category_image: [],
});

const clearError = () => {
    errors.category_image = [];
};

const validate = (): FormData | false => {
    clearError();
    let formData = new FormData();
    if (!form.category_image) {
        errors.category_image.push("Category image is required");
    } else {
        if (!ValidatorUtil.isValidImageFormat(form.category_image)) {
            errors.category_image.push("Invalid image format");
        } else if (!ValidatorUtil.isValidImageSize(form.category_image)) {
            errors.category_image.push("Image size must be less than 2MB");
        } else {
            formData.append("category_image", form.category_image);
        }
    }
    const hasErrors = [errors.category_image.length > 0];
    return hasErrors.includes(true) ? false : formData;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .patchFormData(
                `admin/categories/update-image/${props.data.category_id}`,
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
