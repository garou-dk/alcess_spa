<template>
    <div>
        <header class="bg-sky-800">
            <nav class="p-2">
                <Toolbar style="background-color: transparent; border: none">
                    <template #start>
                        <RouterLink :to="{ name: 'home' }">
                            <div class="flex">
                                <div class="px-2">
                                    <div class="rounded-full bg-white">
                                        <img
                                            :src="Icon"
                                            class="h-16 w-16"
                                            alt="Store Icon"
                                        />
                                    </div>
                                </div>
                                <div class="flex items-center px-2">
                                    <h1 class="text-3xl font-bold text-white">
                                        {{ appName }}
                                    </h1>
                                </div>
                            </div>
                        </RouterLink>
                    </template>
                    <template #center>
                        <div class="w-[500px] max-lg:hidden">
                            <form>
                                <InputGroup>
                                    <InputGroupAddon>
                                        <i class="pi pi-search" />
                                    </InputGroupAddon>
                                    <InputText
                                        v-model="form.search"
                                        :invalid="errors.search.length > 0"
                                        placeholder="Search product"
                                        fluid
                                        name="search"
                                        autocomplete="off"
                                    />
                                </InputGroup>
                            </form>
                        </div>
                    </template>
                    <template #end>
                        <div class="flex">
                            <div v-if="!Page.user" class="px-2">
                                <button
                                    type="button"
                                    class="cursor-pointer text-white"
                                    @click="openLoginForm()"
                                >
                                    <i
                                        class="pi pi-user"
                                        style="font-size: 1.5rem"
                                    />
                                </button>
                            </div>
                        </div>
                    </template>
                </Toolbar>
            </nav>
        </header>
        <div>
            <div class="mb-2 w-full">
                <img :src="HeroBg" class="h-full w-full" />
            </div>
            <div class="p-2">
                <h1 class="mb-4 text-2xl font-semibold bg-white p-5 rounded border-b-4 border-blue-900 text-center">
                    Categories
                </h1>
                <div class="flex flex-wrap p-2">
                    <div
                        v-for="(category, index) in CategoryStore.categories"
                        :key="index"
                        class="p-2"
                    >
                        <button
                            class="primary-bg flex cursor-pointer items-center gap-2 rounded-full p-2 min-w-36 border-4 border-white"
                        >
                            <div
                                v-if="category.category_image"
                                class="flex items-center justify-center rounded-full bg-white p-2"
                            >
                                <img
                                    :src="
                                        UrlUtil.getBaseAppUrl(
                                            `storage/images/category/${category.category_image}`,
                                        )
                                    "
                                    :alt="category.category_image"
                                    class="h-8 w-8"
                                />
                            </div>
                            <div
                                v-else
                                class="flex items-center justify-center rounded-full bg-white p-2"
                            >
                                <Avatar
                                    :label="category.category_name[0]"
                                    shape="circle"
                                />
                            </div>
                            
                            <div class="grow">
                                <span>{{ category.category_name }}</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <h1 class="mb-4 text-2xl font-semibold bg-white p-5 rounded border-b-4 border-blue-900 text-center">
                    Best Selling Products
                </h1>
                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6"
                >
                    <div
                        v-for="(product, index) in products"
                        :key="index"
                        class="p-2"
                    >
                        <div
                            class="max-w-xs overflow-hidden rounded-lg bg-white shadow-md transition-shadow duration-300 hover:shadow-lg"
                        >
                            <div class="flex justify-center">
                                <img
                                    v-if="product.product_image"
                                    class="h-48 w-48 object-cover"
                                    :src="
                                        UrlUtil.getBaseAppUrl(
                                            `storage/images/product/${product.product_image}`,
                                        )
                                    "
                                    :alt="product.product_name"
                                />
                                <div
                                    v-else
                                    class="flex h-48 w-48 items-center justify-center p-4"
                                >
                                    <i class="pi pi-camera text-2xl" />
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-800">
                                    {{ product.product_name }}
                                </h3>
                                <p class="mt-1 text-gray-600">
                                    {{
                                        CurrencyUtil.formatCurrency(
                                            product.product_price,
                                        )
                                    }}
                                </p>
                            </div>
                            <div class="mb-5 flex justify-center">
                                <RouterLink
                                    :to="{
                                        name: 'customer.product-info.index',
                                        params: { id: product.product_id },
                                    }"
                                >
                                    <Button
                                        label="View Details"
                                        class="primary-bg"
                                    />
                                </RouterLink>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Dialog
            v-model:visible="loginFormVisible"
            modal
            header="Login"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <LoginForm>
                <template #footer>
                    <div class="flex">
                        <div class="w-1/2 p-2">
                            <Button
                                type="button"
                                variant="link"
                                label="Forgot password?"
                                class="p-0! text-black!"
                                pt:label:class="font-normal!"
                            />
                        </div>
                        <div class="flex w-1/2 flex-col p-2">
                            <span class="text-end">Don't have an account?</span>
                            <div class="flex justify-end">
                                <Button
                                    type="button"
                                    variant="link"
                                    label="Register"
                                    class="p-0!"
                                    pt:label:class="font-normal!"
                                    @click="openRegisterForm()"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </LoginForm>
        </Dialog>
        <Dialog
            v-model:visible="registerFormVisible"
            modal
            header="Register"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <RegisterForm>
                <template #footer>
                    <div class="flex justify-center p-2">
                        <span>Already have an account?</span>
                        &nbsp;
                        <Button
                            type="button"
                            variant="link"
                            label="Login"
                            class="p-0!"
                            pt:label:class="font-normal!"
                            @click="openLoginForm()"
                        />
                    </div>
                </template>
            </RegisterForm>
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import Icon from "@/../img/logo.png";
import LoginForm from "@/components/forms/LoginForm.vue";
import RegisterForm from "@/components/forms/RegisterForm.vue";
import {
    SearchErrorInterface,
    SearchProductInterface,
} from "@/interfaces/SearchProductInterface";
import Page from "@/stores/Page";
import { onMounted, reactive, ref } from "vue";
import HeroBg from "@/../img/hero-bg.jpg";
import { useCategoryStore } from "@/stores/CategoryState";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";

const appName = import.meta.env.VITE_APP_NAME;
const loginFormVisible = ref<boolean>(false);
const registerFormVisible = ref<boolean>(false);
const CategoryStore = useCategoryStore();
const loadBestSellingService = useAxiosUtil<null, ProductInterface[]>();
const products = ref<ProductInterface[]>([]);
const form: SearchProductInterface = reactive({
    search: null,
});
const errors: SearchErrorInterface = reactive({
    search: [],
});

const openLoginForm = () => {
    registerFormVisible.value = false;
    loginFormVisible.value = true;
};

const openRegisterForm = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = true;
};

const loadBestSellingProducts = async () => {
    await loadBestSellingService.get("best-selling").then(() => {
        if (
            loadBestSellingService.request.status === 200 &&
            loadBestSellingService.request.data
        ) {
            products.value = loadBestSellingService.request.data;
        }
    });
};

onMounted(() => {
    CategoryStore.fetchCategories();
    loadBestSellingProducts();
});
</script>
