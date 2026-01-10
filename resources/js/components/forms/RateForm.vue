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
                        class="rating-yellow"
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
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
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

<style>
/* Yellow star styling for rating - comprehensive targeting */
/* Default unselected stars - gray */
.rating-yellow .p-rating-option,
.rating-yellow .p-rating-option .p-rating-icon,
.rating-yellow .p-rating-option svg,
.rating-yellow button,
.rating-yellow button svg,
.rating-yellow button .p-icon {
    color: #d1d5db !important; /* gray-300 for unselected */
    fill: #d1d5db !important;
}

/* Selected/Active/Filled stars - yellow */
.rating-yellow .p-rating-option-active,
.rating-yellow .p-rating-option-active .p-rating-icon,
.rating-yellow .p-rating-option-active svg,
.rating-yellow .p-rating-option-active .p-icon,
.rating-yellow .p-rating-option.p-focus,
.rating-yellow .p-rating-option.p-focus svg,
.rating-yellow button[data-p-active="true"],
.rating-yellow button[data-p-active="true"] svg,
.rating-yellow button[data-p-active="true"] .p-icon,
.rating-yellow .p-highlight,
.rating-yellow .p-highlight svg,
.rating-yellow .p-highlight .p-icon {
    color: #fbbf24 !important; /* yellow-400 for filled */
    fill: #fbbf24 !important;
}

/* Hover state - yellow */
.rating-yellow .p-rating-option:hover,
.rating-yellow .p-rating-option:hover .p-rating-icon,
.rating-yellow .p-rating-option:hover svg,
.rating-yellow .p-rating-option:hover .p-icon,
.rating-yellow button:hover,
.rating-yellow button:hover svg,
.rating-yellow button:hover .p-icon {
    color: #fbbf24 !important; /* yellow-400 on hover */
    fill: #fbbf24 !important;
}
</style>