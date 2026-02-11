<template>
    <div :class="getResponsiveClasses({
        mobile: 'p-3 bg-gray-50 min-h-screen',
        tablet: 'p-4 bg-gray-50 min-h-screen',
        desktop: 'p-5 bg-gray-50 min-h-screen'
    })">
        <!-- Navigation Tabs -->
        <div :class="getResponsiveClasses({
            mobile: 'card mb-4 bg-white p-3 rounded-lg shadow-sm',
            tablet: 'card mb-4 bg-white p-4 rounded-lg shadow-sm', 
            desktop: 'card mb-4 bg-white p-4 rounded-lg shadow-sm'
        })">
            <div :class="getResponsiveClasses({
                mobile: 'flex flex-col gap-3',
                tablet: 'flex gap-2 flex-wrap items-center justify-between',
                desktop: 'flex gap-2 flex-wrap items-center justify-between'
            })">
                <!-- Navigation Buttons Container -->
                <div :class="getResponsiveClasses({
                    mobile: 'flex gap-2 overflow-x-auto pb-2 -mb-2',
                    tablet: 'flex gap-2 flex-wrap',
                    desktop: 'flex gap-2 flex-wrap'
                })" :style="getResponsiveClasses({
                    mobile: 'scrollbar-width: thin; scrollbar-color: #d1d5db #f9fafb;',
                    tablet: '',
                    desktop: ''
                })">
                    <Button
                        v-for="(value, index) in navs"
                        :key="index"
                        type="button"
                        :label="value.label"
                        :icon="value.icon"
                        :class="[
                            {
                                'primary-bg': value.active
                            },
                            getResponsiveClasses({
                                mobile: 'flex-shrink-0 text-sm',
                                tablet: 'flex-shrink-0 text-sm',
                                desktop: 'flex-shrink-0'
                            })
                        ]"
                        :variant="value.active ? '' : 'outlined'"
                        @click="setNav(index)"
                        class="transition-all"
                    />
                </div>
                
                <!-- Refresh Button -->
                <Button
                    type="button"
                    icon="pi pi-refresh"
                    label="Refresh"
                    size="small"
                    @click="load"
                    :loading="loadService.request.loading"
                    :class="getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'ml-auto',
                        desktop: 'ml-auto'
                    })"
                />
            </div>
        </div>

        <!-- Orders List -->
        <DataView 
            :key="filtered.map(o => o.order_id).join('-')"
            :value="filtered"
            :paginator="filtered.length > getResponsiveClasses({
                mobile: 3,
                tablet: 5,
                desktop: 5
            })"
            :rows="getResponsiveClasses({
                mobile: 3,
                tablet: 5,
                desktop: 5
            })"
            :rowsPerPageOptions="getResponsiveClasses({
                mobile: [3, 5, 10],
                tablet: [5, 10, 20],
                desktop: [5, 10, 20, 50]
            })"
            :pt="{
                paginator: {
                    root: getResponsiveClasses({
                        mobile: 'text-sm',
                        tablet: '',
                        desktop: ''
                    })
                }
            }"
        >
            <template #list="slotProps">
                <div class="flex flex-col gap-4">
                    <div 
                        v-for="(item, index) in slotProps.items" 
                        :key="item.order_id" 
                        class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 hover:shadow-md transition-shadow"
                    >
                        <!-- Order Header -->
                        <div 
                            :class="[orderStatus(item), getResponsiveClasses({
                                mobile: 'p-3',
                                tablet: 'p-4',
                                desktop: 'p-4'
                            })]"
                        >
                            <div :class="getResponsiveClasses({
                                mobile: 'flex flex-col gap-3',
                                tablet: 'flex flex-wrap gap-4 items-center justify-between',
                                desktop: 'flex flex-wrap gap-4 items-center justify-between'
                            })">
                                <div :class="getResponsiveClasses({
                                    mobile: 'grid grid-cols-2 gap-3',
                                    tablet: 'flex flex-wrap gap-4 flex-1',
                                    desktop: 'flex flex-wrap gap-4 flex-1'
                                })">
                                    <div>
                                        <p :class="getResponsiveTextSize('xs') + ' text-gray-600 mb-1'">Order ID</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'font-semibold text-gray-900 text-sm',
                                            tablet: 'font-semibold text-gray-900',
                                            desktop: 'font-semibold text-gray-900'
                                        })">{{ item.order_public_id }}</p>
                                    </div>
                                    <div>
                                        <p :class="getResponsiveTextSize('xs') + ' text-gray-600 mb-1'">Order Type</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'font-medium text-sm',
                                            tablet: 'font-medium',
                                            desktop: 'font-medium'
                                        })">
                                            <i :class="item.order_type == 'Pickup' ? 'pi pi-map-marker' : 'pi pi-truck'" class="mr-1" />
                                            {{ item.order_type }}
                                        </p>
                                    </div>
                                    <div>
                                        <p :class="getResponsiveTextSize('xs') + ' text-gray-600 mb-1'">Order Date</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'font-medium text-sm',
                                            tablet: 'font-medium',
                                            desktop: 'font-medium'
                                        })">{{ DateUtil.formatToMonthDayYear(item.created_at) }}</p>
                                    </div>
                                    <div>
                                        <p :class="getResponsiveTextSize('xs') + ' text-gray-600 mb-1'">Status</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'font-semibold text-sm',
                                            tablet: 'font-semibold',
                                            desktop: 'font-semibold'
                                        })">{{ getCustomerStatusDisplay(item) }}</p>
                                    </div>
                                    <div v-if="item.remarks" :class="getResponsiveClasses({
                                        mobile: 'col-span-2',
                                        tablet: '',
                                        desktop: ''
                                    })">
                                        <p :class="getResponsiveTextSize('xs') + ' text-gray-600 mb-1'">Reason</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'font-semibold text-sm',
                                            tablet: 'font-semibold',
                                            desktop: 'font-semibold'
                                        })">{{ item.remarks }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Order Info -->
                            <div v-if="hasAdditionalInfo(item)" :class="getResponsiveClasses({
                                mobile: 'flex flex-col gap-3 mt-3 pt-3 border-t border-gray-300',
                                tablet: 'flex flex-wrap gap-4 mt-4 pt-4 border-t border-gray-300',
                                desktop: 'flex flex-wrap gap-4 mt-4 pt-4 border-t border-gray-300'
                            })">
                                <div v-if="item.order_type === 'Delivery'" :class="getResponsiveClasses({
                                    mobile: 'w-full',
                                    tablet: 'flex-1 min-w-[200px]',
                                    desktop: 'flex-1 min-w-[200px]'
                                })">
                                    <p :class="getResponsiveTextSize('xs') + ' text-gray-600 mb-1'">Delivery Address</p>
                                    <p :class="getResponsiveTextSize('sm')">{{ showCompleteAddress(item) }}</p>
                                </div>
                                <div v-if="item.payment_method">
                                    <p class="text-xs text-gray-600 mb-1">Payment Method</p>
                                    <p class="font-medium">{{ item.payment_method }}</p>
                                </div>
                                <div v-if="item.delivery_courier">
                                    <p class="text-xs text-gray-600 mb-1">Courier</p>
                                    <p class="font-medium">{{ item.delivery_courier }}</p>
                                </div>
                                <div v-if="item.tracking_number">
                                    <p class="text-xs text-gray-600 mb-1">Tracking Number</p>
                                    <p class="font-mono font-medium">{{ item.tracking_number }}</p>
                                </div>
                                <div v-if="item.estimated_delivery_date_start && item.estimated_delivery_date_end">
                                    <p class="text-xs text-gray-600 mb-1">Estimated Delivery</p>
                                    <p class="font-medium">
                                        {{ DateUtil.formatToMonthDayYear(item.estimated_delivery_date_start) }} - 
                                        {{ DateUtil.formatToMonthDayYear(item.estimated_delivery_date_end) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Delivery Proof Section (for Shipped and Completed orders) -->
                            <div v-if="(item.status === 'Shipped' || item.status === 'Completed') && (hasDeliveryProof(item))" class="mt-4 pt-4 border-t border-gray-300">
                                <div v-if="hasDeliveryProof(item)">
                                    <p class="text-sm font-semibold text-gray-700 mb-3">Preparation & Delivery Proof</p>
                                
                                <!-- Images -->
                                <div v-if="item.delivery_proof_images && parseDeliveryImages(item.delivery_proof_images).length > 0" class="mb-3">
                                    <p class="text-xs text-gray-600 mb-2">Images:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <img
                                            v-for="(image, index) in parseDeliveryImages(item.delivery_proof_images)"
                                            :key="index"
                                            :src="getDeliveryProofUrl(image)"
                                            @click="openImagePreview(getDeliveryProofUrl(image))"
                                            class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity border border-gray-200"
                                            alt="Delivery proof"
                                        />
                                    </div>
                                </div>

                                <!-- Video -->
                                <div v-if="item.delivery_proof_video" class="mb-3">
                                    <p class="text-xs text-gray-600 mb-2">Video:</p>
                                    <video
                                        :src="getDeliveryProofUrl(item.delivery_proof_video)"
                                        controls
                                        class="w-full max-w-md rounded-lg border border-gray-200"
                                    >
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                </div>
                                
                                <div v-else class="text-sm text-gray-500 italic">
                                    No delivery proof uploaded yet.
                                </div>
                            </div>
                        </div>

                        <!-- Products Section - Responsive Layout -->
                        <div v-if="shouldUseCardLayout" class="border-t">
                            <!-- Mobile/Tablet Card Layout -->
                            <div :class="getResponsiveClasses({
                                mobile: 'divide-y divide-gray-200',
                                tablet: 'divide-y divide-gray-200',
                                desktop: ''
                            })">
                                <div
                                    v-for="(productOrder, index) in item.product_orders"
                                    :key="index"
                                    :class="getResponsiveClasses({
                                        mobile: 'p-4 flex flex-col gap-3',
                                        tablet: 'p-4 flex flex-col gap-3',
                                        desktop: ''
                                    })"
                                >
                                    <!-- Product Info -->
                                    <div class="flex items-center gap-3">
                                        <Avatar
                                            v-if="productOrder.product.product_image"
                                            shape="circle"
                                            :image="UrlUtil.getBaseAppUrl(`storage/images/product/${productOrder.product.product_image}`)"
                                            :class="getResponsiveClasses({
                                                mobile: 'w-12 h-12',
                                                tablet: 'w-14 h-14',
                                                desktop: ''
                                            })"
                                            size="large"
                                        />
                                        <Avatar
                                            v-else
                                            shape="circle"
                                            icon="pi pi-camera"
                                            :class="getResponsiveClasses({
                                                mobile: 'w-12 h-12',
                                                tablet: 'w-14 h-14',
                                                desktop: ''
                                            })"
                                            size="large"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p :class="getResponsiveClasses({
                                                mobile: 'font-medium text-gray-900 text-sm',
                                                tablet: 'font-medium text-gray-900',
                                                desktop: ''
                                            })">{{ productOrder.product.product_name }}</p>
                                        </div>
                                    </div>

                                    <!-- Product Details Grid -->
                                    <div :class="getResponsiveClasses({
                                        mobile: 'grid grid-cols-2 gap-3',
                                        tablet: 'grid grid-cols-4 gap-4',
                                        desktop: ''
                                    })">
                                        <div>
                                            <p :class="getResponsiveClasses({
                                                mobile: 'text-xs text-gray-500 mb-1',
                                                tablet: 'text-xs text-gray-500 mb-1',
                                                desktop: ''
                                            })">Price</p>
                                            <p :class="getResponsiveClasses({
                                                mobile: 'font-medium text-sm',
                                                tablet: 'font-medium',
                                                desktop: ''
                                            })">{{ CurrencyUtil.formatCurrency(productOrder.price) }}</p>
                                        </div>
                                        <div>
                                            <p :class="getResponsiveClasses({
                                                mobile: 'text-xs text-gray-500 mb-1',
                                                tablet: 'text-xs text-gray-500 mb-1',
                                                desktop: ''
                                            })">Quantity</p>
                                            <p :class="getResponsiveClasses({
                                                mobile: 'font-medium text-sm',
                                                tablet: 'font-medium',
                                                desktop: ''
                                            })">{{ productOrder.quantity }}</p>
                                        </div>
                                        <div>
                                            <p :class="getResponsiveClasses({
                                                mobile: 'text-xs text-gray-500 mb-1',
                                                tablet: 'text-xs text-gray-500 mb-1',
                                                desktop: ''
                                            })">Subtotal</p>
                                            <p :class="getResponsiveClasses({
                                                mobile: 'font-semibold text-gray-900 text-sm',
                                                tablet: 'font-semibold text-gray-900',
                                                desktop: ''
                                            })">{{ CurrencyUtil.formatCurrency(productOrder.price * productOrder.quantity) }}</p>
                                        </div>
                                        <div v-if="item.status === 'Completed'">
                                            <p :class="getResponsiveClasses({
                                                mobile: 'text-xs text-gray-500 mb-1',
                                                tablet: 'text-xs text-gray-500 mb-1',
                                                desktop: ''
                                            })">Rating</p>
                                            <Button
                                                v-if="!productOrder.rate"
                                                icon="pi pi-star"
                                                label="Rate"
                                                :size="getResponsiveClasses({
                                                    mobile: 'small',
                                                    tablet: 'small',
                                                    desktop: ''
                                                })"
                                                class="primary-bg"
                                                @click="openRate(productOrder)"
                                            />
                                            <Button
                                                v-else
                                                icon="pi pi-star-fill"
                                                label="Rated"
                                                :size="getResponsiveClasses({
                                                    mobile: 'small',
                                                    tablet: 'small',
                                                    desktop: ''
                                                })"
                                                class="primary-bg"
                                                @click="viewRating(productOrder.rate)"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop Table Layout -->
                        <DataTable
                            v-else
                            :value="item.product_orders"
                            class="border-t"
                        >
                            <Column field="product.product_name" header="Product" class="min-w-[250px]">
                                <template #body="{ data }">
                                    <div class="flex items-center gap-3 py-2">
                                        <Avatar
                                            v-if="data.product.product_image"
                                            shape="circle"
                                            :image="UrlUtil.getBaseAppUrl(`storage/images/product/${data.product.product_image}`)"
                                            class="w-12 h-12"
                                            size="large"
                                        />
                                        <Avatar
                                            v-else
                                            shape="circle"
                                            icon="pi pi-camera"
                                            class="w-12 h-12"
                                            size="large"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ data.product.product_name }}</p>
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            <Column header="Price" field="price" class="text-right">
                                <template #body="{ data }">
                                    <span class="font-medium">{{ CurrencyUtil.formatCurrency(data.price) }}</span>
                                </template>
                            </Column>
                            <Column header="Qty" field="quantity" class="text-center">
                                <template #body="{ data }">
                                    <span class="font-medium">{{ data.quantity }}</span>
                                </template>
                            </Column>
                            <Column header="Subtotal" class="text-right">
                                <template #body="{ data }">
                                    <span class="font-semibold text-gray-900">{{ CurrencyUtil.formatCurrency(data.price * data.quantity) }}</span>
                                </template>
                            </Column>
                            <Column header="Rating" class="text-center">
                                <template #body="{ data }">
                                    <template v-if="item.status === 'Completed'">
                                        <Button
                                            v-if="!data.rate"
                                            icon="pi pi-star"
                                            label="Rate"
                                            size="small"
                                            class="primary-bg"
                                            @click="openRate(data)"
                                        />
                                        <Button
                                            v-else
                                            icon="pi pi-star-fill"
                                            label="Rated"
                                            size="small"
                                            class="primary-bg"
                                            @click="viewRating(data.rate)"
                                        />
                                    </template>
                                </template>
                            </Column>
                        </DataTable>

                        <!-- Order Footer -->
                        <div class="p-4 bg-gray-50 border-t">
                            <div class="flex flex-wrap gap-3 justify-between items-center">
                                <!-- Order Total -->
                                <div class="text-right">
                                    <p v-if="item.order_type === 'Delivery' && item.shipping_fee !== null" class="text-sm text-gray-600 mb-1">
                                        Delivery Fee: {{ CurrencyUtil.formatCurrency(item.shipping_fee) }}
                                    </p>
                                    <p class="text-lg font-bold text-gray-900">
                                        Total: {{ CurrencyUtil.formatCurrency(calculateTotal(item)) }}
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2 flex-wrap">
                                    <!-- Processing Status - Not Yet Accepted by Admin -->
                                    <Button
                                        v-if="item.status === 'Processing' && !item.admin_accepted"
                                        label="Cancel Order"
                                        severity="danger"
                                        size="small"
                                        icon="pi pi-times"
                                        @click="cancelOrder(item)"
                                        type="button"
                                    />

                                    <!-- Processing Status - Admin Accepted, Can Pay or Cancel -->
                                    <template v-if="item.status === 'Processing' && item.admin_accepted && !item.proof_of_payment">
                                        <Button
                                            label="Cancel Order"
                                            severity="danger"
                                            size="small"
                                            icon="pi pi-times"
                                            @click="cancelOrder(item)"
                                            type="button"
                                        />
                                        <Button
                                            v-if="item.payment_method === 'Online Payment'"
                                            label="Pay Now"
                                            severity="success"
                                            size="small"
                                            icon="pi pi-money-bill"
                                            @click="openPaymentModal(item)"
                                            type="button"
                                        />
                                    </template>

                                    <!-- Processing Status - Payment Submitted, Awaiting Confirmation -->
                                    <div v-if="item.status === 'Processing' && item.admin_accepted && item.proof_of_payment" class="text-sm text-blue-600 font-medium">
                                        <i class="pi pi-clock mr-1"></i>
                                        Payment submitted - Awaiting confirmation
                                    </div>

                                    <!-- Confirmed Status - Payment Confirmed -->
                                    <div v-if="item.status === 'Confirmed'" class="text-sm text-green-600 font-medium">
                                        <i class="pi pi-check-circle mr-1"></i>
                                        Payment confirmed - Preparing for delivery
                                    </div>

                                    <!-- Shipped Status - Can Mark as Received -->
                                    <MarkAsDelivered
                                        v-if="item.status === 'Shipped'"
                                        :data="item"
                                        @cb="load"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template #empty>
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <div v-if="!loadService.request.loading">
                        <i class="pi pi-shopping-cart text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No orders found</p>
                    </div>
                    <div v-else>
                        <PageLoader />
                    </div>
                </div>
            </template>
        </DataView>

        <!-- Cancel Order Dialog -->
        <Dialog
            v-model:visible="cancelModal.visible"
            header="Cancel Order"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <CancelOrderForm
                v-if="cancelModal.order"
                :order="cancelModal.order"
                @cb="cancelCb"
                @close-popup="closeCancelModal"
            />
        </Dialog>

        <!-- Payment Dialog -->
        <Dialog
            v-model:visible="paymentModal.visible"
            header="Complete Payment"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <PaymentForm
                v-if="paymentModal.order"
                :order="paymentModal.order"
                @cb="paymentModalCb"
                @close-popup="closePaymentModal"
            />
        </Dialog>

        <!-- Rate Product Dialog -->
        <Dialog
            v-model:visible="rateModal.visible"
            header="Rate Product"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <RateForm
                v-if="rateModal.data"
                :data="rateModal.data"
                @cb="rateCb"
            />
        </Dialog>

        <!-- View Rating Dialog -->
        <Dialog
            v-model:visible="viewRateModal.visible"
            header="Your Review"
            :style="{ width: '35rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
        >
            <div v-if="viewRateModal.data" class="flex flex-col gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-3">Rating</p>
                    <div class="flex justify-center gap-1">
                        <i 
                            v-for="star in 5" 
                            :key="star"
                            :class="star <= viewRateModal.data.rate ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-gray-300'"
                            class="text-2xl"
                        ></i>
                    </div>
                </div>
                <div v-if="viewRateModal.data.comment">
                    <p class="text-sm font-medium text-gray-700 mb-2">Comment</p>
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ viewRateModal.data.comment }}</p>
                    </div>
                </div>
                <div v-if="viewRateModal.data.images && viewRateModal.data.images.length > 0">
                    <p class="text-sm font-medium text-gray-700 mb-2">Media</p>
                    <div class="flex flex-wrap gap-2">
                        <div
                            v-for="(image, index) in viewRateModal.data.images"
                            :key="index"
                            @click="openRateMediaPreview(image.image_path)"
                            class="relative w-24 h-24 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition-opacity border-2 border-gray-300 hover:border-blue-600"
                        >
                            <img
                                v-if="isImageFile(image.image_path)"
                                :src="getRateImageUrl(image.image_path)"
                                class="w-full h-full object-cover"
                                alt="Review media"
                            />
                            <video
                                v-else
                                :src="getRateImageUrl(image.image_path)"
                                class="w-full h-full object-cover pointer-events-none"
                            ></video>
                            <div v-if="!isImageFile(image.image_path)" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 pointer-events-none">
                                <i class="pi pi-play text-white text-3xl drop-shadow-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Dialog>

        <!-- Rate Media Preview Dialog -->
        <Dialog
            v-model:visible="rateMediaPreviewModal.visible"
            header="Review Media"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
        >
            <img
                v-if="isImageFile(rateMediaPreviewModal.mediaPath)"
                :src="getRateImageUrl(rateMediaPreviewModal.mediaPath)"
                class="w-full h-auto rounded-lg"
                alt="Review media preview"
            />
            <video
                v-else
                :src="getRateImageUrl(rateMediaPreviewModal.mediaPath)"
                controls
                autoplay
                class="w-full h-auto rounded-lg"
            >
                Your browser does not support the video tag.
            </video>
        </Dialog>

        <!-- Image Preview Dialog -->
        <Dialog
            v-model:visible="imagePreviewModal.visible"
            header="Delivery Proof Image"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
        >
            <img
                :src="imagePreviewModal.imageUrl"
                class="w-full h-auto rounded-lg"
                alt="Delivery proof preview"
            />
        </Dialog>

    </div>
