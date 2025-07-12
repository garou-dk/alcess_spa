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
                            @click="showAddCategory = true"
                            label="Add Category"
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
                    :loading="categoryStore.loadCategoryService.request.loading"
                    :paginator="true"
                    :rows="5"
                    v-model:filters="filters"
                >
                    <Column field="category_name" header="Category Name">
                        <template #body="{ data }">
                            <div class="flex items-center">
                                <Avatar
                                    v-if="data.category_image"
                                    shape="circle"
                                    :image="
                                        UrlUtil.getBaseAppUrl(
                                            `storage/images/category/${data.category_image}`,
                                        )
                                    "
                                    class="aspect-square!"
                                />
                                <Avatar
                                    v-else
                                    shape="circle"
                                    icon="pi pi-camera"
                                />
                                <div class="ml-2 shrink">
                                    {{ data.category_name }}
                                </div>
                            </div>
                        </template>
                    </Column>
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
                                    !categoryStore.loadCategoryService.request
                                        .loading
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
                        showEditCategory = true;
                        actionElement.hide();
                    "
                    class="flex w-full cursor-pointer items-center gap-2 rounded p-2 text-sm hover:bg-gray-300"
                >
                    <i class="pi pi-clipboard" />
                    Edit
                </button>
                <button
                    type="button"
                    @click="
                        showChangeImage = true;
                        actionElement.hide();
                    "
                    class="flex w-full cursor-pointer items-center gap-2 rounded p-2 text-sm hover:bg-gray-300"
                >
                    <i class="pi pi-image" />
                    Change Image
                </button>
                <CategoryChangeStatus
                    v-if="selectedItem"
                    @cb="changeStatusFormCb"
                    :data="selectedItem"
                />
            </div>
        </Popover>
        <Dialog
            v-model:visible="showAddCategory"
            modal
            header="Add new category"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <AddCategoryForm @cb="loadCb" />
        </Dialog>
        <Dialog
            v-model:visible="showEditCategory"
            modal
            header="Update category name"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
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
            header="Change category image"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
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
import { computed, onMounted, ref } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { Popover } from "primevue";
import AddCategoryForm from "@/components/forms/AddCategoryForm.vue";
import UrlUtil from "@/utils/UrlUtil";
import EditCategoryForm from "@/components/forms/EditCategoryForm.vue";
import EditCategoryImageForm from "@/components/forms/EditCategoryImageForm.vue";
import CategoryChangeStatus from "@/components/forms/CategoryChangeStatus.vue";

const categoryStore = useCategoryStore();
const selectedItem = ref<CategoryInterface | null>(null);
const showAddCategory = ref<boolean>(false);
const showEditCategory = ref<boolean>(false);
const showChangeImage = ref<boolean>(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const statusFilter = ref<boolean | null>(null);
const actionElement = ref<null | InstanceType<typeof Popover>>();
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

const openActionElement = (event: Event, value: CategoryInterface) => {
    if (actionElement.value) {
        actionElement.value.toggle(event);
        selectedItem.value = value;
    }
};

const fetchCategories = async () => {
    if (
        categoryStore.categories.length === 0 &&
        !categoryStore.loadCategoryService.request.loading
    ) {
        await categoryStore.fetchCategories();
    }
};

const loadCb = async () => {
    showAddCategory.value = false;
    showEditCategory.value = false;
    showChangeImage.value = false;
    await categoryStore.fetchCategories();
};

const changeStatusFormCb = async () => {
    actionElement.value.hide();
    await categoryStore.fetchCategories();
};

onMounted(() => {
    fetchCategories();
});
</script>
