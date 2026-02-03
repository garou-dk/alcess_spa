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
        <div class="p-2">
            <InputForm
                :errors="errors.email"
                id="email"
                labelName="Email*"
                tag="label"
            >
                <InputText
                    type="email"
                    v-model="form.email"
                    :invalid="errors.email.length > 0"
                    placeholder="Email"
                    fluid
                    id="email"
                    autocomplete="off"
                />
            </InputForm>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.role_id"
                id="role_id"
                label-name="Role*"
                tag="span"
            >
                <Select
                    v-model="form.role_id"
                    :options="uniqueRoles"
                    :invalid="errors.role_id.length > 0"
                    option-label="role_name"
                    option-value="role_id"
                    :loading="roleStore.loadRoleService.request.loading"
                    label-id="role_id"
                    placeholder="Select Role"
                />
            </InputForm>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.password"
                id="password"
                labelName="Password*"
                tag="label"
            >
                <Password
                    v-model="form.password"
                    :invalid="errors.password.length > 0"
                    placeholder="Password"
                    fluid
                    input-id="password"
                    toggle-mask
                    :medium-regex="mediumRegex"
                    :strong-regex="strongRegex"
                >
                    <template #footer>
                        <div class="flex flex-col gap-1">
                            <span
                                v-if="!hasLowercase"
                                class="text-sm text-red-500"
                            >
                                At least one lowercase letter
                            </span>
                            <span
                                v-if="!hasUppercase"
                                class="text-sm text-red-500"
                            >
                                At least one uppercase letter
                            </span>
                            <span
                                v-if="!hasNumber"
                                class="text-sm text-red-500"
                            >
                                At least one number
                            </span>
                            <span
                                v-if="!hasSymbol"
                                class="text-sm text-red-500"
                            >
                                At least one special character
                            </span>
                            <span
                                v-if="!hasEightLength"
                                class="text-sm text-red-500"
                            >
                                At least 8 characters
                            </span>
                        </div>
                    </template>
                </Password>
            </InputForm>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.password_confirmation"
                id="password_confirmation"
                labelName="Confirm Password*"
                tag="label"
            >
                <Password
                    v-model="form.password_confirmation"
                    :invalid="errors.password_confirmation.length > 0"
                    placeholder="Confirm Password"
                    fluid
                    input-id="password_confirmation"
                    toggle-mask
                    :medium-regex="mediumRegex"
                    :strong-regex="strongRegex"
                >
                    <template #footer>
                        <span
                            v-if="!mustSamePassword"
                            class="text-sm text-red-500"
                        >
                            Password does not match
                        </span>
                    </template>
                </Password>
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
                <small class="text-gray-500 mt-1 block">Supported formats: JPEG, PNG, JPG. Max size: 5MB.</small>
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
                :loading="registerService.request.loading"
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
    UserFormErrorInterface,
    UserFormInterface,
} from "@/interfaces/UserFormInterface";
import { UserInterface } from "@/interfaces/UserInterface";
import { useRoleStore } from "@/stores/RoleState";
import useAxiosUtil from "@/utils/AxiosUtil";
import { computed, reactive, ref } from "vue";
import { useToast } from "vue-toastification";
import VuePictureCropper, { cropper } from "vue-picture-cropper";

const registerService = useAxiosUtil<UserFormInterface, UserInterface>();
const toast = useToast();
const emit = defineEmits(["cb"]);
const roleStore = useRoleStore();
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

const strongRegex = ref(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/);
const mediumRegex = ref(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/);

const form: UserFormInterface = reactive({
    full_name: null,
    email: null,
    password: null,
    password_confirmation: null,
    image: null,
    role_id: null,
});

const errors: UserFormErrorInterface = reactive({
    full_name: [],
    email: [],
    password: [],
    password_confirmation: [],
    image: [],
    role_id: [],
});

const hasLowercase = computed(() => /[a-z]/.test(form.password));
const hasUppercase = computed(() => /[A-Z]/.test(form.password));
const hasNumber = computed(() => /\d/.test(form.password));
const hasSymbol = computed(() => /[\W_]/.test(form.password));
const hasEightLength = computed(
    () => form.password && form.password.length >= 8,
);
const mustSamePassword = computed(
    () => form.password === form.password_confirmation,
);

const selectImage = () => {
    if (uploadInput.value) {
        uploadInput.value.click();
    }
};

const uniqueRoles = computed(() => {
    const roles = roleStore.roles;
    const unique = new Map();
    roles.forEach((role) => {
        if (!unique.has(role.role_id)) {
            unique.set(role.role_id, role);
        }
    });
    return Array.from(unique.values());
});

const onFileSelect = (e: Event) => {
    img.value = "";
    result.dataURL = "";
    result.blobURL = "";
    form.image = null;

    const { files } = e.target as HTMLInputElement;
    if (!files || !files.length) return;

    const file = files[0];

    // Validate file type
    const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
    if (!allowedTypes.includes(file.type)) {
        toast.error("Invalid file type. Please upload a JPEG, PNG, or JPG image.");
        e.target.value = ""; // Reset input
        return;
    }

    // Validate file size (5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if (file.size > maxSize) {
        toast.error("File size is too large. Maximum size is 5MB.");
        e.target.value = ""; // Reset input
        return;
    }

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
    errors.full_name = [];
    errors.email = [];
    errors.password = [];
    errors.password_confirmation = [];
};

const clearForm = () => {
    form.full_name = null;
    form.email = null;
    form.password = null;
    form.password_confirmation = null;
    form.image = null;
    form.role_id = null;
    result.dataURL = "";
    result.blobURL = "";
    img.value = "";
};

const validate = () => {
    clearErrors();
    if (!form.full_name) {
        errors.full_name.push("Full name is required");
    }
    if (!form.email) {
        errors.email.push("Email is required");
    }
    if (!form.password) {
        errors.password.push("Password is required");
    }
    if (!form.password_confirmation) {
        errors.password_confirmation.push("Password confirmation is required");
    }
    if (!form.role_id) {
        errors.role_id.push("Role is required");
    }

    const hasErrors = [
        errors.full_name.length > 0,
        errors.email.length > 0,
        errors.password.length > 0,
        errors.password_confirmation.length > 0,
        errors.role_id.length > 0,
        errors.image.length > 0,
    ];

    return hasErrors.includes(true) ? false : form;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await registerService.postFormData("admin/users", data).then(() => {
            if (
                registerService.request.status === 200 &&
                registerService.request.data
            ) {
                clearForm();
                emit("cb");
                toast.success(registerService.request.message);
            } else {
                toast.error(
                    registerService.request.message ?? "Please try again.",
                );
                if (registerService.request.errors) {
                    Object.keys(registerService.request.errors).forEach(
                        (key) => {
                            errors[key] = registerService.request.errors[key];
                        },
                    );
                }
            }
        });
    }
};
</script>
