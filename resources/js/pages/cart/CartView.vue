<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50" :class="getResponsivePadding()">
        <div class="container mx-auto max-w-none" :class="getResponsiveClasses({ mobile: 'px-3', tablet: 'px-4', desktop: 'px-4' })">
            <div class="mx-auto max-w-[1600px]">
                <!-- Header -->
                <div :class="getResponsiveClasses({ mobile: 'mb-4 flex items-center gap-2', tablet: 'mb-5 flex items-center gap-3', desktop: 'mb-6 flex items-center gap-3' })">
                    <button
                        @click="$router.back()"
                        :class="getResponsiveClasses({ 
                            mobile: 'flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 transition-colors cursor-pointer', 
                            tablet: 'flex items-center justify-center w-9 h-9 rounded-full hover:bg-gray-100 transition-colors cursor-pointer',
                            desktop: 'flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 transition-colors cursor-pointer'
                        })"
                    >
                        <i :class="getResponsiveClasses({ mobile: 'pi pi-chevron-left text-lg text-gray-900', tablet: 'pi pi-chevron-left text-xl text-gray-900', desktop: 'pi pi-chevron-left text-xl text-gray-900' })"></i>
                    </button>
                    <h1 :class="getResponsiveTextSize('xl') + ' font-bold text-gray-900'">
                        My Cart
                    </h1>
                </div>

                <div :class="getResponsiveClasses({ mobile: 'space-y-4', tablet: 'space-y-5', desktop: 'grid gap-4 sm:gap-6 lg:grid-cols-3' })">
                    <!-- Cart Items -->
                    <div :class="getResponsiveClasses({ mobile: '', tablet: '', desktop: 'lg:col-span-2' })">
                        <!-- Sticky Header Container with Background Extension -->
                        <div :class="getResponsiveClasses({ mobile: 'sticky top-0 z-10 pt-2', tablet: 'sticky top-0 z-10 pt-3', desktop: 'sticky top-0 z-10 pt-4' })">
                            <!-- Background extension to hide scrolling content - matches header width -->
                            <div class="absolute left-0 right-0 -top-4 bottom-0 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 rounded-t-2xl"></div>
                            
                            <!-- Select All Header -->
                            <div :class="getResponsiveClasses({ mobile: 'relative p-3 bg-gray-50 border border-gray-200 rounded-t-2xl shadow-lg', tablet: 'relative p-4 bg-gray-50 border border-gray-200 rounded-t-2xl shadow-lg', desktop: 'relative p-4 bg-gray-50 border border-gray-200 rounded-t-2xl shadow-lg' })">
                                <div class="flex items-center justify-between">
                                    <div :class="getResponsiveClasses({ mobile: 'flex items-center gap-2', tablet: 'flex items-center gap-3', desktop: 'flex items-center gap-3' })">
                                        <Checkbox
                                            v-model="selectAll"
                                            :binary="true"
                                            @change="toggleSelectAll"
                                            inputId="selectAll"
                                            class="checkbox-blue"
                                        />
                                        <label for="selectAll" :class="getResponsiveClasses({ mobile: 'font-semibold text-gray-700 cursor-pointer text-sm', tablet: 'font-semibold text-gray-700 cursor-pointer', desktop: 'font-semibold text-gray-700 cursor-pointer' })">
                                            Select All
                                        </label>
                                    </div>
                                    <div :class="getResponsiveClasses({ mobile: 'flex items-center gap-2', tablet: 'flex items-center gap-3', desktop: 'flex items-center gap-3' })">
                                        <span :class="getResponsiveClasses({ mobile: 'text-xs text-gray-600 font-medium', tablet: 'text-sm text-gray-600 font-medium', desktop: 'text-sm text-gray-600 font-medium' })">
                                            {{ carts.length }} {{ carts.length === 1 ? 'item' : 'items' }}
                                        </span>
                                        <Button
                                            type="button"
                                            icon="pi pi-trash"
                                            severity="danger"
                                            text
                                            rounded
                                            :disabled="selectedCount === 0"
                                            @click="removeSelected"
                                            :class="selectedCount === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-red-50'"
                                            v-tooltip.top="selectedCount === 0 ? 'Select items to delete' : 'Delete selected items'"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Items List -->
                        <div class="bg-white border-x border-b border-gray-200 rounded-b-2xl shadow-lg -mt-px">
                            <div class="divide-y divide-gray-200">
                                <div
                                    v-for="(cart, index) in carts"
                                    :key="index"
                                    :class="getResponsiveClasses({ mobile: 'p-3 hover:bg-gray-50 transition-all duration-200', tablet: 'p-4 hover:bg-gray-50 transition-all duration-200', desktop: 'p-4 sm:p-6 hover:bg-gray-50 transition-all duration-200' })"
                                >
                                    <div :class="getResponsiveClasses({ mobile: 'flex items-start gap-3', tablet: 'flex items-start gap-4', desktop: 'flex items-start sm:items-center gap-3 sm:gap-6' })">
                                        <!-- Checkbox -->
                                        <Checkbox
                                            v-model="form.carts[index].checked"
                                            :binary="true"
                                            :inputId="`cart-${index}`"
                                            :class="getResponsiveClasses({ mobile: 'checkbox-blue flex-shrink-0 mt-1', tablet: 'checkbox-blue flex-shrink-0 mt-1', desktop: 'checkbox-blue flex-shrink-0 mt-1 sm:mt-0' })"
                                        />
                                        
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            <div :class="getResponsiveClasses({ mobile: 'h-16 w-16 overflow-hidden rounded-xl border-2 border-gray-200', tablet: 'h-20 w-20 overflow-hidden rounded-xl border-2 border-gray-200', desktop: 'h-20 w-20 sm:h-28 sm:w-28 overflow-hidden rounded-xl border-2 border-gray-200' })">
                                                <img
                                                    v-if="cart.product.product_image"
                                                    :src="UrlUtil.getBaseAppUrl(`storage/images/product/${cart.product.product_image}`)"
                                                    :alt="cart.product.product_name"
                                                    class="h-full w-full object-cover"
                                                />
                                                <div
                                                    v-else
                                                    class="flex h-full w-full items-center justify-center bg-gray-100"
                                                >
                                                    <i :class="getResponsiveClasses({ mobile: 'pi pi-image text-xl text-gray-400', tablet: 'pi pi-image text-2xl text-gray-400', desktop: 'pi pi-image text-2xl sm:text-3xl text-gray-400' })"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Product Info and Controls Container -->
                                        <div class="flex-1 min-w-0">
                                            <!-- Mobile/Tablet Layout (Stack vertically) -->
                                            <div :class="getResponsiveClasses({ mobile: 'space-y-3', tablet: 'space-y-3', desktop: 'sm:hidden space-y-3' })">
                                                <!-- Product Info -->
                                                <div class="space-y-1">
                                                    <h3 :class="getResponsiveTextSize('sm') + ' font-bold text-gray-900 line-clamp-2 leading-tight'">
                                                        {{ cart.product.product_name }}
                                                    </h3>
                                                    <p :class="getResponsiveClasses({ mobile: 'text-xs text-gray-600 flex items-center gap-1', tablet: 'text-xs text-gray-600 flex items-center gap-1', desktop: 'text-xs text-gray-600 flex items-center gap-1' })">
                                                        <i class="pi pi-check-circle text-green-600"></i>
                                                        {{ cart.product.product_quantity }} in stock
                                                    </p>
                                                    <p :class="getResponsiveTextSize('sm') + ' font-bold text-gray-900'">
                                                        {{ CurrencyUtil.formatCurrency(cart.product.product_price) }}
                                                    </p>
                                                </div>
                                                
                                                <!-- Ratings -->
                                                <div :class="getResponsiveClasses({ mobile: 'flex items-center gap-2', tablet: 'flex items-center gap-2', desktop: 'flex items-center gap-2' })">
                                                    <div class="flex gap-1">
                                                        <i
                                                            v-for="star in 5"
                                                            :key="star"
                                                            :class="[
                                                                'text-xs',
                                                                star <= Math.round(Number(cart.product.rates_avg_rate) || 0)
                                                                    ? 'pi pi-star-fill text-yellow-400'
                                                                    : 'pi pi-star text-gray-300'
                                                            ]"
                                                        ></i>
                                                    </div>
                                                    <span :class="getResponsiveClasses({ mobile: 'text-xs font-medium text-gray-700', tablet: 'text-xs font-medium text-gray-700', desktop: 'text-xs font-medium text-gray-700' })">
                                                        {{ cart.product.rates_avg_rate ? Number(cart.product.rates_avg_rate).toFixed(1) : '0.0' }}/5
                                                    </span>
                                                </div>
                                                
                                                <!-- Quantity Controls and Delete -->
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-1">
                                                        <Button
                                                            type="button"
                                                            icon="pi pi-minus"
                                                            rounded
                                                            @click="removeQuantity(index)"
                                                            :disabled="form.carts[index].quantity <= 1"
                                                            :class="getResponsiveClasses({ mobile: '!bg-blue-600 !border-blue-600 hover:!bg-blue-700 text-white !w-6 !h-6 !p-0 flex items-center justify-center', tablet: '!bg-blue-600 !border-blue-600 hover:!bg-blue-700 text-white !w-7 !h-7 !p-0 flex items-center justify-center', desktop: '!bg-blue-600 !border-blue-600 hover:!bg-blue-700 text-white !w-7 !h-7 !p-0 flex items-center justify-center' })"
                                                        >
                                                            <i class="pi pi-minus text-xs"></i>
                                                        </Button>
                                                        <input
                                                            type="number"
                                                            v-model.number="form.carts[index].quantity"
                                                            :min="1"
                                                            :max="cart.product.product_quantity"
                                                            :class="getResponsiveClasses({ mobile: 'w-8 h-6 text-center border border-gray-300 rounded font-semibold text-xs focus:outline-none focus:border-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none', tablet: 'w-10 h-7 text-center border border-gray-300 rounded font-semibold text-xs focus:outline-none focus:border-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none', desktop: 'w-10 h-7 text-center border border-gray-300 rounded font-semibold text-xs focus:outline-none focus:border-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none' })"
                                                            @input="validateQuantity(index, cart.product.product_quantity)"
                                                        />
                                                        <Button
                                                            type="button"
                                                            icon="pi pi-plus"
                                                            rounded
                                                            @click="addQuantity(index, cart.product.product_quantity)"
                                                            :disabled="form.carts[index].quantity >= cart.product.product_quantity"
                                                            :class="getResponsiveClasses({ mobile: '!bg-blue-600 !border-blue-600 hover:!bg-blue-700 text-white !w-6 !h-6 !p-0 flex items-center justify-center', tablet: '!bg-blue-600 !border-blue-600 hover:!bg-blue-700 text-white !w-7 !h-7 !p-0 flex items-center justify-center', desktop: '!bg-blue-600 !border-blue-600 hover:!bg-blue-700 text-white !w-7 !h-7 !p-0 flex items-center justify-center' })"
                                                        >
                                                            <i class="pi pi-plus text-xs"></i>
                                                        </Button>
                                                    </div>
                                                    <Button
                                                        type="button"
                                                        icon="pi pi-trash"
                                                        severity="danger"
                                                        text
                                                        rounded
                                                        @click="removeSingleItem(index)"
                                                        :class="getResponsiveClasses({ mobile: 'hover:bg-red-50 !w-7 !h-7', tablet: 'hover:bg-red-50 !w-8 !h-8', desktop: 'hover:bg-red-50 !w-8 !h-8' })"
                                                    />
                                                </div>
                                            </div>

                                            <!-- Desktop Layout (Horizontal) -->
                                            <div class="hidden sm:flex items-center gap-4">
                                                <!-- Product Info -->
                                                <div class="flex-1 min-w-0 space-y-2">
                                                    <h3 :class="getResponsiveTextSize('base') + ' font-bold text-gray-900 line-clamp-2 leading-tight'">
                                                        {{ cart.product.product_name }}
                                                    </h3>
                                                    <p :class="getResponsiveTextSize('sm') + ' text-gray-600 flex items-center gap-1'">
                                                        <i class="pi pi-check-circle text-green-600"></i>
                                                        {{ cart.product.product_quantity }} in stock
                                                    </p>
                                                    <p :class="getResponsiveTextSize('base') + ' font-bold text-gray-900'">
                                                        {{ CurrencyUtil.formatCurrency(cart.product.product_price) }}
                                                    </p>
                                                </div>

                                                <!-- Desktop Ratings Section -->
                                                <div class="hidden lg:flex flex-col items-center justify-center px-6 border-l border-gray-200 min-w-[120px]">
                                                    <p class="text-xs text-gray-500 mb-1">Ratings:</p>
                                                    <div class="flex gap-1 mb-1">
                                                        <i
                                                            v-for="star in 5"
                                                            :key="star"
                                                            :class="[
                                                                'text-lg',
                                                                star <= Math.round(Number(cart.product.rates_avg_rate) || 0)
                                                                    ? 'pi pi-star-fill text-yellow-400'
                                                                    : 'pi pi-star text-gray-300'
                                                            ]"
                                                        ></i>
                                                    </div>
                                                    <p :class="getResponsiveTextSize('sm') + ' font-semibold text-gray-700'">
                                                        {{ cart.product.rates_avg_rate ? Number(cart.product.rates_avg_rate).toFixed(1) : '0.0' }}/5
                                                    </p>
                                                </div>

                                                <!-- Quantity Controls -->
                                                <div class="flex items-center gap-1 flex-shrink-0">
                                                    <Button
                                                        type="button"
                                                        icon="pi pi-minus"
                                                        rounded
                                                        @click="removeQuantity(index)"
                                                        :disabled="form.carts[index].quantity <= 1"
                                                        class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700 text-white !w-8 !h-8 !p-0 flex items-center justify-center"
                                                    >
                                                        <i class="pi pi-minus text-xs"></i>
                                                    </Button>
                                                    <input
                                                        type="number"
                                                        v-model.number="form.carts[index].quantity"
                                                        :min="1"
                                                        :max="cart.product.product_quantity"
                                                        class="w-12 h-8 text-center border border-gray-300 rounded font-semibold text-sm focus:outline-none focus:border-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                        @input="validateQuantity(index, cart.product.product_quantity)"
                                                    />
                                                    <Button
                                                        type="button"
                                                        icon="pi pi-plus"
                                                        rounded
                                                        @click="addQuantity(index, cart.product.product_quantity)"
                                                        :disabled="form.carts[index].quantity >= cart.product.product_quantity"
                                                        class="!bg-blue-600 !border-blue-600 hover:!bg-blue-700 text-white !w-8 !h-8 !p-0 flex items-center justify-center"
                                                    >
                                                        <i class="pi pi-plus text-xs"></i>
                                                    </Button>
                                                </div>

                                                <!-- Delete Button -->
                                                <Button
                                                    type="button"
                                                    icon="pi pi-trash"
                                                    severity="danger"
                                                    text
                                                    rounded
                                                    @click="removeSingleItem(index)"
                                                    class="hover:bg-red-50 flex-shrink-0"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div v-if="carts.length === 0" :class="getResponsiveClasses({ mobile: 'py-8 text-center px-3', tablet: 'py-12 text-center px-4', desktop: 'py-12 sm:py-16 text-center px-4' })">
                                    <div :class="getResponsiveClasses({ mobile: 'inline-block p-4 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-3', tablet: 'inline-block p-5 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4', desktop: 'inline-block p-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full mb-4' })">
                                        <i :class="getResponsiveClasses({ mobile: 'pi pi-shopping-cart text-3xl text-gray-400', tablet: 'pi pi-shopping-cart text-4xl text-gray-400', desktop: 'pi pi-shopping-cart text-4xl sm:text-6xl text-gray-400' })"></i>
                                    </div>
                                    <h3 :class="getResponsiveTextSize('lg') + ' font-bold text-gray-700 mb-2'">Your cart is empty</h3>
                                    <p :class="getResponsiveTextSize('base') + ' text-gray-500'">Add some amazing items to get started!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary Sidebar -->
                    <div :class="getResponsiveClasses({ mobile: '', tablet: '', desktop: 'lg:col-span-1' })">
                        <div :class="getResponsiveClasses({ mobile: 'rounded-2xl bg-white shadow-lg border border-gray-200', tablet: 'rounded-2xl bg-white shadow-lg border border-gray-200', desktop: 'sticky top-4 sm:top-8 rounded-2xl bg-white shadow-lg border border-gray-200' }) + ' ' + getResponsivePadding()">
                            <!-- Header -->
                            <div :class="getResponsiveClasses({ mobile: 'flex items-center gap-2 mb-4 pb-3 border-b border-gray-200', tablet: 'flex items-center gap-2 mb-5 pb-4 border-b border-gray-200', desktop: 'flex items-center gap-2 mb-6 pb-4 border-b border-gray-200' })">
                                <i :class="getResponsiveClasses({ mobile: 'pi pi-shopping-bag text-xl text-blue-600', tablet: 'pi pi-shopping-bag text-2xl text-blue-600', desktop: 'pi pi-shopping-bag text-2xl text-blue-600' })"></i>
                                <h2 :class="getResponsiveTextSize('xl') + ' font-bold text-blue-600'">
                                    Order Summary
                                </h2>
                            </div>

                            <!-- Items and Shipping -->
                            <div :class="getResponsiveClasses({ mobile: 'space-y-3 mb-4', tablet: 'space-y-4 mb-5', desktop: 'space-y-4 mb-6' })">
                                <div class="flex justify-between items-center">
                                    <span :class="getResponsiveClasses({ mobile: 'flex items-center gap-1 text-gray-700', tablet: 'flex items-center gap-2 text-gray-700', desktop: 'flex items-center gap-2 text-gray-700' })">
                                        <i class="pi pi-box text-blue-600"></i>
                                        <span :class="getResponsiveTextSize('sm')">Items ({{ selectedCount }})</span>
                                    </span>
                                    <span :class="getResponsiveTextSize('base') + ' font-bold text-gray-900'">{{ subTotal() }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span :class="getResponsiveClasses({ mobile: 'flex items-center gap-1 text-gray-700', tablet: 'flex items-center gap-2 text-gray-700', desktop: 'flex items-center gap-2 text-gray-700' })">
                                        <i class="pi pi-truck text-blue-600"></i>
                                        <span :class="getResponsiveTextSize('sm')">Shipping</span>
                                    </span>
                                    <div :class="getResponsiveClasses({ mobile: 'flex items-center gap-1', tablet: 'flex items-center gap-2', desktop: 'flex items-center gap-2' })">
                                        <span :class="getResponsiveTextSize('base') + ' font-bold text-green-600'">TBA</span>
                                        <i 
                                            class="pi pi-info-circle text-blue-600 cursor-help" 
                                            v-tooltip.top="'Shipping cost will be calculated at checkout based on your location'"
                                        ></i>
                                    </div>
                                </div>

                                <!-- Divider -->
                                <div class="border-t border-gray-200 my-4"></div>

                                <!-- Total -->
                                <div class="flex justify-between items-center">
                                    <span :class="getResponsiveTextSize('lg') + ' font-bold text-gray-900'">Total:</span>
                                    <span :class="getResponsiveClasses({ mobile: 'text-xl font-bold text-blue-600', tablet: 'text-2xl font-bold text-blue-600', desktop: 'text-2xl font-bold text-blue-600' })">
                                        {{ subTotal() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <div :class="getResponsiveClasses({ mobile: 'mb-4', tablet: 'mb-5', desktop: 'mb-6' })">
                                <Button
                                    :disabled="selectedCount === 0"
                                    type="button"
                                    label="Proceed to Checkout"
                                    icon="pi pi-arrow-right"
                                    iconPos="right"
                                    :class="getResponsiveClasses({ mobile: 'w-full !bg-blue-500 hover:!bg-blue-600 border-0 text-white font-semibold py-2.5 text-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed', tablet: 'w-full !bg-blue-500 hover:!bg-blue-600 border-0 text-white font-semibold py-3 text-base transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed', desktop: 'w-full !bg-blue-500 hover:!bg-blue-600 border-0 text-white font-semibold py-3 text-base transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed' })"
                                    @click="checkoutItems()"
                                />
                            </div>

                            <!-- Security Badges -->
                            <div :class="getResponsiveClasses({ mobile: 'pt-3 border-t border-gray-200 space-y-2', tablet: 'pt-4 border-t border-gray-200 space-y-3', desktop: 'pt-4 border-t border-gray-200 space-y-3' })">
                                <div :class="getResponsiveClasses({ mobile: 'flex items-center gap-1 text-xs text-gray-700', tablet: 'flex items-center gap-2 text-sm text-gray-700', desktop: 'flex items-center gap-2 text-sm text-gray-700' })">
                                    <i :class="getResponsiveClasses({ mobile: 'pi pi-shield text-green-600 text-base', tablet: 'pi pi-shield text-green-600 text-lg', desktop: 'pi pi-shield text-green-600 text-lg' })"></i>
                                    <span>Secure checkout guaranteed</span>
                                </div>
                                <div :class="getResponsiveClasses({ mobile: 'flex items-center gap-1 text-xs text-gray-700', tablet: 'flex items-center gap-2 text-sm text-gray-700', desktop: 'flex items-center gap-2 text-sm text-gray-700' })">
                                    <i :class="getResponsiveClasses({ mobile: 'pi pi-replay text-blue-600 text-base', tablet: 'pi pi-replay text-blue-600 text-lg', desktop: 'pi pi-replay text-blue-600 text-lg' })"></i>
                                    <span>30-day return policy</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Dialog - Responsive -->
        <Dialog
            v-model:visible="showCheckout"
            modal
            header="Checkout"
            :style="isMobile ? { width: '95vw', maxWidth: '500px' } : isTablet ? { width: '90vw', maxWidth: '800px' } : { width: '95vw', maxWidth: '1200px' }"
            :breakpoints="{ '1199px': '90vw', '575px': '95vw' }"
            :pt="{
                header: {
                    class: '!bg-blue-600 !text-white !border-b-2 !border-blue-700 !rounded-t-lg'
                },
                headerTitle: {
                    class: '!text-white !font-bold'
                },
                closeButton: {
                    class: 'hover:!bg-blue-700 !text-white'
                }
            }"
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
import { useResponsive } from "@/composables/useResponsive";

// Use responsive composable
const { isMobile, isTablet, isDesktop, getResponsiveClasses, getResponsiveTextSize, getResponsivePadding, getResponsiveGap } = useResponsive();

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

const validateQuantity = (index: number, max: number) => {
    if (form.carts[index].quantity < 1) {
        form.carts[index].quantity = 1;
    } else if (form.carts[index].quantity > max) {
        form.carts[index].quantity = max;
        toast.warning(`Maximum quantity available is ${max}`);
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

// Cleanup on unmount
import { onUnmounted } from "vue";
onUnmounted(() => {
    // No cleanup needed for useResponsive composable
});
</script>

<style scoped>
/* Custom scrollbar for better mobile experience */
::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #2563eb, #4f46e5);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #1d4ed8, #4338ca);
}

/* Text clamping utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-wrap: break-word;
    hyphens: auto;
}

/* Green checkbox styling */
:deep(.p-checkbox.checkbox-blue .p-checkbox-box) {
    border-color: #d1d5db;
}

:deep(.p-checkbox.checkbox-blue .p-checkbox-box.p-highlight),
:deep(.p-checkbox.checkbox-blue .p-checkbox-box.p-checked) {
    background: rgb(34, 197, 94) !important;
    background-color: rgb(34, 197, 94) !important;
    border-color: rgb(34, 197, 94) !important;
}

:deep(.p-checkbox.checkbox-blue .p-checkbox-box.p-highlight:hover),
:deep(.p-checkbox.checkbox-blue .p-checkbox-box.p-checked:hover) {
    background: rgb(22, 163, 74) !important;
    background-color: rgb(22, 163, 74) !important;
    border-color: rgb(22, 163, 74) !important;
}

:deep(.p-checkbox.checkbox-blue .p-checkbox-box.p-highlight .p-checkbox-icon),
:deep(.p-checkbox.checkbox-blue .p-checkbox-box.p-checked .p-checkbox-icon) {
    color: white !important;
}

:deep(.p-checkbox.checkbox-blue .p-checkbox-box.p-focus) {
    box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, 0.25) !important;
}

/* Additional override for any PrimeVue theme */
:deep(.checkbox-blue.p-checkbox .p-checkbox-box.p-highlight) {
    background: rgb(34, 197, 94) !important;
    border-color: rgb(34, 197, 94) !important;
}

/* Ensure proper spacing on mobile */
@media (max-width: 640px) {
    .container {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
}

/* Fix button sizing consistency */
:deep(.p-button.p-button-rounded) {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

/* Improve input field appearance on mobile */
input[type="number"] {
    -moz-appearance: textfield;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>