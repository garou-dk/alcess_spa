<template>
    <div class="min-h-screen bg-gray-50">
        <div v-if="product != null && !loadService.request.loading" :class="getResponsiveClasses({
            mobile: 'px-4 py-5',
            tablet: 'px-5 py-6',
            desktop: 'px-6 py-6 max-w-[1200px] mx-auto'
        })">
            <!-- Breadcrumb -->
            <nav class="mb-5">
                <ol class="flex items-center gap-1.5 text-sm text-gray-500">
                    <li>
                        <RouterLink :to="{ name: 'customer.home.index' }" class="hover:text-blue-600 transition-colors">Home</RouterLink>
                    </li>
                    <li><i class="pi pi-chevron-right text-[10px] text-gray-400" /></li>
                    <li>
                        <RouterLink :to="{ name: 'customer.browse-products' }" class="hover:text-blue-600 transition-colors">Products</RouterLink>
                    </li>
                    <li><i class="pi pi-chevron-right text-[10px] text-gray-400" /></li>
                    <li class="text-gray-800 font-medium truncate max-w-[200px]">{{ product.product_name }}</li>
                </ol>
            </nav>

            <!-- Product Section -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                <div :class="getResponsiveClasses({
                    mobile: 'flex flex-col',
                    tablet: 'grid grid-cols-1 lg:grid-cols-2',
                    desktop: 'grid grid-cols-1 lg:grid-cols-2'
                })">
                    <!-- Left: Image Gallery -->
                    <div :class="getResponsiveClasses({
                        mobile: 'p-4',
                        tablet: 'p-5 border-b lg:border-b-0 lg:border-r border-gray-100',
                        desktop: 'p-6 border-r border-gray-100'
                    })">
                        <!-- Main Image -->
                        <div
                            :class="getResponsiveClasses({
                                mobile: 'mb-3 bg-gray-50 rounded-xl overflow-hidden flex items-center justify-center cursor-pointer',
                                tablet: 'mb-4 bg-gray-50 rounded-xl overflow-hidden flex items-center justify-center cursor-pointer',
                                desktop: 'mb-4 bg-gray-50 rounded-xl overflow-hidden flex items-center justify-center cursor-pointer'
                            })"
                            :style="getResponsiveClasses({
                                mobile: 'height: 280px;',
                                tablet: 'height: 380px;',
                                desktop: 'height: 420px;'
                            })"
                            @click="currentImage && openMediaViewer(currentImage)"
                        >
                            <img
                                v-if="currentImage"
                                :src="currentImage"
                                :alt="product.product_name"
                                class="max-h-full max-w-full object-contain transition-transform duration-300 hover:scale-105"
                            />
                            <i v-else :class="getResponsiveClasses({
                                mobile: 'pi pi-image text-5xl text-gray-300',
                                tablet: 'pi pi-image text-6xl text-gray-300',
                                desktop: 'pi pi-image text-6xl text-gray-300'
                            })"></i>
                        </div>

                        <!-- Thumbnail Navigation -->
                        <div v-if="carouselImages.length > 1" :class="getResponsiveClasses({
                            mobile: 'flex items-center gap-2',
                            tablet: 'flex items-center gap-2',
                            desktop: 'flex items-center gap-2.5'
                        })">
                            <button
                                @click="previousImage"
                                :disabled="carouselImages.length <= 1"
                                class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer transition-colors flex-shrink-0"
                            >
                                <i class="pi pi-chevron-left text-sm text-gray-600"></i>
                            </button>

                            <div class="flex gap-2 flex-1 overflow-x-auto scrollbar-hide">
                                <button
                                    v-for="(image, index) in carouselImages"
                                    :key="index"
                                    @click="currentImageIndex = index; resetCarouselInterval();"
                                    :class="[
                                        'flex-shrink-0 rounded-lg overflow-hidden border-2 transition-all cursor-pointer',
                                        getResponsiveClasses({
                                            mobile: 'w-14 h-14',
                                            tablet: 'w-16 h-16',
                                            desktop: 'w-18 h-18'
                                        }),
                                        currentImageIndex === index ? 'border-blue-600 shadow-sm' : 'border-gray-200 hover:border-gray-300'
                                    ]"
                                >
                                    <img
                                        :src="image.url"
                                        :alt="`Thumbnail ${index + 1}`"
                                        class="w-full h-full object-cover"
                                    />
                                </button>
                            </div>

                            <button
                                @click="nextImage"
                                :disabled="carouselImages.length <= 1"
                                class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer transition-colors flex-shrink-0"
                            >
                                <i class="pi pi-chevron-right text-sm text-gray-600"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Right: Product Info -->
                    <div :class="getResponsiveClasses({
                        mobile: 'p-4',
                        tablet: 'p-5',
                        desktop: 'p-6 flex flex-col'
                    })">
                        <!-- Category Badge -->
                        <div class="mb-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                <i class="pi pi-tag text-[10px]" />
                                {{ product.category?.category_name || 'Uncategorized' }}
                            </span>
                        </div>

                        <!-- Product Name -->
                        <h1 :class="getResponsiveClasses({
                            mobile: 'text-xl font-bold text-gray-900 mb-2 leading-tight',
                            tablet: 'text-2xl font-bold text-gray-900 mb-2 leading-tight',
                            desktop: 'text-2xl font-bold text-gray-900 mb-2 leading-tight'
                        })">{{ product.product_name }}</h1>

                        <!-- SKU -->
                        <p class="text-xs text-gray-400 mb-4 font-mono">SKU: {{ product.sku || '—' }}</p>

                        <!-- Rating & Sales -->
                        <div class="flex items-center gap-3 mb-4 flex-wrap">
                            <div class="flex items-center gap-1">
                                <div class="flex gap-0.5">
                                    <i v-for="n in 5" :key="n" :class="product.rates_avg_rate && n <= Math.round(product.rates_avg_rate) ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-gray-300'" class="text-sm"></i>
                                </div>
                                <span class="text-sm text-gray-600 font-semibold ml-1">
                                    {{ product.rates_avg_rate ? Number(product.rates_avg_rate).toFixed(1) : '0.0' }}
                                </span>
                            </div>
                            <span class="text-gray-200">|</span>
                            <span class="text-sm text-gray-500">{{ ratings.length }} {{ ratings.length === 1 ? 'review' : 'reviews' }}</span>
                            <span class="text-gray-200">|</span>
                            <span class="text-sm text-gray-500">{{ product.total_sold || 0 }} sold</span>
                        </div>

                        <!-- Price -->
                        <div class="mb-5 pb-5 border-b border-gray-100">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-3xl font-bold text-gray-900',
                                tablet: 'text-3xl font-bold text-gray-900',
                                desktop: 'text-3xl font-bold text-gray-900'
                            })">
                                {{ CurrencyUtil.formatCurrency(product.product_price) }}
                            </p>
                        </div>

                        <!-- Stock Status -->
                        <div class="mb-5">
                            <div v-if="product.product_quantity === 0" class="flex items-center gap-2.5 px-4 py-3 bg-red-50 border border-red-100 rounded-xl">
                                <i class="pi pi-times-circle text-red-500 text-lg"></i>
                                <div>
                                    <p class="text-sm font-semibold text-red-800">Out of Stock</p>
                                    <p class="text-xs text-red-600">This product is currently unavailable.</p>
                                </div>
                            </div>
                            <div v-else class="flex items-center gap-2 text-sm">
                                <i class="pi pi-check-circle text-green-500" />
                                <span class="text-green-700 font-medium">{{ product.product_quantity }} pieces available</span>
                            </div>
                        </div>

                        <!-- Quantity & Add to Cart -->
                        <div v-if="product.product_quantity > 0" class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                    <button
                                        @click="decrementQuantity"
                                        :disabled="form.quantity <= 1"
                                        class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                                    >
                                        <i class="pi pi-minus text-xs"></i>
                                    </button>
                                    <input
                                        type="number"
                                        v-model.number="form.quantity"
                                        @input="validateQuantityInput"
                                        min="1"
                                        :max="product.product_quantity"
                                        class="w-14 h-9 text-center text-sm font-semibold border-x border-gray-200 focus:outline-none bg-white qty-input"
                                    />
                                    <button
                                        @click="incrementQuantity"
                                        :disabled="form.quantity >= product.product_quantity"
                                        class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                                    >
                                        <i class="pi pi-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <button
                                @click="buyNow"
                                :disabled="submitService.request.loading"
                                :class="getResponsiveClasses({
                                    mobile: 'w-full',
                                    tablet: 'w-full lg:w-auto lg:min-w-[200px]',
                                    desktop: 'min-w-[200px]'
                                })"
                                class="flex items-center justify-center gap-2 bg-blue-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-blue-700 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <i v-if="submitService.request.loading" class="pi pi-spin pi-spinner"></i>
                                <i v-else class="pi pi-shopping-cart"></i>
                                <span>Add to Cart</span>
                            </button>
                        </div>

                        <!-- Product Info Accordion -->
                        <div class="mt-auto border-t border-gray-100 pt-5 space-y-1">
                            <button
                                @click="showInfoModal = true"
                                class="w-full py-3 px-4 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-xl transition-colors cursor-pointer flex items-center justify-between group"
                            >
                                <div class="flex items-center gap-2.5">
                                    <i class="pi pi-info-circle text-blue-600" />
                                    <span>Product Information</span>
                                </div>
                                <i class="pi pi-chevron-right text-xs text-gray-400 group-hover:text-blue-600 transition-colors" />
                            </button>
                            <button
                                @click="showSpecModal = true"
                                class="w-full py-3 px-4 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-xl transition-colors cursor-pointer flex items-center justify-between group"
                            >
                                <div class="flex items-center gap-2.5">
                                    <i class="pi pi-list text-blue-600" />
                                    <span>Specifications</span>
                                </div>
                                <i class="pi pi-chevron-right text-xs text-gray-400 group-hover:text-blue-600 transition-colors" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Reviews Section -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Reviews Header -->
                <div :class="getResponsiveClasses({
                    mobile: 'px-4 py-4 border-b border-gray-100',
                    tablet: 'px-5 py-5 border-b border-gray-100',
                    desktop: 'px-6 py-5 border-b border-gray-100'
                })">
                    <h2 :class="getResponsiveClasses({
                        mobile: 'text-lg font-bold text-gray-900',
                        tablet: 'text-xl font-bold text-gray-900',
                        desktop: 'text-xl font-bold text-gray-900'
                    })">Customer Reviews</h2>
                </div>

                <div :class="getResponsiveClasses({
                    mobile: 'p-4',
                    tablet: 'p-5',
                    desktop: 'p-6'
                })">
                    <!-- Rating Summary & Filters -->
                    <div :class="getResponsiveClasses({
                        mobile: 'flex flex-col gap-4 mb-6',
                        tablet: 'flex flex-col lg:flex-row gap-5 mb-6',
                        desktop: 'flex flex-col lg:flex-row gap-6 mb-6'
                    })">
                        <!-- Rating Summary Card -->
                        <div :class="getResponsiveClasses({
                            mobile: 'flex items-center gap-4 bg-gray-50 rounded-xl p-4',
                            tablet: 'flex flex-col items-center justify-center bg-gray-50 rounded-xl p-5 lg:w-56',
                            desktop: 'flex flex-col items-center justify-center bg-gray-50 rounded-xl p-6 lg:w-56'
                        })">
                            <div class="text-center">
                                <p :class="getResponsiveClasses({
                                    mobile: 'text-4xl font-bold text-gray-900',
                                    tablet: 'text-5xl font-bold text-gray-900 mb-1',
                                    desktop: 'text-5xl font-bold text-gray-900 mb-1'
                                })">
                                    {{ product.rates_avg_rate ? Number(product.rates_avg_rate).toFixed(1) : '0.0' }}
                                </p>
                                <p class="text-xs text-gray-500">out of 5</p>
                            </div>
                            <div class="flex flex-col items-center gap-1.5">
                                <div class="flex gap-0.5">
                                    <i v-for="n in 5" :key="n" :class="product.rates_avg_rate && n <= Math.round(product.rates_avg_rate) ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-gray-300'" class="text-lg"></i>
                                </div>
                                <span class="text-xs text-gray-500 font-medium">{{ ratings.length }} {{ ratings.length === 1 ? 'review' : 'reviews' }}</span>
                            </div>
                        </div>

                        <!-- Filter Chips -->
                        <div class="flex-1">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <button
                                    @click="filterRating = null; filterWithComments = false; filterWithMedia = false;"
                                    :class="[
                                        'px-3 py-1.5 rounded-full text-xs font-semibold transition-colors cursor-pointer',
                                        filterRating === null && !filterWithComments && !filterWithMedia
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                    ]"
                                >
                                    All ({{ ratings.length }})
                                </button>
                                <button
                                    v-for="star in [5, 4, 3, 2, 1]"
                                    :key="star"
                                    @click="filterRating = star; filterWithComments = false; filterWithMedia = false;"
                                    :class="[
                                        'px-3 py-1.5 rounded-full text-xs font-semibold transition-colors cursor-pointer flex items-center gap-1',
                                        filterRating === star
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                    ]"
                                >
                                    <i class="pi pi-star-fill text-[10px]" :class="filterRating === star ? 'text-white' : 'text-yellow-400'" />
                                    {{ star }} ({{ getStarCount(star) }})
                                </button>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    @click="filterWithComments = !filterWithComments; filterRating = null; filterWithMedia = false;"
                                    :class="[
                                        'px-3 py-1.5 rounded-full text-xs font-semibold transition-colors cursor-pointer flex items-center gap-1',
                                        filterWithComments
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                    ]"
                                >
                                    <i class="pi pi-comment text-[10px]" />
                                    Comments ({{ getCommentsCount() }})
                                </button>
                                <button
                                    @click="filterWithMedia = !filterWithMedia; filterRating = null; filterWithComments = false;"
                                    :class="[
                                        'px-3 py-1.5 rounded-full text-xs font-semibold transition-colors cursor-pointer flex items-center gap-1',
                                        filterWithMedia
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                    ]"
                                >
                                    <i class="pi pi-images text-[10px]" />
                                    Media ({{ getMediaCount() }})
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Write a Review -->
                    <div v-if="Page.user && product.user_has_purchased" class="mb-6 bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                        <div :class="getResponsiveClasses({
                            mobile: 'px-4 py-3 border-b border-gray-200',
                            tablet: 'px-5 py-3.5 border-b border-gray-200',
                            desktop: 'px-5 py-3.5 border-b border-gray-200'
                        })">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <i class="pi pi-pencil text-blue-600 text-xs" />
                                Write a Review
                            </h3>
                        </div>

                        <div :class="getResponsiveClasses({
                            mobile: 'p-4',
                            tablet: 'p-5',
                            desktop: 'p-5'
                        })">
                            <!-- Already reviewed info -->
                            <div v-if="!product.product_order_id" class="mb-4 px-3 py-2.5 bg-green-50 border border-green-100 rounded-lg flex items-center gap-2">
                                <i class="pi pi-check-circle text-green-600 text-sm"></i>
                                <p class="text-xs text-green-800 font-medium">Thank you! You've already submitted a review for this product.</p>
                            </div>

                            <form @submit.prevent="submitReview">
                                <!-- Star Rating -->
                                <div class="mb-4">
                                    <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Your Rating</label>
                                    <div class="flex gap-1.5">
                                        <button
                                            v-for="star in 5"
                                            :key="star"
                                            type="button"
                                            @click="product.product_order_id && (reviewForm.rating = star)"
                                            :disabled="!product.product_order_id"
                                            :class="[
                                                'text-2xl transition-colors',
                                                star <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-300',
                                                product.product_order_id ? 'cursor-pointer hover:text-yellow-300' : 'cursor-not-allowed opacity-50'
                                            ]"
                                        >
                                            <i :class="star <= reviewForm.rating ? 'pi pi-star-fill' : 'pi pi-star'"></i>
                                        </button>
                                    </div>
                                    <p v-if="reviewErrors.rating" class="text-xs text-red-600 mt-1">{{ reviewErrors.rating }}</p>
                                </div>

                                <!-- Comment -->
                                <div class="mb-4">
                                    <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Your Review</label>
                                    <textarea
                                        v-model="reviewForm.comment"
                                        :disabled="!product.product_order_id"
                                        rows="3"
                                        class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed transition-all bg-white"
                                        placeholder="Share your experience with this product..."
                                    ></textarea>
                                    <p v-if="reviewErrors.comment" class="text-xs text-red-600 mt-1">{{ reviewErrors.comment }}</p>
                                </div>

                                <!-- Media Upload -->
                                <div class="mb-4">
                                    <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">
                                        Photos or Videos
                                        <span class="text-gray-400 font-normal normal-case ml-1">(Optional, max 4 files)</span>
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <label :class="product.product_order_id ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'">
                                            <input
                                                type="file"
                                                ref="mediaInput"
                                                @change="handleMediaUpload"
                                                accept="image/*,video/*"
                                                multiple
                                                :disabled="!product.product_order_id"
                                                class="hidden"
                                            />
                                            <div :class="[
                                                'flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-sm transition-colors',
                                                product.product_order_id ? 'hover:bg-gray-50 bg-white' : 'bg-gray-100'
                                            ]">
                                                <i class="pi pi-camera text-gray-500 text-sm"></i>
                                                <span class="text-xs text-gray-600 font-medium">Upload</span>
                                            </div>
                                        </label>
                                        <span class="text-xs text-gray-400">{{ reviewForm.media.length }} file(s)</span>
                                    </div>

                                    <!-- Preview Media -->
                                    <div v-if="reviewForm.media.length > 0" class="mt-3 flex gap-2 flex-wrap">
                                        <div
                                            v-for="(file, index) in reviewForm.media"
                                            :key="index"
                                            class="relative w-16 h-16 rounded-lg overflow-hidden border border-gray-200"
                                        >
                                            <img
                                                v-if="file.type.startsWith('image/')"
                                                :src="getFilePreview(file)"
                                                class="w-full h-full object-cover"
                                            />
                                            <div v-else-if="file.type.startsWith('video/')" class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                <i class="pi pi-video text-lg text-gray-400"></i>
                                            </div>
                                            <div v-else class="w-full h-full bg-gray-100 flex flex-col items-center justify-center p-1">
                                                <i class="pi pi-file text-lg text-gray-400"></i>
                                            </div>
                                            <button
                                                type="button"
                                                @click="removeMedia(index)"
                                                class="absolute top-0.5 right-0.5 w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 cursor-pointer"
                                            >
                                                <i class="pi pi-times text-[8px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        :disabled="!product.product_order_id || reviewSubmitService.request.loading"
                                        :class="getResponsiveClasses({
                                            mobile: 'w-full',
                                            tablet: 'w-auto',
                                            desktop: 'w-auto'
                                        })"
                                        class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center gap-2"
                                    >
                                        <i v-if="reviewSubmitService.request.loading" class="pi pi-spin pi-spinner text-sm"></i>
                                        <span>Submit Review</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sign In Prompt -->
                    <div v-else-if="!Page.user" class="mb-6 bg-gray-50 rounded-xl border border-gray-200 p-5">
                        <div :class="getResponsiveClasses({
                            mobile: 'flex flex-col items-center text-center gap-3',
                            tablet: 'flex items-center gap-3',
                            desktop: 'flex items-center gap-3'
                        })">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <i class="pi pi-sign-in text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-gray-800 mb-1">Sign in to review</h3>
                                <p class="text-xs text-gray-500">Log in to your account to write a review for this product.</p>
                            </div>
                            <button
                                @click="router.push({ name: 'auth.login' })"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors cursor-pointer flex-shrink-0"
                            >
                                Sign In
                            </button>
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div v-if="filteredRatings.length > 0" class="space-y-0 divide-y divide-gray-100">
                        <div
                            v-for="(review, index) in filteredRatings"
                            :key="index"
                            :class="getResponsiveClasses({
                                mobile: 'py-4 first:pt-0',
                                tablet: 'py-5 first:pt-0',
                                desktop: 'py-5 first:pt-0'
                            })"
                        >
                            <div class="flex items-start gap-3">
                                <!-- Avatar -->
                                <Avatar
                                    :label="review.user.full_name.charAt(0).toUpperCase()"
                                    shape="circle"
                                    :size="getResponsiveClasses({
                                        mobile: 'normal',
                                        tablet: 'normal',
                                        desktop: 'normal'
                                    })"
                                    class="bg-blue-600 text-white flex-shrink-0"
                                />

                                <div class="flex-1 min-w-0">
                                    <!-- Name & Date -->
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ review.user.full_name }}</h4>
                                        <span class="text-xs text-gray-400">{{ formatDate(review.created_at) }}</span>
                                    </div>

                                    <!-- Stars -->
                                    <div class="flex gap-0.5 mb-2">
                                        <i v-for="n in 5" :key="n" :class="n <= review.rate ? 'pi pi-star-fill text-yellow-400' : 'pi pi-star text-gray-300'" class="text-xs"></i>
                                    </div>

                                    <!-- Comment -->
                                    <div v-if="review.comment" class="mb-3">
                                        <p class="text-sm text-gray-700 leading-relaxed">{{ review.comment }}</p>
                                    </div>

                                    <!-- Review Media -->
                                    <div v-if="review.images && review.images.length > 0" class="flex gap-2 mb-3 overflow-x-auto">
                                        <div
                                            v-for="(media, imgIndex) in review.images.slice(0, 4)"
                                            :key="imgIndex"
                                            @click="openMediaViewer(media)"
                                            :class="getResponsiveClasses({
                                                mobile: 'flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden cursor-pointer relative group bg-gray-100',
                                                tablet: 'w-18 h-18 rounded-lg overflow-hidden cursor-pointer relative group bg-gray-100',
                                                desktop: 'w-18 h-18 rounded-lg overflow-hidden cursor-pointer relative group bg-gray-100'
                                            })"
                                        >
                                            <video
                                                v-if="isVideoUrl(media)"
                                                :src="media"
                                                class="w-full h-full object-cover pointer-events-none"
                                            ></video>
                                            <img
                                                v-else
                                                :src="media"
                                                :alt="`Review image ${imgIndex + 1}`"
                                                class="w-full h-full object-cover"
                                                @error="handleImageError"
                                            />
                                            <div v-if="isVideoUrl(media)" class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/50 transition-all pointer-events-none">
                                                <i class="pi pi-play text-white text-lg"></i>
                                            </div>
                                            <div v-else class="absolute inset-0 flex items-center justify-center group-hover:bg-black/20 transition-all pointer-events-none">
                                                <i class="pi pi-search-plus text-white text-sm opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Like & Helpful -->
                                    <div class="flex items-center gap-3">
                                        <button
                                            @click="toggleLike(review)"
                                            :disabled="!Page.user || likeService.request.loading"
                                            :class="[
                                                'flex items-center gap-1.5 text-xs transition-colors',
                                                isLikedByCurrentUser(review) ? 'text-red-500 font-semibold' : 'text-gray-500 hover:text-red-500',
                                                !Page.user ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
                                            ]"
                                        >
                                            <i :class="isLikedByCurrentUser(review) ? 'pi pi-heart-fill' : 'pi pi-heart'" class="text-sm"></i>
                                            <span>{{ isLikedByCurrentUser(review) ? 'Liked' : 'Helpful' }}</span>
                                            <span v-if="review.likes_count && review.likes_count > 0" class="font-semibold">({{ review.likes_count }})</span>
                                        </button>
                                    </div>

                                    <!-- Seller Reply -->
                                    <div v-if="review.reply" class="mt-3 ml-3 p-3 bg-blue-50 rounded-lg border-l-3 border-blue-500">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <i class="pi pi-reply text-blue-600 text-xs" />
                                            <span class="text-xs font-semibold text-blue-800">Seller Response</span>
                                        </div>
                                        <p class="text-xs text-gray-700 leading-relaxed">{{ review.reply }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Reviews -->
                    <div v-else class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                            <i class="pi pi-star text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">No reviews yet. Be the first to review this product!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loadService.request.loading" class="flex justify-center items-center py-24">
            <div class="flex flex-col items-center gap-3">
                <i class="pi pi-spin pi-spinner text-3xl text-blue-600"></i>
                <span class="text-sm text-gray-500 font-medium">Loading product...</span>
            </div>
        </div>

        <!-- Media Viewer Modal -->
        <div
            v-if="mediaViewer.show"
            @click="closeMediaViewer"
            class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
        >
            <button
                @click="closeMediaViewer"
                class="absolute top-4 right-4 text-white/80 hover:text-white text-2xl z-10 cursor-pointer w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 transition-colors"
            >
                <i class="pi pi-times"></i>
            </button>

            <div @click.stop class="relative max-w-5xl max-h-[90vh] w-full">
                <img
                    v-if="!isVideoUrl(mediaViewer.url)"
                    :src="mediaViewer.url"
                    class="max-w-full max-h-[90vh] mx-auto object-contain"
                    alt="Full size media"
                />
                <video
                    v-else
                    :src="mediaViewer.url"
                    class="max-w-full max-h-[90vh] mx-auto"
                    controls
                    autoplay
                ></video>
            </div>
        </div>

        <!-- Product Information Modal -->
        <Dialog
            v-model:visible="showInfoModal"
            modal
            header="Product Information"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '85vw', '575px': '95vw' }"
            :dismissableMask="true"
            :pt="{
                root: { class: 'rounded-2xl overflow-hidden' },
                header: { class: 'border-b border-gray-100 px-6 py-4' },
                content: { class: 'px-6 py-5' },
                mask: { class: 'bg-black/50 backdrop-blur-sm' }
            }"
        >
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide">Description</h3>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ product?.description || 'No description available.' }}</p>
            </div>
        </Dialog>

        <!-- Product Specification Modal -->
        <Dialog
            v-model:visible="showSpecModal"
            modal
            header="Product Specifications"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '85vw', '575px': '95vw' }"
            :dismissableMask="true"
            :pt="{
                root: { class: 'rounded-2xl overflow-hidden' },
                header: { class: 'border-b border-gray-100 px-6 py-4' },
                content: { class: 'px-6 py-5' },
                mask: { class: 'bg-black/50 backdrop-blur-sm' }
            }"
        >
            <div>
                <div v-if="product?.specifications && product.specifications.length > 0">
                    <div
                        v-for="(spec, index) in product.specifications"
                        :key="index"
                        class="flex py-3 border-b border-gray-100 last:border-0"
                    >
                        <span class="text-sm font-semibold text-gray-700 w-1/3 flex-shrink-0">{{ spec.specification_name }}</span>
                        <span class="text-sm text-gray-600">{{ spec.specification_value }}</span>
                    </div>
                </div>
                <div v-else class="text-center py-8">
                    <i class="pi pi-list text-3xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500">No specifications available.</p>
                </div>
            </div>
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
import { useResponsive } from "@/composables/useResponsive";
import { computed, inject, onMounted, onUnmounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";
import Dialog from "primevue/dialog";
import Avatar from "primevue/avatar";

const loadService = useAxiosUtil<null, ProductInterface>();
const submitService = useAxiosUtil<CartFormInterface, null>();
const reviewSubmitService = useAxiosUtil<any, null>();
const likeService = useAxiosUtil<any, any>();
const product = ref<ProductInterface | null>(null);
const route = useRoute();
const toast = useToast();
const router = useRouter();
const mediaInput = ref<HTMLInputElement | null>(null);

// Use responsive composable
const { 
    isMobile, 
    isTablet, 
    isDesktop, 
    getResponsiveClasses, 
    getResponsivePadding, 
    getResponsiveMargin, 
    getResponsiveTextSize,
    getResponsiveGap 
} = useResponsive();



const ratings = ref<IRate[]>([]);
const filterRating = ref<number | null>(null);
const filterWithComments = ref(false);
const filterWithMedia = ref(false);
const currentImageIndex = ref(0);
const showInfoModal = ref(false);
const showSpecModal = ref(false);
let carouselInterval: number | null = null;

const mediaViewer = reactive({
    show: false,
    url: '',
});

const form: CartFormInterface = reactive({
    product_id: null,
    quantity: 1,
});

const errors = reactive({
    product_id: [],
    quantity: [],
});

const reviewForm = reactive({
    rating: 0,
    comment: '',
    media: [] as File[],
});

const reviewErrors = reactive({
    rating: '',
    comment: '',
});

const carouselImages = computed(() => {
    if (!product.value) return [];
    
    const images = [];
    
    if (product.value.product_image) {
        images.push({
            url: UrlUtil.getBaseAppUrl(`storage/images/product/${product.value.product_image}`),
            isMain: true,
        });
    }
    
    if (product.value.featured_images && product.value.featured_images.length > 0) {
        product.value.featured_images.forEach(img => {
            images.push({
                url: UrlUtil.getBaseAppUrl(`storage/images/featured/${img.featured_image}`),
                isMain: false,
            });
        });
    }
    
    return images;
});

const currentImage = computed(() => {
    if (carouselImages.value.length === 0) return null;
    const safeIndex = currentImageIndex.value % carouselImages.value.length;
    return carouselImages.value[safeIndex]?.url || null;
});

const filteredRatings = computed(() => {
    let filtered = ratings.value;
    
    if (filterRating.value !== null) {
        filtered = filtered.filter(rating => rating.rate === filterRating.value);
    }
    
    if (filterWithComments.value) {
        filtered = filtered.filter(rating => rating.comment && rating.comment.trim() !== '');
    }
    
    if (filterWithMedia.value) {
        filtered = filtered.filter(rating => rating.images && rating.images.length > 0);
    }
    
    return filtered;
});

const getStarCount = (star: number) => {
    return ratings.value.filter(rating => rating.rate === star).length;
};

const getCommentsCount = () => {
    return ratings.value.filter(rating => rating.comment && rating.comment.trim() !== '').length;
};

const getMediaCount = () => {
    return ratings.value.filter(rating => rating.images && rating.images.length > 0).length;
};

const incrementQuantity = () => {
    if (product.value && form.quantity < product.value.product_quantity) {
        form.quantity++;
    }
};

const decrementQuantity = () => {
    if (form.quantity > 1) {
        form.quantity--;
    }
};

const validateQuantityInput = () => {
    if (!product.value) return;
    
    if (isNaN(form.quantity) || form.quantity === null) {
        form.quantity = 1;
        return;
    }
    
    if (form.quantity < 1) {
        form.quantity = 1;
    }
    
    if (form.quantity > product.value.product_quantity) {
        form.quantity = product.value.product_quantity;
        toast.warning(`Only ${product.value.product_quantity} pieces available`);
    }
};

const autoAdvanceCarousel = () => {
    if (currentImageIndex.value < carouselImages.value.length - 1) {
        currentImageIndex.value++;
    } else {
        currentImageIndex.value = 0;
    }
};

const nextImage = () => {
    autoAdvanceCarousel();
    resetCarouselInterval();
};

const previousImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--;
    } else {
        currentImageIndex.value = carouselImages.value.length - 1;
    }
    resetCarouselInterval();
};