</template>

<script setup lang="ts">
import CancelOrderForm from '@/components/forms/CancelOrderForm.vue';
import ConfirmCashDeliveryPrice from '@/components/forms/ConfirmCashDeliveryPrice.vue';
import MarkAsDelivered from '@/components/forms/MarkAsDelivered.vue';
import PaymentForm from '@/components/forms/PaymentForm.vue';
import RateForm from '@/components/forms/RateForm.vue';
import { IOrder } from '@/interfaces/IOrder';
import { IOrderNotification } from '@/interfaces/IOrderNotification';
import IProductOrder from '@/interfaces/IProductOrder';
import { IRate } from '@/interfaces/IRate';
import Page from '@/stores/Page';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import UrlUtil from '@/utils/UrlUtil';
import { useResponsive } from '@/composables/useResponsive';
import { useEcho } from '@laravel/echo-vue';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';
import { useRouter } from 'vue-router';

const toast = useToast();
const router = useRouter();
const loadService = useAxiosUtil<null, IOrder[]>();
const data = ref<IOrder[]>([]);
const filtered = ref<IOrder[]>([]);

// Use responsive composable
const { 
    isMobile, 
    isTablet, 
    isDesktop, 
    getResponsiveClasses, 
    getResponsivePadding, 
    getResponsiveMargin, 
    getResponsiveTextSize,
    getResponsiveGap,
    shouldUseCardLayout 
} = useResponsive();

