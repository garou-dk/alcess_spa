<template>
    <div class="min-h-screen bg-gray-50" :class="getResponsivePadding()">
        <div class="mx-auto max-w-6xl">
            <!-- Header -->
            <div class="mb-6 flex items-center gap-3">
                <button
                    @click="$router.back()"
                    class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-white transition-colors cursor-pointer"
                >
                    <i class="pi pi-chevron-left text-xl text-gray-700"></i>
                </button>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Shopping Cart</h1>
                    <p class="text-sm text-gray-500">{{ carts.length }} {{ carts.length === 1 ? 'item' : 'items' }} in your cart</p>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!loadService.request.loading && carts.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 py-20 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-5">
                    <i class="pi pi-shopping-cart text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Your cart is empty</h3>
                <p class="text-gray-500 mb-6">Looks like you haven't added anything yet.</p>
                <Button
                    label="Browse Products"
                    icon="pi pi-arrow-right"
                    iconPos="right"
                    @click="$router.push({ name: 'customer.home.index' })"
                    class="!bg-blue-600 hover:!bg-blue-700 !border-0 font-semibold"
                />
            </div>

            <!-- Loading State -->
            <div v-else-if="loadService.request.loading" class="bg-white rounded-2xl shadow-sm border border-gray-100 py-20 text-center">
                <PageLoader />
            </div>

            <!-- Cart Content -->
            <div v-else class="grid gap-6 lg:grid-cols-3">

                <!-- Cart Items Column -->
                <div class="lg:col-span-2 space-y-0">

                    <!-- Select All Bar -->
                    <div class="bg-white rounded-t-2xl border border-gray-200 px-5 py-3.5 flex items-center justify-between sticky top-0 z-10 shadow-sm">
                        <div class="flex items-center gap-3">
                            <Checkbox
                                v-model="selectAll"
                                :binary="true"
                                @change="toggleSelectAll"
                                inputId="selectAll"
                                class="cart-checkbox"
                            />
                            <label for="selectAll" class="font-semibold text-gray-700 cursor-pointer text-sm select-none">
                                Select All ({{ carts.length }})
                            </label>
                        </div>
                        <Button
                            v-if="selectedCount > 0"
                            type="button"
                            :label="`Remove (${selectedCount})`"
                            icon="pi pi-trash"
                            severity="danger"
                            text
                            size="small"
                            @click="removeSelected"
                            class="!text-red-600 hover:!bg-red-50 font-medium"
                        />
                    </div>

                    <!-- Cart Items List -->
                    <div class="bg-white border-x border-b border-gray-200 rounded-b-2xl shadow-sm divide-y divide-gray-100">
                        <div
                            v-for="(cart, index) in carts"
                            :key="cart.cart_id"
                            class="p-5 hover:bg-gray-50/50 transition-colors"
                        >
                            <div class="flex items-start gap-4">
                                <!-- Checkbox -->
                                <Checkbox
                                    v-model="form.carts[index].checked"
                                    :binary="true"
                                    :inputId="`cart-${index}`"
                                    class="cart-checkbox flex-shrink-0 mt-2"
                                />

                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <div :class="getResponsiveClasses({
                                        mobile: 'h-20 w-20',
                                        tablet: 'h-24 w-24',
                                        desktop: 'h-24 w-24'
                                    })" class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50">
                                        <img
                                            v-if="cart.product.product_image"
                                            :src="UrlUtil.getBaseAppUrl(`storage/images/product/${cart.product.product_image}`)"
                                            :alt="cart.product.product_name"
                                            class="h-full w-full object-cover"
                                        />
                                        <div v-else class="flex h-full w-full items-center justify-center">
                                            <i class="pi pi-image text-2xl text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 line-clamp-2 leading-snug mb-1" :class="getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })">
                                        {{ cart.product.product_name }}
                                    </h3>

                                    <!-- Stock & Rating Row -->
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-xs text-green-600 flex items-center gap-1 font-medium">
                                            <i class="pi pi-check-circle text-[10px]"></i>
                                            {{ cart.product.product_quantity }} in stock
                                        </span>
                                        <span class="text-gray-300">•</span>
                                        <div class="flex items-center gap-1">
                                            <i
                                                v-for="star in 5"
                                                :key="star"
                                                :class="star <= Math.round(Number(cart.product.rates_avg_rate) || 0)
                                                    ? 'pi pi-star-fill text-yellow-400'
                                                    : 'pi pi-star text-gray-300'"
                                                class="text-[10px]"
                                            ></i>
                                            <span class="text-xs text-gray-500 ml-0.5">
                                                {{ cart.product.rates_avg_rate ? Number(cart.product.rates_avg_rate).toFixed(1) : '0.0' }}
                                            </span>
                                        </div>
                                    </div>

                                    <p class="font-bold text-gray-900 mb-3" :class="getResponsiveClasses({
                                        mobile: 'text-base',
                                        tablet: 'text-lg',
                                        desktop: 'text-lg'
                                    })">
                                        {{ CurrencyUtil.formatCurrency(cart.product.product_price) }}
                                    </p>

                                    <!-- Quantity Controls & Delete -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                            <button
                                                type="button"
                                                @click="removeQuantity(index)"
                                                :disabled="form.carts[index].quantity <= 1"
                                                class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                            >
                                                <i class="pi pi-minus text-xs"></i>
                                            </button>
                                            <input
                                                type="number"
                                                v-model.number="form.carts[index].quantity"
                                                :min="1"
                                                :max="cart.product.product_quantity"
                                                class="w-10 h-8 text-center font-semibold text-sm border-x border-gray-200 focus:outline-none bg-white qty-input"
                                                @input="validateQuantity(index, cart.product.product_quantity)"
                                            />
                                            <button
                                                type="button"
                                                @click="addQuantity(index, cart.product.product_quantity)"
                                                :disabled="form.carts[index].quantity >= cart.product.product_quantity"
                                                class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                            >
                                                <i class="pi pi-plus text-xs"></i>
                                            </button>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <!-- Line total -->
                                            <span class="font-bold text-blue-600 hidden sm:block">
                                                {{ CurrencyUtil.formatCurrency(cart.product.product_price * form.carts[index].quantity) }}
                                            </span>
                                            <button
                                                type="button"
                                                @click="removeSingleItem(index)"
                                                class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors"
                                            >
                                                <i class="pi pi-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-4 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Summary Header -->
                        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-600 to-blue-700">
                            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                <i class="pi pi-receipt"></i>
                                Order Summary
                            </h2>
                        </div>

                        <div class="p-6 space-y-4">
                            <!-- Items Subtotal -->
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">
                                    Subtotal ({{ selectedCount }} {{ selectedCount === 1 ? 'item' : 'items' }})
                                </span>
                                <span class="font-bold text-gray-900">{{ subTotal() }}</span>
                            </div>

                            <!-- Shipping -->
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm flex items-center gap-1">
                                    <i class="pi pi-truck text-xs"></i>
                                    Shipping
                                </span>
                                <span class="text-sm font-medium text-gray-500">Calculated at checkout</span>
                            </div>

                            <!-- Divider -->
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-900">Total</span>
                                    <span class="text-xl font-bold text-blue-600">{{ subTotal() }}</span>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <Button
                                :disabled="selectedCount === 0"
                                type="button"
                                label="Proceed to Checkout"
                                icon="pi pi-arrow-right"
                                iconPos="right"
                                class="w-full !bg-blue-600 hover:!bg-blue-700 !border-0 text-white font-bold py-3 transition-all disabled:!opacity-40 disabled:!cursor-not-allowed mt-2"
                                @click="checkoutItems()"
                            />

                            <!-- Security Badges -->
                            <div class="pt-4 border-t border-gray-100 space-y-2.5">
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <i class="pi pi-shield text-green-500"></i>
                                    <span>Secure checkout guaranteed</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <i class="pi pi-replay text-blue-500"></i>
                                    <span>30-day return policy</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Dialog -->
        <Dialog
            v-model:visible="showCheckout"
            modal
            header="Checkout"
            :style="isMobile ? { width: '95vw', maxWidth: '500px' } : isTablet ? { width: '90vw', maxWidth: '800px' } : { width: '95vw', maxWidth: '1200px' }"
            :breakpoints="{ '1199px': '90vw', '575px': '95vw' }"
            :pt="{
                header: {
                    class: '!bg-blue-600 !text-white !border-b-2 !border-blue-700 !rounded-t-lg'
                },
                headerTitle: {
                    class: '!text-white !font-bold'
                },
                closeButton: {
                    class: 'hover:!bg-blue-700 !text-white'
                }
            }"
        >
            <CheckoutForm
                v-if="selectedItems.length > 0"
                :products="selectedItems"
                @cb="load"
            />
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import CheckoutForm from "@/components/forms/CheckoutForm.vue";
import { CartInterface } from "@/interfaces/CartInterface";
import { ProductInterface } from "@/interfaces/ProductInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import CurrencyUtil from "@/utils/CurrencyUtil";
import UrlUtil from "@/utils/UrlUtil";
import { computed, onMounted, reactive, ref } from "vue";
import { useToast } from "vue-toastification";
import { useResponsive } from "@/composables/useResponsive";

