<template>
    <div :class="responsive.getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-3 px-3',
        tablet: 'min-h-screen bg-gray-50 py-4 px-4',
        desktop: 'min-h-screen bg-gray-50 py-4 sm:py-6 px-3 sm:px-6'
    })">
        <!-- Date and Time Display -->
        <DateTimeDisplay />

        <!-- Filters Card -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'mb-3 rounded-lg bg-white border border-gray-200 shadow-sm',
            tablet: 'mb-4 rounded-lg bg-white border border-gray-200 shadow-sm',
            desktop: 'mb-4 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm'
        })">
            <div :class="responsive.getResponsiveClasses({
                mobile: 'border-b border-gray-200 px-3 py-3',
                tablet: 'border-b border-gray-200 px-4 py-3',
                desktop: 'border-b border-gray-200 px-4 py-3'
            })">
                <div class="flex items-center gap-2">
                    <i :class="responsive.getResponsiveClasses({
                        mobile: 'pi pi-filter text-blue-600 text-sm',
                        tablet: 'pi pi-filter text-blue-600',
                        desktop: 'pi pi-filter text-blue-600'
                    })"></i>
                    <h2 :class="responsive.getResponsiveClasses({
                        mobile: 'text-sm font-semibold text-gray-800',
                        tablet: 'text-base font-semibold text-gray-800',
                        desktop: 'font-semibold text-gray-800'
                    })">Filter Ratings</h2>
                </div>
            </div>
            <div :class="responsive.getResponsiveClasses({
                mobile: 'p-3',
                tablet: 'p-4',
                desktop: 'p-4'
            })">
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'space-y-4',
                    tablet: 'space-y-4 md:space-y-0 md:grid md:grid-cols-12 md:gap-4 md:items-end',
                    desktop: 'grid grid-cols-1 md:grid-cols-12 gap-4 items-end'
                })">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full md:col-span-5',
                        desktop: 'md:col-span-5'
                    })">
                        <label :class="responsive.getResponsiveClasses({
                            mobile: 'block text-xs font-semibold text-gray-700 mb-2',
                            tablet: 'block text-sm font-semibold text-gray-700 mb-2',
                            desktop: 'block text-sm font-semibold text-gray-700 mb-2'
                        })">Star Rating</label>
                        <Select
                            v-model="filterRating"
                            :options="ratingOptions"
                            placeholder="All Ratings"
                            optionLabel="label"
                            optionValue="value"
                            showClear
                            :class="[
                                'w-full',
                                responsive.getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                        />
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full md:col-span-5',
                        desktop: 'md:col-span-5'
                    })">
                        <label :class="responsive.getResponsiveClasses({
                            mobile: 'block text-xs font-semibold text-gray-700 mb-2',
                            tablet: 'block text-sm font-semibold text-gray-700 mb-2',
                            desktop: 'block text-sm font-semibold text-gray-700 mb-2'
                        })">Reply Status</label>
                        <Select
                            v-model="filterStatus"
                            :options="statusOptions"
                            placeholder="All Status"
                            optionLabel="label"
                            optionValue="value"
                            showClear
                            :class="[
                                'w-full',
                                responsive.getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                        />
                    </div>
                    <div v-if="filterRating !== null || filterStatus !== null" :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full md:col-span-2',
                        desktop: 'md:col-span-2'
                    })">
                        <Button
                            type="button"
                            icon="pi pi-filter-slash"
                            label="Clear"
                            @click="clearFilters"
                            :class="[
                                'w-full !bg-blue-600 hover:!bg-blue-700 !border-blue-600',
                                responsive.getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Ratings Container -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            tablet: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            desktop: 'rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden'
        })">
            <div
                v-if="errorRates"
                :class="responsive.getResponsiveClasses({
                    mobile: 'p-6 text-center',
                    tablet: 'p-8 text-center',
                    desktop: 'p-8 text-center'
                })"
            >
                <div class="mb-4">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center',
                        tablet: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center',
                        desktop: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-exclamation-triangle text-2xl text-red-600',
                            tablet: 'pi pi-exclamation-triangle text-3xl text-red-600',
                            desktop: 'pi pi-exclamation-triangle text-3xl text-red-600'
                        })"></i>
                    </div>
                </div>
                <h3 :class="responsive.getResponsiveClasses({
                    mobile: 'mb-2 text-base font-semibold text-red-800',
                    tablet: 'mb-2 text-lg font-semibold text-red-800',
                    desktop: 'mb-2 text-lg font-semibold text-red-800'
                })">Failed to Load Ratings</h3>
                <p :class="responsive.getResponsiveClasses({
                    mobile: 'mb-4 text-sm text-red-600',
                    tablet: 'mb-4 text-sm text-red-600',
                    desktop: 'mb-4 text-sm text-red-600'
                })">{{ errorRates }}</p>
                <Button
                    label="Retry"
                    icon="pi pi-refresh"
                    @click="retryRates"
                    class="!bg-red-600 hover:!bg-red-700"
                />
            </div>
            <div v-else class="w-full">
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'flex items-center justify-between px-3 py-3 border-b border-gray-200 bg-gray-50',
                    tablet: 'flex items-center justify-between px-4 py-4 border-b border-gray-200 bg-gray-50',
                    desktop: 'flex items-center justify-between px-5 py-4 border-b border-gray-200'
                })">
                    <div>
                        <h1 :class="responsive.getResponsiveClasses({
                            mobile: 'text-base font-bold text-gray-800',
                            tablet: 'text-lg font-bold text-gray-800',
                            desktop: 'text-lg font-semibold text-gray-800'
                        })">Customer Ratings & Reviews</h1>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">Manage customer feedback and product reviews</p>
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'flex flex-col items-end',
                        tablet: 'flex flex-col items-end',
                        desktop: 'flex items-center gap-2 text-sm text-gray-600'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'hidden',
                            tablet: 'hidden',
                            desktop: 'pi pi-star text-blue-600'
                        })"></i>
                        <span :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-600 font-medium',
                            tablet: 'text-xs text-gray-600 font-medium',
                            desktop: 'font-medium'
                        })">{{ filteredData.length }} Reviews</span>
                    </div>
                </div>
                
                <!-- Desktop DataTable -->
                <DataTable
                    v-if="responsive.isDesktop.value"
                    class="w-full"
                    :value="filteredData"
                    :paginator="true"
                    :rows="5"
                    columnResizeMode="expand"
                    stripedRows
                >
                    <Column field="product.product_name" header="Product" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3 py-1">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100">
                                    <i class="pi pi-box text-blue-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">
                                        {{ data.product.product_name }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="user.full_name" header="Customer" :sortable="true">
                        <template #body="{ data }">
                            <div class="font-medium text-gray-900">
                                {{ data.user.full_name }}
                            </div>
                        </template>
                    </Column>
                    <Column field="rate" header="Rating" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-1">
                                <i v-for="n in 5" :key="n" 
                                   class="pi pi-star-fill text-sm"
                                   :class="n <= data.rate ? 'text-yellow-400' : 'text-gray-300'">
                                </i>
                                <span class="ml-1 text-sm font-medium text-gray-600">({{ data.rate }})</span>
                            </div>
                        </template>
                    </Column>
                    <Column field="comment" header="Comment">
                        <template #body="{ data }">
                            <div class="text-sm text-gray-600 max-w-xs truncate">
                                {{ data.comment || 'No comment' }}
                            </div>
                        </template>
                    </Column>
                    <Column header="Media">
                        <template #body="{ data }">
                            <div v-if="data.images && data.images.length > 0" class="flex gap-1">
                                <div
                                    v-for="(image, index) in data.images.slice(0, 3)"
                                    :key="index"
                                    @click="openMediaPreview(image.image_path)"
                                    class="relative w-10 h-10 rounded overflow-hidden cursor-pointer hover:opacity-80 transition-opacity border border-gray-200"
                                >
                                    <img
                                        v-if="isImageFile(image.image_path)"
                                        :src="getRateImageUrl(image.image_path)"
                                        class="w-full h-full object-cover"
                                        alt="Review media"
                                    />
                                    <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <i class="pi pi-video text-gray-500 text-sm"></i>
                                    </div>
                                </div>
                                <div v-if="data.images.length > 3" class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600">
                                    +{{ data.images.length - 3 }}
                                </div>
                            </div>
                            <span v-else class="text-xs text-gray-400">No media</span>
                        </template>
                    </Column>
                    <Column field="created_at" header="Date" :sortable="true">
                        <template #body="{ data }">
                            <div class="text-sm text-gray-600">
                                {{ DateUtil.formatToMonthDayYear(data.created_at) }}
                            </div>
                        </template>
                    </Column>
                    <Column field="reply" header="Status" :sortable="true">
                        <template #body="{ data }">
                            <span v-if="data.reply" class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="pi pi-check-circle text-xs"></i>
                                Replied
                            </span>
                            <span v-else class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <i class="pi pi-clock text-xs"></i>
                                Pending
                            </span>
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="{ data }">
                            <Button
                                type="button"
                                :icon="data.reply ? 'pi pi-eye' : 'pi pi-reply'"
                                @click="openRate(data)"
                                class="!bg-blue-600 hover:!bg-blue-700 !text-white !rounded-full !w-10 !h-10 !p-0 !border-0"
                                v-tooltip.top="data.reply ? 'View Details' : 'Respond'"
                            />
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-8">
                            <i class="pi pi-star text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">No ratings found</p>
                            <p class="text-xs text-gray-400 mt-1">Customer reviews will appear here</p>
                        </div>
                    </template>
                </DataTable>

                <!-- Mobile/Tablet Card Layout -->
                <div v-else>
                    <div v-if="paginatedData.length === 0 && !loadingRates" :class="responsive.getResponsiveClasses({
                        mobile: 'text-center py-8',
                        tablet: 'text-center py-12',
                        desktop: 'text-center py-12'
                    })">
                        <div class="mb-4">
                            <div :class="responsive.getResponsiveClasses({
                                mobile: 'mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center',
                                tablet: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center',
                                desktop: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center'
                            })">
                                <i :class="responsive.getResponsiveClasses({
                                    mobile: 'pi pi-star text-3xl text-gray-400',
                                    tablet: 'pi pi-star text-4xl text-gray-400',
                                    desktop: 'pi pi-star text-4xl text-gray-400'
                                })"></i>
                            </div>
                        </div>
                        <h3 :class="responsive.getResponsiveClasses({
                            mobile: 'text-base font-semibold text-gray-700 mb-2',
                            tablet: 'text-lg font-semibold text-gray-700 mb-2',
                            desktop: 'text-lg font-semibold text-gray-700 mb-2'
                        })">No ratings found</h3>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-sm text-gray-500',
                            tablet: 'text-sm text-gray-500',
                            desktop: 'text-sm text-gray-500'
                        })">Customer reviews will appear here</p>
                    </div>
                    <div v-else>
                        <!-- Cards Container -->
                        <div :class="responsive.getResponsiveClasses({
                            mobile: 'space-y-3 p-3',
                            tablet: 'space-y-4 p-4',
                            desktop: 'space-y-4 p-4'
                        })">
                            <div 
                                v-for="(rating, index) in paginatedData" 
                                :key="rating.rate_id"
                                :class="responsive.getResponsiveClasses({
                                    mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm',
                                    tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm',
                                    desktop: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm'
                                })"
                            >
                                <!-- Rating Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div :class="responsive.getResponsiveClasses({
                                            mobile: 'flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 flex-shrink-0',
                                            tablet: 'flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 flex-shrink-0',
                                            desktop: 'flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 flex-shrink-0'
                                        })">
                                            <i :class="responsive.getResponsiveClasses({
                                                mobile: 'pi pi-box text-blue-600 text-sm',
                                                tablet: 'pi pi-box text-blue-600',
                                                desktop: 'pi pi-box text-blue-600'
                                            })"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 :class="responsive.getResponsiveClasses({
                                                mobile: 'font-semibold text-gray-900 truncate text-sm',
                                                tablet: 'font-semibold text-gray-900 truncate text-base',
                                                desktop: 'font-semibold text-gray-900 truncate text-base'
                                            })">
                                                {{ rating.product.product_name }}
                                            </h3>
                                            <p :class="responsive.getResponsiveClasses({
                                                mobile: 'text-xs text-gray-600 truncate',
                                                tablet: 'text-sm text-gray-600 truncate',
                                                desktop: 'text-sm text-gray-600 truncate'
                                            })">
                                                by {{ rating.user.full_name }}
                                            </p>
                                        </div>
                                    </div>
                                    <Button
                                        type="button"
                                        :icon="rating.reply ? 'pi pi-eye' : 'pi pi-reply'"
                                        @click="openRate(rating)"
                                        :class="[
                                            '!bg-blue-600 hover:!bg-blue-700 !text-white !rounded-full !border-0',
                                            responsive.getResponsiveClasses({
                                                mobile: '!w-8 !h-8 !p-0 !text-sm',
                                                tablet: '!w-10 !h-10 !p-0',
                                                desktop: '!w-10 !h-10 !p-0'
                                            })
                                        ]"
                                        v-tooltip.top="rating.reply ? 'View Details' : 'Respond'"
                                    />
                                </div>

                                <!-- Rating Stars -->
                                <div class="mb-3">
                                    <div class="flex items-center gap-1 mb-2">
                                        <i v-for="n in 5" :key="n" 
                                           :class="[
                                               'pi pi-star-fill',
                                               responsive.getResponsiveClasses({
                                                   mobile: 'text-sm',
                                                   tablet: 'text-base',
                                                   desktop: 'text-base'
                                               }),
                                               n <= rating.rate ? 'text-yellow-400' : 'text-gray-300'
                                           ]">
                                        </i>
                                        <span :class="responsive.getResponsiveClasses({
                                            mobile: 'ml-1 text-sm font-medium text-gray-600',
                                            tablet: 'ml-1 text-base font-medium text-gray-600',
                                            desktop: 'ml-1 text-base font-medium text-gray-600'
                                        })">({{ rating.rate }})</span>
                                    </div>
                                </div>

                                <!-- Comment -->
                                <div v-if="rating.comment" class="mb-3">
                                    <p :class="responsive.getResponsiveClasses({
                                        mobile: 'text-xs text-gray-600 mb-1',
                                        tablet: 'text-sm text-gray-600 mb-1',
                                        desktop: 'text-sm text-gray-600 mb-1'
                                    })">Comment:</p>
                                    <p :class="responsive.getResponsiveClasses({
                                        mobile: 'text-sm text-gray-800 line-clamp-3',
                                        tablet: 'text-base text-gray-800 line-clamp-3',
                                        desktop: 'text-base text-gray-800 line-clamp-3'
                                    })">
                                        {{ rating.comment }}
                                    </p>
                                </div>

                                <!-- Media -->
                                <div v-if="rating.images && rating.images.length > 0" class="mb-3">
                                    <p :class="responsive.getResponsiveClasses({
                                        mobile: 'text-xs text-gray-600 mb-2',
                                        tablet: 'text-sm text-gray-600 mb-2',
                                        desktop: 'text-sm text-gray-600 mb-2'
                                    })">Media:</p>
                                    <div class="flex gap-2 flex-wrap">
                                        <div
                                            v-for="(image, index) in rating.images.slice(0, 4)"
                                            :key="index"
                                            @click="openMediaPreview(image.image_path)"
                                            :class="responsive.getResponsiveClasses({
                                                mobile: 'relative w-16 h-16 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition-opacity border border-gray-200',
                                                tablet: 'relative w-20 h-20 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition-opacity border border-gray-200',
                                                desktop: 'relative w-20 h-20 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition-opacity border border-gray-200'
                                            })"
                                        >
                                            <img
                                                v-if="isImageFile(image.image_path)"
                                                :src="getRateImageUrl(image.image_path)"
                                                class="w-full h-full object-cover"
                                                alt="Review media"
                                            />
                                            <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                <i class="pi pi-video text-gray-500 text-sm"></i>
                                            </div>
                                        </div>
                                        <div v-if="rating.images.length > 4" :class="responsive.getResponsiveClasses({
                                            mobile: 'w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-600',
                                            tablet: 'w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center text-sm font-medium text-gray-600',
                                            desktop: 'w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center text-sm font-medium text-gray-600'
                                        })">
                                            +{{ rating.images.length - 4 }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Rating Details -->
                                <div :class="responsive.getResponsiveClasses({
                                    mobile: 'flex items-center justify-between pt-3 border-t border-gray-100',
                                    tablet: 'flex items-center justify-between pt-3 border-t border-gray-100',
                                    desktop: 'flex items-center justify-between pt-3 border-t border-gray-100'
                                })">
                                    <div>
                                        <p :class="responsive.getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600',
                                            tablet: 'text-sm text-gray-600',
                                            desktop: 'text-sm text-gray-600'
                                        })">
                                            {{ DateUtil.formatToMonthDayYear(rating.created_at) }}
                                        </p>
                                    </div>
                                    <div>
                                        <span v-if="rating.reply" class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <i class="pi pi-check-circle text-xs"></i>
                                            Replied
                                        </span>
                                        <span v-else class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            <i class="pi pi-clock text-xs"></i>
                                            Pending
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile/Tablet Pagination Controls -->
                        <div v-if="totalPages > 1" :class="responsive.getResponsiveClasses({
                            mobile: 'flex items-center justify-between px-3 py-3 border-t border-gray-200 bg-gray-50',
                            tablet: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50',
                            desktop: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50'
                        })">
                            <div :class="responsive.getResponsiveClasses({
                                mobile: 'text-xs text-gray-600',
                                tablet: 'text-sm text-gray-600',
                                desktop: 'text-sm text-gray-600'
                            })">
                                Showing {{ ((currentPage - 1) * rowsPerPage) + 1 }} to {{ Math.min(currentPage * rowsPerPage, filteredData.length) }} of {{ filteredData.length }} reviews
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    icon="pi pi-chevron-left"
                                    :disabled="currentPage === 1"
                                    @click="currentPage--"
                                    :class="[
                                        'p-button-text p-button-sm',
                                        responsive.getResponsiveClasses({
                                            mobile: '!text-xs !p-1',
                                            tablet: '!text-sm !p-2',
                                            desktop: '!text-sm !p-2'
                                        })
                                    ]"
                                />
                                <span :class="responsive.getResponsiveClasses({
                                    mobile: 'text-xs font-medium text-gray-700 px-2',
                                    tablet: 'text-sm font-medium text-gray-700 px-3',
                                    desktop: 'text-sm font-medium text-gray-700 px-3'
                                })">
                                    {{ currentPage }} of {{ totalPages }}
                                </span>
                                <Button
                                    icon="pi pi-chevron-right"
                                    :disabled="currentPage === totalPages"
                                    @click="currentPage++"
                                    :class="[
                                        'p-button-text p-button-sm',
                                        responsive.getResponsiveClasses({
                                            mobile: '!text-xs !p-1',
                                            tablet: '!text-sm !p-2',
                                            desktop: '!text-sm !p-2'
                                        })
                                    ]"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Dialog
            v-model:visible="replyDialog.visible"
            modal
            :header="replyDialog.data?.reply ? 'Review Details' : 'Respond to Review'"
            :dismissableMask="true"
            :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            class="modal-blue-theme"
        >
            <div v-if="replyDialog.data">
                <!-- Review Details -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-semibold text-gray-900">{{ replyDialog.data.user.full_name }}</span>
                                <div class="flex gap-0.5">
                                    <i v-for="n in 5" :key="n" 
                                       class="pi pi-star-fill text-sm"
                                       :class="n <= replyDialog.data.rate ? 'text-yellow-400' : 'text-gray-300'">
                                    </i>
                                </div>
                            </div>
                            <p class="text-sm text-gray-700 mb-2">{{ replyDialog.data.comment }}</p>
                            <p class="text-xs text-gray-500">{{ DateUtil.formatToMonthDayYear(replyDialog.data.created_at) }}</p>
                        </div>
                    </div>
                    
                    <!-- Review Media -->
                    <div v-if="replyDialog.data.images && replyDialog.data.images.length > 0" class="mt-3">
                        <p class="text-xs font-medium text-gray-700 mb-2">Customer's Media:</p>
                        <div class="flex gap-2 flex-wrap">
                            <div
                                v-for="(image, index) in replyDialog.data.images"
                                :key="index"
                                @click="openMediaPreview(image.image_path)"
                                class="relative w-20 h-20 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition-opacity border-2 border-gray-300 hover:border-blue-500"
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
                                    <i class="pi pi-play text-white text-2xl drop-shadow-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Show existing reply if present -->
                <div v-if="replyDialog.data.reply" class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="pi pi-reply text-blue-600"></i>
                        <span class="font-semibold text-blue-900">Admin Reply</span>
                    </div>
                    <p class="text-sm text-blue-800">{{ replyDialog.data.reply }}</p>
                </div>

                <!-- Show reply form only if no reply exists -->
                <ReplyRateForm v-if="!replyDialog.data.reply" :rate="replyDialog.data" @cb="rateCb" />
            </div>
        </Dialog>

        <!-- Media Preview Modal -->
        <Dialog
            v-model:visible="mediaPreviewModal.visible"
            header="Review Media"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
            class="modal-blue-theme"
        >
            <img
                v-if="isImageFile(mediaPreviewModal.mediaPath)"
                :src="getRateImageUrl(mediaPreviewModal.mediaPath)"
                class="w-full h-auto rounded-lg"
                alt="Review media preview"
            />
            <video
                v-else
                :src="getRateImageUrl(mediaPreviewModal.mediaPath)"
                controls
                autoplay
                class="w-full h-auto rounded-lg"
            >
                Your browser does not support the video tag.
            </video>
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import ReplyRateForm from '@/components/forms/ReplyRateForm.vue';
import { IRate } from '@/interfaces/IRate';
import useAxiosUtil from '@/utils/AxiosUtil';
import DateUtil from '@/utils/DateUtil';
import UrlUtil from '@/utils/UrlUtil';
import DateTimeDisplay from '@/components/DateTimeDisplay.vue';
import { useResponsive } from '@/composables/useResponsive';
import { onMounted, reactive, ref, computed, watch } from 'vue';
import { useToast } from 'vue-toastification';
import TableSkeleton from "@/components/skeletons/TableSkeleton.vue";

