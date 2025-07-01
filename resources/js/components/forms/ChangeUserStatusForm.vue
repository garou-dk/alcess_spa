<template>
    <button
        type="button"
        @click="handleSubmit()"
        class="flex w-full cursor-pointer items-center gap-2 rounded p-2 text-sm hover:bg-gray-300"
    >
        <i class="pi" :class="data.is_active ? 'pi-times' : 'pi-check'" />
        <span v-if="submitService.request.loading"> Please wait... </span>
        <span v-else>
            {{ data.is_active ? "Deactivate" : "Activate" }}
        </span>
    </button>
</template>
<script setup lang="ts">
import {
    ChangeUserStatusFormErrorInterface,
    ChangeUserStatusFormInterface,
} from "@/interfaces/ChangeUserStatusFormInterface";
import { UserInterface } from "@/interfaces/UserInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive } from "vue";
import { useToast } from "vue-toastification";

interface Props {
    data: UserInterface;
}

const props = defineProps<Props>();
const submitService = useAxiosUtil<ChangeUserStatusFormInterface, null>();
const toast = useToast();
const emit = defineEmits(["cb"]);

const form: ChangeUserStatusFormInterface = reactive({
    is_active: props.data.is_active ? 0 : 1,
});

const errors: ChangeUserStatusFormErrorInterface = reactive({
    is_active: [],
});

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearErrors();
    const hasErrors = [errors.is_active.length > 0];
    return hasErrors.includes(true) ? false : form;
};

const handleSubmit = async () => {
    if (submitService.request.loading) {
        return;
    }
    const data = validate();
    if (data) {
        await submitService
            .patch(`admin/users/change-status/${props.data.user_id}`, data)
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
