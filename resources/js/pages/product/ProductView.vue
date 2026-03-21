<template>
    <div :class="responsive.getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-3 px-3',
        tablet: 'min-h-screen bg-gray-50 py-4 px-4',
        desktop: 'min-h-screen bg-gray-50 py-4 sm:py-6 px-3 sm:px-6'
    })">
        <!-- Date and Time Display -->
        <DateTimeDisplay />

        <!-- Statistics Cards Section -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'grid grid-cols-2 gap-3 mb-3',
            tablet: 'grid grid-cols-2 gap-4 mb-4',
            desktop: 'grid grid-cols-1 gap-4 mb-5 md:grid-cols-2 lg:grid-cols-4'
        })">
            <!-- Total Products Card -->
            <div :class="responsive.getResponsiveClasses({
                mobile: 'bg-white rounded-lg border border-gray-200 shadow-sm p-3',
                tablet: 'bg-white rounded-lg border border-gray-200 shadow-sm p-4',
                desktop: 'bg-white rounded-lg border border-gray-200 shadow-sm p-4'
            })">
                <div class="flex items-center justify-between">
                    <div>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs font-medium text-gray-600 mb-1',
                            tablet: 'text-sm font-medium text-gray-600 mb-1',
                            desktop: 'text-sm font-medium text-gray-600 mb-1'
                        })">Total Products</p>
                        <div v-if="progressiveLoad.isLoading.value && paginate.total === 0" class="animate-pulse">
                            <div :class="responsive.getResponsiveClasses({
                                mobile: 'h-5 bg-gray-200 rounded w-6',
                                tablet: 'h-6 bg-gray-200 rounded w-8',
                                desktop: 'h-6 bg-gray-200 rounded w-8'
                            })"></div>
                        </div>
                        <p v-else :class="responsive.getResponsiveClasses({
                            mobile: 'text-lg font-bold text-blue-600 ps-2',
                            tablet: 'text-xl font-bold text-blue-600 ps-2',
                            desktop: 'text-xl font-bold text-blue-600 ps-2'
                        })">{{ paginate.total }}</p>
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'bg-blue-100 p-2 rounded-full',
                        tablet: 'bg-blue-100 p-3 rounded-full',
                        desktop: 'bg-blue-100 p-3 rounded-full'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-box text-blue-600 text-lg',
                            tablet: 'pi pi-box text-blue-600 text-xl',
                            desktop: 'pi pi-box text-blue-600 text-xl'
                        })"></i>
                    </div>
                </div>
            </div>
            
            <!-- Nearly Out of Stock Card -->
            <div :class="responsive.getResponsiveClasses({
                mobile: 'bg-white rounded-lg border border-gray-200 shadow-sm p-3',
                tablet: 'bg-white rounded-lg border border-gray-200 shadow-sm p-4',
                desktop: 'bg-white rounded-lg border border-gray-200 shadow-sm p-4'
            })">
                <div class="flex items-center justify-between">
                    <div>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs font-medium text-gray-600 mb-1',
                            tablet: 'text-sm font-medium text-gray-600 mb-1',
                            desktop: 'text-sm font-medium text-gray-600 mb-1'
                        })">Nearly Out of Stock</p>
                        <LowStockResult />
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'bg-orange-100 p-2 rounded-full',
                        tablet: 'bg-orange-100 p-3 rounded-full',
                        desktop: 'bg-orange-100 p-3 rounded-full'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-exclamation-triangle text-orange-600 text-lg',
                            tablet: 'pi pi-exclamation-triangle text-orange-600 text-xl',
                            desktop: 'pi pi-exclamation-triangle text-orange-600 text-xl'
                        })"></i>
                    </div>
                </div>
            </div>
            
            <!-- Active Products Card -->
            <div :class="responsive.getResponsiveClasses({
                mobile: 'bg-white rounded-lg border border-gray-200 shadow-sm p-3',
                tablet: 'bg-white rounded-lg border border-gray-200 shadow-sm p-4',
                desktop: 'bg-white rounded-lg border border-gray-200 shadow-sm p-4'
            })">
                <div class="flex items-center justify-between">
                    <div>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs font-medium text-gray-600 mb-1',
                            tablet: 'text-sm font-medium text-gray-600 mb-1',
                            desktop: 'text-sm font-medium text-gray-600 mb-1'
                        })">Active Products</p>
                        <ActiveProductCount />
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'bg-green-100 p-2 rounded-full',
                        tablet: 'bg-green-100 p-3 rounded-full',
                        desktop: 'bg-green-100 p-3 rounded-full'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-check-circle text-green-600 text-lg',
                            tablet: 'pi pi-check-circle text-green-600 text-xl',
                            desktop: 'pi pi-check-circle text-green-600 text-xl'
                        })"></i>
                    </div>
                </div>
            </div>
            
            <!-- Out of Stock Card -->
            <div :class="responsive.getResponsiveClasses({
                mobile: 'bg-white rounded-lg border border-gray-200 shadow-sm p-3',
                tablet: 'bg-white rounded-lg border border-gray-200 shadow-sm p-4',
                desktop: 'bg-white rounded-lg border border-gray-200 shadow-sm p-4'
            })">
                <div class="flex items-center justify-between">
                    <div>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs font-medium text-gray-600 mb-1',
                            tablet: 'text-sm font-medium text-gray-600 mb-1',
                            desktop: 'text-sm font-medium text-gray-600 mb-1'
                        })">Out of Stock</p>
                        <OutOfStockCount />
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'bg-red-100 p-2 rounded-full',
                        tablet: 'bg-red-100 p-3 rounded-full',
                        desktop: 'bg-red-100 p-3 rounded-full'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-times-circle text-red-600 text-lg',
                            tablet: 'pi pi-times-circle text-red-600 text-xl',
                            desktop: 'pi pi-times-circle text-red-600 text-xl'
                        })"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filters Card -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'mb-3 rounded-lg bg-white border border-gray-200 shadow-sm',
            tablet: 'mb-4 rounded-lg bg-white border border-gray-200 shadow-sm',
            desktop: 'mb-4 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm'
        })">
            <div :class="responsive.getResponsiveClasses({
                mobile: 'px-3 py-3',
                tablet: 'px-4 py-4',
                desktop: 'px-5 py-4'
            })">
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'space-y-4',
                    tablet: 'space-y-4 md:space-y-0 md:flex md:flex-wrap md:gap-4 md:items-end',
                    desktop: 'flex flex-wrap gap-4 items-end'
                })">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'flex-1 min-w-[200px]',
                        desktop: 'flex-1 min-w-[250px]'
                    })">
                        <label :class="responsive.getResponsiveClasses({
                            mobile: 'block text-xs font-medium text-gray-700 mb-2',
                            tablet: 'block text-sm font-medium text-gray-700 mb-2',
                            desktop: 'block text-sm font-medium text-gray-700 mb-2'
                        })">Search Product</label>
                        <InputText
                            v-model="form.search"
                            id="search"
                            type="text"
                            placeholder="Search by product name"
                            :class="[
                                'w-full',
                                responsive.getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                            :invalid="errors.search.length > 0"
                        />
                    </div>
                    
                    <!-- Category and Status in 2x2 grid for mobile/tablet -->
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'grid grid-cols-2 gap-3',
                        tablet: 'grid grid-cols-2 gap-3 md:contents',
                        desktop: 'contents'
                    })">
                        <div :class="responsive.getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'w-full sm:w-auto sm:min-w-[180px]',
                            desktop: 'w-full sm:w-auto sm:min-w-[200px]'
                        })">
                            <label :class="responsive.getResponsiveClasses({
                                mobile: 'block text-xs font-medium text-gray-700 mb-2',
                                tablet: 'block text-sm font-medium text-gray-700 mb-2',
                                desktop: 'block text-sm font-medium text-gray-700 mb-2'
                            })">Category</label>
                            <Select
                                v-model="form.category_id"
                                :options="categoryState.categories"
                                placeholder="All Categories"
                                :class="[
                                    'w-full',
                                    responsive.getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })
                                ]"
                                :invalid="errors.category_id.length > 0"
                                option-label="category_name"
                                option-value="category_id"
                                :loading="categoryState.loadCategoryService.request.loading"
                                filter
                                showClear
                            />
                        </div>
                        <div :class="responsive.getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'w-full sm:w-auto sm:min-w-[120px]',
                            desktop: 'w-full sm:w-auto sm:min-w-[150px]'
                        })">
                            <label :class="responsive.getResponsiveClasses({
                                mobile: 'block text-xs font-medium text-gray-700 mb-2',
                                tablet: 'block text-sm font-medium text-gray-700 mb-2',
                                desktop: 'block text-sm font-medium text-gray-700 mb-2'
                            })">Status</label>
                            <Select
                                v-model="form.status"
                                :options="[
                                    { label: 'Active', value: 1 },
                                    { label: 'Inactive', value: 0 },
                                ]"
                                placeholder="All Status"
                                :class="[
                                    'w-full',
                                    responsive.getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })
                                ]"
                                :invalid="errors.status.length > 0"
                                showClear
                                option-label="label"
                                option-value="value"
                            />
                        </div>
                    </div>
                    
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full flex justify-center',
                        tablet: 'flex gap-2 ml-auto',
                        desktop: 'flex gap-2 ml-auto'
                    })">
                        <Button
                            v-if="!isStaff()"
                            type="button"
                            icon="pi pi-plus"
                            :label="responsive.getResponsiveClasses({
                                mobile: 'Add Product',
                                tablet: 'Add Product',
                                desktop: 'Add Product'
                            })"
                            :class="[
                                '!bg-green-600 hover:!bg-green-700 !border-green-600 hover:!border-green-700',
                                responsive.getResponsiveClasses({
                                    mobile: 'w-full text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                            @click="addProduct()"
                        />
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Table/Cards Container -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            tablet: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            desktop: 'rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden'
        })">
            <div
                v-if="progressiveLoad.error.value"
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
                })">
                    Failed to Load Products
                </h3>
                <p :class="responsive.getResponsiveClasses({
                    mobile: 'mb-4 text-sm text-red-600',
                    tablet: 'mb-4 text-sm text-red-600',
                    desktop: 'mb-4 text-sm text-red-600'
                })">
                    {{ progressiveLoad.error.value }}
                </p>
                <Button
                    @click="handleRetry"
                    :disabled="progressiveLoad.isLoading.value"
                    :loading="progressiveLoad.isLoading.value"
                    label="Retry"
                    icon="pi pi-refresh"
                    class="!bg-red-600 hover:!bg-red-700 !border-red-600 !transition-colors"
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
                        })">All Products</h1>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">Manage your product inventory</p>
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'flex flex-col items-end',
                        tablet: 'flex flex-col items-end',
                        desktop: 'flex items-center gap-2 text-sm text-gray-600'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'hidden',
                            tablet: 'hidden',
                            desktop: 'pi pi-box text-blue-600'
                        })"></i>
                        <span :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-600 font-medium',
                            tablet: 'text-xs text-gray-600 font-medium',
                            desktop: 'font-medium'
                        })">{{ paginate.total }} Products</span>
                    </div>
                </div>
                
                <!-- Desktop DataTable -->
                <DataTable
                    v-if="responsive.isDesktop.value"
                    class="w-full"
                    :value="paginate.data"
                    columnResizeMode="expand"
                    stripedRows
                    :loading="progressiveLoad.isLoading.value"
                >
                <Column field="product_name" header="Product Name">
                    <template #body="{ data }">
                        <div class="flex items-center">
                            <Avatar
                                v-if="data.product_image"
                                shape="circle"
                                :image="
                                    UrlUtil.getBaseAppUrl(
                                        `storage/images/product/${data.product_image}`,
                                    )
                                "
                                class="aspect-square!"
                            />
                            <Avatar v-else shape="circle" icon="pi pi-camera" />
                            <div class="ml-2 shrink">
                                {{ data.product_name }}
                            </div>
                        </div>
                    </template>
                </Column>
                <Column field="category.category_name" header="Category" />
                <Column field="product_quantity" header="Stock" />
                <Column field="product_price" header="Price">
                    <template #body="{ data }">
                        {{ CurrencyUtil.formatCurrency(data.product_price) }}
                    </template>
                </Column>
                <Column field="batch_number" header="Batch ID">
                    <template #body="{ data }">
                        {{ data.batch_number || '-' }}
                    </template>
                </Column>
                <Column 
                    header="Actions" 
                    :pt="{
                        headerCell: { class: 'text-center' },
                        headerContent: { class: 'justify-center' }
                    }"
                >
                    <template #body="{ data }">
                        <div class="flex gap-2 justify-center">
                            <Button
                                v-if="!isStaff()"
                                icon="pi pi-pencil"
                                rounded
                                class="!bg-blue-600 hover:!bg-blue-700 !text-white !border-blue-600"
                                @click="editProduct(data)"
                                v-tooltip.top="'Edit Product'"
                            />
                            <Button
                                v-if="data.sku"
                                icon="pi pi-barcode"
                                rounded
                                class="!bg-purple-600 hover:!bg-purple-700 !text-white !border-purple-600"
                                @click="showSku(data.sku)"
                                v-tooltip.top="'View Barcode'"
                            />
                            <Button
                                v-if="!isStaff()"
                                icon="pi pi-plus-circle"
                                rounded
                                class="!bg-green-600 hover:!bg-green-700 !text-white !border-green-600"
                                @click="openAddStockModal(data)"
                                v-tooltip.top="'Add Stock'"
                            />
                        </div>
                    </template>
                </Column>
                <template #empty>
                    <div v-if="!progressiveLoad.isLoading.value && progressiveLoad.isComplete.value" class="text-center py-12">
                        <div class="mb-4">
                            <i class="pi pi-inbox text-6xl text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            <span v-if="form.search || form.category_id || form.status !== null">
                                Try adjusting your search criteria or filters to find what you're looking for.
                            </span>
                            <span v-else>
                                Get started by adding your first product to the inventory.
                            </span>
                        </p>
                        <div class="flex justify-center gap-3">
                            <Button
                                v-if="form.search || form.category_id || form.status !== null"
                                type="button"
                                label="Clear Filters"
                                icon="pi pi-filter-slash"
                                class="!bg-gray-600 hover:!bg-gray-700 !border-gray-600 hover:!border-gray-700"
                                @click="clearFilters()"
                            />
                            <Button
                                v-if="!isStaff()"
                                type="button"
                                label="Add Product"
                                icon="pi pi-plus"
                                class="!bg-green-600 hover:!bg-green-700 !border-green-600 hover:!border-green-700"
                                @click="addProduct()"
                            />
                        </div>
                    </div>
                </template>

                <template #footer>
                    <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200">
                        <div class="text-sm text-gray-700">
                            <span v-if="paginate.from && paginate.to">
                                Showing {{ paginate.from }} - {{ paginate.to }} of {{ paginate.total }}
                            </span>
                            <span v-else>No page results</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Button
                                icon="pi pi-chevron-left"
                                text
                                size="small"
                                @click="decrementPage()"
                                :disabled="
                                    paginate.current_page === 1 ||
                                    loadService.request.loading
                                "
                                v-tooltip.top="'Previous'"
                            />
                            <span class="text-sm font-medium text-gray-700 px-3">
                                {{ paginate.current_page }}
                            </span>
                            <Button
                                icon="pi pi-chevron-right"
                                text
                                size="small"
                                @click="incrementPage()"
                                :disabled="
                                    paginate.last_page === paginate.current_page ||
                                    loadService.request.loading
                                "
                                v-tooltip.top="'Next'"
                            />
                        </div>
                    </div>
                </template>
                </DataTable>

                <!-- Mobile/Tablet Card Layout -->
                <div v-else>
                    <div v-if="paginate.data.length === 0 && !progressiveLoad.isLoading.value && progressiveLoad.isComplete.value" :class="responsive.getResponsiveClasses({
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
                                    mobile: 'pi pi-inbox text-3xl text-gray-400',
                                    tablet: 'pi pi-inbox text-4xl text-gray-400',
                                    desktop: 'pi pi-inbox text-4xl text-gray-400'
                                })"></i>
                            </div>
                        </div>
                        <h3 :class="responsive.getResponsiveClasses({
                            mobile: 'text-base font-semibold text-gray-700 mb-2',
                            tablet: 'text-lg font-semibold text-gray-700 mb-2',
                            desktop: 'text-lg font-semibold text-gray-700 mb-2'
                        })">No products found</h3>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-sm text-gray-500 mb-4',
                            tablet: 'text-sm text-gray-500 mb-6',
                            desktop: 'text-sm text-gray-500 mb-6'
                        })">
                            <span v-if="form.search || form.category_id || form.status !== null">
                                Try adjusting your search criteria or filters.
                            </span>
                            <span v-else>
                                Get started by adding your first product.
                            </span>
                        </p>
                        <div :class="responsive.getResponsiveClasses({
                            mobile: 'flex flex-col gap-3',
                            tablet: 'flex justify-center gap-3',
                            desktop: 'flex justify-center gap-3'
                        })">
                            <Button
                                v-if="form.search || form.category_id || form.status !== null"
                                type="button"
                                label="Clear Filters"
                                icon="pi pi-filter-slash"
                                :class="[
                                    '!bg-gray-600 hover:!bg-gray-700 !border-gray-600 hover:!border-gray-700',
                                    responsive.getResponsiveClasses({
                                        mobile: 'w-full text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })
                                ]"
                                @click="clearFilters()"
                            />
                            <Button
                                v-if="!isStaff()"
                                type="button"
                                label="Add Product"
                                icon="pi pi-plus"
                                :class="[
                                    '!bg-green-600 hover:!bg-green-700 !border-green-600 hover:!border-green-700',
                                    responsive.getResponsiveClasses({
                                        mobile: 'w-full text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })
                                ]"
                                @click="addProduct()"
                            />
                        </div>
                    </div>
                    <div v-else>
                        <!-- Cards Container -->
                        <div :class="responsive.getResponsiveClasses({
                            mobile: 'space-y-3 p-3',
                            tablet: 'space-y-4 p-4',
                            desktop: 'space-y-4 p-4'
                        })">
                            <div 
                                v-for="(product, index) in paginate.data" 
                                :key="product.product_id"
                                :class="responsive.getResponsiveClasses({
                                    mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm',
                                    tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm',
                                    desktop: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm'
                                })"
                            >
                                <!-- Product Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <Avatar
                                            v-if="product.product_image"
                                            shape="circle"
                                            :image="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)"
                                            :class="responsive.getResponsiveClasses({
                                                mobile: 'w-10 h-10',
                                                tablet: 'w-12 h-12',
                                                desktop: 'w-12 h-12'
                                            })"
                                        />
                                        <Avatar 
                                            v-else 
                                            shape="circle" 
                                            icon="pi pi-camera"
                                            :class="responsive.getResponsiveClasses({
                                                mobile: 'w-10 h-10 bg-gray-100 text-gray-400',
                                                tablet: 'w-12 h-12 bg-gray-100 text-gray-400',
                                                desktop: 'w-12 h-12 bg-gray-100 text-gray-400'
                                            })"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <h3 :class="responsive.getResponsiveClasses({
                                                mobile: 'font-semibold text-gray-900 truncate text-sm',
                                                tablet: 'font-semibold text-gray-900 truncate text-base',
                                                desktop: 'font-semibold text-gray-900 truncate text-base'
                                            })">
                                                {{ product.product_name }}
                                            </h3>
                                            <p :class="responsive.getResponsiveClasses({
                                                mobile: 'text-xs text-gray-500 truncate',
                                                tablet: 'text-sm text-gray-500 truncate',
                                                desktop: 'text-sm text-gray-500 truncate'
                                            })">
                                                {{ product.category?.category_name || 'No Category' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Details -->
                                <div :class="responsive.getResponsiveClasses({
                                    mobile: 'grid grid-cols-2 gap-3 mb-3',
                                    tablet: 'grid grid-cols-3 gap-4 mb-4',
                                    desktop: 'grid grid-cols-3 gap-4 mb-4'
                                })">
                                    <div>
                                        <p :class="responsive.getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600 mb-1',
                                            tablet: 'text-sm text-gray-600 mb-1',
                                            desktop: 'text-sm text-gray-600 mb-1'
                                        })">Stock:</p>
                                        <span :class="[
                                            'font-semibold',
                                            responsive.getResponsiveClasses({
                                                mobile: 'text-sm',
                                                tablet: 'text-base',
                                                desktop: 'text-base'
                                            }),
                                            product.product_quantity === 0 ? 'text-red-600' : 
                                            product.product_quantity <= 10 ? 'text-orange-600' : 'text-green-600'
                                        ]">
                                            {{ product.product_quantity }}
                                        </span>
                                    </div>
                                    <div>
                                        <p :class="responsive.getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600 mb-1',
                                            tablet: 'text-sm text-gray-600 mb-1',
                                            desktop: 'text-sm text-gray-600 mb-1'
                                        })">Price:</p>
                                        <span :class="responsive.getResponsiveClasses({
                                            mobile: 'font-semibold text-blue-600 text-sm',
                                            tablet: 'font-semibold text-blue-600 text-base',
                                            desktop: 'font-semibold text-blue-600 text-base'
                                        })">
                                            {{ CurrencyUtil.formatCurrency(product.product_price) }}
                                        </span>
                                    </div>
                                    <div :class="responsive.getResponsiveClasses({
                                        mobile: 'col-span-2',
                                        tablet: 'col-span-1',
                                        desktop: 'col-span-1'
                                    })">
                                        <p :class="responsive.getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600 mb-1',
                                            tablet: 'text-sm text-gray-600 mb-1',
                                            desktop: 'text-sm text-gray-600 mb-1'
                                        })">Batch ID:</p>
                                        <span :class="responsive.getResponsiveClasses({
                                            mobile: 'text-sm text-gray-700',
                                            tablet: 'text-base text-gray-700',
                                            desktop: 'text-base text-gray-700'
                                        })">
                                            {{ product.batch_number || '-' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div :class="responsive.getResponsiveClasses({
                                    mobile: 'flex gap-2 pt-3 border-t border-gray-100',
                                    tablet: 'flex gap-3 pt-3 border-t border-gray-100',
                                    desktop: 'flex gap-3 pt-3 border-t border-gray-100'
                                })">
                                    <Button
                                        v-if="!isStaff()"
                                        icon="pi pi-pencil"
                                        label="Edit"
                                        :class="[
                                            '!bg-blue-600 hover:!bg-blue-700 !text-white !border-blue-600',
                                            responsive.getResponsiveClasses({
                                                mobile: 'flex-1 text-xs',
                                                tablet: 'text-sm',
                                                desktop: 'text-sm'
                                            })
                                        ]"
                                        @click="editProduct(product)"
                                    />
                                    <Button
                                        v-if="product.sku"
                                        icon="pi pi-barcode"
                                        :label="responsive.getResponsiveClasses({
                                            mobile: '',
                                            tablet: 'Barcode',
                                            desktop: 'Barcode'
                                        })"
                                        :class="[
                                            '!bg-purple-600 hover:!bg-purple-700 !text-white !border-purple-600',
                                            responsive.getResponsiveClasses({
                                                mobile: 'text-xs',
                                                tablet: 'text-sm',
                                                desktop: 'text-sm'
                                            })
                                        ]"
                                        @click="showSku(product.sku)"
                                    />
                                    <Button
                                        v-if="!isStaff()"
                                        icon="pi pi-plus-circle"
                                        :label="responsive.getResponsiveClasses({
                                            mobile: '',
                                            tablet: 'Add Stock',
                                            desktop: 'Add Stock'
                                        })"
                                        :class="[
                                            '!bg-green-600 hover:!bg-green-700 !text-white !border-green-600',
                                            responsive.getResponsiveClasses({
                                                mobile: 'text-xs',
                                                tablet: 'text-sm',
                                                desktop: 'text-sm'
                                            })
                                        ]"
                                        @click="openAddStockModal(product)"
                                    />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile/Tablet Pagination Controls -->
                        <div v-if="paginate.last_page > 1" :class="responsive.getResponsiveClasses({
                            mobile: 'flex items-center justify-between px-3 py-3 border-t border-gray-200 bg-gray-50',
                            tablet: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50',
                            desktop: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50'
                        })">
                            <div :class="responsive.getResponsiveClasses({
                                mobile: 'text-xs text-gray-600',
                                tablet: 'text-sm text-gray-600',
                                desktop: 'text-sm text-gray-600'
                            })">
                                <span v-if="paginate.from && paginate.to">
                                    Showing {{ paginate.from }} - {{ paginate.to }} of {{ paginate.total }}
                                </span>
                                <span v-else>No results</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    icon="pi pi-chevron-left"
                                    :disabled="paginate.current_page === 1 || loadService.request.loading"
                                    @click="decrementPage()"
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
                                    {{ paginate.current_page }} of {{ paginate.last_page }}
                                </span>
                                <Button
                                    icon="pi pi-chevron-right"
                                    :disabled="paginate.current_page === paginate.last_page || loadService.request.loading"
                                    @click="incrementPage()"
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
            v-model:visible="showProductDialog.visible"
            modal
            :header="showProductDialog.selectedItem ? 'Edit Product' : 'Add Product'"
            :style="responsive.getResponsiveClasses({
                mobile: '{ width: \'100vw\', height: \'100svh\' }',
                tablet: '{ width: \'95vw\', height: \'95vh\' }',
                desktop: '{ width: \'100vw\', height: \'100svh\' }'
            })"
            :class="responsive.getResponsiveClasses({
                mobile: 'p-dialog-maximized',
                tablet: '',
                desktop: 'p-dialog-maximized'
            })"
            :dismissableMask="true"
        >
            <ProductForm :data="showProductDialog.selectedItem" @cb="productFormCb" />
        </Dialog>
        <Dialog
            v-model:visible="showProductSku.visible"
            modal
            header="Barcode/SKU"
            :style="responsive.getResponsiveClasses({
                mobile: '{ width: \'95vw\' }',
                tablet: '{ width: \'28rem\' }',
                desktop: '{ width: \'40rem\' }'
            })"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
        >
            <div class="p-6">
                <div ref="skuPrintable" class="flex items-center justify-center">
                    <div
                        style="display: flex; justify-content: center; width: 100%"
                    >
                        <Vue3Barcode
                            v-if="showProductSku.sku"
                            :value="showProductSku.sku"
                        />
                    </div>
                </div>
                <div class="flex items-center justify-center mt-6">
                    <Button 
                        icon="pi pi-print" 
                        label="Print" 
                        class="!bg-blue-600 hover:!bg-blue-700 !text-white !border-blue-600"
                        @click="printSku()" 
                    />
                </div>
            </div>
        </Dialog>
        <AddStockModal
            :product="showAddStockDialog.selectedProduct"
            :visible="showAddStockDialog.visible"
            @update:visible="showAddStockDialog.visible = $event"
            @success="handleStockAdded"
        />
    </div>
