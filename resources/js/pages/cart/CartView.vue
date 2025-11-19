<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-4 sm:py-8">
        <div class="container mx-auto px-3 sm:px-4">
            <div class="mx-auto max-w-7xl">
                <!-- Header with animated gradient -->
                <div class="mb-6 sm:mb-8 text-center">
                    <div class="inline-block">
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2 sm:mb-3">
                            Shopping Cart
                        </h1>
                        <div class="h-1 w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-full"></div>
                    </div>
                    <p class="text-gray-600 mt-3 text-sm sm:text-base">
                        <i class="pi pi-shopping-bag mr-2"></i>
                        {{ carts.length }} {{ carts.length === 1 ? 'item' : 'items' }} in your cart
                    </p>
                </div>

                <div class="grid gap-4 sm:gap-6 lg:grid-cols-3">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="rounded-2xl sm:rounded-3xl bg-white shadow-xl overflow-hidden border border-gray-100">
                            <!-- Select All Header with gradient -->
                            <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50 px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex items-center gap-3">
                                    <Checkbox
                                        v-model="selectAll"
                                        binary
                                        @change="toggleSelectAll"
                                        class="scale-110"
                                    />
                                    <span class="text-sm sm:text-base font-semibold text-gray-700">
                                        Select All
                                    </span>
                                    <span v-if="selectedCount > 0" class="ml-auto text-xs sm:text-sm px-3 py-1 bg-blue-600 text-white rounded-full font-medium">
                                        {{ selectedCount }} selected
                                    </span>
                                </div>
                            </div>

                            <!-- Cart Items List -->
                            <div class="divide-y divide-gray-100">
                                <div
                                    v-for="(cart, index) in carts"
                                    :key="index"
                                    class="group p-3 sm:p-6 transition-all duration-300"
                                    :class="{ 
                                        'bg-gradient-to-r from-blue-50 to-indigo-50 shadow-inner': form.carts[index].checked,
                                        'hover:bg-gray-50': !form.carts[index].checked
                                    }"
                                >
                                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                                        <!-- Checkbox - Mobile: Top Left -->
                                        <div class="flex items-start sm:pt-2 absolute sm:relative">
                                            <Checkbox
                                                v-model="form.carts[index].checked"
                                                binary
                                                class="transition-transform hover:scale-110 scale-110 sm:scale-100"
                                            />
                                        </div>

                                        <!-- Mobile Layout Container -->
                                        <div class="flex gap-3 sm:gap-4 flex-1 pl-8 sm:pl-0">
                                            <!-- Product Image -->
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="h-20 w-20 sm:h-28 sm:w-28 overflow-hidden rounded-xl sm:rounded-2xl border-2 border-gray-100 transition-all duration-300 group-hover:border-blue-300 group-hover:shadow-lg"
                                                    :class="{ 'border-blue-400 shadow-lg': form.carts[index].checked }"
                                                >
                                                    <img
                                                        v-if="cart.product.product_image"
                                                        :src="
                                                            UrlUtil.getBaseAppUrl(
                                                                `storage/images/product/${cart.product.product_image}`,
                                                            )
                                                        "
                                                        :alt="cart.product.product_name"
                                                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                                                    />
                                                    <div
                                                        v-else
                                                        class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200"
                                                    >
                                                        <i class="pi pi-image text-2xl sm:text-4xl text-gray-400"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Product Details -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h3 class="text-sm sm:text-lg font-bold text-gray-800 line-clamp-2 pr-2">
                                                        {{ cart.product.product_name }}
                                                    </h3>
                                                    <!-- Delete Button - Mobile: Top Right -->
                                                    <Button
                                                        type="button"
                                                        icon="pi pi-trash"
                                                        severity="danger"
                                                        text
                                                        rounded
                                                        size="small"
                                                        @click="removeSingleItem(index)"
                                                        class="hover:scale-110 transition-transform flex-shrink-0 sm:hidden"
                                                    />
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <span class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                                        {{
                                                            CurrencyUtil.formatCurrency(
                                                                cart.product.product_price,
                                                            )
                                                        }}
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-2 mb-3">
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-green-100 to-emerald-100 px-2 sm:px-3 py-1 text-xs font-semibold text-green-700 border border-green-200">
                                                        <i class="pi pi-check-circle text-xs"></i>
                                                        {{ cart.product.product_quantity }} in stock
                                                    </span>
                                                </div>

                                                <!-- Quantity Controls -->
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mb-3">
                                                    <span class="text-xs sm:text-sm font-semibold text-gray-600">Quantity:</span>
                                                    <InputGroup class="w-full sm:w-[140px]">
                                                        <Button
                                                            type="button"
                                                            icon="pi pi-minus"
                                                            severity="secondary"
                                                            size="small"
                                                            @click="removeQuantity(index)"
                                                            :disabled="form.carts[index].quantity <= 1"
                                                            class="hover:bg-blue-100 transition-colors"
                                                        />
                                                        <InputNumber
                                                            size="small"
                                                            v-model="form.carts[index].quantity"
                                                            :min="1"
                                                            :max="cart.product.product_quantity"
                                                            :invalid="
                                                                form.carts[index].quantity <= 0 ||
                                                                form.carts[index].quantity >
                                                                    cart.product.product_quantity
                                                            "
                                                            class="text-center font-bold"
                                                            :pt="{
                                                                root: {
                                                                    class: 'p-inputnumber-custom',
                                                                },
                                                            }"
                                                        />
                                                        <Button
                                                            type="button"
                                                            icon="pi pi-plus"
                                                            severity="secondary"
                                                            size="small"
                                                            @click="
                                                                addQuantity(
                                                                    index,
                                                                    cart.product.product_quantity,
                                                                )
                                                            "
                                                            :disabled="form.carts[index].quantity >= cart.product.product_quantity"
                                                            class="hover:bg-blue-100 transition-colors"
                                                        />
                                                    </InputGroup>
                                                </div>

                                                <!-- Subtotal for this item -->
                                                <div class="flex items-center justify-between sm:justify-start gap-2 p-2 sm:p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                                                    <span class="text-xs sm:text-sm text-gray-600 font-medium">Subtotal:</span>
                                                    <span class="text-base sm:text-lg font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                                        {{ CurrencyUtil.formatCurrency(cart.product.product_price * form.carts[index].quantity) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Delete Button - Desktop -->
                                            <div class="hidden sm:flex items-start">
                                                <Button
                                                    type="button"
                                                    icon="pi pi-trash"
                                                    severity="danger"
                                                    text
                                                    rounded
                                                    @click="removeSingleItem(index)"
                                                    class="hover:scale-110 transition-transform hover:bg-red-50"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div v-if="carts.length === 0" class="py-12 sm:py-16 text-center px-4">
                                    <div class="inline-block p-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4">
                                        <i class="pi pi-shopping-cart text-4xl sm:text-6xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-700 mb-2">Your cart is empty</h3>
                                    <p class="text-sm sm:text-base text-gray-500">Add some amazing items to get started!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-4 sm:top-8 rounded-2xl sm:rounded-3xl bg-white shadow-xl p-4 sm:p-6 border border-gray-100">
                            <div class="flex items-center gap-2 mb-4 sm:mb-6 pb-3 sm:pb-4 border-b border-gray-200">
                                <i class="pi pi-wallet text-xl sm:text-2xl text-blue-600"></i>
                                <h2 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                    Order Summary
                                </h2>
                            </div>

                            <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
                                <div class="flex justify-between items-center text-sm sm:text-base text-gray-600 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <span class="flex items-center gap-2">
                                        <i class="pi pi-shopping-bag text-blue-600"></i>
                                        Items ({{ selectedCount }})
                                    </span>
                                    <span class="font-bold text-gray-800">{{ subTotal() }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center text-sm sm:text-base text-gray-600 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <span class="flex items-center gap-2">
                                        <i class="pi pi-truck text-blue-600"></i>
                                        Shipping
                                    </span>
                                    <span class="font-bold text-green-600 flex items-center gap-1">
                                        TBA
                                    </span>
                                </div>

                                <div class="border-t-2 border-dashed border-gray-300 pt-3 sm:pt-4 flex justify-between items-center text-lg sm:text-xl">
                                    <span class="font-bold text-gray-700">Total:</span>
                                    <span class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                        {{ subTotal() }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-2 sm:space-y-3">
                                <Button
                                    :disabled="selectedCount === 0"
                                    type="button"
                                    label="Proceed to Checkout"
                                    icon="pi pi-arrow-right"
                                    iconPos="right"
                                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 border-0 text-white font-bold py-3 sm:py-4 text-sm sm:text-base transition-all duration-300 hover:shadow-xl hover:scale-[1.02] disabled:opacity-50 disabled:hover:scale-100"
                                    @click="checkoutItems()"
                                />
                                
                                <Button
                                    :disabled="selectedCount === 0"
                                    type="button"
                                    label="Remove Selected"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    outlined
                                    class="w-full font-semibold py-2 sm:py-3 text-sm sm:text-base transition-all duration-300 hover:shadow-md hover:scale-[1.02] disabled:hover:scale-100"
                                    @click="removeSelected()"
                                />
                            </div>

                            <!-- Security Badges -->
                            <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200 space-y-2">
                                <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-600 p-2 bg-green-50 rounded-lg border border-green-200">
                                    <i class="pi pi-shield text-green-600 text-base sm:text-lg"></i>
                                    <span class="font-medium">Secure checkout guaranteed</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-600 p-2 bg-blue-50 rounded-lg border border-blue-200">
                                    <i class="pi pi-replay text-blue-600 text-base sm:text-lg"></i>
                                    <span class="font-medium">30-day return policy</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Dialog - Responsive -->
        <Dialog
            v-model:visible="showCheckout"
            modal
            header="Checkout"
            :style="{ width: '95vw', maxWidth: '1200px' }"
            :breakpoints="{ '1199px': '90vw', '575px': '95vw' }"
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
/* Custom scrollbar for better mobile experience */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #2563eb, #4f46e5);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #1d4ed8, #4338ca);
}
</style>