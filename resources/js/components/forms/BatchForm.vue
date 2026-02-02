<template>
    <BoxShadow>
        <div class="w-full">
            <div 
                :class="[
                    'flex items-center justify-between border-b border-gray-200',
                    responsive.getResponsiveClasses({
                        mobile: 'px-3 py-3',
                        tablet: 'px-4 py-3',
                        desktop: 'px-5 py-4' // Original desktop padding
                    })
                ]"
            >
                <div>
                    <h1 :class="responsive.getResponsiveTextSize('lg') + ' font-semibold text-gray-800 dashboard-section-title'">
                        Nearly Out Of Stock
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">Products requiring restocking</p>
                </div>
                <div class="flex items-center gap-3">
                    <div :class="'flex items-center gap-2 text-gray-600 ' + responsive.getResponsiveTextSize('sm')">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700">
                            <i class="pi pi-exclamation-triangle mr-1"></i>
                            {{ lowStockCount }} items
                        </span>
                    </div>
                    <Button
                        type="button"
                        label="View Table"
                        icon="pi pi-table"
                        size="small"
                        :class="[
                            '!bg-blue-600 hover:!bg-blue-700 !text-white',
                            responsive.getResponsiveClasses({
                                mobile: '!text-xs',
                                tablet: '!text-sm',
                                desktop: '' // Original desktop size
                            })
                        ]"
                        :pt="{
                            icon: { 
                                class: responsive.getResponsiveClasses({
                                    mobile: '!text-xs',
                                    tablet: '!text-sm',
                                    desktop: ''
                                })
                            }
                        }"
                        @click="showModal = true"
                    />
                </div>
            </div>

            <!-- Desktop Table Layout - Hidden on mobile/tablet -->
            <div class="hidden lg:block">

            <DataTable class="w-full" :value="paginate.data" :loading="loadProductService.request.loading"
                columnResizeMode="expand" editMode="cell" @cell-edit-complete="onCellEditComplete" stripedRows
                :pt="{
                    headerCell: { class: 'text-xs' }
                }">          
                <Column field="product_name" header="Product" :sortable="true">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-1">
                            <Avatar v-if="data.product_image" shape="circle" :image="UrlUtil.getBaseAppUrl(
                                `storage/images/product/${data.product_image}`,
                            )" class="aspect-square! w-10 h-10" size="large" />
                            <Avatar v-else shape="circle" icon="pi pi-camera" class="bg-gray-100 text-gray-400" size="large" />
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 truncate text-sm">
                                    {{ data.product_name }}
                                </div>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column field="category.category_name" header="Category" :sortable="true">
                    <template #body="{ data }">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                            {{ data.category.category_name }}
                        </span>
                    </template>
                </Column>
                <Column header="Stock" field="product_quantity" :sortable="true">
                    <template #body="{ data }">
                        <div class="flex items-center gap-1">
                            <span :class="[
                                'font-semibold text-sm',
                                data.low_stock_threshold >= data.product_quantity
                                    ? 'text-red-600'
                                    : 'text-orange-600',
                            ]">
                                {{ data.product_quantity }}
                            </span>
                            <i class="pi pi-exclamation-triangle text-xs" :class="data.low_stock_threshold >= data.product_quantity ? 'text-red-600' : 'text-orange-600'" />
                        </div>
                    </template>
                </Column>
                <Column header="Batch Number" field="batch.batch_number">
                    <template #body="{ data }">
                        <span class="text-sm text-gray-700">{{ data.batch?.batch_number || '-' }}</span>
                    </template>
                </Column>
                <Column header="Action">
                    <template #body="{ data }">
                        <Button type="button" icon="pi pi-plus" severity="success" size="small" class="cursor-pointer"
                            @click="addProduct(data)" />
                    </template>
                </Column>

                <template #empty>
                    <div class="text-center py-8">
                        <i class="pi pi-check-circle text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">No low stock items</p>
                        <p class="text-xs text-gray-400 mt-1">All products are well stocked</p>
                    </div>
                </template>
            </DataTable>
            </div>

            <!-- Mobile/Tablet Card Layout - Shown on mobile/tablet -->
            <div class="block lg:hidden" :class="responsive.getResponsiveClasses({ mobile: 'p-3', tablet: 'p-4' })">
                <div v-if="paginate.data.length === 0" class="text-center py-8">
                    <i class="pi pi-check-circle text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 font-medium">No low stock items</p>
                    <p class="text-xs text-gray-400 mt-1">All products are well stocked</p>
                </div>
                <div v-else :class="'space-y-3 ' + (paginate.data.length > 5 ? 'max-h-96 overflow-y-auto' : '')">
                    <div 
                        v-for="product in paginate.data.slice(0, 5)" 
                        :key="product.product_id"
                        class="bg-gray-50 rounded-lg p-3 border border-gray-200 hover:bg-gray-100 transition-colors"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <Avatar 
                                    v-if="product.product_image" 
                                    shape="circle" 
                                    :image="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" 
                                    :class="responsive.getResponsiveClasses({
                                        mobile: 'w-10 h-10',
                                        tablet: 'w-12 h-12'
                                    }) + ' aspect-square!'" 
                                    size="large" 
                                />
                                <Avatar 
                                    v-else 
                                    shape="circle" 
                                    icon="pi pi-camera" 
                                    :class="responsive.getResponsiveClasses({
                                        mobile: 'w-10 h-10',
                                        tablet: 'w-12 h-12'
                                    }) + ' bg-gray-100 text-gray-400'" 
                                    size="large" 
                                />
                                <div class="flex-1 min-w-0">
                                    <div :class="responsive.getResponsiveTextSize('base') + ' font-medium text-gray-900 truncate'">
                                        {{ product.product_name }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                            {{ product.category.category_name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-3">
                                <Button 
                                    type="button" 
                                    icon="pi pi-plus" 
                                    severity="success" 
                                    size="small" 
                                    class="cursor-pointer"
                                    @click="addProduct(product)" 
                                />
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1">
                                    <span :class="[
                                        'font-semibold text-sm',
                                        product.low_stock_threshold >= product.product_quantity
                                            ? 'text-red-600'
                                            : 'text-orange-600',
                                    ]">
                                        {{ product.product_quantity }}
                                    </span>
                                    <i class="pi pi-exclamation-triangle text-xs" :class="product.low_stock_threshold >= product.product_quantity ? 'text-red-600' : 'text-orange-600'" />
                                    <span class="text-xs text-gray-500 ml-1">in stock</span>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500">
                                Batch: {{ product.batch?.batch_number || '-' }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Show more indicator if there are more than 5 items -->
                <div v-if="paginate.data.length > 5" class="text-center mt-3 pt-3 border-t border-gray-200">
                    <span class="text-xs text-gray-500">Showing 5 of {{ paginate.data.length }} low stock items</span>
                </div>
            </div>
        </div>

        <Dialog
            v-model:visible="showModal"
            modal
            dismissableMask
            header="Nearly Out Of Stock Products"
            :style="{ width: '90vw', maxWidth: '1200px' }"
            :breakpoints="{ '960px': '95vw' }"
            :pt="{
                header: { class: '!bg-blue-600 !text-white !rounded-t-lg !rounded-b-none' },
                closeButton: { class: '!text-white hover:!bg-blue-700 !border-white' },
                closeButtonIcon: { class: '!text-white' }
            }"
        >
            <!-- Desktop Table Layout - Hidden on mobile/tablet -->
            <div class="hidden lg:block">
                <DataTable
                    :value="paginate.data"
                    :loading="loadProductService.request.loading"
                    stripedRows
                    paginator
                    :rows="10"
                    class="w-full"
                >
                    <Column field="product_name" header="Product Name" style="width: 20%">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3 py-1">
                                <Avatar
                                    v-if="data.product_image"
                                    shape="circle"
                                    :image="UrlUtil.getBaseAppUrl(`storage/images/product/${data.product_image}`)"
                                    class="aspect-square! w-10 h-10"
                                    size="large"
                                />
                                <Avatar v-else shape="circle" icon="pi pi-camera" class="bg-gray-100 text-gray-400" size="large" />
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate text-sm">
                                        {{ data.product_name }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="category.category_name" header="Category" style="width: 20%">
                        <template #body="{ data }">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                {{ data.category.category_name }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Current Stock" field="product_quantity" sortable style="width: 20%">
                        <template #body="{ data }">
                            <div class="flex items-center gap-1">
                                <span :class="[
                                    'font-semibold text-sm',
                                    data.low_stock_threshold >= data.product_quantity
                                        ? 'text-red-600'
                                        : 'text-orange-600',
                                ]">
                                    {{ data.product_quantity }}
                                </span>
                                <i class="pi pi-exclamation-triangle text-xs" :class="data.low_stock_threshold >= data.product_quantity ? 'text-red-600' : 'text-orange-600'" />
                            </div>
                        </template>
                    </Column>
                    <Column header="Low Stock Threshold" field="low_stock_threshold" sortable style="width: 20%">
                        <template #body="{ data }">
                            <span class="text-sm text-gray-600">{{ data.low_stock_threshold }}</span>
                        </template>
                    </Column>
                    <Column header="Batch Number" field="batch.batch_number" style="width: 20%">
                        <template #body="{ data }">
                            <span class="text-sm text-gray-700">{{ data.batch?.batch_number || '-' }}</span>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-8">
                            <i class="pi pi-check-circle text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">No low stock items</p>
                            <p class="text-xs text-gray-400 mt-1">All products are well stocked</p>
                        </div>
                    </template>
                </DataTable>
            </div>

            <!-- Mobile/Tablet Card Layout - Shown on mobile/tablet -->
            <div class="block lg:hidden">
                <div v-if="paginate.data.length === 0 && !loadProductService.request.loading" class="text-center py-8">
                    <i class="pi pi-check-circle text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500 font-medium">No low stock items</p>
                    <p class="text-xs text-gray-400 mt-1">All products are well stocked</p>
                </div>
                <div v-else>
                    <!-- Cards Container -->
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'space-y-3 p-3',
                        tablet: 'space-y-4 p-4'
                    })">
                        <div 
                            v-for="product in paginatedProducts" 
                            :key="product.product_id"
                            :class="responsive.getResponsiveClasses({
                                mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm',
                                tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm'
                            })"
                        >
                            <!-- Product Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <Avatar 
                                        v-if="product.product_image" 
                                        shape="circle" 
                                        :image="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" 
                                        :class="responsive.getResponsiveClasses({
                                            mobile: 'w-10 h-10',
                                            tablet: 'w-12 h-12'
                                        }) + ' aspect-square!'" 
                                        size="large" 
                                    />
                                    <Avatar 
                                        v-else 
                                        shape="circle" 
                                        icon="pi pi-camera" 
                                        :class="responsive.getResponsiveClasses({
                                            mobile: 'w-10 h-10',
                                            tablet: 'w-12 h-12'
                                        }) + ' bg-gray-100 text-gray-400'" 
                                        size="large" 
                                    />
                                    <div class="flex-1 min-w-0">
                                        <div :class="responsive.getResponsiveTextSize('base') + ' font-medium text-gray-900 truncate'">
                                            {{ product.product_name }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                                {{ product.category.category_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Stock Information -->
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500 block mb-1">Current Stock</span>
                                    <div class="flex items-center gap-1">
                                        <span :class="[
                                            'font-semibold',
                                            product.low_stock_threshold >= product.product_quantity
                                                ? 'text-red-600'
                                                : 'text-orange-600',
                                        ]">
                                            {{ product.product_quantity }}
                                        </span>
                                        <i class="pi pi-exclamation-triangle text-xs" :class="product.low_stock_threshold >= product.product_quantity ? 'text-red-600' : 'text-orange-600'" />
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500 block mb-1">Threshold</span>
                                    <span class="text-gray-900 font-medium">{{ product.low_stock_threshold }}</span>
                                </div>
                            </div>
                            
                            <!-- Batch Information -->
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Batch Number:</span>
                                    <span class="text-gray-700 font-medium">{{ product.batch?.batch_number || '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <div v-if="paginate.data.length > itemsPerPage" class="mt-4 pt-4 border-t border-gray-200">
                        <Paginator 
                            :rows="itemsPerPage" 
                            :totalRecords="paginate.data.length" 
                            :first="(currentPage - 1) * itemsPerPage"
                            @page="onPageChange"
                            :pt="{
                                root: { class: 'bg-transparent border-0' },
                                pages: { class: 'text-sm' },
                                pageButton: { class: 'text-sm min-w-8 h-8' }
                            }"
                        />
                    </div>
                </div>
            </div>
        </Dialog>

        <AddStockModal
            :visible="addStockModalVisible"
            :product="selectedProduct"
            @update:visible="addStockModalVisible = $event"
            @success="handleStockAdded"
        />
    </BoxShadow>
</template>
<script setup lang="ts">
import { IBatch } from '@/interfaces/IBatch';
import { ProductInterface } from '@/interfaces/ProductInterface';
import useAxiosUtil from '@/utils/AxiosUtil';
import UrlUtil from '@/utils/UrlUtil';
import { DataTableCellEditCompleteEvent } from 'primevue';
import { computed, onMounted, reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';
import AddStockModal from '@/components/modals/AddStockModal.vue';

import { useResponsive } from '@/composables/useResponsive';

interface IForm {
    batch_products: {
        product_id: number;
        quantity: number;
    }[]
}

interface BatchProduct extends ProductInterface {
    quantity: number;
}

const showModal = ref(false);
const addStockModalVisible = ref(false);
const selectedProduct = ref<ProductInterface | null>(null);
const responsive = useResponsive();

// Pagination for mobile/tablet cards
const currentPage = ref(1);
const itemsPerPage = ref(10);

const paginate: {
    batch_id: number;
    data: BatchProduct[],
} = reactive({
    batch_id: 0,
    data: [],
});

const form: IForm = reactive({
    batch_products: []
});

// Computed property for paginated products (mobile/tablet cards)
const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return paginate.data.slice(start, end);
});

const loadProductService = useAxiosUtil<null, IBatch>();
const lowStockCount = computed(() => paginate.data.length);

const loadBatch = async () => {
    await loadProductService.get("admin/batches").then(() => {
        if (
            loadProductService.request.status === 200 &&
            loadProductService.request.data
        ) {
            paginate.batch_id = loadProductService.request.data.batch_id;
            paginate.data = loadProductService.request.data.batch_products
                .filter((product: ProductInterface) => product.product_quantity > 0)
                .map((product: ProductInterface) => {
                    return {
                        ...product,
                        quantity: 0,
                    };
                });
        }
    });
};

const addProduct = async (product: BatchProduct) => {
    selectedProduct.value = product;
    addStockModalVisible.value = true;
};

const handleStockAdded = (updatedProduct: ProductInterface) => {
    // Reload the batch data to get updated stock information
    loadBatch();
    // Note: Toast notification is already shown by AddStockModal
};

// Pagination handler for mobile/tablet cards
const onPageChange = (event: any) => {
    currentPage.value = event.page + 1; // PrimeVue uses 0-based indexing
};

const onCellEditComplete = (event: DataTableCellEditCompleteEvent) => {
    let { data, newValue, field } = event;

    if (field === 'quantity') {
        const product = paginate.data.find((product) => product.product_id === data.product_id);
        product.quantity = newValue ? newValue : 0;
    }
};

const submitService = useAxiosUtil<IForm, null>();
const toast = useToast();

const validate = () => {
    form.batch_products = [];
    paginate.data.forEach((product: BatchProduct) => {
        if (product.quantity > 0) {
            form.batch_products.push({
                product_id: product.product_id,
                quantity: product.quantity
            })
        }
    });
    if (form.batch_products.length === 0) {
        toast.error("Please select at least one product");
        return false;
    }
    return form;
}

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService.post(`admin/batches`, form).then(() => {
            if (submitService.request.status === 200) {
                loadBatch();
                toast.success(submitService.request.message ?? "Batch Created Successfully.");
            }
            else {
                toast.error(submitService.request.message ?? "Please try again.");
            }
        });
    }
};

const toOrdinal = (n: number) => {
    const rem10 = n % 10;
    const rem100 = n % 100;

    if (rem100 >= 11 && rem100 <= 13) return n + "th";
    if (rem10 === 1) return n + "st";
    if (rem10 === 2) return n + "nd";
    if (rem10 === 3) return n + "rd";

    return n + "th";
};



onMounted(() => {
    loadBatch();
});
</script>