</template>
<script setup lang="ts">
import ProductForm from "@/components/forms/ProductForm.vue";
import ActiveProductCount from "@/components/result/ActiveProductCount.vue";
import LowStockResult from "@/components/result/LowStockResult.vue";
import OutOfStockCount from "@/components/result/OutOfStockCount.vue";
import AddStockModal from "@/components/modals/AddStockModal.vue";
import DateTimeDisplay from "@/components/DateTimeDisplay.vue";
import CardSkeleton from "@/components/skeletons/CardSkeleton.vue";
import TableSkeleton from "@/components/skeletons/TableSkeleton.vue";
import { useProgressiveLoad } from "@/composables/useProgressiveLoad";
import { useResponsive } from "@/composables/useResponsive";
import DataTableInterface from "@/interfaces/DataTableInterface";
import {
    ProductInterface,
    ProductSearchErrorInterface,
    ProductSearchInterface,
} from "@/interfaces/ProductInterface";
import { useCategoryStore } from "@/stores/CategoryState";
import useAxiosUtil from "@/utils/AxiosUtil";
import UrlUtil from "@/utils/UrlUtil";
import { onMounted, onUnmounted, reactive, ref, watch } from "vue";
import { useToast } from "vue-toastification";
import { useEcho } from "@laravel/echo-vue";
import CurrencyUtil from "@/utils/CurrencyUtil";

