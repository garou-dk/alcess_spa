<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div v-if="product != null && !loadService.request.loading" :class="getResponsiveClasses({
            mobile: 'container mx-auto px-3',
            tablet: 'container mx-auto px-4', 
            desktop: 'container mx-auto px-4'
        })">
            <!-- Product Section -->
            <div :class="getResponsiveClasses({
                mobile: 'mb-6 bg-white rounded-lg shadow-sm p-4',
                tablet: 'mb-8 bg-white rounded-lg shadow-sm p-5',
                desktop: 'mb-8 bg-white rounded-lg shadow-sm p-6'
            })">
                <div :class="getResponsiveClasses({
                    mobile: 'flex flex-col gap-6',
                    tablet: 'grid grid-cols-1 lg:grid-cols-2 gap-6',
                    desktop: 'grid grid-cols-1 lg:grid-cols-2 gap-8'
                })">
                    <!-- Left: Image Gallery -->
                    <div class="flex flex-col">
                        <!-- Main Image -->
                        <div 
                            :class="getResponsiveClasses({
                                mobile: 'mb-3 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center cursor-pointer hover:opacity-90 transition-opacity',
                                tablet: 'mb-4 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center cursor-pointer hover:opacity-90 transition-opacity',
                                desktop: 'mb-4 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center cursor-pointer hover:opacity-90 transition-opacity'
                            })"
                            :style="getResponsiveClasses({
                                mobile: 'height: 250px;',
                                tablet: 'height: 350px;',
                                desktop: 'height: 400px;'
                            })"
                            @click="currentImage && openMediaViewer(currentImage)"
                        >
                            <img
                                v-if="currentImage"
                                :src="currentImage"
                                :alt="product.product_name"
                                class="max-h-full max-w-full object-contain"
                            />
                            <i v-else :class="getResponsiveClasses({
                                mobile: 'pi pi-image text-4xl text-gray-400',
                                tablet: 'pi pi-image text-5xl text-gray-400',
                                desktop: 'pi pi-image text-6xl text-gray-400'
                            })"></i>
                        </div>
                        
                        <!-- Thumbnail Navigation -->
                        <div :class="getResponsiveClasses({
                            mobile: 'flex items-center gap-1',
                            tablet: 'flex items-center gap-2',
                            desktop: 'flex items-center gap-2'
                        })">
                            <button
                                @click="previousImage"
                                :disabled="carouselImages.length <= 1"
                                :class="getResponsiveClasses({
                                    mobile: 'p-1.5 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
                                    tablet: 'p-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
                                    desktop: 'p-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer'
                                })"
                            >
                                <i class="pi pi-chevron-left"></i>
                            </button>
                            
                            <div :class="getResponsiveClasses({
                                mobile: 'flex gap-1 flex-1 overflow-x-auto',
                                tablet: 'flex gap-2 flex-1 overflow-x-auto',
                                desktop: 'flex gap-2 flex-1 overflow-x-auto'
                            })">
                                <button
                                    v-for="(image, index) in carouselImages"
                                    :key="index"
                                    @click="currentImageIndex = index; resetCarouselInterval();"
                                    :class="[
                                        getResponsiveClasses({
                                            mobile: 'flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all cursor-pointer hover:opacity-80',
                                            tablet: 'flex-shrink-0 w-18 h-18 rounded-lg overflow-hidden border-2 transition-all cursor-pointer hover:opacity-80',
                                            desktop: 'flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all cursor-pointer hover:opacity-80'
                                        }),
                                        currentImageIndex === index ? 'border-blue-600' : 'border-gray-300 hover:border-gray-400'
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
                                :class="getResponsiveClasses({
                                    mobile: 'p-1.5 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
                                    tablet: 'p-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
                                    desktop: 'p-2 rounded-lg bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer'
                                })"
                            >
                                <i class="pi pi-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Right: Product Info -->
                    <div class="flex flex-col">
                        <!-- Category -->
                        <p :class="getResponsiveTextSize('sm') + ' text-gray-500 mb-3'">Category: {{ product.category?.category_name || 'Uncategorized' }}</p>
                        
                        <!-- Product Name -->
                        <h1 :class="getResponsiveClasses({
                            mobile: 'text-2xl font-bold text-gray-900 mb-2',
                            tablet: 'text-3xl font-bold text-gray-900 mb-2',
                            desktop: 'text-3xl font-bold text-gray-900 mb-2'
                        })">{{ product.product_name }}</h1>
                        
                        <!-- SKU -->
                        <p :class="getResponsiveTextSize('sm') + ' text-gray-500 mb-6'">SKU: {{ product.sku }}</p>
                        
                        <!-- Price -->
                        <p :class="getResponsiveClasses({
                            mobile: 'text-3xl font-bold text-gray-900 mb-4',
                            tablet: 'text-4xl font-bold text-gray-900 mb-4',
                            desktop: 'text-4xl font-bold text-gray-900 mb-4'
                        })">
                            {{ CurrencyUtil.formatCurrency(product.product_price) }}
                        </p>
                        
                        <!-- Rating -->
                        <div :class="getResponsiveClasses({
                            mobile: 'flex items-center gap-1 mb-4',
                            tablet: 'flex items-center gap-2 mb-4',
                            desktop: 'flex items-center gap-2 mb-4'
                        })">
                            <div :class="getResponsiveClasses({
                                mobile: 'flex gap-0.5 text-yellow-400 text-lg',
                                tablet: 'flex gap-0.5 text-yellow-400 text-xl',
                                desktop: 'flex gap-0.5 text-yellow-400 text-xl'
                            })">
                                <i v-for="n in 5" :key="n" :class="product.rates_avg_rate && n <= Math.round(product.rates_avg_rate) ? 'pi pi-star-fill' : 'pi pi-star'"></i>
                            </div>
                            <span :class="getResponsiveTextSize('sm') + ' text-gray-600'">
                                {{ product.rates_avg_rate ? Number(product.rates_avg_rate).toFixed(1) : '0' }}/5
                            </span>
                            <span :class="getResponsiveTextSize('sm') + ' text-gray-500'">|</span>
                            <span :class="getResponsiveTextSize('sm') + ' text-gray-600'">{{ ratings.length }} Ratings</span>
                        </div>
                        
                        <!-- Total Sold -->
                        <div :class="getResponsiveClasses({
                            mobile: 'mb-3 text-sm',
                            tablet: 'mb-4 text-sm',
                            desktop: 'mb-4 text-sm'
                        })">
                            <span class="text-gray-700">
                                <span class="font-semibold text-gray-900">{{ product.total_sold || 0 }}</span> items sold
                            </span>
                        </div>
                        
                        <!-- Out of Stock Notice -->
                        <div v-if="product.product_quantity === 0" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-exclamation-triangle text-red-600"></i>
                                <p class="text-sm font-semibold text-red-800">Out of Stock</p>
                            </div>
                            <p class="text-xs text-red-600 mt-1">This product is currently unavailable for purchase.</p>
                        </div>
                        
                        <!-- Quantity -->
                        <div v-if="product.product_quantity > 0" class="mb-6">
                            <p :class="getResponsiveTextSize('sm') + ' text-gray-700 mb-4'">Quantity: <span class="text-gray-500">{{ product.product_quantity }} pieces</span></p>
                            <div :class="getResponsiveClasses({
                                mobile: 'flex items-center justify-center gap-2',
                                tablet: 'flex items-center gap-2',
                                desktop: 'flex items-center gap-2'
                            })">
                                <button
                                    @click="decrementQuantity"
                                    :disabled="form.quantity <= 1"
                                    :class="getResponsiveClasses({
                                        mobile: 'w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
                                        tablet: 'w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
                                        desktop: 'w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer'
                                    })"
                                >
                                    <i class="pi pi-minus text-xs"></i>
                                </button>
                                <input
                                    type="number"
                                    v-model.number="form.quantity"
                                    @input="validateQuantityInput"
                                    min="1"
                                    :max="product.product_quantity"
                                    :class="getResponsiveClasses({
                                        mobile: 'w-14 text-base font-semibold text-center border border-gray-300 rounded-lg py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500',
                                        tablet: 'w-14 text-lg font-semibold text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-blue-500',
                                        desktop: 'w-14 text-lg font-semibold text-center border border-gray-300 rounded-lg py-1 focus:outline-none focus:ring-2 focus:ring-blue-500'
                                    })"
                                />
                                <button
                                    @click="incrementQuantity"
                                    :disabled="form.quantity >= product.product_quantity"
                                    :class="getResponsiveClasses({
                                        mobile: 'w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
                                        tablet: 'w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
                                        desktop: 'w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer'
                                    })"
                                >
                                    <i class="pi pi-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div v-if="product.product_quantity > 0" :class="getResponsiveClasses({
                            mobile: 'flex flex-col gap-3 mb-6',
                            tablet: 'flex gap-3 mb-6',
                            desktop: 'flex gap-3 mb-6'
                        })">
                            <button
                                @click="buyNow"
                                :disabled="submitService.request.loading"
                                :class="getResponsiveClasses({
                                    mobile: 'w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2',
                                    tablet: 'bg-blue-600 text-white py-2.5 px-6 rounded-lg font-medium hover:bg-blue-700 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2',
                                    desktop: 'bg-blue-600 text-white py-3 px-8 rounded-lg font-semibold hover:bg-blue-700 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2'
                                })"
                            >
                                <i v-if="submitService.request.loading" class="pi pi-spin pi-spinner"></i>
                                <i v-else class="pi pi-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                        
                        <!-- Product Info Buttons -->
                        <div :class="getResponsiveClasses({
                            mobile: 'grid grid-cols-1 gap-2',
                            tablet: 'flex flex-col gap-2',
                            desktop: 'flex flex-col gap-2'
                        })">
                            <button
                                @click="showInfoModal = true"
                                :class="getResponsiveClasses({
                                    mobile: 'w-full py-2.5 px-3 text-black font-medium hover:text-blue-600 transition-colors cursor-pointer flex items-center justify-center gap-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-sm',
                                    tablet: 'w-full py-2 px-4 text-black font-medium hover:text-blue-600 transition-colors cursor-pointer flex items-center gap-2',
                                    desktop: 'w-full py-2 px-4 text-black font-medium hover:text-blue-600 transition-colors cursor-pointer flex items-center gap-2'
                                })"
                            >
                                <i class="pi pi-info-circle text-blue-600"></i>
                                Product Information
                            </button>
                            <button
                                @click="showSpecModal = true"
                                :class="getResponsiveClasses({
                                    mobile: 'w-full py-2.5 px-3 text-black font-medium hover:text-blue-600 transition-colors cursor-pointer flex items-center justify-center gap-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-sm',
                                    tablet: 'w-full py-2 px-4 text-black font-medium hover:text-blue-600 transition-colors cursor-pointer flex items-center gap-2',
                                    desktop: 'w-full py-2 px-4 text-black font-medium hover:text-blue-600 transition-colors cursor-pointer flex items-center gap-2'
                                })"
                            >
                                <i class="pi pi-list text-blue-600"></i>
                                Specifications
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Reviews Section -->
            <div :class="getResponsiveClasses({
                mobile: 'bg-white rounded-lg shadow-sm p-4',
                tablet: 'bg-white rounded-lg shadow-sm p-5',
                desktop: 'bg-white rounded-lg shadow-sm p-6'
            })">
                <h2 :class="getResponsiveClasses({
                    mobile: 'text-xl font-bold text-gray-900 mb-4',
                    tablet: 'text-2xl font-bold text-gray-900 mb-6',
                    desktop: 'text-2xl font-bold text-gray-900 mb-6'
                })">Customer Reviews</h2>
                
                <div :class="getResponsiveClasses({
                    mobile: 'flex flex-col gap-4 mb-6',
                    tablet: 'flex flex-col lg:flex-row gap-6 mb-8',
                    desktop: 'flex flex-col lg:flex-row gap-6 mb-8'
                })">
                    <!-- Rating Summary -->
                    <div :class="getResponsiveClasses({
                        mobile: 'flex flex-row items-center justify-center bg-gray-50 rounded-lg p-4 gap-4',
                        tablet: 'flex flex-col items-center justify-center bg-gray-50 rounded-lg p-6 lg:w-64',
                        desktop: 'flex flex-col items-center justify-center bg-gray-50 rounded-lg p-6 lg:w-64'
                    })">
                        <div class="text-center">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-4xl font-bold text-gray-900 mb-1',
                                tablet: 'text-6xl font-bold text-gray-900 mb-2',
                                desktop: 'text-6xl font-bold text-gray-900 mb-2'
                            })">
                                {{ product.rates_avg_rate ? Number(product.rates_avg_rate).toFixed(1) : '0' }}
                            </p>
                            <p :class="getResponsiveClasses({
                                mobile: 'text-sm text-gray-500 mb-2',
                                tablet: 'text-gray-500 mb-3',
                                desktop: 'text-gray-500 mb-3'
                            })">out of 5</p>
                        </div>
                        <div :class="getResponsiveClasses({
                            mobile: 'flex flex-col items-center gap-2',
                            tablet: 'flex flex-col items-center gap-3',
                            desktop: 'flex flex-col items-center gap-3'
                        })">
                            <div :class="getResponsiveClasses({
                                mobile: 'flex gap-1 text-lg text-yellow-400',
                                tablet: 'flex gap-1 text-2xl text-yellow-400',
                                desktop: 'flex gap-1 text-2xl text-yellow-400'
                            })">
                                <i v-for="n in 5" :key="n" :class="product.rates_avg_rate && n <= Math.round(product.rates_avg_rate) ? 'pi pi-star-fill' : 'pi pi-star'"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filter Buttons -->
                    <div class="flex-1">
                        <div :class="getResponsiveClasses({
                            mobile: 'grid grid-cols-2 gap-2 mb-3',
                            tablet: 'flex flex-wrap gap-2 mb-4',
                            desktop: 'flex flex-wrap gap-2 mb-4'
                        })">
                            <button
                                @click="filterRating = null; filterWithComments = false; filterWithMedia = false;"
                                :class="[
                                    getResponsiveClasses({
                                        mobile: 'px-3 py-2 rounded-lg font-medium transition-colors cursor-pointer text-sm',
                                        tablet: 'px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer',
                                        desktop: 'px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer'
                                    }),
                                    filterRating === null && !filterWithComments && !filterWithMedia ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                            >
                                <span :class="getResponsiveClasses({
                                    mobile: 'hidden',
                                    tablet: 'inline',
                                    desktop: 'inline'
                                })">All ({{ ratings.length }})</span>
                                <span :class="getResponsiveClasses({
                                    mobile: 'inline',
                                    tablet: 'hidden',
                                    desktop: 'hidden'
                                })">All ({{ ratings.length }})</span>
                            </button>
                            <button
                                v-for="star in [5, 4, 3, 2, 1]"
                                :key="star"
                                @click="filterRating = star; filterWithComments = false; filterWithMedia = false;"
                                :class="[
                                    getResponsiveClasses({
                                        mobile: 'px-3 py-2 rounded-lg font-medium transition-colors cursor-pointer text-sm',
                                        tablet: 'px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer',
                                        desktop: 'px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer'
                                    }),
                                    filterRating === star ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                            >
                                <span :class="getResponsiveClasses({
                                    mobile: 'hidden',
                                    tablet: 'inline',
                                    desktop: 'inline'
                                })">{{ star }} Star{{ star !== 1 ? 's' : '' }} ({{ getStarCount(star) }})</span>
                                <span :class="getResponsiveClasses({
                                    mobile: 'inline',
                                    tablet: 'hidden',
                                    desktop: 'hidden'
                                })">{{ star }} Stars ({{ getStarCount(star) }})</span>
                            </button>
                        </div>
                        <div :class="getResponsiveClasses({
                            mobile: 'flex flex-col gap-2',
                            tablet: 'flex gap-2',
                            desktop: 'flex gap-2'
                        })">
                            <button
                                @click="filterWithComments = !filterWithComments; filterRating = null; filterWithMedia = false;"
                                :class="[
                                    getResponsiveClasses({
                                        mobile: 'px-3 py-2 rounded-lg font-medium transition-colors cursor-pointer text-sm',
                                        tablet: 'px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer',
                                        desktop: 'px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer'
                                    }),
                                    filterWithComments ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                            >
                                <span :class="getResponsiveClasses({
                                    mobile: 'hidden',
                                    tablet: 'inline',
                                    desktop: 'inline'
                                })">With Comments ({{ getCommentsCount() }})</span>
                                <span :class="getResponsiveClasses({
                                    mobile: 'inline',
                                    tablet: 'hidden',
                                    desktop: 'hidden'
                                })">Comments ({{ getCommentsCount() }})</span>
                            </button>
                            <button
                                @click="filterWithMedia = !filterWithMedia; filterRating = null; filterWithComments = false;"
                                :class="[
                                    getResponsiveClasses({
                                        mobile: 'px-3 py-2 rounded-lg font-medium transition-colors cursor-pointer text-sm',
                                        tablet: 'px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer',
                                        desktop: 'px-4 py-2 rounded-lg font-medium transition-colors cursor-pointer'
                                    }),
                                    filterWithMedia ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                ]"
                            >
                                <span :class="getResponsiveClasses({
                                    mobile: 'hidden',
                                    tablet: 'inline',
                                    desktop: 'inline'
                                })">With Media ({{ getMediaCount() }})</span>
                                <span :class="getResponsiveClasses({
                                    mobile: 'inline',
                                    tablet: 'hidden',
                                    desktop: 'hidden'
                                })">Media ({{ getMediaCount() }})</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Review Form (Moved before reviews) -->
                <div v-if="Page.user && product.user_has_purchased" :class="getResponsiveClasses({
                    mobile: 'mt-6 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200',
                    tablet: 'mt-8 mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200',
                    desktop: 'mt-8 mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200'
                })">
                    <h3 :class="getResponsiveClasses({
                        mobile: 'text-base font-semibold text-gray-900 mb-3',
                        tablet: 'text-lg font-semibold text-gray-900 mb-4',
                        desktop: 'text-lg font-semibold text-gray-900 mb-4'
                    })">Write a Review</h3>
                    
                    <!-- Info message for users who already reviewed -->
                    <div v-if="!product.product_order_id" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-start gap-2">
                        <i class="pi pi-check-circle text-green-600 text-sm mt-0.5"></i>
                        <p class="text-sm text-green-800">Thank you! You have already submitted a review for this product. You can view your review below.</p>
                    </div>
                    
                    <form @submit.prevent="submitReview">
                        <!-- Star Rating -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Rating</label>
                            <div :class="getResponsiveClasses({
                                mobile: 'flex gap-2 justify-center',
                                tablet: 'flex gap-2',
                                desktop: 'flex gap-2'
                            })">
                                <button
                                    v-for="star in 5"
                                    :key="star"
                                    type="button"
                                    @click="product.product_order_id && (reviewForm.rating = star)"
                                    :disabled="!product.product_order_id"
                                    :class="[
                                        getResponsiveClasses({
                                            mobile: 'text-3xl transition-colors p-1',
                                            tablet: 'text-4xl transition-colors p-1',
                                            desktop: 'text-3xl transition-colors'
                                        }),
                                        star <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-300',
                                        product.product_order_id ? 'cursor-pointer hover:text-yellow-200' : 'cursor-not-allowed opacity-50'
                                    ]"
                                >
                                    <i :class="star <= reviewForm.rating ? 'pi pi-star-fill' : 'pi pi-star'"></i>
                                </button>
                            </div>
                            <p v-if="reviewErrors.rating" class="text-sm text-red-600 mt-1">{{ reviewErrors.rating }}</p>
                        </div>
                        
                        <!-- Comment -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                            <textarea
                                v-model="reviewForm.comment"
                                :disabled="!product.product_order_id"
                                rows="4"
                                :class="getResponsiveClasses({
                                    mobile: 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed text-sm',
                                    tablet: 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed text-sm',
                                    desktop: 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed'
                                })"
                                placeholder="Share your experience with this product..."
                            ></textarea>
                            <p v-if="reviewErrors.comment" class="text-sm text-red-600 mt-1">{{ reviewErrors.comment }}</p>
                        </div>
                        
                        <!-- Media Upload -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Add Photos or Videos (Optional)
                                <span class="text-xs text-gray-500 font-normal ml-2">Max: 4 files (50MB each, videos max 1:30 min)</span>
                            </label>
                            <div :class="getResponsiveClasses({
                                mobile: 'flex items-center gap-3',
                                tablet: 'flex items-center gap-4',
                                desktop: 'flex items-center gap-4'
                            })">
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
                                        getResponsiveClasses({
                                            mobile: 'flex items-center gap-2 px-3 py-1.5 bg-white border-2 border-gray-300 rounded-lg transition-colors',
                                            tablet: 'flex items-center gap-2 px-3 py-2 bg-white border-2 border-gray-300 rounded-lg transition-colors',
                                            desktop: 'flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-300 rounded-lg transition-colors'
                                        }),
                                        product.product_order_id ? 'hover:bg-gray-50' : 'bg-gray-100'
                                    ]">
                                        <i :class="getResponsiveClasses({
                                            mobile: 'pi pi-camera text-gray-600 text-sm',
                                            tablet: 'pi pi-camera text-gray-600',
                                            desktop: 'pi pi-camera text-gray-600'
                                        })"></i>
                                        <span :class="getResponsiveClasses({
                                            mobile: 'text-xs text-gray-700',
                                            tablet: 'text-sm text-gray-700',
                                            desktop: 'text-sm text-gray-700'
                                        })">Upload Media</span>
                                    </div>
                                </label>
                                <span :class="getResponsiveClasses({
                                    mobile: 'text-xs text-gray-500',
                                    tablet: 'text-sm text-gray-500',
                                    desktop: 'text-sm text-gray-500'
                                })">{{ reviewForm.media.length }} file(s) selected</span>
                            </div>
                            
                            <!-- Preview uploaded media -->
                            <div v-if="reviewForm.media.length > 0" class="mt-3 flex gap-2 flex-wrap">
                                <div
                                    v-for="(file, index) in reviewForm.media"
                                    :key="index"
                                    class="relative w-20 h-20 rounded-lg overflow-hidden border border-gray-300"
                                >
                                    <img
                                        v-if="file.type.startsWith('image/')"
                                        :src="getFilePreview(file)"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else-if="file.type.startsWith('video/')" class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <i class="pi pi-video text-2xl text-gray-500"></i>
                                    </div>
                                    <div v-else class="w-full h-full bg-gray-200 flex flex-col items-center justify-center p-1">
                                        <i class="pi pi-file text-xl text-gray-500"></i>
                                        <span class="text-xs text-gray-600 truncate w-full text-center">{{ file.name.split('.').pop() }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeMedia(index)"
                                        class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 cursor-pointer"
                                    >
                                        <i class="pi pi-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="!product.product_order_id || reviewSubmitService.request.loading"
                                :class="getResponsiveClasses({
                                    mobile: 'w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
                                    tablet: 'px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer',
                                    desktop: 'px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer'
                                })"
                            >
                                <i v-if="reviewSubmitService.request.loading" class="pi pi-spin pi-spinner mr-2"></i>
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Message for non-logged-in users -->
                <div v-else-if="!Page.user" :class="getResponsiveClasses({
                    mobile: 'mt-6 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200',
                    tablet: 'mt-8 mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200',
                    desktop: 'mt-8 mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200'
                })">
                    <div :class="getResponsiveClasses({
                        mobile: 'flex flex-col items-center text-center gap-3',
                        tablet: 'flex items-start gap-3',
                        desktop: 'flex items-start gap-3'
                    })">
                        <i :class="getResponsiveClasses({
                            mobile: 'pi pi-sign-in text-gray-600 text-2xl',
                            tablet: 'pi pi-sign-in text-gray-600 text-xl mt-0.5',
                            desktop: 'pi pi-sign-in text-gray-600 text-xl mt-0.5'
                        })"></i>
                        <div>
                            <h3 :class="getResponsiveClasses({
                                mobile: 'text-base font-semibold text-gray-900 mb-2',
                                tablet: 'text-lg font-semibold text-gray-900 mb-2',
                                desktop: 'text-lg font-semibold text-gray-900 mb-2'
                            })">Sign In to Review</h3>
                            <p :class="getResponsiveTextSize('sm') + ' text-gray-700 mb-3'">Please sign in to your account to write a review for this product.</p>
                            <button
                                @click="openLoginForm ? openLoginForm() : router.push({ name: 'admin.login' })"
                                :class="getResponsiveClasses({
                                    mobile: 'w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors cursor-pointer',
                                    tablet: 'px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors cursor-pointer',
                                    desktop: 'px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors cursor-pointer'
                                })"
                            >
                                Sign In
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reviews List -->
                <div v-if="filteredRatings.length > 0" :class="getResponsiveClasses({
                    mobile: 'space-y-4',
                    tablet: 'space-y-6',
                    desktop: 'space-y-6'
                })">
                    <div
                        v-for="(review, index) in filteredRatings"
                        :key="index"
                        :class="getResponsiveClasses({
                            mobile: 'border-b border-gray-200 pb-4 last:border-0',
                            tablet: 'border-b border-gray-200 pb-6 last:border-0',
                            desktop: 'border-b border-gray-200 pb-6 last:border-0'
                        })"
                    >
                        <div :class="getResponsiveClasses({
                            mobile: 'flex flex-col gap-3',
                            tablet: 'flex items-start gap-4',
                            desktop: 'flex items-start gap-4'
                        })">
                            <div :class="getResponsiveClasses({
                                mobile: 'flex items-center gap-3',
                                tablet: 'flex-shrink-0',
                                desktop: 'flex-shrink-0'
                            })">
                                <Avatar
                                    :label="review.user.full_name.charAt(0).toUpperCase()"
                                    shape="circle"
                                    :size="getResponsiveClasses({
                                        mobile: 'normal',
                                        tablet: 'large',
                                        desktop: 'large'
                                    })"
                                    class="bg-blue-600 text-white"
                                />
                                <div :class="getResponsiveClasses({
                                    mobile: 'block',
                                    tablet: 'hidden',
                                    desktop: 'hidden'
                                })">
                                    <h4 class="font-semibold text-gray-900 text-sm">{{ review.user.full_name }}</h4>
                                    <p class="text-xs text-gray-500">{{ formatDate(review.created_at) }}</p>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div :class="getResponsiveClasses({
                                    mobile: 'hidden',
                                    tablet: 'block mb-2',
                                    desktop: 'block mb-2'
                                })">
                                    <h4 class="font-semibold text-gray-900">{{ review.user.full_name }}</h4>
                                </div>
                                <div :class="getResponsiveClasses({
                                    mobile: 'flex gap-0.5 text-yellow-400 mb-2 text-base',
                                    tablet: 'flex gap-1 text-yellow-400 mb-2',
                                    desktop: 'flex gap-1 text-yellow-400 mb-2'
                                })">
                                    <i v-for="n in 5" :key="n" :class="n <= review.rate ? 'pi pi-star-fill' : 'pi pi-star'"></i>
                                </div>
                                <p :class="getResponsiveClasses({
                                    mobile: 'hidden',
                                    tablet: 'text-sm text-gray-500 mb-3',
                                    desktop: 'text-sm text-gray-500 mb-3'
                                })">{{ formatDate(review.created_at) }}</p>
                                
                                <!-- Review Content -->
                                <div v-if="review.comment" class="mb-3">
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ review.comment }}</p>
                                </div>
                                
                                <!-- Review Images/Videos (if any) -->
                                <div v-if="review.images && review.images.length > 0" :class="getResponsiveClasses({
                                    mobile: 'flex gap-1 mb-3 overflow-x-auto',
                                    tablet: 'flex gap-2 mb-3',
                                    desktop: 'flex gap-2 mb-3'
                                })">
                                    <div
                                        v-for="(media, imgIndex) in review.images.slice(0, 4)"
                                        :key="imgIndex"
                                        @click="openMediaViewer(media)"
                                        :class="getResponsiveClasses({
                                            mobile: 'flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden cursor-pointer relative group bg-gray-100',
                                            tablet: 'w-20 h-20 rounded-lg overflow-hidden cursor-pointer relative group bg-gray-100',
                                            desktop: 'w-20 h-20 rounded-lg overflow-hidden cursor-pointer relative group bg-gray-100'
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
                                        <!-- Play icon overlay for videos -->
                                        <div v-if="isVideoUrl(media)" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 group-hover:bg-opacity-50 transition-all pointer-events-none">
                                            <i class="pi pi-play text-white text-2xl"></i>
                                        </div>
                                        <!-- Expand icon overlay for images -->
                                        <div v-else class="absolute inset-0 flex items-center justify-center group-hover:bg-black group-hover:bg-opacity-30 transition-all pointer-events-none">
                                            <i class="pi pi-search-plus text-white text-xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Like Button -->
                                <div class="flex items-center gap-4">
                                    <button 
                                        @click="toggleLike(review)"
                                        :disabled="!Page.user || likeService.request.loading"
                                        :class="[
                                            'flex items-center gap-2 text-sm transition-colors',
                                            isLikedByCurrentUser(review) ? 'text-red-500' : 'text-gray-600 hover:text-red-500',
                                            !Page.user ? 'cursor-not-allowed opacity-50' : ''
                                        ]"
                                    >
                                        <i :class="isLikedByCurrentUser(review) ? 'pi pi-heart-fill' : 'pi pi-heart'"></i>
                                        <span>{{ isLikedByCurrentUser(review) ? 'Liked' : 'Like' }}</span>
                                        <span v-if="review.likes_count && review.likes_count > 0" class="font-semibold">({{ review.likes_count }})</span>
                                    </button>
                                    <span class="text-sm text-gray-400">Was this helpful?</span>
                                </div>
                                
                                <!-- Seller Reply -->
                                <div v-if="review.reply" class="mt-4 ml-4 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-600">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="pi pi-reply text-blue-600"></i>
                                        <span class="text-sm font-semibold text-blue-900">Seller Response</span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ review.reply }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- No Reviews -->
                <div v-else class="text-center py-12">
                    <i class="pi pi-star text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No reviews yet. Be the first to review this product!</p>
                </div>
                

            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loadService.request.loading" class="flex justify-center items-center py-20">
            <i class="pi pi-spin pi-spinner text-4xl text-blue-600"></i>
        </div>

        <!-- Media Viewer Modal -->
        <div
            v-if="mediaViewer.show"
            @click="closeMediaViewer"
            class="fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center p-4"
        >
            <button
                @click="closeMediaViewer"
                class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300 z-10 cursor-pointer"
            >
                <i class="pi pi-times"></i>
            </button>

            <div @click.stop class="relative max-w-5xl max-h-[90vh] w-full">
                <!-- Image Viewer -->
                <img
                    v-if="!isVideoUrl(mediaViewer.url)"
                    :src="mediaViewer.url"
                    class="max-w-full max-h-[90vh] mx-auto object-contain"
                    alt="Full size media"
                />

                <!-- Video Viewer -->
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
                mask: { 
                    class: 'bg-black bg-opacity-50 backdrop-blur-sm' 
                },
                root: { 
                    class: 'z-50' 
                }
            }"
        >
            <div class="space-y-4">
                <h3 class="font-semibold text-gray-900 mb-3 text-base">Description</h3>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ product?.description || 'No description available.' }}</p>
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
                mask: { 
                    class: 'bg-black bg-opacity-50 backdrop-blur-sm' 
                },
                root: { 
                    class: 'z-50' 
                }
            }"
        >
            <div class="space-y-4">
                <div v-if="product?.specifications && product.specifications.length > 0" class="space-y-3">
                    <div
                        v-for="(spec, index) in product.specifications"
                        :key="index"
                        class="border-b border-gray-200 pb-3 last:border-0"
                    >
                        <p class="font-semibold text-gray-900 mb-1">{{ spec.specification_name }}</p>
                        <p class="text-gray-700">{{ spec.specification_value }}</p>
                    </div>
                </div>
                <p v-else class="text-gray-500 text-center py-8">No specifications available.</p>
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

