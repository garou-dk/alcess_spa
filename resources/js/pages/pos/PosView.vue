<template>
    <div :class="getResponsiveClasses({
        mobile: 'p-3',
        tablet: 'p-4',
        desktop: 'p-0'
    })">
        <!-- Date and Time Display -->
        <DateTimeDisplay />

        <div :class="getResponsiveClasses({
            mobile: 'grid grid-cols-1 gap-3 mb-4',
            tablet: 'grid grid-cols-2 gap-4 mb-4 md:grid-cols-3',
            desktop: 'grid grid-cols-1 gap-4 mb-5 md:grid-cols-3'
        })">
            <BoxShadow :class="getResponsiveClasses({
                mobile: 'p-3',
                tablet: 'p-3',
                desktop: 'p-4'
            })">
                <div class="flex items-center justify-between">
                    <div>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-xs font-medium text-gray-600 mb-1',
                            tablet: 'text-xs font-medium text-gray-600 mb-1',
                            desktop: 'text-sm font-medium text-gray-600 mb-1'
                        })">Daily Sales</p>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-lg font-bold text-blue-600',
                            tablet: 'text-xl font-bold text-blue-600',
                            desktop: 'text-2xl font-bold text-blue-600'
                        })">{{ CurrencyUtil.formatCurrency(dailyStats.total_sales) }}</p>
                    </div>
                    <div :class="getResponsiveClasses({
                        mobile: 'bg-blue-100 p-2 rounded-full',
                        tablet: 'bg-blue-100 p-2.5 rounded-full',
                        desktop: 'bg-blue-100 p-3 rounded-full'
                    })">
                        <i :class="getResponsiveClasses({
                            mobile: 'pi pi-chart-line text-blue-600 text-lg',
                            tablet: 'pi pi-chart-line text-blue-600 text-lg',
                            desktop: 'pi pi-chart-line text-blue-600 text-xl'
                        })"></i>
                    </div>
                </div>
            </BoxShadow>
            <BoxShadow :class="getResponsiveClasses({
                mobile: 'p-3',
                tablet: 'p-3',
                desktop: 'p-4'
            })">
                <div class="flex items-center justify-between">
                    <div>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-xs font-medium text-gray-600 mb-1',
                            tablet: 'text-xs font-medium text-gray-600 mb-1',
                            desktop: 'text-sm font-medium text-gray-600 mb-1'
                        })">Transactions Today</p>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-lg font-bold text-green-600',
                            tablet: 'text-xl font-bold text-green-600',
                            desktop: 'text-2xl font-bold text-green-600'
                        })">{{ dailyStats.total_transactions }}</p>
                    </div>
                    <div :class="getResponsiveClasses({
                        mobile: 'bg-green-100 p-2 rounded-full',
                        tablet: 'bg-green-100 p-2.5 rounded-full',
                        desktop: 'bg-green-100 p-3 rounded-full'
                    })">
                        <i :class="getResponsiveClasses({
                            mobile: 'pi pi-shopping-cart text-green-600 text-lg',
                            tablet: 'pi pi-shopping-cart text-green-600 text-lg',
                            desktop: 'pi pi-shopping-cart text-green-600 text-xl'
                        })"></i>
                    </div>
                </div>
            </BoxShadow>
            <BoxShadow :class="getResponsiveClasses({
                mobile: 'p-3',
                tablet: 'p-3',
                desktop: 'p-4'
            })">
                <div class="flex items-center justify-between">
                    <div>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-xs font-medium text-gray-600 mb-1',
                            tablet: 'text-xs font-medium text-gray-600 mb-1',
                            desktop: 'text-sm font-medium text-gray-600 mb-1'
                        })">Items in Cart</p>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-lg font-bold text-orange-600',
                            tablet: 'text-xl font-bold text-orange-600',
                            desktop: 'text-2xl font-bold text-orange-600'
                        })">{{ selectedProducts.products.length }}</p>
                    </div>
                    <div :class="getResponsiveClasses({
                        mobile: 'bg-orange-100 p-2 rounded-full',
                        tablet: 'bg-orange-100 p-2.5 rounded-full',
                        desktop: 'bg-orange-100 p-3 rounded-full'
                    })">
                        <i :class="getResponsiveClasses({
                            mobile: 'pi pi-inbox text-orange-600 text-lg',
                            tablet: 'pi pi-inbox text-orange-600 text-lg',
                            desktop: 'pi pi-inbox text-orange-600 text-xl'
                        })"></i>
                    </div>
                </div>
            </BoxShadow>
        </div>
        <div :class="getResponsiveClasses({
            mobile: 'flex flex-col gap-4',
            tablet: 'flex flex-col gap-4',
            desktop: 'flex flex-wrap gap-4'
        })">
            <div :class="getResponsiveClasses({
                mobile: 'w-full',
                tablet: 'w-full',
                desktop: 'flex-1 min-w-0 lg:w-3/5'
            })">
                <BoxShadow>
                    <div :class="getResponsiveClasses({
                        mobile: 'px-3 py-3 border-b border-gray-200',
                        tablet: 'px-4 py-3 border-b border-gray-200',
                        desktop: 'px-5 py-4 border-b border-gray-200'
                    })">
                        <h2 :class="getResponsiveClasses({
                            mobile: 'text-base font-semibold text-gray-800',
                            tablet: 'text-lg font-semibold text-gray-800',
                            desktop: 'text-lg font-semibold text-gray-800'
                        })">Product Search</h2>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">Scan or enter product SKU</p>
                    </div>
                    <div :class="getResponsiveClasses({
                        mobile: 'p-3',
                        tablet: 'p-4',
                        desktop: 'p-5'
                    })">
                        <form id="search-form" @submit.prevent="searchProductSku()">
                            <label :class="getResponsiveClasses({
                                mobile: 'block text-sm font-medium text-gray-700 mb-2',
                                tablet: 'block text-sm font-medium text-gray-700 mb-2',
                                desktop: 'block text-sm font-medium text-gray-700 mb-2'
                            })">Search Product</label>
                            <InputGroup>
                                <InputGroupAddon>
                                    <i class="pi pi-search text-gray-400" />
                                </InputGroupAddon>
                                <InputText
                                    id="search"
                                    placeholder="Enter SKU or scan barcode"
                                    class="w-full"
                                    v-model="skuInput"
                                    autocomplete="off"
                                />
                            </InputGroup>
                        </form>

                    <!-- Search Results Display Area -->
                    <div v-if="searchedProduct" :class="getResponsiveClasses({
                        mobile: 'mt-4 rounded-lg border border-blue-200 bg-blue-50 p-3',
                        tablet: 'mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4',
                        desktop: 'mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4'
                    })">
                        <div :class="getResponsiveClasses({
                            mobile: 'flex flex-col gap-3',
                            tablet: 'flex items-start gap-4',
                            desktop: 'flex items-start gap-4'
                        })">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                <Avatar
                                    v-if="searchedProduct.product_image"
                                    shape="square"
                                    :image="UrlUtil.getBaseAppUrl(`storage/images/product/${searchedProduct.product_image}`)"
                                    :size="getResponsiveClasses({
                                        mobile: 'large',
                                        tablet: 'xlarge',
                                        desktop: 'xlarge'
                                    })"
                                    class="aspect-square!"
                                />
                                <Avatar
                                    v-else
                                    shape="square"
                                    icon="pi pi-camera"
                                    :size="getResponsiveClasses({
                                        mobile: 'large',
                                        tablet: 'xlarge',
                                        desktop: 'xlarge'
                                    })"
                                    class="aspect-square! bg-gray-200"
                                />
                            </div>

                            <!-- Product Details -->
                            <div class="flex-grow min-w-0">
                                <h3 :class="getResponsiveClasses({
                                    mobile: 'text-base font-semibold text-gray-800',
                                    tablet: 'text-lg font-semibold text-gray-800',
                                    desktop: 'text-lg font-semibold text-gray-800'
                                })">{{ searchedProduct.product_name }}</h3>
                                <p :class="getResponsiveClasses({
                                    mobile: 'text-xs text-gray-600',
                                    tablet: 'text-sm text-gray-600',
                                    desktop: 'text-sm text-gray-600'
                                })">SKU: {{ searchedProduct.sku }}</p>
                                <p :class="getResponsiveClasses({
                                    mobile: 'text-base font-bold text-blue-600',
                                    tablet: 'text-lg font-bold text-blue-600',
                                    desktop: 'text-lg font-bold text-blue-600'
                                })">{{ CurrencyUtil.formatCurrency(searchedProduct.product_price) }}</p>
                                <p :class="getResponsiveClasses({
                                    mobile: 'text-xs text-gray-600',
                                    tablet: 'text-sm text-gray-600',
                                    desktop: 'text-sm text-gray-600'
                                })">Available Stock: <span class="font-semibold">{{ searchedProduct.product_quantity }}</span></p>
                            </div>
                        </div>

                        <!-- Add to Cart Section - Mobile/Tablet Responsive -->
                        <div :class="getResponsiveClasses({
                            mobile: 'mt-4 pt-3 border-t border-blue-200',
                            tablet: 'mt-4 pt-3 border-t border-blue-200',
                            desktop: 'mt-0 pt-0 border-t-0'
                        })">
                            <div :class="getResponsiveClasses({
                                mobile: 'flex flex-col gap-3',
                                tablet: 'flex items-center justify-between gap-4',
                                desktop: 'flex flex-col items-end gap-2'
                            })">
                                <div :class="getResponsiveClasses({
                                    mobile: 'flex items-center justify-between',
                                    tablet: 'flex items-center gap-2',
                                    desktop: 'flex items-center gap-2'
                                })">
                                    <label for="search-quantity" :class="getResponsiveClasses({
                                        mobile: 'text-sm font-medium text-gray-700',
                                        tablet: 'text-sm font-medium text-gray-700 whitespace-nowrap',
                                        desktop: 'text-sm font-medium text-gray-700 whitespace-nowrap'
                                    })">Qty:</label>
                                    <div :class="getResponsiveClasses({
                                        mobile: 'w-40',
                                        tablet: 'w-40',
                                        desktop: 'w-32'
                                    })">
                                        <InputNumber
                                            id="search-quantity"
                                            v-model="searchQuantity"
                                            :min="1"
                                            showButtons
                                            buttonLayout="horizontal"
                                            fluid
                                            :pt="{
                                                input: { 
                                                    root: { 
                                                        class: getResponsiveClasses({
                                                            mobile: 'w-12 text-center',
                                                            tablet: 'w-12 text-center',
                                                            desktop: 'w-12 text-center'
                                                        })
                                                    } 
                                                }
                                            }"
                                        />
                                    </div>
                                </div>
                                <Button
                                    label="Add to Cart"
                                    icon="pi pi-shopping-cart"
                                    :class="[
                                        '!bg-green-600 hover:!bg-green-700',
                                        getResponsiveClasses({
                                            mobile: 'w-full',
                                            tablet: 'flex-shrink-0',
                                            desktop: 'flex-shrink-0'
                                        })
                                    ]"
                                    @click="addToCart()"
                                />
                            </div>
                        </div>
                    </div>

                    </div>
                </BoxShadow>

                <BoxShadow class="mt-4">
                    <div :class="getResponsiveClasses({
                        mobile: 'px-3 py-3 border-b border-gray-200',
                        tablet: 'px-4 py-3 border-b border-gray-200',
                        desktop: 'px-5 py-4 border-b border-gray-200'
                    })">
                        <h2 :class="getResponsiveClasses({
                            mobile: 'text-base font-semibold text-gray-800',
                            tablet: 'text-lg font-semibold text-gray-800',
                            desktop: 'text-lg font-semibold text-gray-800'
                        })">Selected Products</h2>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">Review and adjust quantities</p>
                    </div>
                    <div id="results">
                        <!-- Desktop DataTable -->
                        <DataTable
                            v-if="isDesktop"
                            :value="selectedProducts.products"
                            stripedRows
                            :loading="fetchProductSkuService.request.loading"
                            editMode="cell" 
                            @cell-edit-complete="onCellEditComplete"
                        >
                            <Column field="product.product_name" header="Product">
                                <template #body="{ data }">
                                    <div class="flex items-center gap-3 py-1">
                                        <Avatar
                                            v-if="data.product.product_image"
                                            shape="circle"
                                            :image="
                                                UrlUtil.getBaseAppUrl(
                                                    `storage/images/product/${data.product.product_image}`,
                                                )
                                            "
                                            class="w-10 h-10"
                                            size="large"
                                        />
                                        <Avatar
                                            v-else
                                            shape="circle"
                                            icon="pi pi-camera"
                                            class="bg-gray-100 text-gray-400"
                                            size="large"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900 truncate">
                                                {{ data.product.product_name }}
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            <Column field="product.sku" header="SKU">
                                <template #body="{ data }">
                                    <span class="text-sm font-mono text-gray-600">{{ data.product.sku }}</span>
                                </template>
                            </Column>
                            <Column field="product.price" header="Price">
                                <template #body="{ data }">
                                    <span class="text-sm font-semibold text-gray-900">{{ CurrencyUtil.formatCurrency(data.product.product_price) }}</span>
                                </template>
                            </Column>
                            <Column field="quantity" header="Quantity" style="width: 7rem; max-width: 7rem;">
                                <template #editor="{ data, field }">
                                    <InputNumber size="small" style="width: 5rem;" v-model="data[field]" :min="1" :max="data.product.product_quantity" autofocus fluid @focus="e => (e.target as HTMLInputElement).select()" />
                                </template>
                            </Column>
                            <Column header="Subtotal">
                                <template #body="{ data }">
                                    <span class="text-sm font-bold text-blue-600">{{ CurrencyUtil.formatCurrency(data.product.product_price * data.quantity) }}</span>
                                </template>
                            </Column>
                            <Column header="Actions">
                                <template #body="{ data }">
                                    <Button
                                        icon="pi pi-trash"
                                        text
                                        class="!text-red-600 hover:!bg-red-50"
                                        @click="removeFromCart(data.product.product_id)"
                                        v-tooltip.top="'Remove from cart'"
                                    />
                                </template>
                            </Column>
                            <template #empty>
                                <div class="text-center py-8">
                                    <i class="pi pi-shopping-cart text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 font-medium">No products selected</p>
                                    <p class="text-xs text-gray-400 mt-1">Search and add products to get started</p>
                                </div>
                            </template>
                        </DataTable>

                        <!-- Mobile/Tablet Card Layout -->
                        <div v-else>
                            <div v-if="selectedProducts.products.length === 0" class="text-center py-8">
                                <i class="pi pi-shopping-cart text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium">No products selected</p>
                                <p class="text-xs text-gray-400 mt-1">Search and add products to get started</p>
                            </div>
                            <div v-else :class="getResponsiveClasses({
                                mobile: 'space-y-3',
                                tablet: 'space-y-3',
                                desktop: 'space-y-4'
                            })">
                                <div 
                                    v-for="(item, index) in selectedProducts.products" 
                                    :key="item.product.product_id"
                                    :class="getResponsiveClasses({
                                        mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm',
                                        tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm',
                                        desktop: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm'
                                    })"
                                >
                                    <div class="flex items-start gap-3">
                                        <!-- Product Image -->
                                        <Avatar
                                            v-if="item.product.product_image"
                                            shape="square"
                                            :image="UrlUtil.getBaseAppUrl(`storage/images/product/${item.product.product_image}`)"
                                            :class="getResponsiveClasses({
                                                mobile: 'w-12 h-12 flex-shrink-0',
                                                tablet: 'w-14 h-14 flex-shrink-0',
                                                desktop: 'w-16 h-16 flex-shrink-0'
                                            })"
                                        />
                                        <Avatar
                                            v-else
                                            shape="square"
                                            icon="pi pi-camera"
                                            :class="[
                                                'bg-gray-100 text-gray-400 flex-shrink-0',
                                                getResponsiveClasses({
                                                    mobile: 'w-12 h-12',
                                                    tablet: 'w-14 h-14',
                                                    desktop: 'w-16 h-16'
                                                })
                                            ]"
                                        />
                                        
                                        <!-- Product Details -->
                                        <div class="flex-1 min-w-0">
                                            <h3 :class="getResponsiveClasses({
                                                mobile: 'font-semibold text-gray-900 text-sm truncate',
                                                tablet: 'font-semibold text-gray-900 text-base truncate',
                                                desktop: 'font-semibold text-gray-900 text-base truncate'
                                            })">{{ item.product.product_name }}</h3>
                                            <p :class="getResponsiveClasses({
                                                mobile: 'text-xs text-gray-600 font-mono',
                                                tablet: 'text-sm text-gray-600 font-mono',
                                                desktop: 'text-sm text-gray-600 font-mono'
                                            })">SKU: {{ item.product.sku }}</p>
                                            <p :class="getResponsiveClasses({
                                                mobile: 'text-sm font-bold text-blue-600 mt-1',
                                                tablet: 'text-base font-bold text-blue-600 mt-1',
                                                desktop: 'text-base font-bold text-blue-600 mt-1'
                                            })">{{ CurrencyUtil.formatCurrency(item.product.product_price) }}</p>
                                        </div>

                                        <!-- Remove Button -->
                                        <Button
                                            icon="pi pi-trash"
                                            text
                                            :class="[
                                                '!text-red-600 hover:!bg-red-50 flex-shrink-0',
                                                getResponsiveClasses({
                                                    mobile: '!p-2',
                                                    tablet: '!p-2',
                                                    desktop: '!p-2'
                                                })
                                            ]"
                                            @click="removeFromCart(item.product.product_id)"
                                        />
                                    </div>

                                    <!-- Quantity and Subtotal -->
                                    <div :class="getResponsiveClasses({
                                        mobile: 'flex flex-col gap-3 mt-3 pt-3 border-t border-gray-100',
                                        tablet: 'flex items-center justify-between mt-3 pt-3 border-t border-gray-100',
                                        desktop: 'flex items-center justify-between mt-4 pt-3 border-t border-gray-100'
                                    })">
                                        <div :class="getResponsiveClasses({
                                            mobile: 'flex items-center gap-2',
                                            tablet: 'flex items-center gap-2',
                                            desktop: 'flex items-center gap-2'
                                        })">
                                            <label :class="getResponsiveClasses({
                                                mobile: 'text-xs font-medium text-gray-700 flex-shrink-0',
                                                tablet: 'text-sm font-medium text-gray-700 flex-shrink-0',
                                                desktop: 'text-sm font-medium text-gray-700 flex-shrink-0'
                                            })">Qty:</label>
                                            <div :class="getResponsiveClasses({
                                                mobile: 'mr-4',
                                                tablet: 'mr-6',
                                                desktop: 'mr-0'
                                            })">
                                                <InputNumber
                                                    v-model="item.quantity"
                                                    :min="1"
                                                    :max="item.product.product_quantity"
                                                    showButtons
                                                    buttonLayout="horizontal"
                                                    :class="[
                                                        'quantity-selector',
                                                        getResponsiveClasses({
                                                            mobile: '!w-24',
                                                            tablet: '!w-28',
                                                            desktop: '!w-32'
                                                        })
                                                    ]"
                                                    :pt="{
                                                        input: { 
                                                            root: { 
                                                                class: getResponsiveClasses({
                                                                    mobile: '!w-8 text-center',
                                                                    tablet: '!w-10 text-center',
                                                                    desktop: '!w-12 text-center'
                                                                })
                                                            } 
                                                        }
                                                    }"
                                                    @update:modelValue="(newValue) => {
                                                        if (newValue && newValue > item.product.product_quantity) {
                                                            toast.error(`Quantity cannot exceed available stock (${item.product.product_quantity})`);
                                                            item.quantity = item.product.product_quantity;
                                                        }
                                                    }"
                                                />
                                            </div>
                                        </div>
                                        <div :class="getResponsiveClasses({
                                            mobile: 'flex items-center justify-between w-full',
                                            tablet: 'text-right flex-shrink-0',
                                            desktop: 'text-right'
                                        })">
                                            <p :class="getResponsiveClasses({
                                                mobile: 'text-xs text-gray-600',
                                                tablet: 'text-sm text-gray-600',
                                                desktop: 'text-sm text-gray-600'
                                            })">Subtotal:</p>
                                            <p :class="getResponsiveClasses({
                                                mobile: 'text-sm font-bold text-blue-600',
                                                tablet: 'text-base font-bold text-blue-600',
                                                desktop: 'text-base font-bold text-blue-600'
                                            })">{{ CurrencyUtil.formatCurrency(item.product.product_price * item.quantity) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </BoxShadow>
            </div>
            <div :class="getResponsiveClasses({
                mobile: 'w-full',
                tablet: 'w-full',
                desktop: 'w-full lg:w-2/5'
            })">
                <BoxShadow>
                    <div :class="getResponsiveClasses({
                        mobile: 'px-3 py-3 border-b border-gray-200',
                        tablet: 'px-4 py-3 border-b border-gray-200',
                        desktop: 'px-5 py-4 border-b border-gray-200'
                    })">
                        <div class="flex items-center justify-between">
                            <h3 :class="getResponsiveClasses({
                                mobile: 'text-base font-semibold text-gray-800',
                                tablet: 'text-lg font-semibold text-gray-800',
                                desktop: 'text-lg font-semibold text-gray-800'
                            })">Cart Summary</h3>
                            <span :class="getResponsiveClasses({
                                mobile: 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700',
                                tablet: 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700',
                                desktop: 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700'
                            })">
                                {{ selectedProducts.products.length }} Items
                            </span>
                        </div>
                    </div>
                    <div :class="getResponsiveClasses({
                        mobile: 'p-3',
                        tablet: 'p-4',
                        desktop: 'p-5'
                    })">
                        <div id="cart" class="flex flex-col gap-4">

                            <div>
                                <label :class="getResponsiveClasses({
                                    mobile: 'block text-sm font-medium text-gray-700 mb-2',
                                    tablet: 'block text-sm font-medium text-gray-700 mb-2',
                                    desktop: 'block text-sm font-medium text-gray-700 mb-2'
                                })">Customer Name</label>
                                <InputText
                                    v-model="form.customer_name"
                                    placeholder="Enter customer name"
                                    class="w-full"
                                    :invalid="errors.customer_name.length > 0"
                                />
                                <p v-if="errors.customer_name.length > 0" :class="getResponsiveClasses({
                                    mobile: 'text-xs text-red-600 mt-1',
                                    tablet: 'text-xs text-red-600 mt-1',
                                    desktop: 'text-xs text-red-600 mt-1'
                                })">{{ errors.customer_name[0] }}</p>
                            </div>

                            <div>
                                <label :class="getResponsiveClasses({
                                    mobile: 'block text-sm font-medium text-gray-700 mb-2',
                                    tablet: 'block text-sm font-medium text-gray-700 mb-2',
                                    desktop: 'block text-sm font-medium text-gray-700 mb-2'
                                })">Customer Address</label>
                                <Textarea
                                    v-model="form.customer_address"
                                    placeholder="Enter customer address"
                                    rows="2"
                                    class="w-full"
                                    :invalid="errors.customer_address.length > 0"
                                />
                                <p v-if="errors.customer_address.length > 0" :class="getResponsiveClasses({
                                    mobile: 'text-xs text-red-600 mt-1',
                                    tablet: 'text-xs text-red-600 mt-1',
                                    desktop: 'text-xs text-red-600 mt-1'
                                })">{{ errors.customer_address[0] }}</p>
                            </div>

                            <div>
                                <label :class="getResponsiveClasses({
                                    mobile: 'block text-sm font-medium text-gray-700 mb-2',
                                    tablet: 'block text-sm font-medium text-gray-700 mb-2',
                                    desktop: 'block text-sm font-medium text-gray-700 mb-2'
                                })">Prepared by</label>
                                <InputText
                                    v-model="form.prepared_by"
                                    placeholder="Enter preparer's full name"
                                    class="w-full"
                                    :invalid="errors.prepared_by.length > 0"
                                />
                                <p v-if="errors.prepared_by.length > 0" :class="getResponsiveClasses({
                                    mobile: 'text-xs text-red-600 mt-1',
                                    tablet: 'text-xs text-red-600 mt-1',
                                    desktop: 'text-xs text-red-600 mt-1'
                                })">{{ errors.prepared_by[0] }}</p>
                            </div>

                            <div>
                                <label :class="getResponsiveClasses({
                                    mobile: 'block text-sm font-medium text-gray-700 mb-2',
                                    tablet: 'block text-sm font-medium text-gray-700 mb-2',
                                    desktop: 'block text-sm font-medium text-gray-700 mb-2'
                                })">Payment Method</label>
                                <Select
                                    :options="[
                                        { label: 'Cash', value: 'Cash' },
                                        { label: 'E-Wallet', value: 'E-wallet' },
                                        { label: 'Installment', value: 'Installment' },
                                    ]"
                                    placeholder="Select Payment Method"
                                    id="payment-method"
                                    option-label="label"
                                    option-value="value"
                                    v-model="form.payment_method"
                                    :invalid="errors.payment_method.length > 0"
                                    class="w-full"
                                />
                                <p v-if="errors.payment_method.length > 0" :class="getResponsiveClasses({
                                    mobile: 'text-xs text-red-600 mt-1',
                                    tablet: 'text-xs text-red-600 mt-1',
                                    desktop: 'text-xs text-red-600 mt-1'
                                })">{{ errors.payment_method[0] }}</p>
                            </div>
                            
                            <div :class="getResponsiveClasses({
                                mobile: 'pt-3 border-t border-gray-200',
                                tablet: 'pt-4 border-t border-gray-200',
                                desktop: 'pt-4 border-t border-gray-200'
                            })">
                                <div :class="getResponsiveClasses({
                                    mobile: 'flex justify-between items-center mb-3',
                                    tablet: 'flex justify-between items-center mb-4',
                                    desktop: 'flex justify-between items-center mb-4'
                                })">
                                    <span :class="getResponsiveClasses({
                                        mobile: 'text-sm font-semibold text-gray-900',
                                        tablet: 'text-base font-semibold text-gray-900',
                                        desktop: 'text-base font-semibold text-gray-900'
                                    })">Total Amount:</span>
                                    <span id="cart-total" :class="getResponsiveClasses({
                                        mobile: 'text-lg font-bold text-blue-600',
                                        tablet: 'text-xl font-bold text-blue-600',
                                        desktop: 'text-2xl font-bold text-blue-600'
                                    })">
                                        {{ CurrencyUtil.formatCurrency(cartTotal) }}
                                    </span>
                                </div>
                                <Button
                                    type="button"
                                    :label="getResponsiveClasses({
                                        mobile: 'Record Order',
                                        tablet: 'Record Order',
                                        desktop: 'Record Order'
                                    })"
                                    icon="pi pi-check"
                                    :class="[
                                        'w-full !bg-blue-600 hover:!bg-blue-700',
                                        getResponsiveClasses({
                                            mobile: 'text-sm py-3',
                                            tablet: 'text-base py-3',
                                            desktop: 'text-base py-3'
                                        })
                                    ]"
                                    @click="handleSubmit()"
                                    :loading="submitService.request.loading"
                                />
                            </div>
                        </div>
                    </div>
                </BoxShadow>
            </div>
        </div>

        <!-- Walk-In Transaction Section -->
        <div :class="getResponsiveClasses({
            mobile: 'mt-4 rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            tablet: 'mt-4 rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            desktop: 'mt-4 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden'
        })">
            <div :class="getResponsiveClasses({
                mobile: 'flex items-center justify-between px-3 py-3 border-b border-gray-200 bg-gray-50',
                tablet: 'flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50',
                desktop: 'flex items-center justify-between px-4 sm:px-5 py-4 border-b border-gray-200 bg-gray-50'
            })">
                <div>
                    <h2 :class="getResponsiveClasses({
                        mobile: 'text-sm font-bold text-gray-800',
                        tablet: 'text-base font-bold text-gray-800',
                        desktop: 'text-base sm:text-lg font-bold text-gray-800'
                    })">
                        Walk-In Transaction
                    </h2>
                    <p :class="getResponsiveClasses({
                        mobile: 'text-xs text-gray-500 mt-0.5',
                        tablet: 'text-xs text-gray-500 mt-0.5',
                        desktop: 'text-xs text-gray-500 mt-0.5'
                    })">View all walk-in POS transactions</p>
                </div>
                <div :class="getResponsiveClasses({
                    mobile: 'flex items-center gap-1 text-xs text-gray-600 bg-white px-2 py-1 rounded-full border border-gray-200',
                    tablet: 'flex items-center gap-2 text-sm text-gray-600 bg-white px-2.5 py-1.5 rounded-full border border-gray-200',
                    desktop: 'flex items-center gap-2 text-sm text-gray-600 bg-white px-3 py-1.5 rounded-full border border-gray-200'
                })">
                    <i class="pi pi-shop text-blue-600"></i>
                    <span class="font-semibold">{{ salesData.length }}</span>
                </div>
            </div>
            
            <!-- Desktop DataTable -->
            <DataTable 
                v-if="isDesktop"
                class="w-full"
                :value="salesData" 
                :loading="isSalesLoading"
                columnResizeMode="expand"
                :paginator="salesData.length > 10"
                :rows="10"
                stripedRows
                @row-click="onSaleRowClick"
                :pt="{
                    bodyRow: { class: 'cursor-pointer hover:bg-blue-50 transition-colors' }
                }"
            >
                <Column field="sale_code" header="Transaction Code" :sortable="true">
                    <template #body="{ data }">
                        <span class="text-xs font-medium text-gray-700">{{ data.sale_code }}</span>
                    </template>
                </Column>
                <Column header="Product Name" style="min-width: 200px">
                    <template #body="{ data }">
                        <div class="flex flex-col gap-1">
                            <span v-for="(item, index) in data.sale_items" :key="index" class="text-sm text-gray-700">
                                {{ item.product.product_name }} <span class="text-xs text-gray-500">(x{{ item.quantity }})</span>
                            </span>
                        </div>
                    </template>
                </Column>
                <Column field="customer_name" header="Customer Name" :sortable="true" style="min-width: 150px">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-1">
                            <Avatar 
                                shape="circle" 
                                icon="pi pi-user"
                                class="bg-blue-100 text-blue-600"
                                size="large"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 truncate">
                                    {{ data.customer_name }}
                                </div>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column field="customer_address" header="Customer Address" :sortable="true" style="max-width: 200px">
                    <template #body="{ data }">
                        <div class="text-sm text-gray-600 truncate" :title="data.customer_address || 'N/A'">
                            {{ data.customer_address || 'N/A' }}
                        </div>
                    </template>
                </Column>
                <Column field="prepared_by" header="Prepared By" :sortable="true">
                    <template #body="{ data }">
                        <span class="text-sm text-gray-700">{{ data.prepared_by }}</span>
                    </template>
                </Column>
                <Column field="payment_method" header="Payment Method" :sortable="true">
                    <template #body="{ data }">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                            'bg-green-50 text-green-700': data.payment_method === 'Cash',
                            'bg-blue-50 text-blue-700': data.payment_method === 'E-wallet',
                            'bg-purple-50 text-purple-700': data.payment_method === 'Installment'
                        }">
                            <i :class="data.payment_method === 'Cash' ? 'pi pi-money-bill' : data.payment_method === 'E-wallet' ? 'pi pi-wallet' : 'pi pi-credit-card'" class="mr-1"></i>
                            {{ data.payment_method }}
                        </span>
                    </template>
                </Column>
                <Column field="total_amount" header="Total Amount" :sortable="true">
                    <template #body="{ data }">
                        <span class="font-semibold text-gray-900">
                            {{ CurrencyUtil.formatCurrency(data.total_amount) }}
                        </span>
                    </template>
                </Column>
                <template #empty>
                    <div class="text-center py-12">
                        <div class="mb-4">
                            <div class="mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="pi pi-shop text-4xl text-gray-400"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No Walk-In Sales Yet</h3>
                        <p class="text-sm text-gray-500">Walk-in sales transactions will appear here</p>
                    </div>
                </template>
            </DataTable>

            <!-- Mobile/Tablet Card Layout -->
            <div v-else>
                <div v-if="salesData.length === 0" :class="getResponsiveClasses({
                    mobile: 'text-center py-8',
                    tablet: 'text-center py-10',
                    desktop: 'text-center py-12'
                })">
                    <div class="mb-4">
                        <div :class="getResponsiveClasses({
                            mobile: 'mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center',
                            tablet: 'mx-auto w-18 h-18 rounded-full bg-gray-100 flex items-center justify-center',
                            desktop: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center'
                        })">
                            <i :class="getResponsiveClasses({
                                mobile: 'pi pi-shop text-3xl text-gray-400',
                                tablet: 'pi pi-shop text-3xl text-gray-400',
                                desktop: 'pi pi-shop text-4xl text-gray-400'
                            })"></i>
                        </div>
                    </div>
                    <h3 :class="getResponsiveClasses({
                        mobile: 'text-base font-semibold text-gray-700 mb-2',
                        tablet: 'text-lg font-semibold text-gray-700 mb-2',
                        desktop: 'text-lg font-semibold text-gray-700 mb-2'
                    })">No Walk-In Sales Yet</h3>
                    <p :class="getResponsiveClasses({
                        mobile: 'text-xs text-gray-500',
                        tablet: 'text-sm text-gray-500',
                        desktop: 'text-sm text-gray-500'
                    })">Walk-in sales transactions will appear here</p>
                </div>
                <div v-else :class="getResponsiveClasses({
                    mobile: 'p-3 space-y-3',
                    tablet: 'p-4 space-y-4',
                    desktop: 'p-4 space-y-4'
                })">
                    <div 
                        v-for="sale in salesData" 
                        :key="sale.sale_id"
                        @click="onSaleRowClick({ data: sale })"
                        :class="getResponsiveClasses({
                            mobile: 'bg-gray-50 border border-gray-200 rounded-lg p-3 cursor-pointer hover:bg-blue-50 transition-colors',
                            tablet: 'bg-gray-50 border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-blue-50 transition-colors',
                            desktop: 'bg-gray-50 border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-blue-50 transition-colors'
                        })"
                    >
                        <!-- Header Row -->
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 :class="getResponsiveClasses({
                                    mobile: 'text-sm font-semibold text-gray-900',
                                    tablet: 'text-base font-semibold text-gray-900',
                                    desktop: 'text-base font-semibold text-gray-900'
                                })">{{ sale.sale_code }}</h3>
                                <p :class="getResponsiveClasses({
                                    mobile: 'text-xs text-gray-600',
                                    tablet: 'text-sm text-gray-600',
                                    desktop: 'text-sm text-gray-600'
                                })">{{ new Date(sale.created_at).toLocaleDateString() }}</p>
                            </div>
                            <span :class="[
                                'inline-flex items-center rounded-full font-medium',
                                getResponsiveClasses({
                                    mobile: 'px-2 py-1 text-xs',
                                    tablet: 'px-2.5 py-1 text-xs',
                                    desktop: 'px-2.5 py-1 text-xs'
                                }),
                                {
                                    'bg-green-50 text-green-700': sale.payment_method === 'Cash',
                                    'bg-blue-50 text-blue-700': sale.payment_method === 'E-wallet',
                                    'bg-purple-50 text-purple-700': sale.payment_method === 'Installment'
                                }
                            ]">
                                <i :class="[
                                    sale.payment_method === 'Cash' ? 'pi pi-money-bill' : 
                                    sale.payment_method === 'E-wallet' ? 'pi pi-wallet' : 'pi pi-credit-card',
                                    'mr-1'
                                ]"></i>
                                {{ sale.payment_method }}
                            </span>
                        </div>

                        <!-- Customer Info -->
                        <div class="flex items-center gap-3 mb-3">
                            <Avatar 
                                shape="circle" 
                                icon="pi pi-user"
                                :class="[
                                    'bg-blue-100 text-blue-600',
                                    getResponsiveClasses({
                                        mobile: 'w-8 h-8',
                                        tablet: 'w-10 h-10',
                                        desktop: 'w-10 h-10'
                                    })
                                ]"
                            />
                            <div class="flex-1 min-w-0">
                                <p :class="getResponsiveClasses({
                                    mobile: 'text-sm font-medium text-gray-900 truncate',
                                    tablet: 'text-base font-medium text-gray-900 truncate',
                                    desktop: 'text-base font-medium text-gray-900 truncate'
                                })">{{ sale.customer_name }}</p>
                                <p :class="getResponsiveClasses({
                                    mobile: 'text-xs text-gray-600 truncate',
                                    tablet: 'text-sm text-gray-600 truncate',
                                    desktop: 'text-sm text-gray-600 truncate'
                                })">{{ sale.customer_address || 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Products -->
                        <div class="mb-3">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-xs font-medium text-gray-700 mb-1',
                                tablet: 'text-sm font-medium text-gray-700 mb-1',
                                desktop: 'text-sm font-medium text-gray-700 mb-1'
                            })">Products:</p>
                            <div :class="getResponsiveClasses({
                                mobile: 'space-y-1',
                                tablet: 'space-y-1',
                                desktop: 'space-y-1'
                            })">
                                <p v-for="(item, index) in sale.sale_items" :key="index" :class="getResponsiveClasses({
                                    mobile: 'text-xs text-gray-600',
                                    tablet: 'text-sm text-gray-600',
                                    desktop: 'text-sm text-gray-600'
                                })">
                                    {{ item.product.product_name }} <span class="text-gray-500">(x{{ item.quantity }})</span>
                                </p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-xs text-gray-600',
                                tablet: 'text-sm text-gray-600',
                                desktop: 'text-sm text-gray-600'
                            })">Prepared by: {{ sale.prepared_by }}</p>
                            <p :class="getResponsiveClasses({
                                mobile: 'text-sm font-bold text-blue-600',
                                tablet: 'text-base font-bold text-blue-600',
                                desktop: 'text-base font-bold text-blue-600'
                            })">{{ CurrencyUtil.formatCurrency(sale.total_amount) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sale Details Modal -->
        <Dialog
            v-model:visible="viewSaleModal"
            header="Sale Details"
            :style="{ width: '90%' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
            :pt="{
                header: { class: '!bg-blue-600 !text-white' },
                closeButton: { class: '!text-white hover:!bg-blue-700 !border-white' },
                closeButtonIcon: { class: '!text-white' }
            }"
        >
            <div v-if="selectedSale">
                <!-- Sale Information Card -->
                <div :class="getResponsiveClasses({
                    mobile: 'mb-3 border border-gray-200 rounded-lg overflow-hidden bg-white',
                    tablet: 'mb-4 border border-gray-200 rounded-lg overflow-hidden bg-white',
                    desktop: 'mb-4 border border-gray-200 rounded-lg overflow-hidden bg-white'
                })">
                    <div :class="getResponsiveClasses({
                        mobile: 'p-3 space-y-3',
                        tablet: 'p-4 grid grid-cols-2 gap-4',
                        desktop: 'flex flex-wrap p-4 gap-4'
                    })">
                        <div :class="getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'col-span-1',
                            desktop: 'flex-1 min-w-[200px]'
                        })">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-xs font-medium text-gray-700 mb-1',
                                tablet: 'text-xs font-medium text-gray-700 mb-1',
                                desktop: 'text-xs font-medium text-gray-700 mb-1'
                            })">Sale Code</p>
                            <p :class="getResponsiveClasses({
                                mobile: 'text-sm font-semibold text-gray-900',
                                tablet: 'text-sm font-semibold text-gray-900',
                                desktop: 'text-sm font-semibold text-gray-900'
                            })">{{ selectedSale.sale_code }}</p>
                        </div>
                        <div :class="getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'col-span-1',
                            desktop: 'flex-1 min-w-[200px]'
                        })">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-xs font-medium text-gray-700 mb-1',
                                tablet: 'text-xs font-medium text-gray-700 mb-1',
                                desktop: 'text-xs font-medium text-gray-700 mb-1'
                            })">Sale Date</p>
                            <p :class="getResponsiveClasses({
                                mobile: 'text-sm text-gray-900',
                                tablet: 'text-sm text-gray-900',
                                desktop: 'text-sm text-gray-900'
                            })">{{ new Date(selectedSale.created_at).toLocaleDateString() }}</p>
                        </div>
                        <div :class="getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'col-span-1',
                            desktop: 'flex-1 min-w-[200px]'
                        })">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-xs font-medium text-gray-700 mb-1',
                                tablet: 'text-xs font-medium text-gray-700 mb-1',
                                desktop: 'text-xs font-medium text-gray-700 mb-1'
                            })">Customer Name</p>
                            <p :class="getResponsiveClasses({
                                mobile: 'text-sm text-gray-900',
                                tablet: 'text-sm text-gray-900',
                                desktop: 'text-sm text-gray-900'
                            })">{{ selectedSale.customer_name }}</p>
                        </div>
                        <div :class="getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'col-span-1',
                            desktop: 'flex-1 min-w-[200px]'
                        })">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-xs font-medium text-gray-700 mb-1',
                                tablet: 'text-xs font-medium text-gray-700 mb-1',
                                desktop: 'text-xs font-medium text-gray-700 mb-1'
                            })">Customer Address</p>
                            <p :class="getResponsiveClasses({
                                mobile: 'text-sm text-gray-900',
                                tablet: 'text-sm text-gray-900',
                                desktop: 'text-sm text-gray-900'
                            })">{{ selectedSale.customer_address || 'N/A' }}</p>
                        </div>
                        <div :class="getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'col-span-1',
                            desktop: 'flex-1 min-w-[200px]'
                        })">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-xs font-medium text-gray-700 mb-1',
                                tablet: 'text-xs font-medium text-gray-700 mb-1',
                                desktop: 'text-xs font-medium text-gray-700 mb-1'
                            })">Prepared By</p>
                            <p :class="getResponsiveClasses({
                                mobile: 'text-sm text-gray-900',
                                tablet: 'text-sm text-gray-900',
                                desktop: 'text-sm text-gray-900'
                            })">{{ selectedSale.prepared_by }}</p>
                        </div>
                        <div :class="getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'col-span-2',
                            desktop: 'flex-1 min-w-[200px]'
                        })">
                            <p :class="getResponsiveClasses({
                                mobile: 'text-xs font-medium text-gray-700 mb-1',
                                tablet: 'text-xs font-medium text-gray-700 mb-1',
                                desktop: 'text-xs font-medium text-gray-700 mb-1'
                            })">Payment Method</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                                'bg-green-50 text-green-700': selectedSale.payment_method === 'Cash',
                                'bg-blue-50 text-blue-700': selectedSale.payment_method === 'E-wallet',
                                'bg-purple-50 text-purple-700': selectedSale.payment_method === 'Installment'
                            }">
                                <i :class="selectedSale.payment_method === 'Cash' ? 'pi pi-money-bill' : selectedSale.payment_method === 'E-wallet' ? 'pi pi-wallet' : 'pi pi-credit-card'" class="mr-1"></i>
                                {{ selectedSale.payment_method }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sale Items Section -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div :class="getResponsiveClasses({
                        mobile: 'px-3 py-2 bg-gray-50 border-b border-gray-200',
                        tablet: 'px-4 py-3 bg-gray-50 border-b border-gray-200',
                        desktop: 'px-4 py-3 bg-gray-50 border-b border-gray-200'
                    })">
                        <h3 :class="getResponsiveClasses({
                            mobile: 'text-sm font-semibold text-gray-900',
                            tablet: 'text-sm font-semibold text-gray-900',
                            desktop: 'text-sm font-semibold text-gray-900'
                        })">Sale Items</h3>
                    </div>
                    
                    <!-- Desktop DataTable -->
                    <DataTable
                        v-if="isDesktop"
                        :value="selectedSale.sale_items"
                        stripedRows
                    >
                        <Column field="product.product_name" header="Product">
                            <template #body="{ data }">
                                <div class="flex items-center gap-3 py-1">
                                    <Avatar
                                        v-if="data.product.product_image"
                                        shape="circle"
                                        :image="UrlUtil.getBaseAppUrl(`storage/images/product/${data.product.product_image}`)"
                                        class="w-10 h-10"
                                        size="large"
                                    />
                                    <Avatar
                                        v-else
                                        shape="circle"
                                        icon="pi pi-camera"
                                        class="bg-gray-100 text-gray-400"
                                        size="large"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-gray-900 truncate">
                                            {{ data.product.product_name }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Column>
                        <Column header="Price" field="price">
                            <template #body="{ data }">
                                <span class="text-sm text-gray-900">{{ CurrencyUtil.formatCurrency(data.price) }}</span>
                            </template>
                        </Column>
                        <Column header="Quantity" field="quantity">
                            <template #body="{ data }">
                                <span class="text-sm font-medium text-gray-900">{{ data.quantity }}</span>
                            </template>
                        </Column>
                        <Column header="Subtotal">
                            <template #body="{ data }">
                                <span class="text-sm font-semibold text-gray-900">{{ CurrencyUtil.formatCurrency(data.price * data.quantity) }}</span>
                            </template>
                        </Column>
                        <template #footer>
                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                <div class="space-y-2 min-w-[250px]">
                                    <div class="flex justify-between text-base pt-2">
                                        <span class="font-semibold text-gray-900">Total:</span>
                                        <span class="font-bold text-blue-600 text-lg">
                                            {{ CurrencyUtil.formatCurrency(selectedSale.total_amount) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </DataTable>

                    <!-- Mobile/Tablet Card Layout -->
                    <div v-else>
                        <div :class="getResponsiveClasses({
                            mobile: 'space-y-3 p-3',
                            tablet: 'space-y-3 p-4',
                            desktop: 'space-y-4 p-4'
                        })">
                            <div 
                                v-for="(item, index) in selectedSale.sale_items" 
                                :key="index"
                                :class="getResponsiveClasses({
                                    mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm',
                                    tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm',
                                    desktop: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm'
                                })"
                            >
                                <div class="flex items-start gap-3">
                                    <!-- Product Image -->
                                    <Avatar
                                        v-if="item.product.product_image"
                                        shape="square"
                                        :image="UrlUtil.getBaseAppUrl(`storage/images/product/${item.product.product_image}`)"
                                        :class="getResponsiveClasses({
                                            mobile: 'w-12 h-12 flex-shrink-0',
                                            tablet: 'w-14 h-14 flex-shrink-0',
                                            desktop: 'w-16 h-16 flex-shrink-0'
                                        })"
                                    />
                                    <Avatar
                                        v-else
                                        shape="square"
                                        icon="pi pi-camera"
                                        :class="[
                                            'bg-gray-100 text-gray-400 flex-shrink-0',
                                            getResponsiveClasses({
                                                mobile: 'w-12 h-12',
                                                tablet: 'w-14 h-14',
                                                desktop: 'w-16 h-16'
                                            })
                                        ]"
                                    />
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 :class="getResponsiveClasses({
                                            mobile: 'font-semibold text-gray-900 text-sm truncate',
                                            tablet: 'font-semibold text-gray-900 text-base truncate',
                                            desktop: 'font-semibold text-gray-900 text-base truncate'
                                        })">{{ item.product.product_name }}</h3>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-sm font-bold text-blue-600 mt-1',
                                            tablet: 'text-base font-bold text-blue-600 mt-1',
                                            desktop: 'text-base font-bold text-blue-600 mt-1'
                                        })">{{ CurrencyUtil.formatCurrency(item.price) }}</p>
                                    </div>
                                </div>

                                <!-- Quantity and Subtotal -->
                                <div :class="getResponsiveClasses({
                                    mobile: 'flex items-center justify-between mt-3 pt-3 border-t border-gray-100',
                                    tablet: 'flex items-center justify-between mt-3 pt-3 border-t border-gray-100',
                                    desktop: 'flex items-center justify-between mt-4 pt-3 border-t border-gray-100'
                                })">
                                    <div>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600',
                                            tablet: 'text-sm text-gray-600',
                                            desktop: 'text-sm text-gray-600'
                                        })">Quantity:</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-sm font-semibold text-gray-900',
                                            tablet: 'text-base font-semibold text-gray-900',
                                            desktop: 'text-base font-semibold text-gray-900'
                                        })">{{ item.quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600',
                                            tablet: 'text-sm text-gray-600',
                                            desktop: 'text-sm text-gray-600'
                                        })">Subtotal:</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-sm font-bold text-blue-600',
                                            tablet: 'text-base font-bold text-blue-600',
                                            desktop: 'text-base font-bold text-blue-600'
                                        })">{{ CurrencyUtil.formatCurrency(item.price * item.quantity) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Section for Mobile/Tablet -->
                        <div :class="getResponsiveClasses({
                            mobile: 'border-t border-gray-200 p-3 bg-gray-50',
                            tablet: 'border-t border-gray-200 p-4 bg-gray-50',
                            desktop: 'border-t border-gray-200 p-4 bg-gray-50'
                        })">
                            <div class="flex justify-between items-center">
                                <span :class="getResponsiveClasses({
                                    mobile: 'text-base font-semibold text-gray-900',
                                    tablet: 'text-lg font-semibold text-gray-900',
                                    desktop: 'text-lg font-semibold text-gray-900'
                                })">Total:</span>
                                <span :class="getResponsiveClasses({
                                    mobile: 'text-lg font-bold text-blue-600',
                                    tablet: 'text-xl font-bold text-blue-600',
                                    desktop: 'text-xl font-bold text-blue-600'
                                })">
                                    {{ CurrencyUtil.formatCurrency(selectedSale.total_amount) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import { ISale } from '@/interfaces/ISale';
import { ProductInterface } from '@/interfaces/ProductInterface';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import UrlUtil from '@/utils/UrlUtil';
import DateTimeDisplay from '@/components/DateTimeDisplay.vue';
import { DataTableCellEditCompleteEvent } from 'primevue';
import { computed, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useResponsive } from '@/composables/useResponsive';

interface IForm {
    products: {
        product_id: number;
        quantity: number;
    }[];
    customer_name: string | null;
    customer_address: string | null;
    prepared_by: string | null;
    payment_method: string | null;
}

interface IFormError {
    customer_name: string[];
    customer_address: string[];
    prepared_by: string[];
    payment_method: string[];
}

const form : IForm = reactive({
    products: [],
    customer_name: null,
    customer_address: null,
    prepared_by: null,
    payment_method: null,
});

const errors : IFormError = reactive({
    customer_name: [],
    customer_address: [],
    prepared_by: [],
    payment_method: [],
});

const selectedProducts : {
    products: {
        product: ProductInterface;
        quantity: number;
    }[]
} = reactive({
    products: [],
});

// Search state - separate from cart state
const searchedProduct = ref<ProductInterface | null>(null);
const isSearching = ref<boolean>(false);
const searchQuantity = ref<number>(1);

const toast = useToast();
const fetchProductSkuService = useAxiosUtil<null, ProductInterface>();
const skuInput = ref<string | null>(null);

// Use responsive composable
const responsive = useResponsive();
const { 
    isMobile, 
    isTablet, 
    isDesktop, 
    shouldUseCardLayout 
} = responsive;
const getResponsiveClasses = responsive.getResponsiveClasses;

// Daily statistics state
interface DailyStats {
    total_sales: number;
    total_transactions: number;
}

const dailyStats = reactive<DailyStats>({
    total_sales: 0,
    total_transactions: 0,
});

const statsService = useAxiosUtil<null, DailyStats>();

// Function to fetch today's statistics
const fetchDailyStats = async () => {
    try {
        await statsService.get("admin/sales/today-stats");
        
        if (statsService.request.status === 200 && statsService.request.data) {
            dailyStats.total_sales = statsService.request.data.total_sales;
            dailyStats.total_transactions = statsService.request.data.total_transactions;
        }
    } catch (error) {
        // Silently fail - keep showing 0 values
        console.error("Failed to fetch daily stats:", error);
    }
};

// Computed property for cart total calculation
const cartTotal = computed(() => {
    return selectedProducts.products.reduce(
        (total, product) => total + product.product.product_price * product.quantity, 
        0
    );
});

const searchProductSku = async () => {
    const sku = skuInput.value?.trim();
    
    // Clear previous search results before new search
    searchedProduct.value = null;
    
    // If SKU is empty, just clear search results and return (Requirement 1.5)
    if (!sku) {
        return;
    }

    isSearching.value = true;

    try {
        await fetchProductSkuService.get("admin/products/sku-search/" + sku);
        
        if (fetchProductSkuService.request.status === 200 && fetchProductSkuService.request.data) {
            // Only populate search results, do NOT add to cart (Requirement 1.1)
            searchedProduct.value = fetchProductSkuService.request.data;
        } else {
            // Handle product not found errors (Requirement 1.3)
            toast.error(
                fetchProductSkuService.request.message ??
                    "Product not found",
            );
        }
    } catch (error) {
        // Handle any API errors
        toast.error("Failed to search product. Please try again.");
    } finally {
        isSearching.value = false;
    }
};

const addToCart = () => {
    if (!searchedProduct.value) return;

    // Validate quantity is a positive number
    if (!searchQuantity.value || searchQuantity.value < 1) {
        toast.error('Quantity must be at least 1');
        return;
    }

    // Validate quantity on add to cart
    if (searchQuantity.value > searchedProduct.value.product_quantity) {
        toast.error(`Quantity cannot exceed available stock (${searchedProduct.value.product_quantity})`);
        return;
    }

    // Check if product already exists in cart
    const existingProduct = selectedProducts.products.find(
        (item) => item.product.product_id === searchedProduct.value!.product_id
    );

    if (existingProduct) {
        // Calculate new total quantity
        const newTotalQuantity = existingProduct.quantity + searchQuantity.value;
        
        // Validate total quantity doesn't exceed available stock
        if (newTotalQuantity > searchedProduct.value.product_quantity) {
            toast.error(`Total quantity would exceed available stock (${searchedProduct.value.product_quantity})`);
            return;
        }
        
        // Increment quantity by the specified amount
        existingProduct.quantity = newTotalQuantity;
    } else {
        // Add new product to cart with specified quantity
        selectedProducts.products.push({
            product: searchedProduct.value,
            quantity: searchQuantity.value
        });
    }

    // Show success toast
    toast.success(`Added ${searchQuantity.value} ${searchedProduct.value.product_name} to cart`);

    // Clear search results and input
    searchedProduct.value = null;
    skuInput.value = null;
    searchQuantity.value = 1;

    // Focus search input for next product
    const searchInput = document.getElementById('search') as HTMLInputElement;
    if (searchInput) {
        searchInput.focus();
        searchInput.select();
    }
};

const removeFromCart = (productId: number) => {
    // Find the product in the cart
    const productIndex = selectedProducts.products.findIndex(
        (item) => item.product.product_id === productId
    );

    if (productIndex !== -1) {
        const removedProduct = selectedProducts.products[productIndex];
        
        // Remove product from selectedProducts array
        selectedProducts.products.splice(productIndex, 1);
        
        // Show toast notification
        toast.success(`Removed ${removedProduct.product.product_name} from cart`);
    }
};

const onCellEditComplete = (event: DataTableCellEditCompleteEvent) => {
    let { data, newValue, field } = event;

    if (field === 'quantity') {
        const product = selectedProducts.products.find((product) => product.product.product_id === data.product.product_id);
        if (product) {
            // Validate quantity does not exceed available stock
            if (newValue && newValue > product.product.product_quantity) {
                // Show error toast if validation fails
                toast.error(`Quantity cannot exceed available stock (${product.product.product_quantity})`);
                // Revert to previous valid quantity - do nothing, keep old value
                return;
            }
            
            // Update quantity if valid
            product.quantity = newValue ? newValue : product.quantity;
        }
    }
};

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearErrors();
    let noError = true;

    form.products = [];

    if (selectedProducts.products.length === 0) {
        noError = false;
        toast.error("Please select at least one product");
        return noError;
    }

    if (!form.customer_name) {
        noError = false;
        errors.customer_name.push("Customer name is required");
    }

    if (!form.customer_address) {
        noError = false;
        errors.customer_address.push("Customer address is required");
    }

    if (!form.prepared_by) {
        noError = false;
        errors.prepared_by.push("Prepared by is required");
    }

    if (!form.payment_method) {
        noError = false;
        errors.payment_method.push("Payment method is required");
    }

    if (!noError) {
        toast.error("Please fill in all required fields");
        return false;
    }
    
    selectedProducts.products.forEach((product) => {
        form.products.push({
            product_id: product.product.product_id,
            quantity: product.quantity
        });
    });

    return form;
};

const submitService = useAxiosUtil<IForm, ISale>();
const router = useRouter();

const handleSubmit = async () => {
    const validated = validate();
    if (validated) {
        await submitService.post("admin/sales", validated);
        
        if (submitService.request.status === 200 && submitService.request.data) {
            clearErrors();
            toast.success(submitService.request.message);
            
            // Refresh daily statistics after successful sale
            await fetchDailyStats();
            
            await router.push({ name: "admin.pos.find", params: { id: submitService.request.data.sale_id } });
        }
        else {
            toast.error(submitService.request.message ?? "Please try again");
            if (submitService.request.errors) {
                Object.keys(submitService.request.errors).forEach((key) => {
                    errors[key] = submitService.request.errors[key];
                });
            }
        }
    }
}

// Walk-In Sales History
const salesData = ref<ISale[]>([]);
const loadSalesService = useAxiosUtil<null, ISale[]>();
const isSalesLoading = ref(false);
const selectedSale = ref<ISale | null>(null);
const viewSaleModal = ref(false);

const loadSalesHistory = async () => {
    isSalesLoading.value = true;
    await loadSalesService.get("admin/sales").then(() => {
        if (loadSalesService.request.status === 200 && loadSalesService.request.data) {
            // Filter sales to only show today's transactions
            const today = new Date().toISOString().split('T')[0];
            salesData.value = loadSalesService.request.data.filter((sale: ISale) => {
                const saleDate = new Date(sale.created_at).toISOString().split('T')[0];
                return saleDate === today;
            });
        } else {
            toast.error(loadSalesService.request.message ?? "Failed to load sales history");
        }
    }).finally(() => {
        isSalesLoading.value = false;
    });
};

const onSaleRowClick = (event: any) => {
    selectedSale.value = event.data;
    viewSaleModal.value = true;
};

// Load daily statistics and sales history when component mounts
onMounted(() => {
    fetchDailyStats();
    loadSalesHistory();
});

</script>