const startCarousel = () => {
    if (carouselImages.value.length > 1) {
        carouselInterval = window.setInterval(() => {
            autoAdvanceCarousel();
        }, 4000);
    }
};

const stopCarousel = () => {
    if (carouselInterval) {
        clearInterval(carouselInterval);
        carouselInterval = null;
    }
};

const resetCarouselInterval = () => {
    stopCarousel();
    startCarousel();
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
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
            ratings.value.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
            startCarousel();
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
    } else if (product.value && form.quantity > product.value.product_quantity) {
        errors.quantity.push(
            `Only ${product.value.product_quantity} available`,
        );
    }

    const hasErrors = [
        errors.product_id.length > 0,
        errors.quantity.length > 0,
    ];

    return hasErrors.includes(true) ? false : form;
};

const submitForm = async () => {
    if (!Page.user) {
        router.push({ name: 'auth.login' });
        return;
    }
    
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

const buyNow = async () => {
    if (!Page.user) {
        router.push({ name: 'auth.login' });
        return;
    }
    
    await submitForm();
};

const handleMediaUpload = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        const files = Array.from(target.files);
        const validFiles: File[] = [];
        
        if (files.length > 4) {
            toast.error('You can only upload up to 4 files per review.');
            if (target) {
                target.value = '';
            }
            return;
        }
        
        for (const file of files) {
            if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
                toast.error(`File "${file.name}" is not supported. Only images and videos are allowed.`);
                continue;
            }
            
            const maxSize = 50 * 1024 * 1024;
            if (file.size > maxSize) {
                toast.error(`File "${file.name}" is too large. Maximum size is 50MB.`);
                continue;
            }
            
            if (file.type.startsWith('video/')) {
                try {
                    const duration = await getVideoDuration(file);
                    const maxDuration = 90;
                    if (duration > maxDuration) {
                        toast.error(`Video "${file.name}" is too long. Maximum duration is 1 minute 30 seconds (${Math.round(duration)}s detected).`);
                        continue;
                    }
                } catch (error) {
                    toast.error(`Failed to validate video "${file.name}". Please try another file.`);
                    continue;
                }
            }
            
            validFiles.push(file);
        }
        
        reviewForm.media = validFiles;
        
        if (validFiles.length > 0) {
            const imageCount = validFiles.filter(f => f.type.startsWith('image/')).length;
            const videoCount = validFiles.filter(f => f.type.startsWith('video/')).length;
            const summary = [];
            if (imageCount > 0) summary.push(`${imageCount} image${imageCount > 1 ? 's' : ''}`);
            if (videoCount > 0) summary.push(`${videoCount} video${videoCount > 1 ? 's' : ''}`);
            toast.success(`Selected: ${summary.join(' and ')}`);
        }
        
        if (validFiles.length === 0 && target) {
            target.value = '';
        }
    }
};

