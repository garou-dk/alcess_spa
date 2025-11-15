<template>
    <div>
        <BoxShadow>
            <div class="w-full">
                <DataTable
                    :value="data"
                    :paginator="true"
                    :rows="5"
                    :loading="loadService.request.loading"
                >
                    <Column field="product.product_name" header="Product" />
                    <Column field="user.full_name" header="Customer" />
                    <Column field="rate" header="Rate" />
                    <Column field="comment" header="Comment" />
                    <Column field="created_at" header="Date">
                        <template #body="{ data }">
                            {{ DateUtil.formatToMonthDayYear(data.created_at) }}
                        </template>
                    </Column>
                    <Column header="Action">
                        <template #body="{ data }">
                            <Button
                                type="button"
                                label="Respond"
                                icon="pi pi-pencil"
                                size="small"
                                @click="openRate(data)"
                            />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </BoxShadow>
        <Dialog
            v-model:visible="replyDialog.visible"
            header="Rate Reply"
            :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <ReplyRateForm v-if="replyDialog.data" :rate="replyDialog.data" @cb="rateCb" />
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import ReplyRateForm from '@/components/forms/ReplyRateForm.vue';
import { IRate } from '@/interfaces/IRate';
import useAxiosUtil from '@/utils/AxiosUtil';
import DateUtil from '@/utils/DateUtil';
import { onMounted, reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';


const loadService = useAxiosUtil<null, IRate[]>();
const toast = useToast();

const data = ref<IRate[]>([]);

const replyDialog : {
    visible : boolean,
    data : IRate | null
} = reactive({
    visible : false,
    data : null
});

const load = async () => {
    await loadService.get("admin/rates").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load rates");
        }
    });
}

const openRate = (item : IRate) => {
    replyDialog.data = item;
    replyDialog.visible = true;
}

const rateCb = () => {
    load();
    replyDialog.visible = false;
}

onMounted(() => {
    load();
});
</script>