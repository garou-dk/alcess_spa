<template>
    <div>
        <div class="p-2">
            <div class="p-2">
                <InputForm
                    :errors="[]"
                    id="bank_name"
                    labelName="E-Wallet Name"
                    tag="label"
                >
                    <InputText
                        :model-value="props.data.bank_name"
                        id="bank_name"
                        type="text"
                        fluid
                        readonly
                    />
                </InputForm>
            </div>
            <div class="p-2">
                <InputForm
                    :errors="[]"
                    id="transaction_number"
                    labelName="Transaction Number"
                    tag="label"
                >
                    <InputText
                        :model-value="props.data.transaction_number"
                        id="transaction_id"
                        type="text"
                        fluid
                        readonly
                    />
                </InputForm>
            </div>
            <div>
                <p class="font-semibold">Payment Image</p>
            </div>
            <div class="flex justify-center">
                <img :src="UrlUtil.getBaseApiUrl(`admin/orders/payment-image/${props.data.order_id}`)" alt="Payment Image" class="max-lg:w-full lg:max-w-full" />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import UrlUtil from '@/utils/UrlUtil';

interface Props {
    data: IOrder;
}

const props = defineProps<Props>();

</script>