const getVideoDuration = (file: File): Promise<number> => {
    return new Promise((resolve, reject) => {
        const video = document.createElement('video');
        video.preload = 'metadata';
        
        video.onloadedmetadata = () => {
            window.URL.revokeObjectURL(video.src);
            resolve(video.duration);
        };
        
        video.onerror = () => {
            reject(new Error('Failed to load video'));
        };
        
        video.src = URL.createObjectURL(file);
    });
};

const isVideoUrl = (url: string | any): boolean => {
    if (typeof url !== 'string') return false;
    const videoExtensions = ['.mp4', '.mov', '.avi', '.webm', '.mkv'];
    return videoExtensions.some(ext => url.toLowerCase().includes(ext));
};

const openMediaViewer = (url: string) => {
    mediaViewer.url = url;
    mediaViewer.show = true;
    document.body.style.overflow = 'hidden';
    document.addEventListener('keydown', handleEscKey);
};

const closeMediaViewer = () => {
    mediaViewer.show = false;
    mediaViewer.url = '';
    document.body.style.overflow = '';
    document.removeEventListener('keydown', handleEscKey);
};

const handleEscKey = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
        closeMediaViewer();
    }
};

const handleImageError = (event: Event) => {
    const img = event.target as HTMLImageElement;
    console.error('Failed to load image:', img.src);
};

