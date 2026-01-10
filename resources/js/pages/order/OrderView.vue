<template>
    <div :class="getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-3 px-3',
        tablet: 'min-h-screen bg-gray-50 py-4 px-4',
        desktop: 'min-h-screen bg-gray-50 py-4 sm:py-6 px-3 sm:px-6'
    })">
        <!-- Date and Time Display -->
        <DateTimeDisplay />

        <!-- Filters Card -->
        <div :class="getResponsiveClasses({
            mobile: 'mb-3 rounded-lg bg-white border border-gray-200 shadow-sm',
            tablet: 'mb-4 rounded-lg bg-white border border-gray-200 shadow-sm',
            desktop: 'mb-4 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm'
        })">
            <div :class="getResponsiveClasses({
                mobile: 'border-b border-gray-200 px-3 py-3',
                tablet: 'border-b border-gray-200 px-4 py-3',
                desktop: 'border-b border-gray-200 px-4 py-3'
            })">
                <div class="flex items-center gap-2">
                    <i :class="getResponsiveClasses({
                        mobile: 'pi pi-filter text-blue-600 text-sm',
                        tablet: 'pi pi-filter text-blue-600',
                        desktop: 'pi pi-filter text-blue-600'
                    })"></i>
                    <h2 :class="getResponsiveClasses({
                        mobile: 'text-sm font-semibold text-gray-800',
                        tablet: 'text-base font-semibold text-gray-800',
                        desktop: 'font-semibold text-gray-800'
                    })">Filter Orders</h2>
                </div>
            </div>
            <div :class="getResponsiveClasses({
                mobile: 'p-3',
                tablet: 'p-4',
                desktop: 'p-4'
            })">
                <div :class="getResponsiveClasses({
                    mobile: 'space-y-4',
                    tablet: 'space-y-4 md:space-y-0 md:grid md:grid-cols-11 md:gap-4 md:items-end',
                    desktop: 'grid grid-cols-1 md:grid-cols-11 gap-4 items-end'
                })">
                    <div :class="getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full md:col-span-5',
                        desktop: 'md:col-span-5'
                    })">
                        <label :class="getResponsiveClasses({
                            mobile: 'block text-xs font-semibold text-gray-700 mb-2',
                            tablet: 'block text-sm font-semibold text-gray-700 mb-2',
                            desktop: 'block text-sm font-semibold text-gray-700 mb-2'
                        })">Search</label>
                        <InputGroup>
                            <InputGroupAddon>
                                <i :class="getResponsiveClasses({
                                    mobile: 'pi pi-search text-gray-400 text-sm',
                                    tablet: 'pi pi-search text-gray-400',
                                    desktop: 'pi pi-search text-gray-400'
                                })" />
                            </InputGroupAddon>
                            <InputText
                                v-model="filters.global.value"
                                id="search"
                                :placeholder="getResponsiveClasses({
                                    mobile: 'Search by Order ID or Customer',
                                    tablet: 'Search by Order ID or Customer Name',
                                    desktop: 'Search by Order ID or Customer Name'
                                })"
                                :class="[
                                    'w-full',
                                    getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })
                                ]"
                            />
                        </InputGroup>
                    </div>
                    
                    <!-- Status and Order Type in 2x2 grid for mobile/tablet -->
                    <div :class="getResponsiveClasses({
                        mobile: 'grid grid-cols-2 gap-3',
                        tablet: 'grid grid-cols-2 gap-3 md:contents',
                        desktop: 'contents'
                    })">
                        <div :class="getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'w-full md:col-span-3',
                            desktop: 'md:col-span-3'
                        })">
                            <label :class="getResponsiveClasses({
                                mobile: 'block text-xs font-semibold text-gray-700 mb-2',
                                tablet: 'block text-sm font-semibold text-gray-700 mb-2',
                                desktop: 'block text-sm font-semibold text-gray-700 mb-2'
                            })">Status</label>
                            <Select 
                                v-model="filters.status.value" 
                                :options="orderStatuses" 
                                option-label="label" 
                                option-value="value" 
                                placeholder="Select"
                                :class="[
                                    'w-full !border !border-gray-300 !rounded-md',
                                    getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })
                                ]"
                            />
                        </div>
                        <div :class="getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'w-full md:col-span-3',
                            desktop: 'md:col-span-3'
                        })">
                            <label :class="getResponsiveClasses({
                                mobile: 'block text-xs font-semibold text-gray-700 mb-2',
                                tablet: 'block text-sm font-semibold text-gray-700 mb-2',
                                desktop: 'block text-sm font-semibold text-gray-700 mb-2'
                            })">Order Type</label>
                            <Select 
                                v-model="filters.order_type.value" 
                                :options="orderTypes" 
                                option-label="label" 
                                option-value="value" 
                                placeholder="Select"
                                :class="[
                                    'w-full !border !border-gray-300 !rounded-md',
                                    getResponsiveClasses({
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
        </div>

        <!-- Orders Table Card -->
        <div :class="getResponsiveClasses({
            mobile: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            tablet: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            desktop: 'rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden'
        })">
            <div
                v-if="progressiveLoad.error.value"
                :class="getResponsiveClasses({
                    mobile: 'p-6 text-center',
                    tablet: 'p-8 text-center',
                    desktop: 'p-8 text-center'
                })"
            >
                <div class="mb-4">
                    <div :class="getResponsiveClasses({
                        mobile: 'mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center',
                        tablet: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center',
                        desktop: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center'
                    })">
                        <i :class="getResponsiveClasses({
                            mobile: 'pi pi-exclamation-triangle text-2xl text-red-600',
                            tablet: 'pi pi-exclamation-triangle text-3xl text-red-600',
                            desktop: 'pi pi-exclamation-triangle text-3xl text-red-600'
                        })"></i>
                    </div>
                </div>
                <h3 :class="getResponsiveClasses({
                    mobile: 'mb-2 text-base font-semibold text-red-800',
                    tablet: 'mb-2 text-lg font-semibold text-red-800',
                    desktop: 'mb-2 text-lg font-semibold text-red-800'
                })">
                    Failed to Load Orders
                </h3>
                <p :class="getResponsiveClasses({
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
                <div :class="getResponsiveClasses({
                    mobile: 'flex items-center justify-between px-3 py-3 border-b border-gray-200 bg-gray-50',
                    tablet: 'flex items-center justify-between px-4 py-4 border-b border-gray-200 bg-gray-50',
                    desktop: 'flex items-center justify-between px-4 sm:px-5 py-4 border-b border-gray-200 bg-gray-50'
                })">
                    <div>
                        <h2 :class="getResponsiveClasses({
                            mobile: 'text-base font-bold text-gray-800',
                            tablet: 'text-lg font-bold text-gray-800',
                            desktop: 'text-base sm:text-lg font-bold text-gray-800'
                        })">
                            Order History
                        </h2>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">View all your past and current orders</p>
                    </div>
                    <div :class="getResponsiveClasses({
                        mobile: 'flex items-center gap-1 text-xs text-gray-600 bg-white px-2 py-1 rounded-full border border-gray-200',
                        tablet: 'flex items-center gap-2 text-sm text-gray-600 bg-white px-3 py-1.5 rounded-full border border-gray-200',
                        desktop: 'flex items-center gap-2 text-sm text-gray-600 bg-white px-3 py-1.5 rounded-full border border-gray-200'
                    })">
                        <i class="pi pi-shopping-cart text-blue-600"></i>
                        <span class="font-semibold">{{ isDesktop ? data.length : filteredData.length }}</span>
                    </div>
                </div>
                
                <!-- Desktop DataTable -->
                <DataTable 
                    v-if="isDesktop"
                    class="w-full"
                    :value="data" 
                    :filters="filters"
                    :globalFilterFields="['order_public_id', 'user.full_name']"
                    columnResizeMode="expand"
                    :paginator="data.length > 10"
                    :rows="10"
                    stripedRows
                    @row-click="onRowClick"
                    :pt="{
                        bodyRow: { class: 'cursor-pointer hover:bg-blue-50 transition-colors' }
                    }"
                >
                    <Column field="created_at" header="Order Date" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-calendar text-gray-400 text-sm"></i>
                                <span class="text-sm text-gray-600">
                                    {{ DateUtil.formatToMonthDayYear(data.created_at) }}
                                </span>
                            </div>
                        </template>
                    </Column>
                    <Column field="order_public_id" header="Order ID" :sortable="true">
                        <template #body="{ data }">
                            <span class="text-xs font-medium text-gray-700">{{ data.order_public_id }}</span>
                        </template>
                    </Column>
                    <Column header="Product Name">
                        <template #body="{ data }">
                            <div class="flex flex-col gap-1">
                                <span v-for="(item, index) in data.product_orders" :key="index" class="text-sm text-gray-700">
                                    {{ item.product.product_name }} <span class="text-xs text-gray-500">(x{{ item.quantity }})</span>
                                </span>
                            </div>
                        </template>
                    </Column>
                    <Column field="user.full_name" header="Customer Name" :sortable="true">
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
                                        {{ data.user.full_name }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="order_type" header="Order Type" :sortable="true">
                        <template #body="{ data }">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                                'bg-purple-50 text-purple-700': data.order_type === 'Delivery',
                                'bg-green-50 text-green-700': data.order_type === 'Pickup'
                            }">
                                <i :class="data.order_type === 'Delivery' ? 'pi pi-truck' : 'pi pi-shopping-bag'" class="mr-1"></i>
                                {{ data.order_type }}
                            </span>
                        </template>
                    </Column>
                    <Column field="amount" header="Amount" :sortable="true">
                        <template #body="{ data }">
                            <span class="font-semibold text-gray-900">
                                {{ 
                                    data.total_amount ?
                                        CurrencyUtil.formatCurrency(data.total_amount)
                                        : CurrencyUtil.formatCurrency(data.product_orders.reduce((total, item) => total + item.price * item.quantity, 0))
                                }}
                            </span>
                        </template>
                    </Column>
                    <Column field="status" header="Order Status" :sortable="true">
                        <template #body="{ data }">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="getAdminStatusClass(data)">
                                {{ getAdminStatusDisplay(data) }}
                            </span>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-12">
                            <div class="mb-4">
                                <div class="mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="pi pi-shopping-cart text-4xl text-gray-400"></i>
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Orders Yet</h3>
                            <p class="text-sm text-gray-500">Your order history will appear here once you make a purchase</p>
                        </div>
                    </template>
                </DataTable>

                <!-- Mobile/Tablet Card Layout -->
                <div v-else>
                    <div v-if="filteredData.length === 0" :class="getResponsiveClasses({
                        mobile: 'text-center py-8',
                        tablet: 'text-center py-12',
                        desktop: 'text-center py-12'
                    })">
                        <div class="mb-4">
                            <div :class="getResponsiveClasses({
                                mobile: 'mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center',
                                tablet: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center',
                                desktop: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center'
                            })">
                                <i :class="getResponsiveClasses({
                                    mobile: 'pi pi-shopping-cart text-3xl text-gray-400',
                                    tablet: 'pi pi-shopping-cart text-4xl text-gray-400',
                                    desktop: 'pi pi-shopping-cart text-4xl text-gray-400'
                                })"></i>
                            </div>
                        </div>
                        <h3 :class="getResponsiveClasses({
                            mobile: 'text-base font-semibold text-gray-700 mb-2',
                            tablet: 'text-lg font-semibold text-gray-700 mb-2',
                            desktop: 'text-lg font-semibold text-gray-700 mb-2'
                        })">No Orders Yet</h3>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-sm text-gray-500',
                            tablet: 'text-sm text-gray-500',
                            desktop: 'text-sm text-gray-500'
                        })">Your order history will appear here once you make a purchase</p>
                    </div>
                    <div v-else>
                        <!-- Cards Container -->
                        <div :class="getResponsiveClasses({
                            mobile: 'space-y-3 p-3',
                            tablet: 'space-y-4 p-4',
                            desktop: 'space-y-4 p-4'
                        })">
                            <div 
                                v-for="(order, index) in paginatedData" 
                                :key="order.order_id"
                                :class="getResponsiveClasses({
                                    mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm cursor-pointer hover:bg-blue-50 transition-colors',
                                    tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm cursor-pointer hover:bg-blue-50 transition-colors',
                                    desktop: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm cursor-pointer hover:bg-blue-50 transition-colors'
                                })"
                                @click="onRowClick({ data: order })"
                            >
                            <!-- Order Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="pi pi-calendar text-gray-400 text-sm"></i>
                                        <span :class="getResponsiveClasses({
                                            mobile: 'text-sm text-gray-600',
                                            tablet: 'text-sm text-gray-600',
                                            desktop: 'text-sm text-gray-600'
                                        })">
                                            {{ DateUtil.formatToMonthDayYear(order.created_at) }}
                                        </span>
                                    </div>
                                    <p :class="getResponsiveClasses({
                                        mobile: 'text-xs font-medium text-gray-700',
                                        tablet: 'text-sm font-medium text-gray-700',
                                        desktop: 'text-sm font-medium text-gray-700'
                                    })">{{ order.order_public_id }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="getAdminStatusClass(order)">
                                    {{ getAdminStatusDisplay(order) }}
                                </span>
                            </div>

                            <!-- Customer Info -->
                            <div class="flex items-center gap-3 mb-3">
                                <Avatar 
                                    shape="circle" 
                                    icon="pi pi-user"
                                    :class="getResponsiveClasses({
                                        mobile: 'bg-blue-100 text-blue-600 w-8 h-8',
                                        tablet: 'bg-blue-100 text-blue-600 w-10 h-10',
                                        desktop: 'bg-blue-100 text-blue-600 w-10 h-10'
                                    })"
                                />
                                <div class="flex-1 min-w-0">
                                    <div :class="getResponsiveClasses({
                                        mobile: 'font-medium text-gray-900 truncate text-sm',
                                        tablet: 'font-medium text-gray-900 truncate text-base',
                                        desktop: 'font-medium text-gray-900 truncate text-base'
                                    })">
                                        {{ order.user.full_name }}
                                    </div>
                                </div>
                            </div>

                            <!-- Products -->
                            <div class="mb-3">
                                <p :class="getResponsiveClasses({
                                    mobile: 'text-xs font-medium text-gray-700 mb-2',
                                    tablet: 'text-sm font-medium text-gray-700 mb-2',
                                    desktop: 'text-sm font-medium text-gray-700 mb-2'
                                })">Products:</p>
                                <div class="space-y-1">
                                    <span v-for="(item, index) in order.product_orders" :key="index" :class="getResponsiveClasses({
                                        mobile: 'block text-sm text-gray-700',
                                        tablet: 'block text-sm text-gray-700',
                                        desktop: 'block text-sm text-gray-700'
                                    })">
                                        {{ item.product.product_name }} <span class="text-xs text-gray-500">(x{{ item.quantity }})</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Order Details Footer -->
                            <div :class="getResponsiveClasses({
                                mobile: 'flex items-center justify-between pt-3 border-t border-gray-100',
                                tablet: 'flex items-center justify-between pt-3 border-t border-gray-100',
                                desktop: 'flex items-center justify-between pt-3 border-t border-gray-100'
                            })">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="{
                                        'bg-purple-50 text-purple-700': order.order_type === 'Delivery',
                                        'bg-green-50 text-green-700': order.order_type === 'Pickup'
                                    }">
                                        <i :class="order.order_type === 'Delivery' ? 'pi pi-truck' : 'pi pi-shopping-bag'" class="mr-1"></i>
                                        {{ order.order_type }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <p :class="getResponsiveClasses({
                                        mobile: 'text-xs text-gray-600',
                                        tablet: 'text-sm text-gray-600',
                                        desktop: 'text-sm text-gray-600'
                                    })">Total:</p>
                                    <span :class="getResponsiveClasses({
                                        mobile: 'text-sm font-semibold text-gray-900',
                                        tablet: 'text-base font-semibold text-gray-900',
                                        desktop: 'text-base font-semibold text-gray-900'
                                    })">
                                        {{ 
                                            order.total_amount ?
                                                CurrencyUtil.formatCurrency(order.total_amount)
                                                : CurrencyUtil.formatCurrency(order.product_orders.reduce((total, item) => total + item.price * item.quantity, 0))
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        </div>
                        
                        <!-- Mobile/Tablet Pagination Controls -->
                        <div v-if="filteredData.length > rowsPerPage" :class="getResponsiveClasses({
                            mobile: 'flex items-center justify-between px-3 py-3 border-t border-gray-200 bg-gray-50',
                            tablet: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50',
                            desktop: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50'
                        })">
                            <div :class="getResponsiveClasses({
                                mobile: 'text-xs text-gray-600',
                                tablet: 'text-sm text-gray-600',
                                desktop: 'text-sm text-gray-600'
                            })">
                                Showing {{ ((currentPage - 1) * rowsPerPage) + 1 }} to {{ Math.min(currentPage * rowsPerPage, filteredData.length) }} of {{ filteredData.length }} orders
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    icon="pi pi-chevron-left"
                                    :disabled="currentPage === 1"
                                    @click="currentPage--"
                                    :class="[
                                        'p-button-text p-button-sm',
                                        getResponsiveClasses({
                                            mobile: '!text-xs !p-1',
                                            tablet: '!text-sm !p-2',
                                            desktop: '!text-sm !p-2'
                                        })
                                    ]"
                                />
                                <span :class="getResponsiveClasses({
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
                                        getResponsiveClasses({
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

        <Popover ref="editElement">
            <div v-if="selectedOrder" class="min-w-48">
                <div>
                    <button
                        type="button"
                        class="hover:bg-gray-100 hover:cursor-pointer p-3 w-full text-start flex items-center gap-2 text-sm font-medium text-gray-700 transition-colors rounded"
                        @click="showOrder($event)"
                    >
                        <i class="pi pi-eye text-blue-600" /> View Products
                    </button>
                </div>
                <!-- Processing Status - Not Yet Accepted: Accept/Decline -->
                <div class="border-t border-gray-200" v-if="selectedOrder.status === 'Processing' && !selectedOrder.admin_accepted"></div>
                <div v-if="selectedOrder.status === 'Processing' && !selectedOrder.admin_accepted">
                    <button
                        type="button"
                        class="hover:bg-green-50 hover:cursor-pointer p-3 w-full text-start flex items-center gap-2 text-sm font-medium text-green-700 transition-colors rounded"
                        @click="showAcceptOrder($event)"
                    >
                        <i class="pi pi-check" /> Accept Order
                    </button>
                </div>
                <div class="border-t border-gray-200" v-if="selectedOrder.status === 'Processing' && !selectedOrder.admin_accepted"></div>
                <div v-if="selectedOrder.status === 'Processing' && !selectedOrder.admin_accepted">
                    <button
                        type="button"
                        class="hover:bg-red-50 hover:cursor-pointer p-3 w-full text-start flex items-center gap-2 text-sm font-medium text-red-700 transition-colors rounded"
                        @click="showDeclineOrder($event)"
                    >
                        <i class="pi pi-times" /> Decline Order
                    </button>
                </div>

                <!-- Processing Status - Accepted with Payment: View Payment & Confirm Payment -->
                <div class="border-t border-gray-200" v-if="selectedOrder.status === 'Processing' && selectedOrder.admin_accepted && selectedOrder.proof_of_payment"></div>
                <div v-if="selectedOrder.status === 'Processing' && selectedOrder.admin_accepted && selectedOrder.proof_of_payment">
                    <button
                        type="button"
                        class="hover:bg-blue-50 hover:cursor-pointer p-3 w-full text-start flex items-center gap-2 text-sm font-medium text-blue-700 transition-colors rounded"
                        @click="showOrderPayment($event)"
                    >
                        <i class="pi pi-eye" /> View Payment
                    </button>
                </div>
                <div class="border-t border-gray-200" v-if="selectedOrder.status === 'Processing' && selectedOrder.admin_accepted && selectedOrder.proof_of_payment"></div>
                <div v-if="selectedOrder.status === 'Processing' && selectedOrder.admin_accepted && selectedOrder.proof_of_payment">
                    <button
                        type="button"
                        class="hover:bg-green-50 hover:cursor-pointer p-3 w-full text-start flex items-center gap-2 text-sm font-medium text-green-700 transition-colors rounded"
                        @click="confirmPaymentStatus($event)"
                    >
                        <i class="pi pi-check" /> Confirm Payment
                    </button>
                </div>
                <div v-if="selectedOrder.payment_method === 'Cash on Delivery' && selectedOrder.status === 'Shipped'">
                    <MarkPaidForm :data="selectedOrder" @cb="setNewOrder" />
                </div>
                <!-- Confirmed Status: Generate Invoice & Set Delivery -->
                <div class="border-t border-gray-200" v-if="selectedOrder.status === 'Confirmed'"></div>
                <div v-if="selectedOrder.status === 'Confirmed'">
                    <button
                        type="button"
                        class="hover:bg-purple-50 hover:cursor-pointer p-3 w-full text-start flex items-center gap-2 text-sm font-medium text-purple-700 transition-colors rounded"
                        @click="generateInvoiceAndSetDelivery($event)"
                    >
                        <i class="pi pi-file-pdf" /> Generate Invoice & Set Delivery
                    </button>
                </div>
            </div>
        </Popover>
        <Dialog
            v-model:visible="approvalVisible.visible"
            header="Change Status"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
        >
            <ApprovalForm :data="selectedOrder" @cb="setNewOrder" @close-popup="closePopup()" :status="approvalVisible.status" />
        </Dialog>
        <Dialog
            v-model:visible="viewOrderModal.visible"
            header="Order Details"
            :style="{ width: '90%' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
        >
            <ViewOrderForm 
                :data="viewOrderModal.order" 
                @approve="handleApprove"
                @decline="handleDecline"
                @viewPayment="handleViewPayment"
                @markPaid="handleMarkPaid"
                @generateInvoice="handleGenerateInvoice"
                @viewInvoice="handleViewInvoice"
                @setDelivery="handleSetDelivery"
            />
        </Dialog>
        <Dialog
            v-model:visible="setDeliveryModal.visible"
            header="Set to Delivery - Upload Proof"
            :style="{ width: '50rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
        >
            <SetDeliveryProofForm :order="setDeliveryModal.order" @cb="setDeliveryConfirmCb" />
        </Dialog>
        <Dialog
            v-model:visible="viewPaymentModal.visible"
            header="View Payment Details"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
        >
            <ViewPaymentForm :data="viewPaymentModal.order" @cb="markPaidCb" />
        </Dialog>
        <Dialog
            v-model:visible="markPaidModal.visible"
            header="Mark as Paid"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
            :pt="{
                header: { class: '!bg-green-600 !text-white' },
                closeButton: { class: '!text-white hover:!bg-green-700 !border-white' },
                closeButtonIcon: { class: '!text-white' }
            }"
        >
            <MarkPaidForm :data="markPaidModal.order" @cb="markPaidConfirmCb" />
        </Dialog>
    </div>
</template>

<style>
.p-dialog .p-dialog-header {
    background-color: #2563eb !important;
    color: white !important;
}

.p-dialog .p-dialog-header .p-dialog-header-close {
    color: white !important;
    border-color: white !important;
}

.p-dialog .p-dialog-header .p-dialog-header-close:hover {
    background-color: #1d4ed8 !important;
}

.p-dialog .p-dialog-header .p-dialog-header-close svg {
    color: white !important;
    fill: white !important;
}

.p-dialog .p-dialog-header .p-dialog-header-close .p-button-icon {
    color: white !important;
}

/* AGGRESSIVE FIX: Remove all borders and shadows from tab-style filter buttons and containers */
.p-selectbutton,
.p-selectbutton *,
div[class*="selectbutton"],
div[class*="divcard"] {
    border: 0 !important;
    border-width: 0 !important;
    border-style: none !important;
    border-color: transparent !important;
    box-shadow: none !important;
    outline: none !important;
}

.p-button.p-button-outlined,
.p-button.p-button-text,
.p-selectbutton .p-button,
.p-selectbutton button {
    border: 0 !important;
    border-width: 0 !important;
}

/* ULTRA AGGRESSIVE: Selected tab button FIRST - white text with blue background */
.p-selectbutton .p-button.p-highlight,
.p-selectbutton button.p-highlight,
.p-selectbutton .p-button[aria-pressed="true"],
.p-selectbutton button[aria-pressed="true"],
button[class*="p-button"].p-highlight,
button[class*="p-button"][aria-pressed="true"] {
    background-color: #2563eb !important; /* blue-600 */
    background: #2563eb !important;
    color: #ffffff !important;
    border: 0 !important;
    border-width: 0 !important;
}

.p-selectbutton .p-button.p-highlight:hover,
.p-selectbutton button.p-highlight:hover,
.p-selectbutton .p-button[aria-pressed="true"]:hover,
.p-selectbutton button[aria-pressed="true"]:hover {
    background-color: #1d4ed8 !important; /* blue-700 */
    background: #1d4ed8 !important;
    color: #ffffff !important;
}

/* Icon color for selected buttons */
.p-selectbutton .p-button.p-highlight .p-button-icon,
.p-selectbutton button.p-highlight .p-button-icon,
.p-selectbutton .p-button.p-highlight i,
.p-selectbutton button.p-highlight i {
    color: #ffffff !important;
}

/* Unselected tab buttons - gray color */
.p-selectbutton .p-button:not(.p-highlight):not([aria-pressed="true"]),
.p-selectbutton button:not(.p-highlight):not([aria-pressed="true"]) {
    border: 0 !important;
    border-width: 0 !important;
    background-color: transparent !important;
    background: transparent !important;
    color: #6b7280 !important; /* gray-500 */
}

.p-selectbutton .p-button:not(.p-highlight):not([aria-pressed="true"]):hover,
.p-selectbutton button:not(.p-highlight):not([aria-pressed="true"]):hover {
    background-color: #f3f4f6 !important; /* gray-100 */
    background: #f3f4f6 !important;
    color: #4b5563 !important; /* gray-600 */
}

/* Icon color for unselected buttons */
.p-selectbutton .p-button:not(.p-highlight) .p-button-icon,
.p-selectbutton button:not(.p-highlight) .p-button-icon,
.p-selectbutton .p-button:not(.p-highlight) i,
.p-selectbutton button:not(.p-highlight) i {
    color: #6b7280 !important;
}

/* Target the specific container */
.divcard,
[class*="divcard"],
.mb-4.bg-white.p-4.rounded-lg.shadow-sm,
.mb-4.bg-white.p-4 {
    border: 0 !important;
    border-width: 0 !important;
    border-style: none !important;
    box-shadow: none !important;
}
</style>

<script setup lang="ts">
import GCashIcon from "@/../img/gcash.png";
import CashIcon from "@/../img/cash.png";
import { Button, Popover } from "primevue";
import { onMounted, reactive, ref, computed, watch } from "vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { IOrder } from "@/interfaces/IOrder";
import { useToast } from "vue-toastification";
import { useRoute, useRouter } from "vue-router";
import DateUtil from "@/utils/DateUtil";
import CurrencyUtil from "@/utils/CurrencyUtil";
import { FilterMatchMode } from '@primevue/core/api';
import ApprovalForm from "@/components/forms/ApprovalForm.vue";
import ViewOrderForm from "@/components/forms/ViewOrderForm.vue";
import ViewPaymentForm from "@/components/forms/ViewPaymentForm.vue";
// SetToDeliveryForm removed - now using SetDeliveryProofForm which requires delivery proof upload
import SetDeliveryProofForm from "@/components/forms/SetDeliveryProofForm.vue";
import MarkPaidForm from "@/components/forms/MarkPaidForm.vue";
import DateTimeDisplay from "@/components/DateTimeDisplay.vue";
import TableSkeleton from "@/components/skeletons/TableSkeleton.vue";
import { useProgressiveLoad } from "@/composables/useProgressiveLoad";
import { useResponsive } from "@/composables/useResponsive";

// Use responsive composable
const { getResponsiveClasses, isDesktop } = useResponsive();

const editElement = ref<null | InstanceType<typeof Popover>>();

const approvalVisible = ref<{ visible: boolean, status: string | null }>({
    visible: false,
    status: null
});
const selectedOrder = ref<IOrder | null>(null);

const toast = useToast();
const route = useRoute();
const router = useRouter();

const filters = ref({
    global: { value: '', matchMode: FilterMatchMode.CONTAINS },
    status: { value: '', matchMode: FilterMatchMode.EQUALS },
    order_type: { value: '', matchMode: FilterMatchMode.EQUALS },
});

const data = ref<IOrder[]>([]);

// Pagination for mobile/tablet card layout
const currentPage = ref(1);
const rowsPerPage = ref(10);

// Computed properties for filtering and pagination
const filteredData = computed(() => {
    let filtered = data.value;
    
    // Apply global filter (search)
    if (filters.value.global.value) {
        const searchTerm = filters.value.global.value.toLowerCase();
        filtered = filtered.filter(order => 
            order.order_public_id.toLowerCase().includes(searchTerm) ||
            order.user.full_name.toLowerCase().includes(searchTerm)
        );
    }
    
    // Apply status filter
    if (filters.value.status.value) {
        filtered = filtered.filter(order => order.status === filters.value.status.value);
    }
    
    // Apply order type filter
    if (filters.value.order_type.value) {
        filtered = filtered.filter(order => order.order_type === filters.value.order_type.value);
    }
    
    return filtered;
});

const totalPages = computed(() => {
    return Math.ceil(filteredData.value.length / rowsPerPage.value);
});

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * rowsPerPage.value;
    const end = start + rowsPerPage.value;
    return filteredData.value.slice(start, end);
});