const navs = ref([
    { label: 'All', icon: 'pi pi-list', active: true },
    { label: 'Processing', icon: 'pi pi-clock', active: false },
    { label: 'Confirmed', icon: 'pi pi-check-circle', active: false },
    { label: 'Shipped', icon: 'pi pi-truck', active: false },
    { label: 'Completed', icon: 'pi pi-box', active: false },
]);

const orderStatus = (item: IOrder) => {
    // Use actual database status for color mapping
    const statusMap = {
        'Processing': 'bg-yellow-50 border-l-4 border-yellow-400',
        'Confirmed': 'bg-blue-50 border-l-4 border-blue-600',
        'Shipped': 'bg-purple-50 border-l-4 border-purple-400',
        'Completed': 'bg-green-50 border-l-4 border-green-400',
        'Cancelled': 'bg-red-50 border-l-4 border-red-400',
    };
    return statusMap[item.status] || 'bg-gray-50';
};

const cancelModal = ref({ visible: false, order: null as IOrder | null });
const paymentModal = ref({ visible: false, order: null as IOrder | null });
const rateModal = ref({ visible: false, data: null as IProductOrder | null });
const viewRateModal = ref({ visible: false, data: null as IRate | null });

const hasAdditionalInfo = (item: IOrder) => {
    return item.order_type === 'Delivery' || 
           item.payment_method || 
           item.delivery_courier || 
           item.tracking_number || 
           (item.estimated_delivery_date_start && item.estimated_delivery_date_end);
};