const { isMobile, isTablet, isDesktop, getResponsiveClasses, getResponsiveTextSize, getResponsivePadding } = useResponsive();

const loadService = useAxiosUtil<null, CartInterface[]>();
const removeService = useAxiosUtil<{ cart_ids: number[] }, null>();
const carts = ref<CartInterface[]>([]);
const toast = useToast();
const form: {
    carts: {
        checked: boolean;
        cart_id: number;
        quantity: number;
    }[];
} = reactive({
    carts: [],
});

const showCheckout = ref<boolean>(false);
const selectedItems = ref<{
    product: ProductInterface;
    quantity: number;
}[]>([]);

const selectAll = ref<boolean>(false);

const selectedCount = computed(() => {
    return form.carts.filter((cart) => cart.checked).length;
});

const toggleSelectAll = () => {
    form.carts.forEach((cart) => {
        cart.checked = selectAll.value;
    });
};

const checkoutItems = () => {
    selectedItems.value = [];
    form.carts.forEach((cart) => {
        if (cart.checked) {
            const find = carts.value.find((c) => c.cart_id === cart.cart_id);
            if (find) {
                selectedItems.value.push({
                    product: find.product,
                    quantity: cart.quantity,
                });
            }
        }
    });
    showCheckout.value = true;
};

const subTotal = () => {
    let total = 0;
    form.carts.forEach((cart) => {
        if (cart.checked) {
            const find = carts.value.find((c) => c.cart_id === cart.cart_id);
            if (find) {
                total += find.product.product_price * cart.quantity;
            }
        }
    });
    return CurrencyUtil.formatCurrency(total);
};