const removeMedia = (index: number) => {
    reviewForm.media.splice(index, 1);
};

const getFilePreview = (file: File) => {
    return URL.createObjectURL(file);
};

const validateReview = () => {
    reviewErrors.rating = '';
    reviewErrors.comment = '';
    
    let isValid = true;
    
    if (reviewForm.rating === 0) {
        reviewErrors.rating = 'Please select a rating';
        isValid = false;
    }
    
    if (!reviewForm.comment.trim()) {
        reviewErrors.comment = 'Please write a review';
        isValid = false;
    }
    
    return isValid;
};

const submitReview = async () => {
    if (!validateReview()) {
        toast.error('Please fill in all required fields');
        return;
    }
    
    if (!product.value?.product_order_id) {
        toast.error('Unable to submit review. Please ensure you have received this product.');
        return;
    }
    
    if (reviewSubmitService.request.loading) {
        return;
    }
    
    const formData = new FormData();
    formData.append('rate', reviewForm.rating.toString());
    formData.append('comment', reviewForm.comment);
    
    reviewForm.media.forEach((file) => {
        formData.append('media[]', file);
    });
    
    await reviewSubmitService.postFormData(`customer/rates/${product.value.product_order_id}`, formData).then(() => {
        if (reviewSubmitService.request.status === 200) {
            toast.success('Review submitted successfully!');
            reviewForm.rating = 0;
            reviewForm.comment = '';
            reviewForm.media = [];
            if (mediaInput.value) {
                mediaInput.value.value = '';
            }
            if (product.value) {
                product.value.product_order_id = null;
                product.value.user_has_reviewed = true;
            }
            load();
        } else {
            toast.error(reviewSubmitService.request.message ?? 'Failed to submit review');
        }
    });
};