// Reset pagination when filters change
watch([() => filters.value.global.value, () => filters.value.status.value, () => filters.value.order_type.value], () => {
    currentPage.value = 1;
});

const loadService = useAxiosUtil<null, IOrder[]>();

const orderStatuses = ref<{
    label: string;
    value: string;
}[]>([
    { label: 'All', value: '' },
    { label: 'Processing', value: 'Processing' },
    { label: 'Confirmed', value: 'Confirmed' },
    { label: 'Shipped', value: 'Shipped' },
    { label: 'Completed', value: 'Completed' },
    { label: 'Cancelled', value: 'Cancelled' },
]);

const orderTypes = ref<{
    label: string;
    value: string;
}[]>([
    { label: 'All', value: '' },
    { label: 'Pickup', value: 'Pickup' },
    { label: 'Delivery', value: 'Delivery' },
]);

// Initialize progressive loading composable
const progressiveLoad = useProgressiveLoad();

// Primary data loader: Load order list (critical data)
const loadPrimaryData = async () => {
    await loadService.get("admin/orders").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load orders");
        }
    });
};

// Secondary data loader: Filter options (already loaded, no additional fetch needed)
const loadSecondaryData = async () => {
    // Filter options (orderStatuses, orderTypes) are already defined
    // No additional data fetching needed for secondary load
    return Promise.resolve();
};

