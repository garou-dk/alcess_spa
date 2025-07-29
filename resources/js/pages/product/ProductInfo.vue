<template>
    <div>
        <div v-if="product != null && !loadService.request.loading" class="flex flex-wrap items-center justify-center">
            <div class="lg:w-3/4 max-lg:w-full p-2 bg-white rounded shadow flex flex-wrap mb-2">
                <div class="p-2 lg:w-1/2 max-lg:w-full flex items-center justify-center">
                    <div>
                        <div v-if="product.featured_images.length > 0 || product.product_image" class="w-96">
                            <Carousel :value="product.featured_images" :numVisible="1" :numScroll="1">
                                <template #item="slotProps">
                                    <div class="m-2 p-4">
                                        <div class="mb-4">
                                            <div class="flex justify-center">
                                                <button @click="selectedVideo = slotProps.data.featured_image"
                                                    v-if="ValidatorUtil.getFileType(slotProps.data.featured_image) == 'video'"
                                                    type="button" class="relative cursor-pointer inline-block">
                                                    <span
                                                        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10">
                                                        <svg class="w-10 h-10 text-white drop-shadow-md"
                                                            fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M8 5v14l11-7z" />
                                                        </svg>
                                                    </span>
                                                    <img :src="UrlUtil.getBaseAppUrl(`storage/images/thumbnail/${slotProps.data.thumbnail}`)"
                                                        :alt="slotProps.data.name" class="block object-cover" />
                                                </button>
                                                <button
                                                    v-else-if="ValidatorUtil.getFileType(slotProps.data.featured_image) == 'image'"
                                                    type="button" class="cursor-pointer">
                                                    <Image alt="Image" preview>
                                                        <template #previewicon>
                                                            <i class="pi pi-eye"></i>
                                                        </template>
                                                        <template #image>
                                                            <img :src="UrlUtil.getBaseAppUrl(`storage/images/thumbnail/${slotProps.data.thumbnail}`)"
                                                                alt="image" />
                                                        </template>
                                                        <template #original="slotProp2">
                                                            <img :src="UrlUtil.getBaseAppUrl(`storage/images/product/${slotProps.data.featured_image}`)"
                                                                alt="preview" :style="slotProp2.style" />
                                                        </template>
                                                    </Image>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Carousel>
                        </div>
                        <div v-else
                            class="flex items-center justify-center w-96 h-96 bg-gray-100 border border-gray-200 mb-5">
                            <i class="pi pi-camera text-4xl text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div class="p-2 lg:w-1/2 max-lg:w-full">
                    <p class="text-3xl">{{ product.product_name }}</p>
                    <p class="mb-3">SKU: {{ product.sku }}</p>
                    <p class="mb-2 text-3xl">{{ CurrencyUtil.formatCurrency(product.product_price) }}</p>
                    <div class="text-blue-600 text-3xl flex gap-1 mb-3">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </div>
                    <form method="POST" @submit.prevent="submitForm()">
                        <div class="mb-3">
                            <p class="text-gray-700">Quantity</p>
                            <p class="text-xs text-gray-500 mb-1">
                                {{ product.product_quantity }} in stock
                                {{ product.unit.unit_name }}/s</p>
                            <InputForm :errors="[]" id="quantity" label-name="" tag="label">
                                <InputNumber v-model="form.quantity" :invalid="errors.quantity.length > 0"
                                    placeholder="Enter Quantity" fluid input-id="quantity" :min="1"
                                    :max="product.product_quantity" />
                            </InputForm>
                        </div>
                        <div class="flex justify-center mt-5 mb-2 w-full">
                            <Button type="submit" :loading="submitService.request.loading" label="Add to Cart" class="primary-bg" icon="pi pi-shopping-cart" />
                        </div>
                    </form>
                </div>
                <div class="w-full flex flex-wrap">
                    <div class="lg:w-1/2 max-lg:w-full p-2">
                        <h2 class="mb-2 text-xl">PRODUCT DESCRIPTION</h2>
                        <p>{{ product.description }}</p>
                    </div>
                    <div class="lg:w-1/2 max-lg:w-full p-2">
                        <div v-if="product.specifications.length > 0">
                            <h2 class="text-xl">PRODUCT SPECIFICATIONS</h2>
                            <p v-for="(specification, index) in product.specifications" :key="index">{{
                                specification.specification_name }}: {{ specification.specification_value }}</p>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
            <div class="lg:w-3/4 max-lg:w-full p-3 bg-white rounded shadow">
                <h1 class="text-2xl mb-2">Customer Reviews</h1>

                <!-- Rating Summary and Filter Buttons -->
                <div class="flex flex-wrap px-2">
                    <div class="lg:shrink max-lg:w-full px-2 flex flex-wrap items-center justify-center">
                        <div>
                            <p class="text-2xl text-blue-700 text-center">4.5 out of 5</p>
                            <div class="text-blue-600 text-3xl flex gap-1 mb-3">
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-fill"></i>
                                <i class="pi pi-star-half"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex flex-wrap px-2 gap-2 justify-evenly grow items-center max-lg:w-full">
                        <button type="button"
                            class="bg-blue-600 text-white py-2 px-4 rounded shadow hover:bg-blue-700 grow">All</button>
                        <button type="button"
                            class="bg-gray-300 text-black border border-blue-600 hover:bg-blue-600 hover:text-white py-2 px-4 rounded grow">
                            5 Stars (4)
                        </button>
                        <button type="button"
                            class="bg-gray-300 text-black border border-blue-600 hover:bg-blue-600 hover:text-white py-2 px-4 rounded grow">
                            4 Stars (1)
                        </button>
                        <button type="button"
                            class="bg-gray-300 text-black border border-blue-600 hover:bg-blue-600 hover:text-white py-2 px-4 rounded grow">
                            3 Stars (0)
                        </button>
                        <button type="button"
                            class="bg-gray-300 text-black border border-blue-600 hover:bg-blue-600 hover:text-white py-2 px-4 rounded grow">
                            2 Stars (0)
                        </button>
                        <button type="button"
                            class="bg-gray-300 text-black border border-blue-600 hover:bg-blue-600 hover:text-white py-2 px-4 rounded grow">
                            1 Star (0)
                        </button>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="mt-4">
                    <ul class="bg-gray-50 rounded shadow-md divide-y">
                        <!-- Review Item -->
                        <li class="flex items-start gap-4 p-4">
                            <img class="w-10 h-10 rounded-md"
                                src="https://img.daisyui.com/images/profile/demo/1@94.webp" />
                            <div class="flex-1">
                                <div class="font-semibold">Dio Lupa</div>
                                <div class="text-blue-600 text-sm flex gap-1">
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                </div>
                                <p class="text-xs mt-1">Sleek design, great battery life, and performance is top-notch!
                                </p>
                            </div>
                        </li>

                        <li class="flex items-start gap-4 p-4">
                            <img class="w-10 h-10 rounded-md"
                                src="https://img.daisyui.com/images/profile/demo/4@94.webp" />
                            <div class="flex-1">
                                <div class="font-semibold">Ellie Beilish</div>
                                <div class="text-blue-600 text-sm flex gap-1">
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                </div>
                                <p class="text-xs mt-1">Super fast and reliable—perfect for work and gaming. Love it!
                                </p>
                            </div>
                        </li>

                        <li class="flex items-start gap-4 p-4">
                            <img class="w-10 h-10 rounded-md"
                                src="https://img.daisyui.com/images/profile/demo/3@94.webp" />
                            <div class="flex-1">
                                <div class="font-semibold">Sabrino Gardener</div>
                                <div class="text-blue-600 text-sm flex gap-1">
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-fill"></i>
                                    <i class="pi pi-star-half"></i>
                                </div>
                                <p class="text-xs mt-1">Best laptop I’ve owned! Lightweight, powerful, and easy to use.
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
        <div v-if="loadService.request.loading" class="flex justify-center p-5">
            <PageLoader />
        </div>
        <Dialog v-model:visible="showVideo" modal header="Play Video" :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" @show="playVideo" @hide="pauseVideo">
            <video ref="videoPlayer" class="w-full" controls :key="selectedVideo">
                <source :src="videoUrl" type="video/mp4" />
                Your browser does not support the video tag.
            </video>
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import { CartFormInterface } from '@/interfaces/CartInterface';
import { ProductInterface } from '@/interfaces/ProductInterface';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import UrlUtil from '@/utils/UrlUtil';
import ValidatorUtil from '@/utils/ValidatorUtil';
import { computed, nextTick, onMounted, reactive, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

const loadService = useAxiosUtil<null, ProductInterface>();
const submitService = useAxiosUtil<CartFormInterface, null>();
const product = ref<ProductInterface | null>(null);
const route = useRoute();
const showVideo = ref<boolean>(false);
const selectedVideo = ref<string | null>(null);
const toast = useToast();
const router = useRouter();
const videoUrl = computed(() =>
    selectedVideo.value
        ? UrlUtil.getBaseAppUrl(`storage/images/product/${selectedVideo.value}`)
        : ''
);
const videoPlayer = ref(null);

const form: CartFormInterface = reactive({
    product_id: null,
    quantity: 1,
});

const errors = reactive({
    product_id: [],
    quantity: [],
});

const clearErrors = () => {
    errors.product_id = [];
    errors.quantity = [];
}

const load = async () => {
    await loadService.get(`find-product/${route.params['id']}`).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            product.value = loadService.request.data;
        }
        else {
            toast.error(loadService.request.message ?? 'Failed to load product');
            router.push({ name: 'customer.home.index' });
        }
    });
}

