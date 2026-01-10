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
                <input
                    ref="uploadInput"
                    type="file"
                    name="image"
                    id="image"
                    class="hidden"
                    accept="image/png, image/jpeg, image/jpg"
                    @change="onFileSelect"
                />
                <button
                    v-if="!form.image && !result.dataURL"
                    type="button"
                    class="w-full cursor-pointer border border-dotted border-sky-800 p-2"
                    @click="selectImage()"
                >
                    <div class="flex flex-col">
                        <i class="pi pi-image" />
                        <span class="text-sm">Select an Image</span>
                    </div>
                </button>
                <div v-else class="flex flex-col">
                    <img
                        :src="result.dataURL"
                        class="w-full"
                        alt="User Image"
                    />
                    <div class="mt-2 flex flex-wrap justify-center gap-2">
                        <Button
                            type="button"
                            label="Change Image"
                            icon="pi pi-pencil"
                            class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                            @click="selectImage"
                        />
                        <Button
                            type="button"
                            label="Remove Image"
                            icon="pi pi-trash"
                            severity="danger"
                            @click="
                                form.image = null;
                                result.dataURL = '';
                                result.blobURL = '';
                            "
                        />
                    </div>
                </div>
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
        <Dialog
            v-model:visible="showCropperModal"
            modal
            header="Crop Image"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <VuePictureCropper
                :boxStyle="{
                    width: '100%',
                    height: '100%',
                    backgroundColor: '#f8f8f8',
                    margin: 'auto',
                }"
                :img="img"
                :options="{
                    viewMode: 1,
                    dragMode: 'crop',
                    aspectRatio: 1,
                }"
            />
            <div class="mt-2 flex justify-center">
                <Button
                    type="button"
                    label="Crop Image"
                    icon="pi pi-image"
                    class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                    @click="getCropResult()"
                />
            </div>
        </Dialog>
    </form>
</template>
<script setup lang="ts">
import {
    ChangeProfileFormErrorInterface,
    ChangeProfileFormInterface,
} from "@/interfaces/ChangeProfileFormInterface";
import { UserInterface } from "@/interfaces/UserInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive, ref } from "vue";
import VuePictureCropper, { cropper } from "vue-picture-cropper";
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
const img = ref<string>("");
const result: {
    dataURL: string;
    blobURL: string;
} = reactive({
    dataURL: "",
    blobURL: "",
});
const uploadInput = ref<HTMLInputElement | null>(null);
const showCropperModal = ref<boolean>(false);

const form: ChangeProfileFormInterface = reactive({
    image: null,
});

const errors: ChangeProfileFormErrorInterface = reactive({
    image: [],
});

const selectImage = () => {
    if (uploadInput.value) {
        uploadInput.value.click();
    }
};

const onFileSelect = (e: Event) => {
    img.value = "";
    result.dataURL = "";
    result.blobURL = "";
    form.image = null;

    const { files } = e.target as HTMLInputElement;
    if (!files || !files.length) return;

    const file = files[0];
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        img.value = String(reader.result);
        showCropperModal.value = true;
        if (!uploadInput.value) return;
        uploadInput.value.value = "";
    };
};

const getCropResult = async () => {
    if (!cropper) return;
    const base64 = cropper.getDataURL();
    const blob: Blob | null = await cropper.getBlob();
    if (!blob) return;
    const file = await cropper.getFile({
        fileName: "cropped-image",
    });
    result.dataURL = base64;
    result.blobURL = URL.createObjectURL(blob);
    form.image = file;
    showCropperModal.value = false;
};

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
    data.append("image", form.image);
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
