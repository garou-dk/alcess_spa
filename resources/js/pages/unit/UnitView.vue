<template>
    <div>
        <BoxShadow class="mb-5">
            <div class="flex w-full">
                <form action="" class="flex w-full flex-wrap">
                    <div class="grow p-2">
                        <InputForm
                            :errors="[]"
                            label-name="Search"
                            tag="label"
                            id="search"
                        >
                            <InputText
                                v-model="filters.global.value"
                                id="search"
                                placeholder="Search"
                                fluid
                            />
                        </InputForm>
                    </div>
                    <div class="p-2">
                        <InputForm
                            :errors="[]"
                            id="status"
                            label-name="Status"
                            tag="span"
                        >
                            <Select
                                v-model="statusFilter"
                                :options="[
                                    { label: 'Active', value: false },
                                    { label: 'Inactive', value: true },
                                ]"
                                option-label="label"
                                option-value="value"
                                label-id="status"
                                placeholder="Select Status"
                                show-clear
                            />
                        </InputForm>
                    </div>
                    <div class="flex items-end p-2">
                        <Button
                            type="button"
                            @click="showAddUnit = true"
                            label="Add Unit"
                            icon="pi pi-plus"
                            class="primary-bg"
                        />
                    </div>
                </form>
            </div>
        </BoxShadow>
        <BoxShadow>
            <div class="w-full p-5">
                <DataTable
                    :value="showItems()"
                    :loading="unitStore.loadUnitService.request.loading"
                    :paginator="true"
                    :rows="5"
                    v-model:filters="filters"
                >
                    <Column field="unit_name" header="Unit Name" />
                    <Column field="deleted_at" header="Status">
                        <template #body="{ data }">
                            <span
                                class="rounded-full px-2 py-1 text-white"
                                :class="
                                    !data.deleted_at
                                        ? 'bg-green-500'
                                        : 'bg-red-500'
                                "
                            >
                                {{ !data.deleted_at ? "Active" : "Inactive" }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Action">
                        <template #body="{ data }">
                            <Button
                                type="button"
                                @click="openActionElement($event, data)"
                                icon="pi pi-ellipsis-h"
                                text
                                raised
                                class="text-black!"
                            />
                        </template>
                    </Column>
                    <template #empty>
                        <p>
                            <span
                                v-if="
                                    !unitStore.loadUnitService.request.loading
                                "
                                >No result found</span
                            >
                            <span v-else>Loading...</span>
                        </p>
                    </template>
                </DataTable>
            </div>
        </BoxShadow>
        <Popover
            ref="actionElement"
            :dismissable="true"
            class="mr-5 inline-flex"
        >
            <div v-if="selectedItem">
                <button
                    type="button"
                    @click="
                        showEditUnit = true;
                        actionElement.hide();
                    "
                    class="flex w-full cursor-pointer items-center gap-2 rounded p-2 text-sm hover:bg-gray-300"
                >
                    <i class="pi pi-clipboard" />
                    Edit
                </button>
                <ChangeUnitStatusForm
                    v-if="selectedItem"
                    @cb="changeStatusFormCb"
                    :data="selectedItem"
                />
            </div>
        </Popover>
        <Dialog
            v-model:visible="showAddUnit"
            modal
            header="Add new unit"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <AddUnitForm @cb="loadCb" />
        </Dialog>
        <Dialog
            v-model:visible="showEditUnit"
            modal
            header="Update unit"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <EditUnitForm
                v-if="selectedItem"
                :data="selectedItem"
                @cb="loadCb"
            />
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import { useUnitStore } from "@/stores/UnitState";
import { computed, onMounted, ref } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { UnitInterface } from "@/interfaces/UnitInterface";
import { Popover } from "primevue";
import AddUnitForm from "@/components/forms/AddUnitForm.vue";
import EditUnitForm from "@/components/forms/EditUnitForm.vue";
import ChangeUnitStatusForm from "@/components/forms/ChangeUnitStatusForm.vue";

const unitStore = useUnitStore();
const selectedItem = ref<UnitInterface | null>(null);
const showAddUnit = ref<boolean>(false);
const showEditUnit = ref<boolean>(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const statusFilter = ref<boolean | null>(null);
const actionElement = ref<null | InstanceType<typeof Popover>>();
const deletedItems = computed(() =>
    unitStore.units.filter((item) => item.deleted_at !== null),
);
const activeItems = computed(() =>
    unitStore.units.filter((item) => item.deleted_at === null),
);

const showItems = () => {
    if (statusFilter.value === true) {
        return deletedItems.value;
    } else if (statusFilter.value === false) {
        return activeItems.value;
    } else {
        return unitStore.units;
    }
};

const openActionElement = (event: Event, value: UnitInterface) => {
    if (actionElement.value) {
        actionElement.value.toggle(event);
        selectedItem.value = value;
    }
};

const fetchUnits = async () => {
    if (
        unitStore.units.length === 0 &&
        !unitStore.loadUnitService.request.loading
    ) {
        await unitStore.fetchUnits();
    }
};

const loadCb = async () => {
    showAddUnit.value = false;
    showEditUnit.value = false;
    await unitStore.fetchUnits();
};

const changeStatusFormCb = async () => {
    actionElement.value.hide();
    await unitStore.fetchUnits();
};

onMounted(() => {
    fetchUnits();
});
</script>