const load = () => {
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
        toast.success("Order data reloaded successfully");
    } catch (error) {
        toast.error("Retry failed. Please try again later.");
    }
};

const selectOrder = (event: Event, order: IOrder) => {
    selectedOrder.value = order;
    editElement.value.toggle(event);
}

const setNewOrder = (value: IOrder) => {
    selectedOrder.value = value;
    approvalVisible.value.status = null;
    approvalVisible.value.visible = false;
    viewOrderModal.visible = false;
    load();
}

const setApproval = (event: Event, status: string) => {
    editElement.value.toggle(event);
    approvalVisible.value.status = status;
    approvalVisible.value.visible = true;
}

const closePopup = () => {
    approvalVisible.value.visible = false;
}

const viewOrderModal : {
    visible: boolean;
    order: IOrder | null
} = reactive({
    visible: false,
    order: null
});

const showOrder = (event: Event) => {
    viewOrderModal.order = selectedOrder.value;
    viewOrderModal.visible = true;
    editElement.value.toggle(event);
}

const onRowClick = (event: any) => {
    // Open Order Details modal directly when row is clicked
    viewOrderModal.order = event.data;
    viewOrderModal.visible = true;
}

// Handler functions for actions from ViewOrderForm
// Simplified status display - no more mapping needed
const getAdminStatusDisplay = (order: IOrder) => {
    return order.status;
};

