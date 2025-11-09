<template>
    <form class="flex flex-col gap-2">
        <div>
            <InputForm
                :errors="errors.bank_name"
                id="bank_name"
                labelName="Bank Name/E-Wallet*"
                tag="label"
            >
                <InputText
                    v-model="form.bank_name"
                    id="bank_name"
                    type="text"
                    placeholder="Enter bank/e-wallet name"
                    fluid
                    :invalid="errors.bank_name.length > 0"
                />
            </InputForm>
        </div>
        <div>
            <InputForm
                :errors="errors.transaction_number"
                id="transaction_number"
                labelName="Transaction Number*"
                tag="label"
            >
                <InputText
                    v-model="form.transaction_number"
                    id="transaction_number"
                    type="text"
                    placeholder="Enter transaction number"
                    fluid
                    :invalid="errors.transaction_number.length > 0"
                />
            </InputForm>
        </div>
    </form>
</template>
<script setup lang="ts">
import { IOrder } from '@/interfaces/IOrder';
import { reactive } from 'vue';
import { useToast } from 'vue-toastification';


interface Props {
    order: IOrder;
}

interface IForm {
    payment_proof: File | null;
    bank_name: string | null;
    transaction_number: string | null;
}

interface IFormError {
    payment_proof: string[];
    bank_name: string[];
    transaction_number: string[];
}

const props = defineProps<Props>();
const emit = defineEmits(["cb", "closePopup"]);
const toast = useToast();

const form = reactive<IForm>({
    payment_proof: null,
    bank_name: null,
    transaction_number: null
});

const errors = reactive<IFormError>({
    payment_proof: [],
    bank_name: [],
    transaction_number: []
});

</script>