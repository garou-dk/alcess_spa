<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="container mx-auto px-4">
            <div class="mx-auto max-w-6xl">
                <!-- Header -->
                <div class="mb-8 text-center">
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">
                        Shopping Cart
                    </h1>
                    <p class="text-gray-600">
                        {{ carts.length }} {{ carts.length === 1 ? 'item' : 'items' }} in your cart
                    </p>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="rounded-2xl bg-white shadow-lg overflow-hidden">
                            <!-- Select All Header -->
                            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <Checkbox
                                        v-model="selectAll"
                                        binary
                                        @change="toggleSelectAll"
                                    />
                                    <span class="text-sm font-medium text-gray-700">
                                        Select All
                                    </span>
                                    <span v-if="selectedCount > 0" class="ml-auto text-sm text-gray-600">
                                        {{ selectedCount }} selected
                                    </span>
                                </div>
                            </div>

                            <!-- Cart Items List -->
                            <div class="divide-y divide-gray-100">
                                <div
                                    v-for="(cart, index) in carts"
                                    :key="index"
                                    class="group p-6 transition-all duration-200 hover:bg-gray-50"
                                    :class="{ 'bg-blue-50/30': form.carts[index].checked }"
                                >
                                    <div class="flex gap-4">
                                        <!-- Checkbox -->
                                        <div class="flex items-start pt-2">
                                            <Checkbox
                                                v-model="form.carts[index].checked"
                                                binary
                                                class="transition-transform hover:scale-110"
                                            />
                                        </div>

                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="h-28 w-28 overflow-hidden rounded-xl border-2 border-gray-100 transition-all duration-200 group-hover:border-gray-200 group-hover:shadow-md"
                                            >
                                                <img
                                                    v-if="cart.product.product_image"
                                                    :src="
                                                        UrlUtil.getBaseAppUrl(
                                                            `storage/images/product/${cart.product.product_image}`,
                                                        )
                                                    "
                                                    :alt="cart.product.product_name"
                                                    class="h-full w-full object-cover"
                                                />
                                                <div
                                                    v-else
                                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200"
                                                >
                                                    <i class="pi pi-image text-4xl text-gray-400"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Product Details -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-semibold text-gray-800 mb-2 truncate">
                                                {{ cart.product.product_name }}
                                            </h3>
                                            
                                            <div class="mb-3">
                                                <span class="text-2xl font-bold text-blue-600">
                                                    {{
                                                        CurrencyUtil.formatCurrency(
                                                            cart.product.product_price,
                                                        )
                                                    }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                                    <i class="pi pi-check-circle text-xs"></i>
                                                    {{ cart.product.product_quantity }} in stock
                                                </span>
                                            </div>

                                            <!-- Quantity Controls -->
                                            <div class="flex items-center gap-4">
                                                <span class="text-sm font-medium text-gray-600">Quantity:</span>
                                                <InputGroup class="w-[140px]">
                                                    <Button
                                                        type="button"
                                                        icon="pi pi-minus"
                                                        severity="secondary"
                                                        size="small"
                                                        @click="removeQuantity(index)"
                                                        :disabled="form.carts[index].quantity <= 1"
                                                        class="hover:bg-gray-200 transition-colors"
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
                                                        class="text-center font-semibold"
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
                                                        class="hover:bg-gray-200 transition-colors"
                                                    />
                                                </InputGroup>
                                            </div>

                                            <!-- Subtotal for this item -->
                                            <div class="mt-3">
                                                <span class="text-sm text-gray-600">Subtotal: </span>
                                                <span class="text-lg font-bold text-gray-800">
                                                    {{ CurrencyUtil.formatCurrency(cart.product.product_price * form.carts[index].quantity) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Delete Button -->
                                        <div class="flex items-start">
                                            <Button
                                                type="button"
                                                icon="pi pi-trash"
                                                severity="danger"
                                                text
                                                rounded
                                                @click="removeSingleItem(index)"
                                                class="hover:scale-110 transition-transform"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div v-if="carts.length === 0" class="py-16 text-center">
                                    <i class="pi pi-shopping-cart text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Your cart is empty</h3>
                                    <p class="text-gray-500">Add some items to get started!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-8 rounded-2xl bg-white shadow-lg p-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">
                                Order Summary
                            </h2>

                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-gray-600">
                                    <span>Items ({{ selectedCount }}):</span>
                                    <span class="font-semibold">{{ subTotal() }}</span>
                                </div>
                                
                                <div class="flex justify-between text-gray-600">
                                    <span>Shipping:</span>
                                    <span class="font-semibold text-green-600">To be calculated</span>
                                </div>

                                <div class="border-t pt-4 flex justify-between text-xl font-bold text-gray-800">
                                    <span>Total:</span>
                                    <span class="text-blue-600">{{ subTotal() }}</span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <Button
                                    :disabled="selectedCount === 0"
                                    type="button"
                                    label="Proceed to Checkout"
                                    icon="pi pi-shopping-cart"
                                    class="w-full bg-blue-600 hover:bg-blue-700 border-blue-600 text-white font-semibold py-3 transition-all duration-200 hover:shadow-lg disabled:opacity-50"
                                    @click="checkoutItems()"
                                />
                                
                                <Button
                                    :disabled="selectedCount === 0"
                                    type="button"
                                    label="Remove Selected"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    outlined
                                    class="w-full font-semibold py-3 transition-all duration-200 hover:shadow-md"
                                    @click="removeSelected()"
                                />
                            </div>

                            <!-- Security Badge -->
                            <div class="mt-6 pt-6 border-t">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i class="pi pi-shield text-green-600"></i>
                                    <span>Secure checkout guaranteed</span>
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
            :style="{ width: '75vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
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