const categoryState = useCategoryStore();
const responsive = useResponsive();
const loadService = useAxiosUtil<
    ProductSearchInterface,
    DataTableInterface<ProductInterface>
>();
const toast = useToast();
const skuPrintable = ref<HTMLElement>();
const authService = useAxiosUtil<null, any>();
const currentUserRole = ref<string | null>(null);
const showProductDialog: {
    visible: boolean;
    selectedItem: ProductInterface | null;
} = reactive({
    visible: false,
    selectedItem: null,
});
const showProductSku: {
    visible: boolean;
    sku: string | null;
} = reactive({
    visible: false,
    sku: null,
});

const showAddStockDialog: {
    visible: boolean;
    selectedProduct: ProductInterface | null;
} = reactive({
    visible: false,
    selectedProduct: null,
});

const form: ProductSearchInterface = reactive({
    search: null,
    limit: 5,
    page: 1,
    category_id: null,
    status: null,
    available_online: null,
    low_stock: null,
});

const errors: ProductSearchErrorInterface = reactive({
    search: [],
    limit: [],
    page: [],
    category_id: [],
    status: [],
    available_online: [],
    low_stock: [],
});

const paginate: DataTableInterface<ProductInterface> = reactive({
    data: [],
    current_page: 0,
    from: 0,
    to: 0,
    total: 0,
    last_page: 0,
});