// Check if order has delivery proof (images or video)
const hasDeliveryProof = (order: IOrder) => {
    const hasImages = order.delivery_proof_images && 
                     (Array.isArray(order.delivery_proof_images) ? order.delivery_proof_images.length > 0 : true);
    const hasVideo = order.delivery_proof_video && order.delivery_proof_video.trim() !== '';
    return hasImages || hasVideo;
};

// Parse delivery proof images - handle both array and JSON string
const parseDeliveryImages = (images: string | string[]) => {
    if (Array.isArray(images)) {
        return images;
    }
    try {
        return JSON.parse(images);
    } catch {
        return [];
    }
};

// Get full URL for delivery proof files
const getDeliveryProofUrl = (filename: string) => {
    const url = UrlUtil.getBaseAppUrl(`storage/images/delivery-proof/${filename}`);
    console.log('🖼️ Image URL:', { filename, url });
    return url;
};

// Image preview modal
const imagePreviewModal = ref({ visible: false, imageUrl: '' });

const openImagePreview = (imageUrl: string) => {
    imagePreviewModal.value.imageUrl = imageUrl;
    imagePreviewModal.value.visible = true;
};

// Rate media preview modal
const rateMediaPreviewModal = ref({ visible: false, mediaPath: '' });

const openRateMediaPreview = (mediaPath: string) => {
    rateMediaPreviewModal.value.mediaPath = mediaPath;
    rateMediaPreviewModal.value.visible = true;
};