// Inject openLoginForm from parent (CustomerIndex)
const openLoginForm = inject<(() => void) | null>('openLoginForm', null);

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
    // Ensure index is within bounds
    const safeIndex = currentImageIndex.value % carouselImages.value.length;
    return carouselImages.value[safeIndex]?.url || null;
});

const filteredRatings = computed(() => {
    let filtered = ratings.value;
    
    // Filter by star rating
    if (filterRating.value !== null) {
        filtered = filtered.filter(rating => rating.rate === filterRating.value);
    }
    
    // Filter by comments
    if (filterWithComments.value) {
        filtered = filtered.filter(rating => rating.comment && rating.comment.trim() !== '');
    }
    
    // Filter by media (images)
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
    
    // Ensure quantity is a number
    if (isNaN(form.quantity) || form.quantity === null) {
        form.quantity = 1;
        return;
    }
    
    // Ensure quantity is at least 1
    if (form.quantity < 1) {
        form.quantity = 1;
    }
    
    // Ensure quantity doesn't exceed available stock
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
        }, 4000); // Auto-advance every 4 seconds
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
            // Sort reviews by date (newest first)
            ratings.value.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
            
            // Start carousel after product data is loaded
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
        // Open login modal if available, otherwise show toast
        if (openLoginForm) {
            openLoginForm();
        } else {
            toast.error('Please login to add items to cart');
        }
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
        // Open login modal if available, otherwise show toast
        if (openLoginForm) {
            openLoginForm();
        } else {
            toast.error('Please login to add items to cart');
        }
        return;
    }
    
    // Add to cart with the selected quantity
    await submitForm();
};