const showSku = (value: string) => {
    showProductSku.sku = value;
    showProductSku.visible = true;
};

const printSku = () => {
    const printable = skuPrintable.value;

    if (printable) {
        const printContents = printable.innerHTML;
        const printWindow = window.open("", "", "width=1000,height=600");

        if (printContents && printWindow) {
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Barcode Print</title>
                        <style>
                            @media print {
                                img { display: block !important; }
                            }
                        </style>
                    </head>
                    <body style="font-family: Arial, sans-serif; text-align: center; padding: 20px;">
                        ${printContents}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            
            // Wait for content to load before printing
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }
    }
};

const addProduct = () => {
    showProductDialog.selectedItem = null;
    showProductDialog.visible = true;
};

const editProduct = (data: ProductInterface) => {
    showProductDialog.selectedItem = data;
    showProductDialog.visible = true;
};

const productFormCb = () => {
    showProductDialog.visible = false;
    load();
};

const openAddStockModal = (product: ProductInterface) => {
    showAddStockDialog.selectedProduct = product;
    showAddStockDialog.visible = true;
};

const handleStockAdded = () => {
    load();
};

const clearFilters = () => {
    form.search = null;
    form.category_id = null;
    form.status = null;
    form.page = 1;
    load(true);
};

const clearError = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearError();
    if (!form.limit) {
        errors.limit.push("Limit is required");
    }
    if (!form.page) {
        errors.page.push("Page is required");
    }

    const hasErrors = [errors.limit.length > 0, errors.page.length > 0];
    return hasErrors.includes(true) ? false : form;
};