const getRateImageUrl = (filename: string) => {
    return UrlUtil.getBaseAppUrl(`storage/images/rate_images/${filename}`);
};

const isImageFile = (filename: string) => {
    const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp'];
    return imageExtensions.some(ext => filename.toLowerCase().endsWith(ext));
};

// Display status for customer - now matches database status exactly
const getCustomerStatusDisplay = (item: IOrder) => {
    // Processing: Waiting for admin to accept OR waiting for customer to pay OR waiting for admin to confirm payment
    if (item.status === 'Processing') {
        if (!item.admin_accepted) {
            return 'Processing - Awaiting Review';
        } else if (!item.proof_of_payment) {
            return 'Processing - Payment Required';
        } else {
            return 'Processing - Payment Under Review';
        }
    }
    
    // Confirmed: Payment confirmed, preparing for delivery
    if (item.status === 'Confirmed') {
        return 'Confirmed';
    }
    
    // Shipped: Out for delivery
    if (item.status === 'Shipped') {
        return 'Shipped';
    }
    
    // Completed: Order delivered and received
    if (item.status === 'Completed') {
        return 'Completed';
    }
    
    // Cancelled: Order cancelled
    if (item.status === 'Cancelled') {
        return 'Cancelled';
    }
    
    // For any other status, show as-is
    return item.status;
};