const loadService = useAxiosUtil<null, IRate[]>();
const toast = useToast();
const responsive = useResponsive();

const data = ref<IRate[]>([]);

// Filter refs
const filterRating = ref<number | null>(null);
const filterStatus = ref<string | null>(null);

// Filter options
const ratingOptions = [
    { label: '5 Stars', value: 5 },
    { label: '4 Stars', value: 4 },
    { label: '3 Stars', value: 3 },
    { label: '2 Stars', value: 2 },
    { label: '1 Star', value: 1 },
];

const statusOptions = [
    { label: 'Replied', value: 'replied' },
    { label: 'Pending', value: 'pending' },
];

// Computed property for filtered data
const filteredData = computed(() => {
    let result = data.value;
    
    if (filterRating.value !== null) {
        result = result.filter(item => item.rate === filterRating.value);
    }
    
    if (filterStatus.value !== null) {
        if (filterStatus.value === 'replied') {
            result = result.filter(item => item.reply !== null);
        } else if (filterStatus.value === 'pending') {
            result = result.filter(item => item.reply === null);
        }
    }
    
    return result;
});

const clearFilters = () => {
    filterRating.value = null;
    filterStatus.value = null;
};

// Pagination for mobile/tablet card layout
const currentPage = ref(1);
const rowsPerPage = ref(10);

