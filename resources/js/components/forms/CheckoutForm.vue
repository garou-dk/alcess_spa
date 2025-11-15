<template>
    <div class="py-4">
        <div v-if="!loadAddressService.request.loading">
            <form @submit.prevent="handleSubmit()" v-if="locationSelected" class="space-y-6">
                <!-- Customer Information Card -->
                <div class="rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="rounded-full bg-blue-600 p-2">
                            <i class="pi pi-user text-white text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800">Customer Information</h2>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-blue-100">
                        <div class="flex items-center gap-3">
                            <i class="pi pi-user text-blue-600"></i>
                            <div>
                                <p class="font-semibold text-gray-800">{{ Page.user.full_name }}</p>
                                <p class="text-sm text-gray-600 flex items-center gap-2 mt-1">
                                    <i class="pi pi-phone text-xs"></i>
                                    {{ locationSelected.contact_number }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items Card -->
                <div class="rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-gray-700 p-2">
                                <i class="pi pi-shopping-bag text-white text-lg"></i>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800">Order Items</h2>
                            <span class="ml-auto bg-gray-700 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                {{ props.products.length }} {{ props.products.length === 1 ? 'item' : 'items' }}
                            </span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <div v-for="(data, index) in props.products" :key="index" class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex gap-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <div class="h-20 w-20 rounded-lg overflow-hidden border-2 border-gray-100">
                                        <img
                                            v-if="data.product.product_image"
                                            :src="
                                                UrlUtil.getBaseAppUrl(
                                                    `storage/images/product/${data.product.product_image}`,
                                                )
                                            "
                                            :alt="data.product.product_name"
                                            class="h-full w-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200"
                                        >
                                            <i class="pi pi-image text-3xl text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- Product Details -->
                                <div class="flex-1 min-w-0 flex flex-col justify-center">
                                    <h3 class="font-semibold text-gray-800 truncate mb-1">
                                        {{ data.product.product_name }}
                                    </h3>
                                    <div class="flex items-center gap-4 text-sm">
                                        <span class="text-gray-600">
                                            {{ CurrencyUtil.formatCurrency(data.product.product_price) }}
                                        </span>
                                        <span class="text-gray-400">×</span>
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded font-medium">
                                            Qty: {{ data.quantity }}
                                        </span>
                                    </div>
                                </div>
                                <!-- Item Total -->
                                <div class="flex items-center">
                                    <span class="font-bold text-lg text-gray-800">
                                        {{ CurrencyUtil.formatCurrency(data.product.product_price * data.quantity) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment & Delivery Card -->
                <div class="rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-200 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-green-600 p-2">
                                <i class="pi pi-credit-card text-white text-lg"></i>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800">Payment & Delivery</h2>
                        </div>
                    </div>
                    <div class="p-5 space-y-5">
                        <!-- Order Type -->
                        <div>
                            <InputForm
                                :errors="errors.order_type"
                                label-name="Order Type*"
                                id="order_type"
                                tag="span"
                            >
                                <Select
                                    v-model="form.order_type"
                                    :options="orderTypes"
                                    :invalid="errors.order_type.length > 0"
                                    placeholder="Select Order Type"
                                    class="w-full"
                                >
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value" class="flex items-center gap-2">
                                            <i :class="slotProps.value === 'Delivery' ? 'pi pi-truck' : 'pi pi-shopping-bag'" class="text-sm"></i>
                                            <span>{{ slotProps.value }}</span>
                                        </div>
                                        <span v-else>{{ slotProps.placeholder }}</span>
                                    </template>
                                    <template #option="slotProps">
                                        <div class="flex items-center gap-2">
                                            <i :class="slotProps.option === 'Delivery' ? 'pi pi-truck' : 'pi pi-shopping-bag'" class="text-sm"></i>
                                            <span>{{ slotProps.option }}</span>
                                        </div>
                                    </template>
                                </Select>
                            </InputForm>
                        </div>

                        <!-- Delivery Address (shown only for Delivery) -->
                        <div v-if="form.order_type == 'Delivery'" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <i class="pi pi-map-marker text-blue-600 text-xl mt-1"></i>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-2">Delivery Address</h3>
                                    <p class="text-sm text-gray-700 leading-relaxed">
                                        {{ fullAddress }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div v-if="form.order_type">
                            <InputForm
                                :errors="errors.payment_method"
                                label-name="Payment Method*"
                                id="payment_method"
                                tag="span"
                            >
                                <Select
                                    v-model="form.payment_method"
                                    :options="paymentMethodAvailable"
                                    :invalid="errors.payment_method.length > 0"
                                    placeholder="Select Payment Method"
                                    class="w-full"
                                >
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value" class="flex items-center gap-2">
                                            <i :class="getPaymentIcon(slotProps.value)" class="text-sm"></i>
                                            <span>{{ slotProps.value }}</span>
                                        </div>
                                        <span v-else>{{ slotProps.placeholder }}</span>
                                    </template>
                                    <template #option="slotProps">
                                        <div class="flex items-center gap-2">
                                            <i :class="getPaymentIcon(slotProps.option)" class="text-sm"></i>
                                            <span>{{ slotProps.option }}</span>
                                        </div>
                                    </template>
                                </Select>
                            </InputForm>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Card -->
                <div class="rounded-xl bg-gradient-to-br from-gray-800 to-gray-900 text-white shadow-lg overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-white/10 p-2">
                                <i class="pi pi-calculator text-white text-lg"></i>
                            </div>
                            <h2 class="text-lg font-bold">Order Summary</h2>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <!-- Subtotal -->
                        <div class="flex justify-between items-center text-gray-300">
                            <span>Subtotal</span>
                            <span class="font-semibold text-white text-lg">
                                {{ CurrencyUtil.formatCurrency(totalCost) }}
                            </span>
                        </div>

                        <!-- Delivery Fee -->
                        <div v-if="form.order_type" class="flex justify-between items-center text-gray-300">
                            <span>Delivery Fee</span>
                            <span v-if="form.order_type == 'Pickup'" class="text-green-400 font-semibold">
                                FREE
                            </span>
                            <span v-else class="text-amber-400 text-sm">
                                To be determined
                            </span>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-700 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold">Total</span>
                                <div class="text-right">
                                    <span class="text-2xl font-bold text-green-400">
                                        {{ CurrencyUtil.formatCurrency(totalCost) }}
                                    </span>
                                    <p v-if="form.order_type && form.order_type != 'Pickup'" class="text-xs text-amber-400 mt-1">
                                        + Delivery fee (TBD)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Info Message -->
                        <div v-if="form.order_type == 'Delivery'" class="bg-white/10 rounded-lg p-3 text-sm text-gray-300 flex items-start gap-2">
                            <i class="pi pi-info-circle text-blue-400 mt-0.5"></i>
                            <p>Delivery fee will be determined by the seller after order approval based on your location.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center pt-2">
                    <Button
                        type="submit"
                        label="Place Order"
                        icon="pi pi-check-circle"
                        class="bg-green-600 hover:bg-green-700 border-green-600 text-white font-bold text-lg px-8 py-3 shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50"
                        :disabled="!form.payment_method || !form.order_type"
                        :loading="submitService.request.loading"
                    />
                </div>

                <!-- Security Notice -->
                <div class="text-center text-sm text-gray-500 flex items-center justify-center gap-2">
                    <i class="pi pi-lock text-green-600"></i>
                    <span>Your payment information is secure and encrypted</span>
                </div>
            </form>

            <!-- No Address Set State -->
            <div v-else class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-100 mb-4">
                    <i class="pi pi-map-marker text-4xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No Address Set</h3>
                <p class="text-gray-600">Please set your delivery address before placing an order.</p>
            </div>
        </div>

        <!-- Loading State -->
        <div v-else class="flex justify-center items-center py-16">
            <div class="text-center">
                <PageLoader />
                <p class="text-gray-600 mt-4">Loading your information...</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { IAddress } from '@/interfaces/IAddress';
import { IOrder } from '@/interfaces/IOrder';
import { ProductInterface } from '@/interfaces/ProductInterface';
import Page from '@/stores/Page';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import UrlUtil from '@/utils/UrlUtil';
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

interface IForm {
    order_type: string | null;
    payment_method: string | null;
    products: {
        product_id: number;
        quantity: number;
    }[];
}

interface IFormError {
    order_type: string[];
    payment_method: string[];
    products: {
        product_id: string[];
        quantity: string[];
    }[];
}

interface Props {
    products: {
        product: ProductInterface,
        quantity: number
    }[];
}

const router = useRouter();

const orderTypes = ref<string[]>([
    'Delivery'
]);

const paymentDeliveryMethods = ref<string[]>([
    'Cash on Delivery',
    'Online Payment',
]);

const paymentPickupMethods = ref<string[]>([
    'Pickup Payment',
    'Online Payment',
]);

const paymentMethodAvailable = ref<string[]>([]);

const toast = useToast();
const props = defineProps<Props>();
const locationSelected = ref<IAddress | null>(null);
const loadAddressService = useAxiosUtil<null, IAddress>();
const fullAddress = computed(() => {
    return locationSelected.value ? 
        `${locationSelected.value.other_details}, ${locationSelected.value.barangay.barangay_name}, ${locationSelected.value.barangay.municity.municity_name}, ${locationSelected.value.barangay.municity.province.province_name}, ${locationSelected.value.barangay.municity.province.region.region_name}, ${locationSelected.value.barangay.municity.province.region.island_group.island_group_name}, ${locationSelected.value.postal_code}` : 
        '';
});
const totalCost = computed(() => {
    return props.products.reduce((total, product) => {
        return total + (product.product.product_price * product.quantity);
    }, 0);
});

const getPaymentIcon = (method: string) => {
    if (method.includes('Online')) return 'pi pi-credit-card';
    if (method.includes('Cash')) return 'pi pi-money-bill';
    return 'pi pi-wallet';
};

const loadAddress = async () => {
    await loadAddressService.get(`customer/addresses/${Page.user.user_id}`).then(() => {
        if (loadAddressService.request.status === 200 && loadAddressService.request.data) {
            locationSelected.value = loadAddressService.request.data;
        }
    });
}

const form : IForm = reactive({
    order_type: null,
    payment_method: null,
    products: props.products.map((product) => {
        return {
            product_id: product.product.product_id,
            quantity: product.quantity
        }
    })
});

const errors : IFormError = reactive({
    order_type: [],
    payment_method: [],
    products: props.products.map((product) => {
        return {
            product_id: [],
            quantity: []
        }
    })
});

watch(
    () => form.order_type,
    () => {
        if (form.order_type) {
            if (form.order_type === 'Pickup') {
                paymentMethodAvailable.value = paymentPickupMethods.value;
            }
            else {
                paymentMethodAvailable.value = paymentDeliveryMethods.value;
            }
        }
        else {
            form.payment_method = null;
            paymentMethodAvailable.value = [];
        }
    },
);

const submitService = useAxiosUtil<IForm, IOrder>();

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearErrors();

    if (!form.order_type) {
        errors.order_type.push("Order type is required");
    }
    if (!form.payment_method) {
        errors.payment_method.push("Payment method is required");
    }

    if (form.order_type && !orderTypes.value.includes(form.order_type)) {
        errors.order_type.push("Order type is invalid");
    }

    if (form.order_type && !orderTypes.value.includes(form.order_type) && form.payment_method) {
        if (form.order_type == 'Pickup' && !paymentPickupMethods.value.includes(form.payment_method)) {
            errors.payment_method.push("Payment method is invalid");
        }
        else if (form.order_type == 'Delivery' && !paymentDeliveryMethods.value.includes(form.payment_method)) {
            errors.payment_method.push("Payment method is invalid");
        }
    }

    const hasErrors = [errors.order_type.length > 0, errors.payment_method.length > 0];
    return hasErrors.includes(true) ? false : form;
}

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService.post("customer/orders", data).then(() => {
            if (
                submitService.request.status === 200 &&
                submitService.request.data
            ) {
                toast.success(submitService.request.message ?? "Order placed successfully.");
                router.push({ name: "customer-order" });
            } else {
                toast.error(
                    submitService.request.message ?? "Please try again",
                );
                if (submitService.request.errors) {
                    Object.keys(submitService.request.errors).forEach((key) => {
                        errors[key] = submitService.request.errors[key];
                    });
                }
            }
        });
    }
    else {
        toast.error("Please fill in the required fields.");
    }
}

const emit = defineEmits(['cb']);

watch(() => submitService.request.status, (newStatus) => {
    if (newStatus === 200) {
        emit('cb');
    }
});

onMounted(() => {
    loadAddress();
});
</script>