const calculateTotal = (item: IOrder) => {
    const productTotal = item.product_orders.reduce((total, product) => 
        total + (product.price * product.quantity), 0
    );
    const shippingFee = item.shipping_fee || 0;
    return productTotal + shippingFee;
};

const openRate = (item: IProductOrder) => {
    // Redirect to product page with review section
    router.push({ 
        name: 'customer.product-info.index', 
        params: { id: item.product_id },
        query: { 
            scrollToReview: 'true',
            productOrderId: item.product_order_id 
        }
    });
};

const rateCb = () => {
    load();
    rateModal.value.visible = false;
};

const viewRating = (value: IRate) => {
    viewRateModal.value.data = value;
    viewRateModal.value.visible = true;
};

const load = async () => {
    await loadService.get("customer/orders").then(() => {
        if (loadService.request.status == 200 && loadService.request.data) {
            data.value = loadService.request.data;
            filtered.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load orders");
        }
    });
};

const setNav = async (index: number) => {
    navs.value.forEach((item, i) => {
        item.active = i === index;
    });
    
    const activeNav = navs.value[index];
    
    // Force reload when switching to Completed tab to ensure rates are loaded
    if (activeNav.label === 'Completed') {
        await load();
    }
    
    if (activeNav.label === 'All') {
        filtered.value = data.value;
    } else {
        // Filter by actual database status
        filtered.value = data.value.filter(item => item.status === activeNav.label);
    }
};