// Computed properties for mobile/tablet pagination
const totalPages = computed(() => {
    return Math.ceil(filteredData.value.length / rowsPerPage.value);
});

const paginatedData = computed(() => {
    if (responsive.isDesktop.value) {
        return filteredData.value; // Desktop uses DataTable pagination
    }
    const start = (currentPage.value - 1) * rowsPerPage.value;
    const end = start + rowsPerPage.value;
    return filteredData.value.slice(start, end);
});

const replyDialog : {
    visible : boolean,
    data : IRate | null
} = reactive({
    visible : false,
    data : null
});

const mediaPreviewModal = reactive({
    visible: false,
    mediaPath: ''
});

const loadingRates = ref(false);
const errorRates = ref<string | null>(null);

const retryRates = async () => {
    errorRates.value = null;
    await load();
};

const load = async () => {
    loadingRates.value = true;
    errorRates.value = null;
    try {
        await loadService.get("admin/rates").then(() => {
            if (loadService.request.status === 200 && loadService.request.data) {
                data.value = loadService.request.data;
            } else {
                toast.error(loadService.request.message ?? "Failed to load rates");
                errorRates.value = loadService.request.message ?? "Failed to load rates";
            }
        });
    } catch (error) {
        errorRates.value = 'Failed to load rates. Please try again.';
    } finally {
        loadingRates.value = false;
    }
}

const openRate = (item : IRate) => {
    replyDialog.data = item;
    replyDialog.visible = true;
}

const rateCb = () => {
    load();
    replyDialog.visible = false;
}

const getRateImageUrl = (filename: string) => {
    return UrlUtil.getBaseAppUrl(`storage/images/rate_images/${filename}`);
};

const isImageFile = (filename: string) => {
    const imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp'];
    return imageExtensions.some(ext => filename.toLowerCase().endsWith(ext));
};

const openMediaPreview = (mediaPath: string) => {
    mediaPreviewModal.mediaPath = mediaPath;
    mediaPreviewModal.visible = true;
};

// Watchers for mobile pagination reset
watch(
    () => filteredData.value,
    () => {
        currentPage.value = 1;
    },
);

onMounted(() => {
    load();
});
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>