// Initialize progressive loading composable
const progressiveLoad = useProgressiveLoad();

// Primary data loader: Load product list (critical data)
const loadPrimaryData = async () => {
    if (!form.limit) {
        form.limit = 5;
    }

    const data = validate();

    if (data) {
        await loadService.get("admin/products", data).then(() => {
            if (
                loadService.request.status === 200 &&
                loadService.request.data
            ) {
                Object.keys(paginate).forEach((key) => {
                    paginate[key] = loadService.request.data[key];
                });
            } else {
                toast.error(loadService.request.message ?? "Please try again");
                if (loadService.request.errors) {
                    Object.keys(loadService.request.errors).forEach((key) => {
                        errors[key] = loadService.request.errors[key];
                    });
                }
            }
        });
    } else {
        toast.error("Please fill in the required fields.");
    }
};

// Secondary data loader: Statistics cards load after product list
const loadSecondaryData = async () => {
    // Statistics cards (LowStockResult, ActiveProductCount, OutOfStockCount)
    // will load via their own components
    return Promise.resolve();
};

const load = (resetPage: boolean = false) => {
    if (resetPage) {
        form.page = 1;
    }
    
    // Use progressive loading pattern - non-blocking
    progressiveLoad.loadData({
        primaryLoader: loadPrimaryData,
        secondaryLoader: loadSecondaryData,
    });
};