const cancelOrder = (item: IOrder) => {
    cancelModal.value.order = item;
    cancelModal.value.visible = true;
};

const cancelCb = () => {
    cancelModal.value.visible = false;
    load();
};

const closeCancelModal = () => {
    cancelModal.value.order = null;
    cancelModal.value.visible = false;
};

const showCompleteAddress = (value: IOrder) => {
    return `${value.other_details}, ${value.barangay.barangay_name}, ${value.barangay.municity.municity_name}, ${value.barangay.municity.province.province_name}`;
};

const openPaymentModal = (item: IOrder) => {
    console.log('Open Payment Modal Clicked', item);
    if (!item) {
        console.error('No item provided to openPaymentModal');
        return;
    }
    paymentModal.value.order = item;
    paymentModal.value.visible = true;
};

const buyAgainService = useAxiosUtil();
const buyAgain = async (order: IOrder) => {
    await buyAgainService.post(`customer/orders/buy-again/${order.order_id}`, {});
    if (buyAgainService.request.status === 200) {
        toast.success('Items added to cart!');
        // Ideally prompt to go to cart
    } else {
        toast.error(buyAgainService.request.message || 'Failed to add items to cart.');
    }
};

const closePaymentModal = () => {
    paymentModal.value.order = null;
    paymentModal.value.visible = false;
};

const paymentModalCb = () => {
    paymentModal.value.visible = false;
    load();
};

// Check if any orders are awaiting admin review or pending action
const hasPendingOrders = computed(() => {
    return data.value.some(order => 
        order.status === 'Processing' && !order.admin_accepted
    );
});

// Auto-polling: refresh data every 15s when orders are awaiting admin review
// This ensures "Pay Now" appears promptly even if WebSocket events are missed
let pollingInterval: ReturnType<typeof setInterval> | null = null;

const startPolling = () => {
    stopPolling();
    pollingInterval = setInterval(() => {
        if (hasPendingOrders.value) {
            load();
        } else {
            stopPolling();
        }
    }, 15000);
};

const stopPolling = () => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
};

// Start/stop polling based on whether there are pending orders
watch(hasPendingOrders, (pending) => {
    if (pending) {
        startPolling();
    } else {
        stopPolling();
    }
});

const { leave } = useEcho(
    `detect-order.${Page.user.user_id}`,
    [".customer-order.event"],
    (value: IOrderNotification) => {
        load();
    },
);

