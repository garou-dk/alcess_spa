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
                    <div>
                        <div
                            v-if="
                                product.featured_images.length > 0 ||
                                product.product_image
                            "
                            class="w-96"
                        >
                            <Carousel
                                :value="product.featured_images"
                                :numVisible="1"
                                :numScroll="1"
                            >
                                <template #item="slotProps">
                                    <div class="m-2 p-4">
                                        <div class="mb-4">
                                            <div class="flex justify-center">
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
                                                            UrlUtil.getBaseAppUrl(
                                                                `storage/images/thumbnail/${slotProps.data.thumbnail}`,
                                                            )
                                                        "
                                                        :alt="
                                                            slotProps.data.name
                                                        "
                                                        class="block object-cover"
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
                                                    class="cursor-pointer"
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
                                                                    UrlUtil.getBaseAppUrl(
                                                                        `storage/images/thumbnail/${slotProps.data.thumbnail}`,
                                                                    )
                                                                "
                                                                alt="image"
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
                <h1 class="mb-2 text-2xl">Customer Reviews</h1>

                <!-- Rating Summary and Filter Buttons -->
                <div class="flex flex-wrap px-2">
                    <div
                        class="flex flex-wrap items-center justify-center px-2 max-lg:w-full lg:shrink"
                    >
                        <div>
                            <p v-if="product" class="text-center text-2xl text-blue-700">
                                <span v-if="product.rates_avg_rate">
                                    {{ product.rates_avg_rate }} out of 5
                                </span>
                                <span v-else>No Reviews</span>
                            </p>
                            <div v-if="product.rates_avg_rate" class="mb-3 flex gap-1 text-3xl text-blue-600">
                                <i v-for="n in 5" :class="{'pi pi-star-fill': n <= product.rates_avg_rate, 'pi pi-star': n > product.rates_avg_rate}"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div
                        v-if="product.rates_avg_rate"
                        class="flex grow flex-wrap items-center justify-evenly gap-2 px-2 max-lg:w-full"
                    >
                        <button
                            type="button"
                            class="grow rounded bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700"
                        >
                            All
                        </button>
                        <button
                            type="button"
                            class="grow rounded border border-blue-600 bg-gray-300 px-4 py-2 text-black hover:bg-blue-600 hover:text-white"
                        >
                            5 Stars (4)
                        </button>
                        <button
                            type="button"
                            class="grow rounded border border-blue-600 bg-gray-300 px-4 py-2 text-black hover:bg-blue-600 hover:text-white"
                        >
                            4 Stars (1)
                        </button>
                        <button
                            type="button"
                            class="grow rounded border border-blue-600 bg-gray-300 px-4 py-2 text-black hover:bg-blue-600 hover:text-white"
                        >
                            3 Stars (0)
                        </button>
                        <button
                            type="button"
                            class="grow rounded border border-blue-600 bg-gray-300 px-4 py-2 text-black hover:bg-blue-600 hover:text-white"
                        >
                            2 Stars (0)
                        </button>
                        <button
                            type="button"
                            class="grow rounded border border-blue-600 bg-gray-300 px-4 py-2 text-black hover:bg-blue-600 hover:text-white"
                        >
                            1 Star (0)
                        </button>
                    </div>
                </div>

                <!-- Reviews List -->
                <div v-if="product.rates_avg_rate && ratings.length > 0" class="mt-4 mb-4">
                    <ul class="divide-y rounded bg-gray-50 shadow-md">
                        <!-- Review Item -->
                        <li v-for="(item, index) in ratings" :key="index" class="flex items-start gap-4 p-4">
                            <Avatar
                                shape="circle"
                                :label="item.user.full_name.charAt(0)"
                                class="aspect-square!"
                                size="large"
                            />
                            <div class="flex-1">
                                <div class="font-semibold">{{ item.user.full_name }}</div>
                                <div class="flex gap-1 text-sm text-blue-600">
                                    <i v-for="n in 5" :class="{'pi pi-star-fill': n <= item.rate, 'pi pi-star': n > item.rate}"></i>
                                </div>
                                <p class="mt-1 text-xs">
                                    {{ item.comment }}
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
                <source :src="videoUrl" type="video/mp4" />
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

// Arrow function to pause and reset video on dialog hide
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
