<template>
    <div class="py-3">
        <div v-if="!loadAddressService.request.loading">
            <form @submit.prevent="handleSubmit()" v-if="locationSelected" class="flex flex-col gap-2">
                <div class="p-2 bg-gray-100 rounded">
                    <p>
                        <span class="font-semibold">{{ Page.user.full_name }}</span> ({{ locationSelected.contact_number }})
                    </p>
                </div>
                <div>
                    <div v-for="(data, index) in props.products" class="p-2 bg-gray-100 rounded flex gap-2">
                        <div
                            v-if="data.product.product_image"
                            class="h-24 w-24 min-w-24"
                        >
                            <img
                                :src="
                                    UrlUtil.getBaseAppUrl(
                                        `storage/images/product/${data.product.product_image}`,
                                    )
                                "
                                :alt="data.product.product_name"
                            />
                        </div>
                        <div
                            v-else
                            class="flex h-24 w-24 min-w-24 items-center justify-center bg-gray-200"
                        >
                            <i
                                class="pi pi-camera text-4xl text-gray-400"
                            ></i>
                        </div>
                        <div class="grow whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ data.product.product_name }}
                        </div>
                        <div>
                            {{ CurrencyUtil.formatCurrency(data.product.product_price) }} (x{{ data.quantity }})
                        </div>
                    </div>
                </div>
                <div class="p-2 bg-gray-100 rounded flex flex-col gap-2">
                    <h1 class="font-semibold mb-2">Payment Methods</h1>
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
                            />
                        </InputForm>
                    </div>
                    <div v-if="form.order_type == 'Delivery'">
                        <InputForm
                            :errors="[]"
                            label-name="Delivery Address"
                            id="delivery_address"
                            tag="label"
                        >
                            <Textarea
                                :model-value="fullAddress"
                                readonly
                                id="delivery_address"
                            />
                        </InputForm>
                    </div>
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
                            />
                        </InputForm>
                    </div>
                    <div>
                        <div class="p-2 bg-gray-100 rounded">
                            <p class="text-end">Sub Total: {{ CurrencyUtil.formatCurrency(totalCost) }}</p>
                            <p v-if="form.order_type" class="text-end">Delivery Fee: {{ form.order_type == "Pickup" ? "N/A" : "To be determined by the seller (After Approval)" }}</p>
                            <p v-if="form.order_type" class="text-end">Total: {{ CurrencyUtil.formatCurrency(totalCost) }}{{ form.order_type == "Pickup" ? " " : " + To be determined" }}</p>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <Button
                            type="submit"
                            label="Place Order"
                            icon="pi pi-save"
                            class="primary-bg"
                            :disabled="!form.payment_method || !form.order_type"
                            :loading="submitService.request.loading"
                        />
                    </div>
                </div>
            </form>
            <div v-else>
                <p class="text-center">Please set your address first.</p>
            </div>
        </div>
        <div v-else class="flex justify-center p-5">
            <PageLoader />
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

onMounted(() => {
    loadAddress();
});
</script>