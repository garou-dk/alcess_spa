<template>
    <div class="p-4">
        <!-- Add flow: only one specification allowed — hide Add button after 1 is added -->
        <div v-if="specifications.length < 1" class="flex justify-end mb-4">
            <Button
                type="button"
                icon="pi pi-plus"
                label="Add Specification"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                @click="showAddSpecificationForm()"
            />
        </div>

        <!-- Specifications List -->
        <div v-if="specifications.length > 0" class="space-y-2 mb-4">
            <div
                v-for="(spec, index) in specifications"
                :key="index"
                class="bg-white rounded-lg border border-gray-200 p-4 flex items-center justify-between"
            >
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ spec.specification_name }}</p>
                    <p class="text-sm text-gray-600">{{ spec.specification_value }}</p>
                </div>
                <div class="flex gap-2">
                    <Button
                        type="button"
                        icon="pi pi-pencil"
                        class="!bg-blue-500 hover:!bg-blue-600 !text-white"
                        @click="editSpecification(index)"
                        v-tooltip.top="'Edit'"
                    />
                    <Button
                        type="button"
                        icon="pi pi-trash"
                        class="!bg-red-500 hover:!bg-red-600 !text-white"
                        @click="removeSpecification(index)"
                        v-tooltip.top="'Delete'"
                    />
                </div>
            </div>
        </div>

        <div v-else class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-gray-200">
            <i class="pi pi-list text-4xl mb-2"></i>
            <p>No specifications added yet</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between mt-6">
            <Button
                type="button"
                label="Back"
                icon="pi pi-arrow-left"
                class="!bg-gray-500 hover:!bg-gray-600 !text-white"
                @click="$emit('back')"
            />
            <Button
                type="button"
                label="Next"
                icon="pi pi-arrow-right"
                iconPos="right"
                class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                @click="handleNext"
            />
        </div>

        <!-- Add/Edit Specification Dialog -->
        <Dialog
            v-model:visible="specificationDialog"
            :header="editingIndex !== null ? 'Edit Specification' : 'Add Specification'"
            modal
            :dismissableMask="true"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :pt="{
                header: { class: '!bg-blue-600 !text-white' },
                closeButton: { class: '!text-white hover:!bg-blue-700 !border-white' },
                closeButtonIcon: { class: '!text-white' }
            }"
        >
            <form @submit.prevent="saveSpecification" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specification Name*</label>
                    <InputText
                        v-model="currentSpec.specification_name"
                        placeholder="e.g., Processor, RAM, Storage"
                        class="w-full"
                        :invalid="specErrors.specification_name.length > 0"
                    />
                    <p v-if="specErrors.specification_name.length > 0" class="text-xs text-red-600 mt-1">
                        {{ specErrors.specification_name[0] }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specification Value*</label>
                    <InputText
                        v-model="currentSpec.specification_value"
                        placeholder="e.g., Intel Core i7, 16GB, 512GB SSD"
                        class="w-full"
                        :invalid="specErrors.specification_value.length > 0"
                    />
                    <p v-if="specErrors.specification_value.length > 0" class="text-xs text-red-600 mt-1">
                        {{ specErrors.specification_value[0] }}
                    </p>
                </div>
                <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
                    <Button
                        type="button"
                        label="Cancel"
                        class="!bg-gray-500 hover:!bg-gray-600 !text-white"
                        @click="specificationDialog = false"
                    />
                    <Button
                        type="submit"
                        :label="editingIndex !== null ? 'Update' : 'Add'"
                        class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                    />
                </div>
            </form>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import { inject, reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';

const emit = defineEmits(['next', 'back']);
const toast = useToast();

const tempSpecifications = inject<any>('tempSpecifications');
const specifications = ref<any[]>(tempSpecifications?.value || []);

const specificationDialog = ref(false);
const editingIndex = ref<number | null>(null);
const currentSpec = reactive({
    specification_name: '',
    specification_value: ''
});

const specErrors = reactive({
    specification_name: [] as string[],
    specification_value: [] as string[]
});

const showAddSpecificationForm = () => {
    editingIndex.value = null;
    currentSpec.specification_name = '';
    currentSpec.specification_value = '';
    specErrors.specification_name = [];
    specErrors.specification_value = [];
    specificationDialog.value = true;
};

const editSpecification = (index: number) => {
    editingIndex.value = index;
    const spec = specifications.value[index];
    currentSpec.specification_name = spec.specification_name;
    currentSpec.specification_value = spec.specification_value;
    specErrors.specification_name = [];
    specErrors.specification_value = [];
    specificationDialog.value = true;
};

const saveSpecification = () => {
    // Validate
    specErrors.specification_name = [];
    specErrors.specification_value = [];
    
    if (!currentSpec.specification_name) {
        specErrors.specification_name.push('Specification name is required');
    }
    if (!currentSpec.specification_value) {
        specErrors.specification_value.push('Specification value is required');
    }
    
    if (specErrors.specification_name.length > 0 || specErrors.specification_value.length > 0) {
        return;
    }
    
    if (editingIndex.value !== null) {
        // Update existing
        specifications.value[editingIndex.value] = {
            specification_name: currentSpec.specification_name,
            specification_value: currentSpec.specification_value
        };
        toast.success('Specification updated');
    } else {
        // Add new
        specifications.value.push({
            specification_name: currentSpec.specification_name,
            specification_value: currentSpec.specification_value
        });
        toast.success('Specification added');
    }
    
    specificationDialog.value = false;
};

const removeSpecification = (index: number) => {
    specifications.value.splice(index, 1);
    toast.success('Specification removed');
};

const handleNext = () => {
    // Update the injected ref
    if (tempSpecifications) {
        tempSpecifications.value = specifications.value;
    }
    emit('next');
};
</script>
