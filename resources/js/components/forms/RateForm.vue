<template>
    <form @submit.prevent="handleSubmit()" class="flex flex-col gap-2">
        <div>
            <InputForm
                :errors="errors.rate"
                id="rate"
                labelName="Rate*"
                tag="span"
            >
                <div class="flex justify-center">
                    <Rating
                        v-model="form.rate"
                        :invalid="errors.rate.length > 0"
                        :cancel="false"
                    />   
                </div>     
            </InputForm>
        </div>
        <div>
            <InputForm
                :errors="errors.comment"
                id="comment"
                labelName="Comment"
                tag="label"
            >
                <Textarea
                    v-model="form.comment"
                    id="comment"
                    placeholder="Enter your comment"
                    fluid
                    :invalid="errors.comment.length > 0"
                />
            </InputForm>
        </div>
        <div class="flex justify-center">
            <Button
                type="submit"
                label="Submit"
                icon="pi pi-save"
                :loading="submitService.request.loading"
            />
        </div>
    </form>
</template>
<script setup lang="ts">
import IProductOrder from '@/interfaces/IProductOrder';
import useAxiosUtil from '@/utils/AxiosUtil';
import { reactive } from 'vue';
import { useToast } from 'vue-toastification';


interface Props {
    data: IProductOrder;
}

interface IForm {
    rate: number | null;
    comment: string | null;
}

interface IFormError {
    rate: string[];
    comment: string[];
}

const props = defineProps<Props>();
const emit = defineEmits(["cb"]);
const submitService = useAxiosUtil<IForm, null>();
const toast = useToast();

const form : IForm = reactive({
    rate: null,
    comment: null,
});
const errors = reactive<IFormError>({
    rate: [],
    comment: [],
});

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () =>{
    clearErrors();
    if (!form.rate) {
        errors.rate.push("Rate is required");
    }
    return Object.keys(errors).every((key) => errors[key].length === 0);
}

const handleSubmit = async () => {
    if (validate()) {
        await submitService.post(`customer/rates/${props.data.product_order_id}`, form).then(() => {
            if (submitService.request.status === 200 && submitService.request.data) {
                toast.success(submitService.request.message);
                emit("cb", submitService.request.data);
            }
            else {
                toast.error(submitService.request.message ?? "Please try again.");
            }
        });
    }
}
</script>