<template>
    <div class="py-3 sm:py-4 px-2 sm:px-0">
        <!-- Sold Out / Insufficient Stock Modal -->
        <Dialog
            v-model:visible="showSoldOutModal"
            modal
            header="Items Unavailable"
            :style="{ width: '90vw', maxWidth: '500px' }"
            :breakpoints="{ '575px': '95vw' }"
            :pt="{
                header: {
                    class: '!bg-red-600 !text-white !border-b-2 !border-red-700 !rounded-t-lg'
                },
                headerTitle: {
                    class: '!text-white !font-bold'
                },
                closeButton: {
                    class: 'hover:!bg-red-700 !text-white'
                }
            }"
        >
            <div class="py-4">
                <!-- Warning Icon -->
                <div class="text-center mb-4">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-3">
                        <i class="pi pi-exclamation-triangle text-4xl text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Sorry, Some Items Are No Longer Available</h3>
                    <p class="text-sm text-gray-600">{{ soldOutMessage }}</p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-center gap-3 mt-6">
                    <Button
                        label="Return to Cart"
                        icon="pi pi-shopping-cart"
                        class="!bg-blue-600 hover:!bg-blue-700 !border-blue-600 text-white"
                        @click="handleReturnToCart"
                    />
                </div>
            </div>
        </Dialog>
        <div v-if="!loadAddressService.request.loading">
            <form @submit.prevent="handleSubmit()" v-if="locationSelected" class="space-y-4 sm:space-y-6">
                <!-- Customer Information Card -->
                <div class="rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm">
                    <div class="border-b border-gray-200 px-3 sm:px-4 py-3">
                        <h2 class="text-base sm:text-lg font-bold text-gray-800">Customer Information</h2>
                    </div>
                    <div class="p-3 sm:p-4">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <!-- Profile Picture -->
                            <div class="flex-shrink-0">
                                <div v-if="Page.user.profile_picture" class="w-12 h-12 sm:w-16 sm:h-16 rounded-full overflow-hidden border-2 border-blue-200">
                                    <img
                                        :src="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.profile_picture}`)"
                                        :alt="Page.user.full_name"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <div v-else class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center border-2 border-blue-200">
                                    <span class="text-white font-bold text-lg sm:text-2xl">
                                        {{ Page.user.full_name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                            </div>
                            <!-- Customer Details -->
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-gray-900 text-base sm:text-lg truncate">{{ Page.user.full_name }}</p>
                                <p class="text-sm sm:text-base text-gray-600 flex items-center gap-2 mt-1">
                                    <i class="pi pi-phone text-blue-600"></i>
                                    <span class="truncate">{{ locationSelected.contact_number }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items Card -->
                <div class="rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 px-3 sm:px-4 py-3 flex items-center justify-between">
                        <h2 class="text-base sm:text-lg font-bold text-gray-800">Order Items</h2>
                        <span class="text-sm text-gray-600 font-medium">
                            {{ props.products.length }} {{ props.products.length === 1 ? 'item' : 'items' }}
                        </span>
                    </div>
                    <div class="divide-y divide-gray-100 overflow-y-auto" style="max-height: 400px;">
                        <div v-for="(data, index) in props.products" :key="index" class="p-3 sm:p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex gap-2 sm:gap-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-lg overflow-hidden border-2 border-gray-100">
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
                                            <i class="pi pi-image text-2xl sm:text-3xl text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- Product Details -->
                                <div class="flex-1 min-w-0 flex flex-col justify-center">
                                    <h3 class="font-semibold text-gray-800 text-sm sm:text-base line-clamp-2 sm:truncate mb-1">
                                        {{ data.product.product_name }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm">
                                        <span class="text-gray-600">
                                            {{ CurrencyUtil.formatCurrency(data.product.product_price) }}
                                        </span>
                                        <span class="text-gray-400">×</span>
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded font-medium text-xs">
                                            Qty: {{ data.quantity }}
                                        </span>
                                    </div>
                                </div>
                                <!-- Item Total -->
                                <div class="flex items-center">
                                    <span class="font-bold text-sm sm:text-lg text-gray-800 whitespace-nowrap">
                                        {{ CurrencyUtil.formatCurrency(data.product.product_price * data.quantity) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment & Delivery Card -->
                <div class="rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm">
                    <div class="border-b border-gray-200 px-3 sm:px-4 py-3">
                        <h2 class="text-base sm:text-lg font-bold text-gray-800">Payment & Delivery</h2>
                    </div>
                    <div class="p-3 sm:p-5 space-y-4 sm:space-y-5">
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

                        <!-- Address (shown when order type is selected) -->
                        <div v-if="form.order_type" class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                            <div class="flex items-start gap-2 sm:gap-3">
                                <i class="pi pi-map-marker text-blue-600 text-lg sm:text-xl mt-1"></i>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-800 mb-2 text-sm sm:text-base">
                                        {{ form.order_type === 'Delivery' ? 'Customer Delivery Address' : 'Company Address' }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-gray-700 leading-relaxed break-words">
                                        {{ fullAddress }}
                                    </p>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- Order Summary Card -->
                <div class="rounded-lg sm:rounded-xl bg-gradient-to-br from-gray-800 to-gray-900 text-white shadow-lg overflow-hidden">
                    <div class="px-3 sm:px-5 py-3 sm:py-4 border-b border-gray-700">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="rounded-full bg-white/10 p-1.5 sm:p-2">
                                <i class="pi pi-calculator text-white text-base sm:text-lg"></i>
                            </div>
                            <h2 class="text-base sm:text-lg font-bold">Order Summary</h2>
                        </div>
                    </div>
                    <div class="p-3 sm:p-5 space-y-3 sm:space-y-4">
                        <!-- Subtotal -->
                        <div class="flex justify-between items-center text-gray-300">
                            <span class="text-sm sm:text-base">Subtotal</span>
                            <span class="font-semibold text-white text-base sm:text-lg">
                                {{ CurrencyUtil.formatCurrency(totalCost) }}
                            </span>
                        </div>

                        <!-- Delivery Fee -->
                        <div v-if="form.order_type" class="flex justify-between items-center text-gray-300">
                            <span class="text-sm sm:text-base">Delivery Fee</span>
                            <span v-if="form.order_type == 'Pickup'" class="text-green-400 font-semibold text-sm sm:text-base">
                                FREE
                            </span>
                            <span v-else class="text-amber-400 text-xs sm:text-sm">
                                To be determined
                            </span>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-700 pt-3 sm:pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-lg sm:text-xl font-bold">Total</span>
                                <div class="text-right">
                                    <span class="text-xl sm:text-2xl font-bold text-green-400">
                                        {{ CurrencyUtil.formatCurrency(totalCost) }}
                                    </span>
                                    <p v-if="form.order_type && form.order_type != 'Pickup'" class="text-[10px] sm:text-xs text-amber-400 mt-1">
                                        + Delivery fee (TBD)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Info Message -->
                        <div v-if="form.order_type == 'Delivery'" class="bg-white/10 rounded-lg p-2.5 sm:p-3 text-xs sm:text-sm text-gray-300 flex items-start gap-2">
                            <i class="pi pi-info-circle text-blue-400 mt-0.5 flex-shrink-0"></i>
                            <p class="leading-relaxed">Delivery fee will be determined by the seller after order approval based on your location.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center pt-1 sm:pt-2">
                    <Button
                        type="submit"
                        label="Place Order"
                        icon="pi pi-check-circle"
                        :class="[
                            'w-full sm:w-auto !bg-blue-600 hover:!bg-blue-700 !border-blue-600 text-white font-bold text-base sm:text-lg px-6 sm:px-8 py-2.5 sm:py-3 shadow-lg hover:shadow-xl transition-all duration-200',
                            { 'opacity-50': submitService.request.loading || !form.order_type }
                        ]"
                        :disabled="!form.order_type"
                        :loading="submitService.request.loading"
                    />
                </div>

                <!-- Security Notice -->
                <div class="text-center text-xs sm:text-sm text-gray-500 flex items-center justify-center gap-1.5 sm:gap-2">
                    <i class="pi pi-lock text-green-600 text-sm"></i>
                    <span>Your payment information is secure and encrypted</span>
                </div>
            </form>

            <!-- No Address Set State -->
            <div v-else class="text-center py-12 sm:py-16 px-4">
                <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-red-100 mb-3 sm:mb-4">
                    <i class="pi pi-map-marker text-3xl sm:text-4xl text-red-600"></i>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">No Address Set</h3>
                <p class="text-sm sm:text-base text-gray-600">Please set your delivery address before placing an order.</p>
            </div>
        </div>

        <!-- Loading State -->
        <div v-else class="flex justify-center items-center py-12 sm:py-16">
            <div class="text-center">
                <PageLoader />
                <p class="text-gray-600 mt-4 text-sm sm:text-base">Loading your information...</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Dialog from 'primevue/dialog';
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
    'Delivery',
    'Pickup'
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
const companyAddress = 'Door 1 Yong Building A, Pitchon Street, Magallanes, Davao City, Davao Del Sur, Region XI, Mindanao, 8105';

const fullAddress = computed(() => {
    // If Pickup is selected, show company address
    if (form.order_type === 'Pickup') {
        return companyAddress;
    }
    
    // If Delivery is selected, show customer address
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
            // Automatically set payment method to "Online Payment"
            form.payment_method = 'Online Payment';
        }
        else {
            form.payment_method = null;
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

const showSoldOutModal = ref<boolean>(false);
const soldOutMessage = ref<string>('');

const handleReturnToCart = () => {
    showSoldOutModal.value = false;
    emit('cb'); // This will refresh the cart
};

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
                // Check if this is a stock-related error (status 422 with sold out message)
                const message = submitService.request.message ?? "Please try again";
                
                if (message.includes('sold out') || message.includes('Insufficient stock') || message.includes('no longer available')) {
                    // Show the sold-out modal instead of a toast
                    soldOutMessage.value = message;
                    showSoldOutModal.value = true;
                } else {
                    toast.error(message);
                    if (submitService.request.errors) {
                        Object.keys(submitService.request.errors).forEach((key) => {
                            errors[key] = submitService.request.errors[key];
                        });
                    }
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