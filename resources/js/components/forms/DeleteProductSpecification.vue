<template>
    <Button
        type="button"
        icon="pi pi-trash"
        severity="danger"
        :loading="submitService.request.loading"
        class="h-10 w-10 flex items-center justify-center"
        @click="handleSubmit()"
    />
</template>
<script setup lang="ts">
import { ProductInterface } from "@/interfaces/ProductInterface";
import { ProductSpecificationInterface } from "@/interfaces/ProductSpecificationInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";

interface Props {
    data: ProductSpecificationInterface;
}

const props = defineProps<Props>();
const toast = useToast();
const emit = defineEmits(["cb"]);
const submitService = useAxiosUtil<null, ProductInterface>();

const handleSubmit = async () => {
    await submitService
        .deleteRequest(
            `admin/specifications/${props.data.specification_id}`,
            null,
        )
        .then(() => {
            if (submitService.request.status === 200) {
                toast.success(submitService.request.message);
                emit("cb", submitService.request.data);
            } else {
                toast.error(
                    submitService.request.message ?? "Please try again.",
                );
            }
        });
};
</script>