const getAdminStatusClass = (order: IOrder) => {
    const statusClasses: Record<string, string> = {
        'Processing': 'bg-yellow-50 text-yellow-700',
        'Confirmed': 'bg-blue-50 text-blue-700',
        'Shipped': 'bg-purple-50 text-purple-700',
        'Completed': 'bg-green-50 text-green-700',
        'Cancelled': 'bg-red-50 text-red-700',
    };
    return statusClasses[order.status] || 'bg-gray-50 text-gray-700';
};

const handleApprove = (order: IOrder) => {
    selectedOrder.value = order;
    approvalVisible.value.status = 'Confirmed';
    approvalVisible.value.visible = true;
}

const handleDecline = (order: IOrder) => {
    selectedOrder.value = order;
    approvalVisible.value.status = 'Rejected';
    approvalVisible.value.visible = true;
}

const handleViewPayment = (order: IOrder) => {
    viewPaymentModal.order = order;
    viewPaymentModal.visible = true;
}

const markPaidModal = reactive({
    visible: false,
    order: null as IOrder | null
});

const handleMarkPaid = (order: IOrder) => {
    markPaidModal.order = order;
    markPaidModal.visible = true;
}

const markPaidConfirmCb = () => {
    load();
    markPaidModal.visible = false;
    viewOrderModal.visible = false;
}

