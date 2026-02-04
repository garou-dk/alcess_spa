<template>
    <div>
        <div v-if="!submitService.request.loading">
            <MediaUploader
                v-model="form.featured_image"
                label="Add Featured Image/Video"
                accept="image/png, image/jpeg, image/jpg, video/mp4"
                :max-size-m-b="20"
                @update:modelValue="handleSubmit" 
            />
            <!-- 
                Note: Original logic submitted immediately on selection/crop. 
                MediaUploader updates modelValue on selection/crop completion.
                So we can watch the model or listen to update event to trigger submit.
                However, to prevent double submits if user re-selects, maybe better to have a save button?
                Original UX was: click (+) -> select -> (crop if image) -> auto submit.
                MediaUploader creates a preview. 
                The Refactor below keeps the "Immediate Submit" pattern by calling handleSubmit on change.
                But MediaUploader shows preview. 
                If we want to keep the exact UX (button only, no preview until saved?), 
                we might need to tweak MediaUploader usage or just let it show preview then user clicks save?
                Actually, original code replaced the button with spinner immediately.
                Let's stick to the immediate submit for now as that seems to be the design.
                But wait, MediaUploader is designed to show a preview state.
                If I use it, it will show the image, then I submit.
                Actually, better UX: Show preview + Save button? 
                Or: Listener `update:modelValue` triggers submit.
            -->
        </div>
        <div v-else>
            <button
                type="button"
                class="h-28 w-28 cursor-pointer rounded border border-dotted border-sky-800 bg-gray-500 hover:bg-gray-100 flex items-center justify-center p-0"
            >
                <i
                    class="pi pi-spin pi-spinner"
                    style="font-size: 2rem; color: white"
                ></i>
            </button>
        </div>
    </div>
</template>
<script setup lang="ts">
import {
    FeaturedImageFormErrorInterface,
    FeaturedImageFormInterface,
} from "@/interfaces/FeaturedImageInterface";
import { ProductInterface } from "@/interfaces/ProductInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import ValidatorUtil from "@/utils/ValidatorUtil";
import MediaUploader from "@/components/common/MediaUploader.vue";
import { reactive, watch } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    data: ProductInterface;
}

const form: FeaturedImageFormInterface = reactive({
    featured_image: null,
});

const errors: FeaturedImageFormErrorInterface = reactive({
    featured_image: [],
});

const props = defineProps<Props>();
const emit = defineEmits(["cb"]);
const submitService = useAxiosUtil<FormData, ProductInterface>();
const toast = useToast();

const clearErrors = () => {
    errors.featured_image = [];
};

const validate = (): FormData | false => {
    clearErrors();
    let formData = new FormData();
    if (!form.featured_image) {
        errors.featured_image.push("Featured image is required"); // Should adhere to non-empty check
    } else {
         // MediaUploader handles types, but double check doesn't hurt or can be skipped
         formData.append("featured_image", form.featured_image);
    }
    const hasErrors = [errors.featured_image.length > 0];
    return hasErrors.includes(true) ? false : formData;
};

const handleSubmit = async () => {
    if (!form.featured_image) return; // Wait for file

    const data = validate();
    if (data) {
        await submitService
            .postFormData(
                `admin/featured-images/${props.data.product_id}`,
                data,
            )
            .then(() => {
                if (submitService.request.status === 200) {
                    toast.success(submitService.request.message);
                    emit("cb", submitService.request.data);
                    // Reset is handled by parent re-rendering or we clear form
                    form.featured_image = null; 
                } else {
                    toast.error(
                        submitService.request.message ?? "Please try again",
                    );
                    form.featured_image = null; // Clear to allow retry
                }
            });
    } else {
        toast.error("Please fill in the required fields.");
    }
};

// Watch for file changes to trigger auto-submit (mimicking original behavior)
watch(() => form.featured_image, (newVal) => {
    if (newVal) {
        handleSubmit();
    }
});
</script>
