<template>
    <div>
        <div class="mb-2">
            <form class="flex items-center justify-center">
                <div
                    class="overflow-auto rounded bg-white p-2 max-lg:w-full lg:w-3/4"
                >
                    <h1
                        class="mb-2 border-b border-black px-5 py-3 text-2xl font-semibold"
                    >
                        MY CART
                    </h1>
                    <div
                        v-for="(cart, index) in carts"
                        class="flex border-b border-black"
                        :key="index"
                    >
                        <div class="flex items-center justify-center p-2">
                            <Checkbox
                                v-model="form.carts[index].checked"
                                binary
                            />
                        </div>
                        <div class="flex items-center justify-center p-2">
                            <div
                                v-if="cart.product.product_image"
                                class="h-24 w-24"
                            >
                                <img
                                    :src="
                                        UrlUtil.getBaseAppUrl(
                                            `storage/images/product/${cart.product.product_image}`,
                                        )
                                    "
                                    :alt="cart.product.product_name"
                                />
                            </div>
                            <div
                                v-else
                                class="flex h-24 w-24 items-center justify-center bg-gray-200"
                            >
                                <i
                                    class="pi pi-camera text-4xl text-gray-400"
                                ></i>
                            </div>
                        </div>
                        <div class="grow p-2">
                            <h2 class="mb-2 text-lg">
                                {{ cart.product.product_name }}
                            </h2>
                            <p class="mb-2 ps-2 font-semibold">
                                {{
                                    CurrencyUtil.formatCurrency(
                                        cart.product.product_price,
                                    )
                                }}
                            </p>
                            <div class="flex flex-col">
                                <span class="ps-2 text-sm"
                                    >Available:
                                    {{ cart.product.product_quantity }}</span
                                >
                                <div class="max-w-full ps-2">
                                    <InputGroup class="w-[140px]!">
                                        <Button
                                            type="button"
                                            icon="pi pi-minus"
                                            severity="secondary"
                                            @click="removeQuantity(index)"
                                        />
                                        <InputNumber
                                            size="small"
                                            v-model="form.carts[index].quantity"
                                            :min="1"
                                            :max="cart.product.product_quantity"
                                            :invalid="
                                                form.carts[index].quantity <=
                                                    0 ||
                                                form.carts[index].quantity >
                                                    cart.product
                                                        .product_quantity
                                            "
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
                                            @click="
                                                addQuantity(
                                                    index,
                                                    cart.product
                                                        .product_quantity,
                                                )
                                            "
                                        />
                                    </InputGroup>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-center">
                            <!-- <form action="{{ route('cart.destroy', ['id' => $cart->cart_id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-2xl p-5 cursor-pointer hover:text-red-500">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form> -->
                        </div>
                    </div>
                    <div class="p-2">
                        <p class="text-lg font-semibold">Order Summary</p>
                        <p>
                            Total:
                            <span id="total" class="font-semibold">{{
                                subTotal()
                            }}</span>
                        </p>
                    </div>
                    <div class="flex justify-end gap-2">
                        <Button
                            :disabled="
                                form.carts.filter((cart) => cart.checked)
                                    .length === 0
                            "
                            type="button"
                            label="Remove"
                            icon="pi pi-trash"
                            severity="danger"
                            @click="removeSelected()"
                        />
                        <Button
                            :disabled="
                                form.carts.filter((cart) => cart.checked)
                                    .length === 0
                            "
                            type="button"
                            label="Checkout"
                            icon="pi pi-shopping-cart"
                            class="primary-bg"
                            @click="checkoutItems()"
                        />
                    </div>
                </div>
            </form>
        </div>
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
import { onMounted, reactive, ref } from "vue";
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
    product: ProductInterface,
    quantity: number
}[]>([]);

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
}

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
};

onMounted(() => {
    load();
});
</script>
