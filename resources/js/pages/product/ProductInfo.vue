<template>
    <div>
        <div
            v-if="product != null && !loadService.request.loading"
            class="flex flex-wrap items-center justify-center"
        >
            <div
                class="mb-2 flex flex-wrap rounded bg-white p-2 shadow max-lg:w-full lg:w-3/4"
            >
                <div
                    class="flex items-center justify-center p-2 max-lg:w-full lg:w-1/2"
                >
                    <div class="flex items-center justify-center w-full">
                        <div
                            v-if="carouselImages.length > 0"
                            class="w-96"
                        >
                            <Carousel
                                :value="carouselImages"
                                :numVisible="1"
                                :numScroll="1"
                            >
                                <template #item="slotProps">
                                    <div class="flex items-center justify-center p-4 h-full">
                                        <div class="w-full">
                                            <div class="flex justify-center items-center">
                                                <button
                                                    @click="
                                                        selectedVideo =
                                                            slotProps.data
                                                                .featured_image
                                                    "
                                                    v-if="
                                                        ValidatorUtil.getFileType(
                                                            slotProps.data
                                                                .featured_image,
                                                        ) == 'video'
                                                    "
                                                    type="button"
                                                    class="relative inline-block cursor-pointer"
                                                >
                                                    <span
                                                        class="absolute top-1/2 left-1/2 z-10 -translate-x-1/2 -translate-y-1/2"
                                                    >
                                                        <svg
                                                            class="h-10 w-10 text-white drop-shadow-md"
                                                            fill="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                d="M8 5v14l11-7z"
                                                            />
                                                        </svg>
                                                    </span>
                                                    <img
                                                        :src="
                                                            slotProps.data.isMainProduct
                                                                ? UrlUtil.getBaseAppUrl(
                                                                    `storage/images/product/${slotProps.data.thumbnail}`,
                                                                )
                                                                : UrlUtil.getBaseAppUrl(
                                                                    `storage/images/thumbnail/${slotProps.data.thumbnail}`,
                                                                )
                                                        "
                                                        :alt="
                                                            slotProps.data.name
                                                        "
                                                        class="block object-cover mx-auto"
                                                    />
                                                </button>
                                                <button
                                                    v-else-if="
                                                        ValidatorUtil.getFileType(
                                                            slotProps.data
                                                                .featured_image,
                                                        ) == 'image'
                                                    "
                                                    type="button"
                                                    class="cursor-pointer flex justify-center"
                                                >
                                                    <Image alt="Image" preview>
                                                        <template #previewicon>
                                                            <i
                                                                class="pi pi-eye"
                                                            ></i>
                                                        </template>
                                                        <template #image>
                                                            <img
                                                                :src="
                                                                    slotProps.data.isMainProduct
                                                                        ? UrlUtil.getBaseAppUrl(
                                                                            `storage/images/product/${slotProps.data.thumbnail}`,
                                                                        )
                                                                        : UrlUtil.getBaseAppUrl(
                                                                            `storage/images/thumbnail/${slotProps.data.thumbnail}`,
                                                                        )
                                                                "
                                                                alt="image"
                                                                class="mx-auto"
                                                            />
                                                        </template>
                                                        <template
                                                            #original="slotProp2"
                                                        >
                                                            <img
                                                                :src="
                                                                    UrlUtil.getBaseAppUrl(
                                                                        `storage/images/product/${slotProps.data.featured_image}`,
                                                                    )
                                                                "
                                                                alt="preview"
                                                                :style="
                                                                    slotProp2.style
                                                                "
                                                            />
                                                        </template>
                                                    </Image>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Carousel>
                        </div>
                        <div
                            v-else
                            class="mb-5 flex h-96 w-96 items-center justify-center border border-gray-200 bg-gray-100"
                        >
                            <i class="pi pi-camera text-4xl text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div class="p-2 max-lg:w-full lg:w-1/2">
                    <p class="text-3xl">{{ product.product_name }}</p>
                    <p class="mb-3">SKU: {{ product.sku }}</p>
                    <p class="mb-2 text-3xl">
                        {{ CurrencyUtil.formatCurrency(product.product_price) }}
                    </p>
                    <div class="mb-3 flex gap-1 text-3xl text-blue-600">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </div>
                    <form method="POST" @submit.prevent="submitForm()">
                        <div class="mb-3">
                            <p class="text-gray-700">Quantity</p>
                            <p class="mb-1 text-xs text-gray-500">
                                {{ product.product_quantity }} in stock
                                {{ product.unit.unit_name }}/s
                            </p>
                            <InputForm
                                v-if="Page.user != null"
                                :errors="[]"
                                id="quantity"
                                label-name=""
                                tag="label"
                            >
                                <InputNumber
                                    v-model="form.quantity"
                                    :invalid="errors.quantity.length > 0"
                                    placeholder="Enter Quantity"
                                    fluid
                                    input-id="quantity"
                                    :min="1"
                                    :max="product.product_quantity"
                                />
                            </InputForm>
                        </div>
                        <div v-if="Page.user != null" class="mt-5 mb-2 flex w-full justify-center">
                            <Button
                                type="submit"
                                :loading="submitService.request.loading"
                                label="Add to Cart"
                                class="primary-bg"
                                icon="pi pi-shopping-cart"
                            />
                        </div>
                    </form>
                </div>
                <div class="flex w-full flex-wrap">
                    <div class="p-2 max-lg:w-full lg:w-1/2">
                        <h2 class="mb-2 text-xl">PRODUCT DESCRIPTION</h2>
                        <p>{{ product.description }}</p>
                    </div>
                    <div class="p-2 max-lg:w-full lg:w-1/2">
                        <div v-if="product.specifications.length > 0">
                            <h2 class="text-xl">PRODUCT SPECIFICATIONS</h2>
                            <p
                                v-for="(
                                    specification, index
                                ) in product.specifications"
                                :key="index"
                            >
                                {{ specification.specification_name }}:
                                {{ specification.specification_value }}
                            </p>
                        </div>
                        <br />
                    </div>
                </div>
            </div>
            <div class="rounded bg-white p-3 shadow max-lg:w-full lg:w-3/4">
                <h1 class="mb-4 text-2xl font-semibold">Customer Reviews</h1>

                <!-- Rating Summary and Filter Buttons -->
                <div class="flex flex-wrap gap-4 px-2">
                    <div
                        class="flex flex-wrap items-center justify-center rounded-lg border border-gray-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 max-lg:w-full lg:shrink"
                    >
                        <div class="text-center">
                            <p v-if="product" class="mb-2 text-5xl font-bold text-blue-700">
                                <span v-if="product.rates_avg_rate">
                                    {{ Number(product.rates_avg_rate).toFixed(1) }}
                                </span>
                                <span v-else class="text-3xl text-gray-500">No Ratings</span>
                            </p>
                            <div v-if="product.rates_avg_rate" class="mb-2 flex justify-center gap-1 text-2xl text-yellow-400">
                                <i v-for="n in 5" :key="n" :class="{'pi pi-star-fill': n <= Math.round(product.rates_avg_rate), 'pi pi-star': n > Math.round(product.rates_avg_rate)}"></i>
                            </div>
                            <p v-if="product.rates_avg_rate" class="text-sm text-gray-600">
                                Based on {{ ratings.length }} review{{ ratings.length !== 1 ? 's' : '' }}
                            </p>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div
                        v-if="product.rates_avg_rate"
                        class="flex grow flex-wrap items-center justify-evenly gap-2 px-2 max-lg:w-full"
                    >
                        <button
                            type="button"
                            @click="filterRating = null"
                            :class="filterRating === null ? 'bg-blue-600 text-white' : 'border border-blue-600 bg-white text-blue-600 hover:bg-blue-50'"
                            class="grow rounded-lg px-4 py-2 font-medium shadow-sm transition-colors"
                        >
                            All ({{ ratings.length }})
                        </button>
                        <button
                            v-for="star in [5, 4, 3, 2, 1]"
                            :key="star"
                            type="button"
                            @click="filterRating = star"
                            :class="filterRating === star ? 'bg-blue-600 text-white' : 'border border-blue-600 bg-white text-blue-600 hover:bg-blue-50'"
                            class="grow rounded-lg px-4 py-2 font-medium shadow-sm transition-colors"
                        >
                            {{ star }} Star{{ star !== 1 ? 's' : '' }} ({{ getStarCount(star) }})
                        </button>
                    </div>
                </div>

                <!-- Reviews List -->
                <div v-if="product.rates_avg_rate && filteredRatings.length > 0" class="mt-6">
                    <ul class="space-y-4">
                        <!-- Review Item -->
                        <li v-for="(item, index) in filteredRatings" :key="index" class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md">
                            <div class="flex items-start gap-4">
                                <Avatar
                                    shape="circle"
                                    :label="item.user.full_name.charAt(0).toUpperCase()"
                                    class="aspect-square! bg-gradient-to-br from-blue-500 to-indigo-600 text-white"
                                    size="large"
                                />
                                <div class="flex-1">
                                    <div class="mb-1 flex items-center justify-between">
                                        <div class="font-semibold text-gray-900">{{ item.user.full_name }}</div>
                                        <div class="flex gap-0.5 text-lg text-yellow-400">
                                            <i v-for="n in 5" :key="n" :class="{'pi pi-star-fill': n <= item.rate, 'pi pi-star': n > item.rate}"></i>
                                        </div>
                                    </div>
                                    <p v-if="item.comment" class="mt-2 text-sm leading-relaxed text-gray-700">
                                        {{ item.comment }}
                                    </p>
                                    
                                    <!-- Seller Reply Section -->
                                    <div v-if="item.reply" class="mt-4 rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4">
                                        <div class="mb-2 flex items-center gap-2">
                                            <i class="pi pi-reply text-blue-600"></i>
                                            <span class="text-sm font-semibold text-blue-900">Seller Response</span>
                                        </div>
                                        <p class="text-sm leading-relaxed text-gray-700">
                                            {{ item.reply }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- No Reviews Message -->
                <div v-else-if="product.rates_avg_rate && filteredRatings.length === 0" class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-8 text-center">
                    <i class="pi pi-star mb-3 text-4xl text-gray-400"></i>
                    <p class="text-gray-600">No reviews found for this filter.</p>
                </div>

                <div v-else-if="!product.rates_avg_rate" class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-8 text-center">
                    <i class="pi pi-star mb-3 text-4xl text-gray-400"></i>
                    <p class="text-gray-600">Be the first to review this product!</p>
                </div>
            </div>
        </div>
        <div v-if="loadService.request.loading" class="flex justify-center p-5">
            <PageLoader />
        </div>
        <Dialog
            v-model:visible="showVideo"
            modal
            header="Play Video"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            @show="playVideo"
            @hide="pauseVideo"
        >
            <video
                ref="videoPlayer"
                class="w-full"
                controls
                :key="selectedVideo"
            >
                <source :src="videoUrl" type="video/mp2" />
                Your browser does not support the video tag.
            </video>
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import { CartFormInterface } from "@/interfaces/CartInterface";
import { IRate } from "@/interfaces/IRate";
import { ProductInterface } from "@/interfaces/ProductInterface";
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import CurrencyUtil from "@/utils/CurrencyUtil";
import UrlUtil from "@/utils/UrlUtil";
import ValidatorUtil from "@/utils/ValidatorUtil";
import { computed, nextTick, onMounted, reactive, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";

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
        : "",
);
const videoPlayer = ref(null);

const ratings = ref<IRate[]>([]);
const filterRating = ref<number | null>(null);

const carouselImages = computed(() => {
    if (!product.value) return [];
    
    const images = [];
    
    // Add main product image first if it exists
    if (product.value.product_image) {
        images.push({
            featured_image: product.value.product_image,
            thumbnail: product.value.product_image,
            name: product.value.product_name,
            isMainProduct: true,
        });
    }
    
    // Then add featured images
    if (product.value.featured_images && product.value.featured_images.length > 0) {
        images.push(...product.value.featured_images);
    }
    
    return images;
});

const form: CartFormInterface = reactive({
    product_id: null,
    quantity: 1,
});

const errors = reactive({
    product_id: [],
    quantity: [],
});

const filteredRatings = computed(() => {
    if (filterRating.value === null) {
        return ratings.value;
    }
    return ratings.value.filter(rating => rating.rate === filterRating.value);
});

const getStarCount = (star: number) => {
    return ratings.value.filter(rating => rating.rate === star).length;
};

const clearErrors = () => {
    errors.product_id = [];
    errors.quantity = [];
};

const load = async () => {
    await loadService.get(`find-product/${route.params["id"]}`).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            ratings.value = [];
            product.value = loadService.request.data;
            Object.entries(loadService.request.data.grouped_rates).forEach(
                ([key, value]) => {
                    value.forEach((rate: IRate) => {
                        ratings.value.push(rate);
                    });
                },
            );
            console.log(ratings.value);
            
        } else {
            toast.error(
                loadService.request.message ?? "Failed to load product",
            );
            router.push({ name: "customer.home.index" });
        }
    });
};

