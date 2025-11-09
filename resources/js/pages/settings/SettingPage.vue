<template>
    <div>
        <BoxShadow class="p-5">
            <form class="w-full">
                <div>
                    <InputForm
                        :errors="errors.account_number"
                        id="account_number"
                        labelName="Account Number*"
                        tag="label"
                    >
                        <InputText
                            v-model="form.account_number"
                            id="account_number"
                            type="text"
                            placeholder="Enter account number"
                            fluid
                            :invalid="errors.account_number.length > 0"
                        />
                    </InputForm>
                </div>
            </form>
        </BoxShadow>
        <input type="file" @change="onFileChange" class="hidden" />
    </div>
</template>
<script setup lang="ts">
import { event } from '@primeuix/themes/aura/timeline';
import { reactive, ref, watch } from 'vue';

interface IForm {
    account_number: string | null;
    screenshot: File | null;
}

interface IFormError {
    account_number: string[];
    screenshot: string[];
}

const form = reactive<IForm>({
    account_number: null,
    screenshot: null
});

const errors = reactive<IFormError>({
    account_number: [],
    screenshot: []
});


const onFileChange = (event: Event) => {
    const files = (event.target as HTMLInputElement).files;
    if (files && files.length > 0) {
        form.screenshot = files[0];
    }
    else {
        form.screenshot = null;
    }
}
</script>