const setDeliveryModal = reactive({
    visible: false,
    order: null as IOrder | null
});

const generateInvoiceService = useAxiosUtil<null, any>();

const handleGenerateInvoice = async (order: IOrder) => {
    try {
        await generateInvoiceService.post(`admin/orders/generate-invoice/${order.order_id}`, null);
        
        if (generateInvoiceService.request.status === 200 && generateInvoiceService.request.data) {
            const sale = generateInvoiceService.request.data;
            toast.success("Sales invoice generated successfully");
            
            // Close the view order modal
            viewOrderModal.visible = false;
            
            // Navigate to invoice page with return context
            router.push({ 
                name: 'admin.order.invoice', 
                params: { 
                    orderId: order.order_id,
                    saleId: sale.sale_id
                },
                query: { 
                    returnToOrder: 'true',
                    orderId: order.order_id.toString()
                }
            });
        } else {
            toast.error(generateInvoiceService.request.message ?? "Failed to generate invoice");
        }
    } catch (error) {
        console.error('Error generating invoice:', error);
        toast.error("An error occurred while generating invoice");
    }
}

const handleViewInvoice = (order: IOrder) => {
    if (order.sale_id) {
        // Navigate to existing invoice without return context (just view)
        router.push({ 
            name: 'admin.order.invoice', 
            params: { 
                orderId: order.order_id,
                saleId: order.sale_id
            }
        });
    }
}

