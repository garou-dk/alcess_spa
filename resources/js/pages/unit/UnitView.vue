<template>
    <div :class="responsive.getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-3 px-3',
        tablet: 'min-h-screen bg-gray-50 py-4 px-4',
        desktop: 'min-h-screen bg-gray-50 py-4 sm:py-6 px-3 sm:px-6'
    })">
        <!-- Date and Time Display -->
        <DateTimeDisplay />

        <!-- Filters Card -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'mb-3 rounded-lg bg-white border border-gray-200 shadow-sm',
            tablet: 'mb-4 rounded-lg bg-white border border-gray-200 shadow-sm',
            desktop: 'mb-4 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm'
        })">
            <div :class="responsive.getResponsiveClasses({
                mobile: 'px-3 py-3',
                tablet: 'px-4 py-4',
                desktop: 'px-5 py-4'
            })">
                <form @submit.prevent :class="responsive.getResponsiveClasses({
                    mobile: 'space-y-4',
                    tablet: 'space-y-4 md:space-y-0 md:flex md:flex-wrap md:gap-4 md:items-end',
                    desktop: 'flex flex-wrap gap-4 items-end'
                })">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'flex-1 min-w-[200px]',
                        desktop: 'flex-1 min-w-[250px]'
                    })">
                        <label :class="responsive.getResponsiveClasses({
                            mobile: 'block text-xs font-medium text-gray-700 mb-2',
                            tablet: 'block text-sm font-medium text-gray-700 mb-2',
                            desktop: 'block text-sm font-medium text-gray-700 mb-2'
                        })">Search Unit</label>
                        <InputText
                            type="text"
                            v-model="filters.global.value"
                            placeholder="Search by unit name"
                            :class="[
                                'w-full',
                                responsive.getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                            id="search"
                        />
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full sm:w-auto sm:min-w-[120px]',
                        desktop: 'w-full sm:w-auto sm:min-w-[150px]'
                    })">
                        <label :class="responsive.getResponsiveClasses({
                            mobile: 'block text-xs font-medium text-gray-700 mb-2',
                            tablet: 'block text-sm font-medium text-gray-700 mb-2',
                            desktop: 'block text-sm font-medium text-gray-700 mb-2'
                        })">Status</label>
                        <Select
                            v-model="statusFilter"
                            :options="[
                                { label: 'Active', value: false },
                                { label: 'Inactive', value: true },
                            ]"
                            option-label="label"
                            option-value="value"
                            placeholder="All Status"
                            show-clear
                            :class="[
                                'w-full',
                                responsive.getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                        />
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full flex justify-center',
                        tablet: 'flex gap-2 ml-auto',
                        desktop: 'flex gap-2 ml-auto'
                    })">
                        <Button
                            type="button"
                            @click="showAddUnit = true"
                            icon="pi pi-plus"
                            :label="responsive.getResponsiveClasses({
                                mobile: 'Add Unit',
                                tablet: 'Add Unit',
                                desktop: 'Add Unit'
                            })"
                            :class="[
                                '!bg-green-600 hover:!bg-green-700',
                                responsive.getResponsiveClasses({
                                    mobile: 'w-full text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                        />
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Units Table/Cards Container -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            tablet: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            desktop: 'rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden'
        })">
            <div
                v-if="errorUnits"
                :class="responsive.getResponsiveClasses({
                    mobile: 'p-6 text-center',
                    tablet: 'p-8 text-center',
                    desktop: 'p-8 text-center'
                })"
            >
                <div class="mb-4">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center',
                        tablet: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center',
                        desktop: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-exclamation-triangle text-2xl text-red-600',
                            tablet: 'pi pi-exclamation-triangle text-3xl text-red-600',
                            desktop: 'pi pi-exclamation-triangle text-3xl text-red-600'
                        })"></i>
                    </div>
                </div>
                <h3 :class="responsive.getResponsiveClasses({
                    mobile: 'mb-2 text-base font-semibold text-red-800',
                    tablet: 'mb-2 text-lg font-semibold text-red-800',
                    desktop: 'mb-2 text-lg font-semibold text-red-800'
                })">Failed to Load Units</h3>
                <p :class="responsive.getResponsiveClasses({
                    mobile: 'mb-4 text-sm text-red-600',
                    tablet: 'mb-4 text-sm text-red-600',
                    desktop: 'mb-4 text-sm text-red-600'
                })">{{ errorUnits }}</p>
                <Button
                    label="Retry"
                    icon="pi pi-refresh"
                    @click="retryUnits"
                    class="!bg-red-600 hover:!bg-red-700"
                />
            </div>
            <div v-else class="w-full">
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'flex items-center justify-between px-3 py-3 border-b border-gray-200 bg-gray-50',
                    tablet: 'flex items-center justify-between px-4 py-4 border-b border-gray-200 bg-gray-50',
                    desktop: 'flex items-center justify-between px-5 py-4 border-b border-gray-200'
                })">
                    <div>
                        <h1 :class="responsive.getResponsiveClasses({
                            mobile: 'text-base font-bold text-gray-800',
                            tablet: 'text-lg font-bold text-gray-800',
                            desktop: 'text-lg font-semibold text-gray-800'
                        })">All Units</h1>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">Manage product measurement units</p>
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'flex flex-col items-end',
                        tablet: 'flex flex-col items-end',
                        desktop: 'flex items-center gap-2 text-sm text-gray-600'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'hidden',
                            tablet: 'hidden',
                            desktop: 'pi pi-box text-blue-600'
                        })"></i>
                        <span :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-600 font-medium',
                            tablet: 'text-xs text-gray-600 font-medium',
                            desktop: 'font-medium'
                        })">{{ showItems().length }} Units</span>
                    </div>
                </div>
                
                <!-- Desktop DataTable -->
                <DataTable
                    v-if="responsive.isDesktop.value"
                    class="w-full"
                    :value="showItems()"
                    :paginator="true"
                    :rows="5"
                    v-model:filters="filters"
                    columnResizeMode="expand"
                    stripedRows
                >
                    <Column field="unit_name" header="Unit Name" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3 py-1">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100">
                                    <i class="pi pi-box text-blue-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">
                                        {{ data.unit_name }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="deleted_at" header="Status" :sortable="true">
                        <template #body="{ data }">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                :class="!data.deleted_at ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                            >
                                <i :class="!data.deleted_at ? 'pi pi-check-circle' : 'pi pi-times-circle'" class="mr-1"></i>
                                {{ !data.deleted_at ? "Active" : "Inactive" }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <Button
                                    type="button"
                                    @click="openEditUnit(data)"
                                    icon="pi pi-pencil"
                                    class="!bg-blue-600 hover:!bg-blue-700 !text-white !rounded-full !w-10 !h-10 !p-0 !border-0"
                                    v-tooltip.top="'Edit'"
                                />
                                <Button
                                    type="button"
                                    @click="toggleUnitStatus(data)"
                                    :icon="data.deleted_at ? 'pi pi-check-circle' : 'pi pi-times-circle'"
                                    :class="[
                                        '!rounded-full !w-10 !h-10 !p-0 !border-0',
                                        data.deleted_at ? '!bg-green-600 hover:!bg-green-700 !text-white' : '!bg-red-600 hover:!bg-red-700 !text-white'
                                    ]"
                                    v-tooltip.top="data.deleted_at ? 'Activate' : 'Deactivate'"
                                />
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-8">
                            <i class="pi pi-box text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">No units found</p>
                            <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                        </div>
                    </template>
                </DataTable>

                <!-- Mobile/Tablet Card Layout -->
                <div v-else>
                    <div v-if="paginatedData.length === 0 && !loadingUnits" :class="responsive.getResponsiveClasses({
                        mobile: 'text-center py-8',
                        tablet: 'text-center py-12',
                        desktop: 'text-center py-12'
                    })">
                        <div class="mb-4">
                            <div :class="responsive.getResponsiveClasses({
                                mobile: 'mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center',
                                tablet: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center',
                                desktop: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center'
                            })">
                                <i :class="responsive.getResponsiveClasses({
                                    mobile: 'pi pi-box text-3xl text-gray-400',
                                    tablet: 'pi pi-box text-4xl text-gray-400',
                                    desktop: 'pi pi-box text-4xl text-gray-400'
                                })"></i>
                            </div>
                        </div>
                        <h3 :class="responsive.getResponsiveClasses({
                            mobile: 'text-base font-semibold text-gray-700 mb-2',
                            tablet: 'text-lg font-semibold text-gray-700 mb-2',
                            desktop: 'text-lg font-semibold text-gray-700 mb-2'
                        })">No units found</h3>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-sm text-gray-500',
                            tablet: 'text-sm text-gray-500',
                            desktop: 'text-sm text-gray-500'
                        })">Try adjusting your filters</p>
                    </div>
                    <div v-else>
                        <!-- Cards Container -->
                        <div :class="responsive.getResponsiveClasses({
                            mobile: 'space-y-3 p-3',
                            tablet: 'space-y-4 p-4',
                            desktop: 'space-y-4 p-4'
                        })">
                            <div 
                                v-for="(unit, index) in paginatedData" 
                                :key="unit.unit_id"
                                :class="responsive.getResponsiveClasses({
                                    mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm',
                                    tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm',
                                    desktop: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm'
                                })"
                            >
                                <!-- Unit Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div :class="responsive.getResponsiveClasses({
                                            mobile: 'flex items-center justify-center w-10 h-10 rounded-full bg-blue-100',
                                            tablet: 'flex items-center justify-center w-12 h-12 rounded-full bg-blue-100',
                                            desktop: 'flex items-center justify-center w-12 h-12 rounded-full bg-blue-100'
                                        })">
                                            <i :class="responsive.getResponsiveClasses({
                                                mobile: 'pi pi-box text-blue-600',
                                                tablet: 'pi pi-box text-blue-600 text-lg',
                                                desktop: 'pi pi-box text-blue-600 text-lg'
                                            })"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 :class="responsive.getResponsiveClasses({
                                                mobile: 'font-semibold text-gray-900 truncate text-sm',
                                                tablet: 'font-semibold text-gray-900 truncate text-base',
                                                desktop: 'font-semibold text-gray-900 truncate text-base'
                                            })">
                                                {{ unit.unit_name }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>

                                <!-- Unit Details -->
                                <div :class="responsive.getResponsiveClasses({
                                    mobile: 'flex items-center justify-between pt-3 border-t border-gray-100',
                                    tablet: 'flex items-center justify-between pt-3 border-t border-gray-100',
                                    desktop: 'flex items-center justify-between pt-3 border-t border-gray-100'
                                })">
                                    <div>
                                        <p :class="responsive.getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600 mb-1',
                                            tablet: 'text-sm text-gray-600 mb-1',
                                            desktop: 'text-sm text-gray-600 mb-1'
                                        })">Status:</p>
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                            :class="!unit.deleted_at ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                                        >
                                            <i :class="!unit.deleted_at ? 'pi pi-check-circle' : 'pi pi-times-circle'" class="mr-1"></i>
                                            {{ !unit.deleted_at ? "Active" : "Inactive" }}
                                        </span>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center gap-2">
                                        <Button
                                            type="button"
                                            @click="openEditUnit(unit)"
                                            icon="pi pi-pencil"
                                            :class="[
                                                '!bg-blue-600 hover:!bg-blue-700 !text-white !rounded-full !border-0',
                                                responsive.getResponsiveClasses({
                                                    mobile: '!w-8 !h-8 !p-0 !text-sm',
                                                    tablet: '!w-10 !h-10 !p-0',
                                                    desktop: '!w-10 !h-10 !p-0'
                                                })
                                            ]"
                                            v-tooltip.top="'Edit'"
                                        />
                                        <Button
                                            type="button"
                                            @click="toggleUnitStatus(unit)"
                                            :icon="unit.deleted_at ? 'pi pi-check-circle' : 'pi pi-times-circle'"
                                            :class="[
                                                '!rounded-full !border-0',
                                                responsive.getResponsiveClasses({
                                                    mobile: '!w-8 !h-8 !p-0 !text-sm',
                                                    tablet: '!w-10 !h-10 !p-0',
                                                    desktop: '!w-10 !h-10 !p-0'
                                                }),
                                                unit.deleted_at ? '!bg-green-600 hover:!bg-green-700 !text-white' : '!bg-red-600 hover:!bg-red-700 !text-white'
                                            ]"
                                            v-tooltip.top="unit.deleted_at ? 'Activate' : 'Deactivate'"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile/Tablet Pagination Controls -->
                        <div v-if="totalPages > 1" :class="responsive.getResponsiveClasses({
                            mobile: 'flex items-center justify-between px-3 py-3 border-t border-gray-200 bg-gray-50',
                            tablet: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50',
                            desktop: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50'
                        })">
                            <div :class="responsive.getResponsiveClasses({
                                mobile: 'text-xs text-gray-600',
                                tablet: 'text-sm text-gray-600',
                                desktop: 'text-sm text-gray-600'
                            })">
                                Showing {{ ((currentPage - 1) * rowsPerPage) + 1 }} to {{ Math.min(currentPage * rowsPerPage, filteredData.length) }} of {{ filteredData.length }} units
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    icon="pi pi-chevron-left"
                                    :disabled="currentPage === 1"
                                    @click="currentPage--"
                                    :class="[
                                        'p-button-text p-button-sm',
                                        responsive.getResponsiveClasses({
                                            mobile: '!text-xs !p-1',
                                            tablet: '!text-sm !p-2',
                                            desktop: '!text-sm !p-2'
                                        })
                                    ]"
                                />
                                <span :class="responsive.getResponsiveClasses({
                                    mobile: 'text-xs font-medium text-gray-700 px-2',
                                    tablet: 'text-sm font-medium text-gray-700 px-3',
                                    desktop: 'text-sm font-medium text-gray-700 px-3'
                                })">
                                    {{ currentPage }} of {{ totalPages }}
                                </span>
                                <Button
                                    icon="pi pi-chevron-right"
                                    :disabled="currentPage === totalPages"
                                    @click="currentPage++"
                                    :class="[
                                        'p-button-text p-button-sm',
                                        responsive.getResponsiveClasses({
                                            mobile: '!text-xs !p-1',
                                            tablet: '!text-sm !p-2',
                                            desktop: '!text-sm !p-2'
                                        })
                                    ]"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Dialog
            v-model:visible="showAddUnit"
            modal
            header="Add New Unit"
            :dismissableMask="true"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <AddUnitForm @cb="loadCb" />
        </Dialog>
        <Dialog
            v-model:visible="showEditUnit"
            modal
            header="Update Unit"
            :dismissableMask="true"
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
import { useResponsive } from "@/composables/useResponsive";
import { computed, onMounted, ref, watch } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { UnitInterface } from "@/interfaces/UnitInterface";
import AddUnitForm from "@/components/forms/AddUnitForm.vue";
import EditUnitForm from "@/components/forms/EditUnitForm.vue";
import DateTimeDisplay from "@/components/DateTimeDisplay.vue";
import TableSkeleton from "@/components/skeletons/TableSkeleton.vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";

