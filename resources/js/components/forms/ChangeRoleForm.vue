<template>
    <form @submit.prevent="handleSubmit()">
        <div class="p-2">
            <InputForm
                :errors="[]"
                id="current_user"
                labelName="Change role for"
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
                :errors="errors.role_id"
                id="role_id"
                label-name="Role*"
                tag="span"
            >
                <Select
                    v-model="form.role_id"
                    :options="roleStore.roles"
                    :invalid="errors.role_id.length > 0"
                    option-label="role_name"
                    option-value="role_id"
                    :loading="roleStore.loadRoleService.request.loading"
                    label-id="role_id"
                    placeholder="Select Role"
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
            />
        </div>
    </form>
</template>
<script setup lang="ts">
import {
    ChangeRoleFormErrorInterface,
    ChangeRoleFormInterface,
} from "@/interfaces/ChangeRoleFormInterface";
import { UserInterface } from "@/interfaces/UserInterface";
import { useRoleStore } from "@/stores/RoleState";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    data: UserInterface;
}

const props = defineProps<Props>();
const submitService = useAxiosUtil<ChangeRoleFormInterface, null>();
const toast = useToast();
const emit = defineEmits(["cb"]);
const roleStore = useRoleStore();

const form: ChangeRoleFormInterface = reactive({
    role_id: props.data.role_id,
});

const errors: ChangeRoleFormErrorInterface = reactive({
    role_id: [],
});

const clearError = () => {
    errors.role_id = [];
};

const validate = () => {
    clearError();
    if (!form.role_id) {
        errors.role_id.push("Role is required");
    }
    const hasErrors = [errors.role_id.length > 0];
    return hasErrors.includes(true) ? false : form;
};

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService
            .patch(`admin/users/change-role/${props.data.user_id}`, data)
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