const validate = () => {
    clearErrors();
    if (!product.value) {
        errors.product_id.push("Product is required");
    } else {
        form.product_id = product.value.product_id;
    }
    if (!form.quantity || form.quantity < 1) {
        errors.quantity.push("Quantity must be at least 1");
    } else if (form.quantity > product.value?.product_quantity) {
        errors.quantity.push(
            `Only ${product.value?.product_quantity} available`,
        );
    }

    const hasErrors = [
        errors.product_id.length > 0,
        errors.quantity.length > 0,
    ];

    return hasErrors.includes(true) ? false : form;
};

const submitForm = async () => {
    const data = validate();

    if (data) {
        await submitService.post("customer/carts", data).then(() => {
            if (submitService.request.status === 200) {
                toast.success(
                    submitService.request.message ??
                        "Product added to cart successfully",
                );
                form.quantity = 1;
            } else {
                toast.error(
                    submitService.request.message ??
                        "Failed to add product to cart",
                );
                if (submitService.request.errors) {
                    Object.keys(submitService.request.errors).forEach((key) => {
                        errors[key] = submitService.request.errors[key];
                    });
                }
            }
        });
    } else {
        toast.error("Please fix the errors in the form");
    }
};

const playVideo = () => {
    nextTick(() => {
        const video = videoPlayer.value;
        if (video) {
            video.load();
            video.play().catch(() => {
                toast.error("Failed to play video");
            });
        }
    });
};

const pauseVideo = () => {
    const video = videoPlayer.value;
    if (video) {
        video.pause();
        video.currentTime = 0;
    }
};

watch(selectedVideo, () => {
    if (selectedVideo.value) {
        showVideo.value = true;
    } else {
        showVideo.value = false;
    }
});

onMounted(() => {
    load();
});
</script>