onMounted(() => {
    load();
    
    // Add visibility change listener to reload when tab becomes visible
    const handleVisibilityChange = () => {
        if (!document.hidden) {
            load();
        }
    };
    
    document.addEventListener('visibilitychange', handleVisibilityChange);
    
    // Cleanup on unmount
    onUnmounted(() => {
        document.removeEventListener('visibilitychange', handleVisibilityChange);
    });
});

onUnmounted(() => {
    leave();
    stopPolling();
});

// Also reload when route changes back to this page
watch(() => router.currentRoute.value.fullPath, (newPath, oldPath) => {
    if (newPath.includes('/orders') && oldPath && !oldPath.includes('/orders')) {
        load();
    }
});
</script>

<style scoped>
/* Remove border from tab buttons container */
.card.mb-4.bg-white.p-4.rounded-lg.shadow-sm {
    border: none !important;
    box-shadow: none !important;
}

/* Custom scrollbar for mobile navigation */
@media (max-width: 767px) {
    .overflow-x-auto::-webkit-scrollbar {
        height: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f9fafb;
        border-radius: 2px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 2px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
}

/* Mobile/Tablet navigation button font sizes */
@media (max-width: 1023px) {
    .card.mb-4 :deep(.p-button .p-button-label) {
        font-size: 0.875rem !important; /* text-sm */
    }
    
    .card.mb-4 :deep(.p-button .p-button-icon) {
        font-size: 0.875rem !important; /* text-sm */
    }
}

/* Mobile pagination responsiveness */
@media (max-width: 767px) {
    /* Make pagination buttons smaller on mobile */
    :deep(.p-paginator .p-paginator-pages .p-paginator-page) {
        min-width: 2rem !important;
        height: 2rem !important;
        font-size: 0.875rem !important;
        padding: 0.25rem !important;
    }
    
    /* Make pagination controls more compact */
    :deep(.p-paginator) {
        padding: 0.5rem !important;
        gap: 0.25rem !important;
    }
    
    /* Hide some pagination elements on very small screens */
    :deep(.p-paginator .p-dropdown) {
        font-size: 0.75rem !important;
    }
    
    /* Make first/last buttons smaller */
    :deep(.p-paginator .p-paginator-first),
    :deep(.p-paginator .p-paginator-prev),
    :deep(.p-paginator .p-paginator-next),
    :deep(.p-paginator .p-paginator-last) {
        min-width: 2rem !important;
        height: 2rem !important;
        padding: 0.25rem !important;
    }
}

@media (max-width: 1023px) and (min-width: 768px) {
    /* Tablet pagination adjustments */
    :deep(.p-paginator .p-paginator-pages .p-paginator-page) {
        min-width: 2.25rem !important;
        height: 2.25rem !important;
    }
}

/* Unselected tab buttons - gray color (ONLY in navigation tabs container) */
.card.mb-4 :deep(.p-button[variant="outlined"]),
.card.mb-4 :deep(.p-button:not(.primary-bg)) {
    background-color: transparent !important;
    color: #6b7280 !important; /* gray-500 */
    border: none !important;
}

.card.mb-4 :deep(.p-button[variant="outlined"]:hover),
.card.mb-4 :deep(.p-button:not(.primary-bg):hover) {
    background-color: #f3f4f6 !important; /* gray-100 */
    color: #4b5563 !important; /* gray-600 */
}

/* ULTRA AGGRESSIVE: Selected tab button - white text with bright blue background (matching header) */
:deep(.p-button.primary-bg),
:deep(button.primary-bg),
:deep(.primary-bg) {
    background-color: #2563eb !important; /* blue-600 - bright blue like header */
    background: #2563eb !important;
    background-image: none !important;
    color: #ffffff !important;
    border: none !important;
}

:deep(.p-button.primary-bg:hover),
:deep(button.primary-bg:hover),
:deep(.primary-bg:hover) {
    background-color: #1d4ed8 !important; /* blue-700 - slightly darker on hover */
    background: #1d4ed8 !important;
    background-image: none !important;
    color: #ffffff !important;
}

/* Icon colors (ONLY in navigation tabs container) */
.card.mb-4 :deep(.p-button:not(.primary-bg) .p-button-icon),
.card.mb-4 :deep(.p-button:not(.primary-bg) i) {
    color: #6b7280 !important;
}

.card.mb-4 :deep(.p-button.primary-bg .p-button-icon),
.card.mb-4 :deep(.p-button.primary-bg i) {
    color: #ffffff !important;
}
</style>