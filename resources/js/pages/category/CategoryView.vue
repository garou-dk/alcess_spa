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
                        })">Search Category</label>
                        <InputText
                            type="text"
                            v-model="filters.global.value"
                            placeholder="Search by category name"
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
                            @click="showAddCategory = true"
                            icon="pi pi-plus"
                            :label="responsive.getResponsiveClasses({
                                mobile: 'Add Category',
                                tablet: 'Add Category',
                                desktop: 'Add Category'
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
        
        <!-- Categories Table/Cards Container -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            tablet: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            desktop: 'rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden'
        })">
            <div
                v-if="errorCategories"
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
                })">Failed to Load Categories</h3>
                <p :class="responsive.getResponsiveClasses({
                    mobile: 'mb-4 text-sm text-red-600',
                    tablet: 'mb-4 text-sm text-red-600',
                    desktop: 'mb-4 text-sm text-red-600'
                })">{{ errorCategories }}</p>
                <Button
                    label="Retry"
                    icon="pi pi-refresh"
                    @click="retryCategories"
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
                        })">All Categories</h1>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">Manage product categories</p>
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'flex flex-col items-end',
                        tablet: 'flex flex-col items-end',
                        desktop: 'flex items-center gap-2 text-sm text-gray-600'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'hidden',
                            tablet: 'hidden',
                            desktop: 'pi pi-tag text-blue-600'
                        })"></i>
                        <span :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-600 font-medium',
                            tablet: 'text-xs text-gray-600 font-medium',
                            desktop: 'font-medium'
                        })">{{ showItems().length }} Categories</span>
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
                    <Column field="category_name" header="Category" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3 py-1">
                                <div v-if="data.category_image" class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                    <img
                                        :src="UrlUtil.getBaseAppUrl(`storage/images/category/${data.category_image}`)"
                                        :alt="data.category_name"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <Avatar
                                    v-else
                                    shape="circle"
                                    icon="pi pi-tag"
                                    class="bg-blue-100 text-blue-600"
                                    size="large"
                                />
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">
                                        {{ data.category_name }}
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
                                    @click="openEditCategory(data)"
                                    icon="pi pi-pencil"
                                    class="!bg-blue-600 hover:!bg-blue-700 !text-white !rounded-full !w-10 !h-10 !p-0 !border-0"
                                    v-tooltip.top="'Edit Name'"
                                />
                                <Button
                                    type="button"
                                    @click="openChangeImage(data)"
                                    icon="pi pi-image"
                                    class="!bg-purple-600 hover:!bg-purple-700 !text-white !rounded-full !w-10 !h-10 !p-0 !border-0"
                                    v-tooltip.top="'Change Image'"
                                />
                                <Button
                                    type="button"
                                    @click="toggleCategoryStatus(data)"
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
                            <i class="pi pi-tag text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">No categories found</p>
                            <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                        </div>
                    </template>
                </DataTable>

                <!-- Mobile/Tablet Card Layout -->
                <div v-else>
                    <div v-if="paginatedData.length === 0 && !loadingCategories" :class="responsive.getResponsiveClasses({
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
                                    mobile: 'pi pi-tag text-3xl text-gray-400',
                                    tablet: 'pi pi-tag text-4xl text-gray-400',
                                    desktop: 'pi pi-tag text-4xl text-gray-400'
                                })"></i>
                            </div>
                        </div>
                        <h3 :class="responsive.getResponsiveClasses({
                            mobile: 'text-base font-semibold text-gray-700 mb-2',
                            tablet: 'text-lg font-semibold text-gray-700 mb-2',
                            desktop: 'text-lg font-semibold text-gray-700 mb-2'
                        })">No categories found</h3>
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
                                v-for="(category, index) in paginatedData" 
                                :key="category.category_id"
                                :class="responsive.getResponsiveClasses({
                                    mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm',
                                    tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm',
                                    desktop: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm'
                                })"
                            >
                                <!-- Category Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div v-if="category.category_image" :class="responsive.getResponsiveClasses({
                                            mobile: 'w-10 h-10 rounded-full overflow-hidden flex-shrink-0',
                                            tablet: 'w-12 h-12 rounded-full overflow-hidden flex-shrink-0',
                                            desktop: 'w-12 h-12 rounded-full overflow-hidden flex-shrink-0'
                                        })">
                                            <img
                                                :src="UrlUtil.getBaseAppUrl(`storage/images/category/${category.category_image}`)"
                                                :alt="category.category_name"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        <Avatar
                                            v-else
                                            shape="circle"
                                            icon="pi pi-tag"
                                            :class="responsive.getResponsiveClasses({
                                                mobile: 'w-10 h-10 bg-blue-100 text-blue-600',
                                                tablet: 'w-12 h-12 bg-blue-100 text-blue-600',
                                                desktop: 'w-12 h-12 bg-blue-100 text-blue-600'
                                            })"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <h3 :class="responsive.getResponsiveClasses({
                                                mobile: 'font-semibold text-gray-900 truncate text-sm',
                                                tablet: 'font-semibold text-gray-900 truncate text-base',
                                                desktop: 'font-semibold text-gray-900 truncate text-base'
                                            })">
                                                {{ category.category_name }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category Details -->
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
                                            :class="!category.deleted_at ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                                        >
                                            <i :class="!category.deleted_at ? 'pi pi-check-circle' : 'pi pi-times-circle'" class="mr-1"></i>
                                            {{ !category.deleted_at ? "Active" : "Inactive" }}
                                        </span>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center gap-2">
                                        <Button
                                            type="button"
                                            @click="openEditCategory(category)"
                                            icon="pi pi-pencil"
                                            :class="[
                                                '!bg-blue-600 hover:!bg-blue-700 !text-white !rounded-full !border-0',
                                                responsive.getResponsiveClasses({
                                                    mobile: '!w-8 !h-8 !p-0 !text-sm',
                                                    tablet: '!w-10 !h-10 !p-0',
                                                    desktop: '!w-10 !h-10 !p-0'
                                                })
                                            ]"
                                            v-tooltip.top="'Edit Name'"
                                        />
                                        <Button
                                            type="button"
                                            @click="openChangeImage(category)"
                                            icon="pi pi-image"
                                            :class="[
                                                '!bg-purple-600 hover:!bg-purple-700 !text-white !rounded-full !border-0',
                                                responsive.getResponsiveClasses({
                                                    mobile: '!w-8 !h-8 !p-0 !text-sm',
                                                    tablet: '!w-10 !h-10 !p-0',
                                                    desktop: '!w-10 !h-10 !p-0'
                                                })
                                            ]"
                                            v-tooltip.top="'Change Image'"
                                        />
                                        <Button
                                            type="button"
                                            @click="toggleCategoryStatus(category)"
                                            :icon="category.deleted_at ? 'pi pi-check-circle' : 'pi pi-times-circle'"
                                            :class="[
                                                '!rounded-full !border-0',
                                                responsive.getResponsiveClasses({
                                                    mobile: '!w-8 !h-8 !p-0 !text-sm',
                                                    tablet: '!w-10 !h-10 !p-0',
                                                    desktop: '!w-10 !h-10 !p-0'
                                                }),
                                                category.deleted_at ? '!bg-green-600 hover:!bg-green-700 !text-white' : '!bg-red-600 hover:!bg-red-700 !text-white'
                                            ]"
                                            v-tooltip.top="category.deleted_at ? 'Activate' : 'Deactivate'"
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
                                Showing {{ ((currentPage - 1) * rowsPerPage) + 1 }} to {{ Math.min(currentPage * rowsPerPage, filteredData.length) }} of {{ filteredData.length }} categories
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
            v-model:visible="showAddCategory"
            modal
            header="Add New Category"
            :dismissableMask="true"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :pt="{
                header: { class: '!bg-blue-600 !text-white' },
                closeButton: { class: '!text-white hover:!bg-blue-700 !border-white' },
                closeButtonIcon: { class: '!text-white' }
            }"
        >
            <AddCategoryForm @cb="loadCb" />
        </Dialog>
        <Dialog
            v-model:visible="showEditCategory"
            modal
            header="Update Category Name"
            :dismissableMask="true"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :pt="{
                header: { class: '!bg-blue-600 !text-white' },
                closeButton: { class: '!text-white hover:!bg-blue-700 !border-white' },
                closeButtonIcon: { class: '!text-white' }
            }"
        >
            <EditCategoryForm
                v-if="selectedItem"
                :data="selectedItem"
                @cb="loadCb"
            />
        </Dialog>
        <Dialog
            v-model:visible="showChangeImage"
            modal
            header="Change Category Image"
            :dismissableMask="true"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :pt="{
                header: { class: '!bg-blue-600 !text-white' },
                closeButton: { class: '!text-white hover:!bg-blue-700 !border-white' },
                closeButtonIcon: { class: '!text-white' }
            }"
        >
            <EditCategoryImageForm
                v-if="selectedItem"
                :data="selectedItem"
                @cb="loadCb"
            />
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import { CategoryInterface } from "@/interfaces/CategoryInterface";
import { useCategoryStore } from "@/stores/CategoryState";
import { useResponsive } from "@/composables/useResponsive";
import { computed, onMounted, ref, watch } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import AddCategoryForm from "@/components/forms/AddCategoryForm.vue";
import UrlUtil from "@/utils/UrlUtil";
import EditCategoryForm from "@/components/forms/EditCategoryForm.vue";
import EditCategoryImageForm from "@/components/forms/EditCategoryImageForm.vue";
import DateTimeDisplay from "@/components/DateTimeDisplay.vue";
import TableSkeleton from "@/components/skeletons/TableSkeleton.vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";