// Retry handler for failed data loads
const handleRetry = async () => {
    try {
        await progressiveLoad.retry();
        toast.success("Product data reloaded successfully");
    } catch (error) {
        toast.error("Retry failed. Please try again later.");
    }
};

const incrementPage = () => {
    if (paginate.current_page < paginate.last_page) {
        form.page = paginate.current_page + 1;
        load();
    }
};

const decrementPage = () => {
    if (paginate.current_page > 1) {
        form.page = paginate.current_page - 1;
        load();
    }
};

const { leave } = useEcho(
    "product",
    [".product.update"],
    (value: ProductInterface) => {
        const index = paginate.data.findIndex(
            (item) => item.product_id === value.product_id,
        );

        if (index !== -1) {
            paginate.data[index] = value;
        }
    },
);

useEcho("product", [".product.count"], (value: { count: number }) => {
    if (paginate.total != value.count) {
        paginate.total = value.count;
        load();
    }
});

// Auto-filter when category changes
watch(
    () => form.category_id,
    () => {
        form.page = 1;
        load(true);
    },
);

// Auto-filter when status changes
watch(
    () => form.status,
    () => {
        form.page = 1;
        load(true);
    },
);

// Debounced search - auto-filter as user types
let searchTimeout: number | null = null;
watch(
    () => form.search,
    () => {
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        searchTimeout = window.setTimeout(() => {
            form.page = 1;
            load(true);
        }, 500); // Wait 500ms after user stops typing
    },
);