const handleSetDelivery = (order: IOrder) => {
    setDeliveryModal.order = order;
    setDeliveryModal.visible = true;
}

const setDeliveryConfirmCb = () => {
    load();
    setDeliveryModal.visible = false;
    viewOrderModal.visible = false;
}

const viewPaymentModal : {
    visible: boolean;
    order: IOrder | null
} = reactive({
    visible: false,
    order: null
});

const showOrderPayment = (event: Event) => {
    viewPaymentModal.order = selectedOrder.value;
    viewPaymentModal.visible = true;
    editElement.value.toggle(event);
}

const confirmOrderCb = () => {
    load();
    viewPaymentModal.visible = false;
}

const viewDeliveryModal : {
    visible: boolean;
    order: IOrder | null
} = reactive({
    visible: false,
    order: null
});

const showAcceptOrder = (event: Event) => {
    editElement.value.toggle(event);
    approvalVisible.value.status = 'Accept';
    approvalVisible.value.visible = true;
};

const showDeclineOrder = (event: Event) => {
    editElement.value.toggle(event);
    approvalVisible.value.status = 'Decline';
    approvalVisible.value.visible = true;
};

const confirmPaymentStatus = async (event: Event) => {
    editElement.value.toggle(event);
    
    if (!selectedOrder.value) return;
    
    try {
        const response = await useAxiosUtil().patch(
            `admin/orders/confirm-payment-status/${selectedOrder.value.order_id}`,
            {}
        );
        
        if (response.request.status === 200) {
            toast.success('Payment confirmed successfully!');
            load();
        } else {
            toast.error(response.request.message ?? 'Failed to confirm payment');
        }
    } catch (error) {
        toast.error('An error occurred while confirming payment');
    }
};

