<template>
    <form @submit.prevent="handleSubmit()">
        <div class="p-2">
            <InputForm
                :errors="[]"
                id="current_user"
                labelName="Change profile for"
                tag="label"
            >
                <InputText
                    :model-value="props.data.full_name"
                    id="current_user"
                    readonly
                />
            </InputForm>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.image"
                id="image"
                label-name="Image"
                tag="label"
            >
                <MediaUploader
                    v-model="form.image"
                    :aspect-ratio="1"
                    label="Upload Profile Picture"
                />
            </InputForm>
        </div>
        <div class="flex justify-center p-2">
            <Button
                type="submit"
                label="Save"
                icon="pi pi-save"
                fluid
                :loading="submitService.request.loading"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
            />
        </div>
    </form>
</template>
<script setup lang="ts">
import {
    ChangeProfileFormErrorInterface,
    ChangeProfileFormInterface,
} from "@/interfaces/ChangeProfileFormInterface";
import { UserInterface } from "@/interfaces/UserInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import MediaUploader from "@/components/common/MediaUploader.vue";
import { reactive } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    data: UserInterface;
}

const submitService = useAxiosUtil<
    ChangeProfileFormInterface | FormData,
    null
>();
const emit = defineEmits(["cb"]);
const toast = useToast();
const props = defineProps<Props>();

const form: ChangeProfileFormInterface = reactive({
    image: null,
});

const errors: ChangeProfileFormErrorInterface = reactive({
    image: [],
});

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearErrors();
    if (!form.image) {
        errors.image.push("Image is required");
    }

    const hasErrors = [errors.image.length > 0];
    let data = new FormData();
    if (form.image) {
        data.append("image", form.image);
    }
    return hasErrors.includes(true) ? false : data;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .patchFormData(
                `admin/users/change-profile/${props.data.user_id}`,
                data,
            )
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