// Check user role
const checkUserRole = async () => {
    await authService.get('check').then(() => {
        if (authService.request.status === 200 && authService.request.data) {
            currentUserRole.value = authService.request.data.role?.role_name || null;
        }
    });
};

// Check if user is staff (read-only access)
const isStaff = () => {
    return currentUserRole.value === 'Staff';
};

// Non-blocking onMounted - page renders immediately with skeletons
onMounted(() => {
    checkUserRole();
    load();
});

onUnmounted(() => {
    leave();
});
</script>

<style scoped>
/* Aggressive centering for Actions column header - scoped to this component only */
:deep(.p-datatable) th:last-child {
    text-align: center !important;
}

:deep(.p-datatable) th:last-child * {
    text-align: center !important;
    justify-content: center !important;
}

:deep(.p-datatable) th:last-child .p-column-header-content {
    justify-content: center !important;
    display: flex !important;
    width: 100% !important;
}

:deep(.p-datatable) th:last-child .p-column-title {
    text-align: center !important;
    display: block !important;
    width: 100% !important;
}

:deep(.p-datatable) th:last-child span {
    display: block !important;
    text-align: center !important;
    width: 100% !important;
}

:deep(.p-datatable) th:last-child div {
    justify-content: center !important;
    text-align: center !important;
}
</style>