const handleMediaUpload = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        const files = Array.from(target.files);
        const validFiles: File[] = [];
        
        // Check max file count
        if (files.length > 4) {
            toast.error('You can only upload up to 4 files per review.');
            if (target) {
                target.value = '';
            }
            return;
        }
        
        // Validate each file
        for (const file of files) {
            // Check if file is image or video
            if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
                toast.error(`File "${file.name}" is not supported. Only images and videos are allowed.`);
                continue;
            }
            
            // Check file size
            const maxSize = 50 * 1024 * 1024; // 50MB in bytes
            if (file.size > maxSize) {
                toast.error(`File "${file.name}" is too large. Maximum size is 50MB.`);
                continue;
            }
            
            // Check video duration (max 1 minute 30 seconds)
            if (file.type.startsWith('video/')) {
                try {
                    const duration = await getVideoDuration(file);
                    const maxDuration = 90; // 90 seconds = 1 minute 30 seconds
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
        
        // Show summary
        if (validFiles.length > 0) {
            const imageCount = validFiles.filter(f => f.type.startsWith('image/')).length;
            const videoCount = validFiles.filter(f => f.type.startsWith('video/')).length;
            const summary = [];
            if (imageCount > 0) summary.push(`${imageCount} image${imageCount > 1 ? 's' : ''}`);
            if (videoCount > 0) summary.push(`${videoCount} video${videoCount > 1 ? 's' : ''}`);
            toast.success(`Selected: ${summary.join(' and ')}`);
        }
        
        // Reset input if no valid files
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
    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden';
    // Add ESC key listener
    document.addEventListener('keydown', handleEscKey);
};

const closeMediaViewer = () => {
    mediaViewer.show = false;
    mediaViewer.url = '';
    // Restore body scroll
    document.body.style.overflow = '';
    // Remove ESC key listener
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
    
    // Prevent double submission
    if (reviewSubmitService.request.loading) {
        return;
    }
    
    const formData = new FormData();
    formData.append('rate', reviewForm.rating.toString());
    formData.append('comment', reviewForm.comment);
    
    // Add media files
    reviewForm.media.forEach((file) => {
        formData.append('media[]', file);
    });
    
    await reviewSubmitService.postFormData(`customer/rates/${product.value.product_order_id}`, formData).then(() => {
        if (reviewSubmitService.request.status === 200) {
            toast.success('Review submitted successfully!');
            // Reset form
            reviewForm.rating = 0;
            reviewForm.comment = '';
            reviewForm.media = [];
            if (mediaInput.value) {
                mediaInput.value.value = '';
            }
            // Clear the product_order_id to hide the form
            if (product.value) {
                product.value.product_order_id = null;
                product.value.user_has_reviewed = true;
            }
            // Reload product to get updated reviews
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
            
            // Update the review in the ratings array
            const reviewIndex = ratings.value.findIndex(r => r.rate_id === review.rate_id);
            if (reviewIndex !== -1) {
                ratings.value[reviewIndex].likes_count = likes_count;
                
                // Update likes array
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
    
    // Check if user came from Rate button (scroll to review section)
    if (route.query.scrollToReview === 'true') {
        // Wait for page to load, then scroll to review section
        setTimeout(() => {
            const reviewSection = document.querySelector('h2');
            if (reviewSection && reviewSection.textContent?.includes('Customer Reviews')) {
                reviewSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Highlight the review form briefly
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
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
    appearance: textfield;
}
</style>
