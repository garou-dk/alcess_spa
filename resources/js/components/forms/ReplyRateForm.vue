<template>
    <form @submit.prevent="handleSubmit()">
        <div class="p-2">
            <InputForm
                :errors="errors.reply"
                id="reply"
                labelName="Reply*"
                tag="label"
            >
                <Textarea
                    v-model="form.reply"
                    id="reply"
                    placeholder="Enter your reply"
                    :invalid="errors.reply.length > 0"
                />
            </InputForm>
        </div>
        <div class="p-2 flex justify-center">
            <Button
                type="submit"
                label="Save"
                icon="pi pi-save"
                :loading="submitService.request.loading"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
            />
        </div>
    </form>
</template>
<script setup lang="ts">
import { IRate } from '@/interfaces/IRate';
import useAxiosUtil from '@/utils/AxiosUtil';
import { reactive } from 'vue';
import { useToast } from 'vue-toastification';


interface Props {
    rate: IRate;
}
interface IForm {
    reply: string | null;
}

interface IFormError {
    reply: string[];
}

const props = defineProps<Props>();
const emit = defineEmits(["cb"]);

const form : IForm = reactive({
    reply: null
});

const errors = reactive<IFormError>({
    reply: []
});

const submitService = useAxiosUtil<IForm, IRate>();
const toast = useToast();

const clearErrors = () => {
    errors.reply = [];
}

const validate = () => {
    clearErrors();
    if (!form.reply) {
        errors.reply.push("Reply is required");
    }
    return Object.keys(errors).every((key) => errors[key].length === 0);
}

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService.patch(`admin/rates/${props.rate.rate_id}`, form)
            .then(() => {
                if (submitService.request.status === 200) {
                    toast.success(submitService.request.message ?? 'Reply saved successfully.');
                    emit("cb", submitService.request.data);
                }
                else {
                    toast.error(submitService.request.message ?? 'Failed to save reply.');
                    if (submitService.request.errors) {
                        Object.keys(submitService.request.errors).forEach((key) => {
                            errors[key] = submitService.request.errors[key];
                        });
                    }
                }
            });
    }
    else {
        toast.error('Please fix the errors in the form.');
    }
}
</script>