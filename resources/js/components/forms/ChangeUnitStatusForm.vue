<template>
    <button
        type="button"
        @click="handleSubmit()"
        class="flex w-full cursor-pointer items-center gap-2 rounded p-2 text-sm hover:bg-gray-300"
    >
        <i class="pi" :class="data.deleted_at ? 'pi-check' : 'pi-times'" />
        <span v-if="submitService.request.loading"> Please wait... </span>
        <span v-else>
            {{ data.deleted_at ? "Activate" : "Deactivate" }}
        </span>
    </button>
</template>
<script setup lang="ts">
import { UnitInterface } from "@/interfaces/UnitInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";

interface Props {
    data: UnitInterface;
}

const props = defineProps<Props>();
const submitService = useAxiosUtil<null, null>();
const toast = useToast();
const emit = defineEmits(["cb"]);

const handleSubmit = async () => {
    if (submitService.request.loading) {
        return;
    }
    if (!props.data.deleted_at) {
        await submitService
            .deleteRequest(`admin/units/${props.data.unit_id}`, null)
            .then(() => {
                if (submitService.request.status === 200) {
                    emit("cb");
                    toast.success(submitService.request.message);
                } else {
                    toast.error(
                        submitService.request.message ?? "Please try again.",
                    );
                }
            });
    } else {
        await submitService
            .patch(`admin/units/recover/${props.data.unit_id}`, null)
            .then(() => {
                if (submitService.request.status === 200) {
                    emit("cb");
                    toast.success(submitService.request.message);
                } else {
                    toast.error(
                        submitService.request.message ?? "Please try again.",
                    );
                }
            });
    }
};
</script>