const categoryStore = useCategoryStore();
const responsive = useResponsive();
const selectedItem = ref<CategoryInterface | null>(null);
const showAddCategory = ref<boolean>(false);
const showEditCategory = ref<boolean>(false);
const showChangeImage = ref<boolean>(false);
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
    categoryStore.categories.filter((item) => item.deleted_at !== null),
);
const activeItems = computed(() =>
    categoryStore.categories.filter((item) => item.deleted_at === null),
);

const showItems = () => {
    if (statusFilter.value === true) {
        return deletedItems.value;
    } else if (statusFilter.value === false) {
        return activeItems.value;
    } else {
        return categoryStore.categories;
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

const openEditCategory = (category: CategoryInterface) => {
    selectedItem.value = category;
    showEditCategory.value = true;
};

const openChangeImage = (category: CategoryInterface) => {
    selectedItem.value = category;
    showChangeImage.value = true;
};

const toggleCategoryStatus = async (category: CategoryInterface) => {
    if (statusService.request.loading) {
        return;
    }
    
    if (!category.deleted_at) {
        await statusService.deleteRequest(`admin/categories/${category.category_id}`, null).then(() => {
            if (statusService.request.status === 200) {
                toast.success(statusService.request.message);
                categoryStore.fetchCategories();
            } else {
                toast.error(statusService.request.message ?? "Please try again.");
            }
        });
    } else {
        await statusService.patch(`admin/categories/recover/${category.category_id}`, null).then(() => {
            if (statusService.request.status === 200) {
                toast.success(statusService.request.message);
                categoryStore.fetchCategories();
            } else {
                toast.error(statusService.request.message ?? "Please try again.");
            }
        });
    }
};

const loadingCategories = ref(false);
const errorCategories = ref<string | null>(null);

const retryCategories = async () => {
    errorCategories.value = null;
    await fetchCategories();
};

const fetchCategories = async () => {
    if (categoryStore.categories.length === 0) {
        loadingCategories.value = true;
        errorCategories.value = null;
        try {
            await categoryStore.fetchCategories();
        } catch (error) {
            errorCategories.value = 'Failed to load categories. Please try again.';
        } finally {
            loadingCategories.value = false;
        }
    }
};

const loadCb = async () => {
    showAddCategory.value = false;
    showEditCategory.value = false;
    showChangeImage.value = false;
    await categoryStore.fetchCategories();
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
    () => categoryStore.categories,
    () => {
        currentPage.value = 1;
    },
);

onMounted(() => {
    fetchCategories();
});
</script>