const generateInvoiceAndSetDelivery = (event: Event) => {
    viewDeliveryModal.order = selectedOrder.value;
    viewDeliveryModal.visible = true;
    editElement.value.toggle(event);
};

const showDelivery = (event: Event) => {
    viewDeliveryModal.order = selectedOrder.value;
    viewDeliveryModal.visible = true;
    editElement.value.toggle(event);
}

const confirmDeliveryCb = () => {
    load();
    viewDeliveryModal.visible = false;
    viewOrderModal.visible = false;
}

const confirmPaymentCb = () => {
    load();
    viewPaymentModal.visible = false;
}

const markPaidCb = () => {
    load();
    viewPaymentModal.visible = false;
    viewOrderModal.visible = false;
}

// Non-blocking onMounted - page renders immediately with skeletons
onMounted(() => {
    load();
    
    // Check if status filter should be set from query parameter
    if (route.query.status) {
        filters.value.status.value = route.query.status as string;
        // Clean up the status query parameter after applying it
        router.replace({ query: { ...route.query, status: undefined } });
    }
    
    // Check if we should auto-open order modal (after invoice print)
    if (route.query.openOrderId) {
        const orderId = parseInt(route.query.openOrderId as string);
        
        // Wait for data to load
        const checkDataInterval = setInterval(() => {
            if (data.value.length > 0) {
                clearInterval(checkDataInterval);
                
                const order = data.value.find(o => o.order_id === orderId);
                
                if (order) {
                    viewOrderModal.order = order;
                    viewOrderModal.visible = true;
                    
                    // Check if we should also show delivery modal
                    if (route.query.showDeliveryModal === 'true') {
                        // Small delay to ensure order modal is open first
                        setTimeout(() => {
                            setDeliveryModal.order = order;
                            setDeliveryModal.visible = true;
                        }, 300);
                    }
                }
                
                // Clean up query params
                router.replace({ query: {} });
            }
        }, 100);
        
        // Timeout after 5 seconds
        setTimeout(() => {
            clearInterval(checkDataInterval);
        }, 5000);
    }
});
</script>