const setCartForm = () => {
    form.carts = [];
    form.carts = carts.value.map((cart) => ({
        checked: false,
        cart_id: cart.cart_id,
        quantity: cart.quantity,
    }));
};

const load = async () => {
    await loadService.get("customer/carts").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            carts.value = loadService.request.data;
            setCartForm();
            showCheckout.value = false;
        } else {
            toast.error(loadService.request.message ?? "Failed to load carts");
        }
    });
};

const addQuantity = (index: number, max: number) => {
    if (form.carts[index].quantity < max) {
        form.carts[index].quantity += 1;
    }
};

const removeQuantity = (index: number) => {
    if (form.carts[index].quantity > 1) {
        form.carts[index].quantity -= 1;
    }
};

const validateQuantity = (index: number, max: number) => {
    if (form.carts[index].quantity < 1) {
        form.carts[index].quantity = 1;
    } else if (form.carts[index].quantity > max) {
        form.carts[index].quantity = max;
        toast.warning(`Maximum quantity available is ${max}`);
    }
};

const removeSingleItem = async (index: number) => {
    const cart_id = form.carts[index].cart_id;
    form.carts.splice(index, 1);
    carts.value = carts.value.filter((cart) => cart.cart_id !== cart_id);
    await removeService.deleteRequest("customer/carts/remove/multiple", {
        cart_ids: [cart_id],
    });
    toast.success("Item removed from cart");
};

const removeSelected = async () => {
    const cart_ids = form.carts
        .filter((cart) => cart.checked)
        .map((cart) => cart.cart_id);
    if (cart_ids.length === 0) {
        toast.warning("Please select at least one item to remove");
        return;
    }
    form.carts = form.carts.filter((cart) => !cart.checked);
    carts.value = carts.value.filter(
        (cart) => !cart_ids.includes(cart.cart_id),
    );
    await removeService.deleteRequest("customer/carts/remove/multiple", {
        cart_ids,
    });
    toast.success(`${cart_ids.length} item(s) removed from cart`);
    selectAll.value = false;
};

onMounted(() => {
    load();
});
</script>

<style scoped>
/* Quantity input - hide spinners */
.qty-input {
    -moz-appearance: textfield;
}
.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Line clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Checkbox styling - green when checked */
:deep(.cart-checkbox .p-checkbox-box) {
    border-color: #d1d5db;
    border-radius: 4px;
}

:deep(.cart-checkbox .p-checkbox-box.p-highlight),
:deep(.cart-checkbox .p-checkbox-box.p-checked) {
    background: #2563eb !important;
    background-color: #2563eb !important;
    border-color: #2563eb !important;
}

:deep(.cart-checkbox .p-checkbox-box.p-highlight:hover),
:deep(.cart-checkbox .p-checkbox-box.p-checked:hover) {
    background: #1d4ed8 !important;
    background-color: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
}

:deep(.cart-checkbox .p-checkbox-box.p-highlight .p-checkbox-icon),
:deep(.cart-checkbox .p-checkbox-box.p-checked .p-checkbox-icon) {
    color: white !important;
}

:deep(.cart-checkbox .p-checkbox-box.p-focus) {
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25) !important;
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 4px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>