const isLikedByCurrentUser = (review: IRate) => {
    if (!Page.user || !review.likes) return false;
    return review.likes.some(like => like.user_id === Page.user?.user_id);
};

const toggleLike = async (review: IRate) => {
    if (!Page.user) {
        toast.error('Please login to like reviews');
        return;
    }

    await likeService.post(`customer/rates/${review.rate_id}/like`, {}).then(() => {
        if (likeService.request.status === 200 && likeService.request.data) {
            const { liked, likes_count } = likeService.request.data;
            
            const reviewIndex = ratings.value.findIndex(r => r.rate_id === review.rate_id);
            if (reviewIndex !== -1) {
                ratings.value[reviewIndex].likes_count = likes_count;
                
                if (liked) {
                    if (!ratings.value[reviewIndex].likes) {
                        ratings.value[reviewIndex].likes = [];
                    }
                    ratings.value[reviewIndex].likes!.push({
                        rate_like_id: 0,
                        rate_id: review.rate_id,
                        user_id: Page.user!.user_id,
                    });
                } else {
                    if (ratings.value[reviewIndex].likes) {
                        ratings.value[reviewIndex].likes = ratings.value[reviewIndex].likes!.filter(
                            like => like.user_id !== Page.user!.user_id
                        );
                    }
                }
            }
        } else {
            toast.error(likeService.request.message ?? 'Failed to toggle like');
        }
    });
};

onMounted(() => {
    load();
    
    if (route.query.scrollToReview === 'true') {
        setTimeout(() => {
            const reviewSection = document.querySelector('h2');
            if (reviewSection && reviewSection.textContent?.includes('Customer Reviews')) {
                reviewSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                const reviewForm = reviewSection.parentElement?.querySelector('form');
                if (reviewForm) {
                    reviewForm.classList.add('ring-4', 'ring-blue-300', 'ring-opacity-50');
                    setTimeout(() => {
                        reviewForm.classList.remove('ring-4', 'ring-blue-300', 'ring-opacity-50');
                    }, 2000);
                }
            }
        }, 500);
    }
});

onUnmounted(() => {
    stopCarousel();
});
</script>

<style scoped>
/* Hide number input arrows */
.qty-input::-webkit-inner-spin-button,
.qty-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.qty-input {
    -moz-appearance: textfield;
    appearance: textfield;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
    appearance: textfield;
}

/* Hide scrollbar for thumbnail navigation */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Line clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
