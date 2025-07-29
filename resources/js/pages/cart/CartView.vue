<template>
    <div>
        <div class="mb-2">
            <form class="flex justify-center items-center">
                <div class="lg:w-3/4 max-lg:w-full rounded bg-white p-2 overflow-auto">
                    <h1 class="text-2xl font-semibold px-5 py-3 border-b border-black mb-2">MY CART</h1>
                    <div v-for="(cart, index) in carts" class="flex border-b border-black" :key="index">
                        <div class="p-2 flex items-center justify-center">
                            <Checkbox v-model="form.carts[index].checked" binary />
                        </div>
                        <div class="p-2 flex items-center justify-center">
                            <img :src="UrlUtil.getBaseAppUrl(`storage/images/product/${cart.product.product_image}`)"
                            :alt="cart.product.product_name" class="w-36 h-36">
                        </div>
                        <div class="p-2 grow">
                            <h2 class="text-lg mb-2">{{ cart.product.product_name }}</h2>
                            <p class="ps-2 font-semibold mb-2">{{ CurrencyUtil.formatCurrency(cart.product.product_price) }}</p>
                            <div class="flex flex-col">
                                <span class="text-sm ps-2">Available: {{ cart.product.product_quantity }}</span>
                                <div class="ps-2">
                                    <InputNumber v-model="form.carts[index].quantity" :min="1" :max="cart.product.product_quantity"
                                    :invalid="form.carts[index].quantity <= 0 || form.carts[index].quantity > cart.product.product_quantity"
                                    class="w-24" />
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
                        <p>Total: <span id="total" class="font-semibold">{{ subTotal() }}</span></p>
                    </div>
                    <div class="flex justify-end">
                        <Button label="Checkout" icon="pi pi-shopping-cart" class="primary-bg" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup lang="ts">
import { CartInterface } from '@/interfaces/CartInterface';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import UrlUtil from '@/utils/UrlUtil';
import { onMounted, reactive, ref } from 'vue';
import { useToast } from 'vue-toastification';

const loadService = useAxiosUtil<null, CartInterface[]>();
const carts = ref<CartInterface[]>([]);
const toast = useToast();
const form : {
    carts: {
        checked: boolean;
        cart_id: number;
        quantity: number;
    }[];
} = reactive({
    carts: [],
});

const subTotal = () => {
    console.log(form.carts);
    
    let total = 0;
    form.carts.forEach(cart => {
        if (cart.checked) {
            const find = carts.value.find(c => c.cart_id === cart.cart_id);
            if (find) {
                total += find.product.product_price * cart.quantity;
            }
        }
    });
    return CurrencyUtil.formatCurrency(total);
}

const setCartForm = () => {
    form.carts = [];
    form.carts = carts.value.map(cart => ({
        checked: false,
        cart_id: cart.cart_id,
        quantity: cart.quantity,
    }));
}

const load = async () => {
    await loadService.get("customer/carts").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            carts.value = loadService.request.data;
            setCartForm();
        }
        else {
            toast.error(loadService.request.message ?? 'Failed to load carts');
        }
    });
}

onMounted(() => {
    load();
});
</script>