const validate = () => {
    clearErrors();
    if (!product.value) {
        errors.product_id.push('Product is required');
    } else {
        form.product_id = product.value.product_id;
    }
    if (!form.quantity || form.quantity < 1) {
        errors.quantity.push('Quantity must be at least 1');
    } else if (form.quantity > product.value?.product_quantity) {
        errors.quantity.push(`Only ${product.value?.product_quantity} available`);
    }

    const hasErrors = [errors.product_id.length > 0, errors.quantity.length > 0];

    return hasErrors.includes(true) ? false : form;
}

const submitForm = async () => {
    const data = validate();

    if (data) {
        await submitService.post('customer/carts', data).then(() => {
            if (submitService.request.status === 200) {
                toast.success(submitService.request.message ?? 'Product added to cart successfully');
                form.quantity = 1;
            } else {
                toast.error(submitService.request.message ?? 'Failed to add product to cart');
                if (submitService.request.errors) {
                    Object.keys(submitService.request.errors).forEach((key) => {
                        errors[key] = submitService.request.errors[key];
                    });
                }
            }
        });
    }
    else {
        toast.error('Please fix the errors in the form');
    }
}

const playVideo = () => {
    nextTick(() => {
        const video = videoPlayer.value
        if (video) {
            video.load()
            video.play().catch(err => {
                console.warn('Auto-play failed:', err)
            })
        }
    })
}

// Arrow function to pause and reset video on dialog hide
const pauseVideo = () => {
    const video = videoPlayer.value
    if (video) {
        video.pause()
        video.currentTime = 0
    }
}

watch(selectedVideo, () => {
    if (selectedVideo.value) {
        showVideo.value = true;
    } else {
        showVideo.value = false;
    }
})

onMounted(() => {
    load();
});
</script>