const unitStore = useUnitStore();
const responsive = useResponsive();
const selectedItem = ref<UnitInterface | null>(null);
const showAddUnit = ref<boolean>(false);
const showEditUnit = ref<boolean>(false);
const toast = useToast();
const statusService = useAxiosUtil<null, null>();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const statusFilter = ref<boolean | null>(null);

// Pagination for mobile/tablet card layout
const currentPage = ref(1);
const rowsPerPage = ref(10);

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

// Computed properties for mobile/tablet pagination
const filteredData = computed(() => {
    return showItems();
});

const totalPages = computed(() => {
    return Math.ceil(filteredData.value.length / rowsPerPage.value);
});

const paginatedData = computed(() => {
    if (responsive.isDesktop.value) {
        return filteredData.value; // Desktop uses DataTable pagination
    }
    const start = (currentPage.value - 1) * rowsPerPage.value;
    const end = start + rowsPerPage.value;
    return filteredData.value.slice(start, end);
});

const openEditUnit = (unit: UnitInterface) => {
    selectedItem.value = unit;
    showEditUnit.value = true;
};

const toggleUnitStatus = async (unit: UnitInterface) => {
    if (statusService.request.loading) {
        return;
    }
    
    if (!unit.deleted_at) {
        await statusService.deleteRequest(`admin/units/${unit.unit_id}`, null).then(() => {
            if (statusService.request.status === 200) {
                toast.success(statusService.request.message);
                unitStore.fetchUnits();
            } else {
                toast.error(statusService.request.message ?? "Please try again.");
            }
        });
    } else {
        await statusService.patch(`admin/units/recover/${unit.unit_id}`, null).then(() => {
            if (statusService.request.status === 200) {
                toast.success(statusService.request.message);
                unitStore.fetchUnits();
            } else {
                toast.error(statusService.request.message ?? "Please try again.");
            }
        });
    }
};

const loadingUnits = ref(false);
const errorUnits = ref<string | null>(null);

const retryUnits = async () => {
    errorUnits.value = null;
    await fetchUnits();
};

const fetchUnits = async () => {
    if (unitStore.units.length === 0) {
        loadingUnits.value = true;
        errorUnits.value = null;
        try {
            await unitStore.fetchUnits();
        } catch (error) {
            errorUnits.value = 'Failed to load units. Please try again.';
        } finally {
            loadingUnits.value = false;
        }
    }
};

const loadCb = async () => {
    showAddUnit.value = false;
    showEditUnit.value = false;
    await unitStore.fetchUnits();
};

// Watchers for mobile pagination reset
watch(
    () => statusFilter.value,
    () => {
        currentPage.value = 1;
    },
);

watch(
    () => filters.value.global.value,
    () => {
        currentPage.value = 1;
    },
);

watch(
    () => unitStore.units,
    () => {
        currentPage.value = 1;
    },
);

onMounted(() => {
    fetchUnits();
});
</script>
