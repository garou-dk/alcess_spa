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
                    })">Filter Walk-In Sales</h2>
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
                                    mobile: 'Search by Sale Code',
                                    tablet: 'Search by Sale Code or Customer Name',
                                    desktop: 'Search by Sale Code or Customer Name'
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
                    <div :class="getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'w-full md:col-span-3',
                        desktop: 'md:col-span-3'
                    })">
                        <label :class="getResponsiveClasses({
                            mobile: 'block text-xs font-semibold text-gray-700 mb-2',
                            tablet: 'block text-sm font-semibold text-gray-700 mb-2',
                            desktop: 'block text-sm font-semibold text-gray-700 mb-2'
                        })">Payment Method</label>
                        <Select 
                            v-model="filters.payment_method.value" 
                            :options="paymentMethods" 
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
                        })">Date Range</label>
                        <DatePicker 
                            v-model="dateRange" 
                            selectionMode="range" 
                            placeholder="Select Date Range"
                            :class="[
                                'w-full',
                                getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                            dateFormat="mm/dd/yy"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Table Card -->
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
                    Failed to Load Sales
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
                            Walk-In Sales History
                        </h2>
                        <p :class="getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">View all walk-in POS transactions</p>
                    </div>
                    <div :class="getResponsiveClasses({
                        mobile: 'flex flex-col items-end',
                        tablet: 'flex flex-col items-end',
                        desktop: 'flex flex-col items-end'
                    })">
                        <span :class="getResponsiveClasses({
                            mobile: 'text-xs text-gray-600 font-medium',
                            tablet: 'text-xs text-gray-600 font-medium',
                            desktop: 'text-xs text-gray-600 font-medium'
                        })">Total Sales</span>
                        <span :class="getResponsiveClasses({
                            mobile: 'text-base font-bold text-gray-800',
                            tablet: 'text-lg font-bold text-gray-800',
                            desktop: 'text-lg font-bold text-gray-800'
                        })">{{ CurrencyUtil.formatCurrency(totalAmount) }}</span>
                    </div>
                </div>
                
                <!-- Desktop DataTable -->
                <DataTable 
                    v-if="isDesktop"
                    class="w-full"
                    :value="paginatedData" 
                    :globalFilterFields="['sale_code', 'customer_name']"
                    columnResizeMode="expand"
                    stripedRows
                    @row-click="onRowClick"
                    :pt="{
                        bodyRow: { class: 'cursor-pointer hover:bg-blue-50 transition-colors' }
                    }"
                >
                    <Column field="created_at" header="Sale Date" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-calendar text-gray-400 text-sm"></i>
                                <span class="text-sm text-gray-600">
                                    {{ DateUtil.formatToMonthDayYear(data.created_at) }}
                                </span>
                            </div>
                        </template>
                    </Column>
                    <Column field="sale_code" header="Sale Code" :sortable="true">
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
                    <Column field="payment_method" header="Payment Method" :sortable="true">
                        <template #body="{ data }">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                                'bg-green-50 text-green-700': data.payment_method === 'Cash',
                                'bg-blue-50 text-blue-700': data.payment_method === 'E-wallet',
                                'bg-purple-50 text-purple-700': data.payment_method && data.payment_method.startsWith('Installment')
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
                    <Column header="Action">
                        <template #body="{ data }">
                            <Button 
                                icon="pi pi-print" 
                                label="Print Invoice"
                                class="!bg-blue-600 hover:!bg-blue-700 !text-white !text-xs"
                                @click.stop="handleViewInvoice(data)"
                            />
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
                                    mobile: 'pi pi-shop text-3xl text-gray-400',
                                    tablet: 'pi pi-shop text-4xl text-gray-400',
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
                            mobile: 'text-sm text-gray-500',
                            tablet: 'text-sm text-gray-500',
                            desktop: 'text-sm text-gray-500'
                        })">Walk-in sales transactions will appear here</p>
                    </div>
                    <div v-else>
                        <!-- Cards Container -->
                        <div :class="getResponsiveClasses({
                            mobile: 'space-y-3 p-3',
                            tablet: 'space-y-4 p-4',
                            desktop: 'space-y-4 p-4'
                        })">
                            <div 
                                v-for="(sale, index) in paginatedData" 
                                :key="sale.sale_id"
                                :class="getResponsiveClasses({
                                    mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm cursor-pointer hover:bg-blue-50 transition-colors',
                                    tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm cursor-pointer hover:bg-blue-50 transition-colors',
                                    desktop: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm cursor-pointer hover:bg-blue-50 transition-colors'
                                })"
                                @click="onRowClick({ data: sale })"
                            >
                                <!-- Sale Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i class="pi pi-calendar text-gray-400 text-sm"></i>
                                            <span :class="getResponsiveClasses({
                                                mobile: 'text-sm text-gray-600',
                                                tablet: 'text-sm text-gray-600',
                                                desktop: 'text-sm text-gray-600'
                                            })">
                                                {{ DateUtil.formatToMonthDayYear(sale.created_at) }}
                                            </span>
                                        </div>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs font-medium text-gray-700',
                                            tablet: 'text-sm font-medium text-gray-700',
                                            desktop: 'text-sm font-medium text-gray-700'
                                        })">{{ sale.sale_code }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                                        'bg-green-50 text-green-700': sale.payment_method === 'Cash',
                                        'bg-blue-50 text-blue-700': sale.payment_method === 'E-wallet',
                                        'bg-purple-50 text-purple-700': sale.payment_method && sale.payment_method.startsWith('Installment')
                                    }">
                                        <i :class="sale.payment_method === 'Cash' ? 'pi pi-money-bill' : sale.payment_method === 'E-wallet' ? 'pi pi-wallet' : 'pi pi-credit-card'" class="mr-1"></i>
                                        {{ sale.payment_method }}
                                    </span>
                                </div>

                                <!-- Customer Info -->
                                <div v-if="sale.customer_name" class="mb-3">
                                    <p :class="getResponsiveClasses({
                                        mobile: 'text-xs font-medium text-gray-700 mb-1',
                                        tablet: 'text-sm font-medium text-gray-700 mb-1',
                                        desktop: 'text-sm font-medium text-gray-700 mb-1'
                                    })">Customer:</p>
                                    <p :class="getResponsiveClasses({
                                        mobile: 'text-sm text-gray-900',
                                        tablet: 'text-base text-gray-900',
                                        desktop: 'text-base text-gray-900'
                                    })">{{ sale.customer_name }}</p>
                                </div>

                                <!-- Products -->
                                <div class="mb-3">
                                    <p :class="getResponsiveClasses({
                                        mobile: 'text-xs font-medium text-gray-700 mb-2',
                                        tablet: 'text-sm font-medium text-gray-700 mb-2',
                                        desktop: 'text-sm font-medium text-gray-700 mb-2'
                                    })">Products:</p>
                                    <div class="space-y-1">
                                        <span v-for="(item, index) in sale.sale_items" :key="index" :class="getResponsiveClasses({
                                            mobile: 'block text-sm text-gray-700',
                                            tablet: 'block text-sm text-gray-700',
                                            desktop: 'block text-sm text-gray-700'
                                        })">
                                            {{ item.product.product_name }} <span class="text-xs text-gray-500">(x{{ item.quantity }})</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Sale Details Footer -->
                                <div :class="getResponsiveClasses({
                                    mobile: 'flex items-center justify-between pt-3 border-t border-gray-100',
                                    tablet: 'flex items-center justify-between pt-3 border-t border-gray-100',
                                    desktop: 'flex items-center justify-between pt-3 border-t border-gray-100'
                                })">
                                    <div class="text-left">
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600',
                                            tablet: 'text-sm text-gray-600',
                                            desktop: 'text-sm text-gray-600'
                                        })">Total Amount:</p>
                                    </div>
                                    <div class="text-right">
                                        <span :class="getResponsiveClasses({
                                            mobile: 'text-base font-bold text-blue-600',
                                            tablet: 'text-lg font-bold text-blue-600',
                                            desktop: 'text-lg font-bold text-blue-600'
                                        })">
                                            {{ CurrencyUtil.formatCurrency(sale.total_amount) }}
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
                                Showing {{ ((currentPage - 1) * rowsPerPage) + 1 }} to {{ Math.min(currentPage * rowsPerPage, filteredData.length) }} of {{ filteredData.length }} sales
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

        <!-- Sale Details Modal -->
        <Dialog
            v-model:visible="viewSaleModal"
            header="Sale Details"
            :style="{ width: '90%' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            modal
            :dismissableMask="true"
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
                            tablet: 'w-full',
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
                            tablet: 'w-full',
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
                            })">{{ DateUtil.formatToMonthDayYear(selectedSale.created_at) }}</p>
                        </div>
                        <div :class="getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'w-full',
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
                            tablet: 'w-full',
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
                            tablet: 'w-full',
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
                            tablet: 'w-full col-span-2',
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
                                'bg-purple-50 text-purple-700': selectedSale.payment_method && selectedSale.payment_method.startsWith('Installment')
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
                        mobile: 'px-3 py-3 bg-gray-50 border-b border-gray-200',
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
                            tablet: 'space-y-4 p-4',
                            desktop: 'space-y-4 p-4'
                        })">
                            <div 
                                v-for="(item, index) in selectedSale.sale_items" 
                                :key="index"
                                :class="getResponsiveClasses({
                                    mobile: 'bg-gray-50 border border-gray-200 rounded-lg p-3',
                                    tablet: 'bg-gray-50 border border-gray-200 rounded-lg p-4',
                                    desktop: 'bg-gray-50 border border-gray-200 rounded-lg p-4'
                                })"
                            >
                                <!-- Product Info -->
                                <div class="flex items-start gap-3 mb-3">
                                    <Avatar 
                                        v-if="item.product.product_image" 
                                        shape="circle" 
                                        :image="UrlUtil.getBaseAppUrl(`storage/images/product/${item.product.product_image}`)"
                                        :class="getResponsiveClasses({
                                            mobile: 'w-12 h-12 flex-shrink-0',
                                            tablet: 'w-16 h-16 flex-shrink-0',
                                            desktop: 'w-16 h-16 flex-shrink-0'
                                        })"
                                    />
                                    <Avatar 
                                        v-else 
                                        shape="circle" 
                                        icon="pi pi-camera" 
                                        :class="getResponsiveClasses({
                                            mobile: 'w-12 h-12 flex-shrink-0 bg-gray-100 text-gray-400',
                                            tablet: 'w-16 h-16 flex-shrink-0 bg-gray-100 text-gray-400',
                                            desktop: 'w-16 h-16 flex-shrink-0 bg-gray-100 text-gray-400'
                                        })"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <h4 :class="getResponsiveClasses({
                                            mobile: 'font-medium text-gray-900 text-sm',
                                            tablet: 'font-medium text-gray-900 text-base',
                                            desktop: 'font-medium text-gray-900 text-base'
                                        })">
                                            {{ item.product.product_name }}
                                        </h4>
                                    </div>
                                </div>

                                <!-- Item Details -->
                                <div :class="getResponsiveClasses({
                                    mobile: 'grid grid-cols-2 gap-3 pt-3 border-t border-gray-200',
                                    tablet: 'grid grid-cols-3 gap-4 pt-3 border-t border-gray-200',
                                    desktop: 'grid grid-cols-3 gap-4 pt-3 border-t border-gray-200'
                                })">
                                    <div>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs font-medium text-gray-600 mb-1',
                                            tablet: 'text-sm font-medium text-gray-600 mb-1',
                                            desktop: 'text-sm font-medium text-gray-600 mb-1'
                                        })">Price</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-sm font-semibold text-gray-900',
                                            tablet: 'text-base font-semibold text-gray-900',
                                            desktop: 'text-base font-semibold text-gray-900'
                                        })">
                                            {{ CurrencyUtil.formatCurrency(item.price) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs font-medium text-gray-600 mb-1',
                                            tablet: 'text-sm font-medium text-gray-600 mb-1',
                                            desktop: 'text-sm font-medium text-gray-600 mb-1'
                                        })">Quantity</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-sm font-semibold text-gray-900',
                                            tablet: 'text-base font-semibold text-gray-900',
                                            desktop: 'text-base font-semibold text-gray-900'
                                        })">
                                            {{ item.quantity }}
                                        </p>
                                    </div>
                                    <div :class="getResponsiveClasses({
                                        mobile: 'col-span-2',
                                        tablet: 'col-span-1',
                                        desktop: 'col-span-1'
                                    })">
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs font-medium text-gray-600 mb-1',
                                            tablet: 'text-sm font-medium text-gray-600 mb-1',
                                            desktop: 'text-sm font-medium text-gray-600 mb-1'
                                        })">Subtotal</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-base font-bold text-blue-600',
                                            tablet: 'text-lg font-bold text-blue-600',
                                            desktop: 'text-lg font-bold text-blue-600'
                                        })">
                                            {{ CurrencyUtil.formatCurrency(item.price * item.quantity) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile/Tablet Total Summary -->
                        <div :class="getResponsiveClasses({
                            mobile: 'flex justify-between items-center pt-3 px-3 pb-3 border-t border-gray-200 bg-gray-50',
                            tablet: 'flex justify-between items-center pt-4 px-4 pb-4 border-t border-gray-200 bg-gray-50',
                            desktop: 'flex justify-between items-center pt-4 px-4 pb-4 border-t border-gray-200 bg-gray-50'
                        })">
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

                <!-- Modal Actions -->
                <div :class="getResponsiveClasses({
                    mobile: 'mt-6 flex justify-center',
                    tablet: 'mt-6 flex justify-end',
                    desktop: 'mt-6 flex justify-end'
                })">
                    <Button
                        label="Print Invoice"
                        icon="pi pi-print"
                        class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                        @click="handleViewInvoice(selectedSale)"
                    />
                </div>
            </div>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, computed, watch } from "vue";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ISale } from "@/interfaces/ISale";
import { useToast } from "vue-toastification";
import DateUtil from "@/utils/DateUtil";
import CurrencyUtil from "@/utils/CurrencyUtil";
import UrlUtil from "@/utils/UrlUtil";
import DateTimeDisplay from "@/components/DateTimeDisplay.vue";
import { FilterMatchMode } from '@primevue/core/api';
import { useProgressiveLoad } from "@/composables/useProgressiveLoad";
import { useResponsive } from "@/composables/useResponsive";
import { useRouter } from "vue-router";

// Use responsive composable
const { getResponsiveClasses, isDesktop } = useResponsive();

const toast = useToast();
const router = useRouter();

const filters = ref({
    global: { value: '', matchMode: FilterMatchMode.CONTAINS },
    payment_method: { value: '', matchMode: FilterMatchMode.EQUALS },
});

const dateRange = ref<Date[] | null>(null);
const data = ref<ISale[]>([]);
const loadService = useAxiosUtil<null, ISale[]>();

// Pagination for mobile/tablet card layout
const currentPage = ref(1);
const rowsPerPage = ref(10);

const paymentMethods = ref([
    { label: 'All', value: '' },
    { label: 'Cash', value: 'Cash' },
    { label: 'E-Wallet', value: 'E-wallet' },
    { label: 'Installment', value: 'Installment' },
]);

// Computed property to filter data by date range and payment method
const filteredData = computed(() => {
    let filtered = data.value;

    // Apply global filter (search)
    if (filters.value.global.value) {
        const searchTerm = filters.value.global.value.toLowerCase();
        filtered = filtered.filter(sale => 
            sale.sale_code.toLowerCase().includes(searchTerm) ||
            (sale.customer_name && sale.customer_name.toLowerCase().includes(searchTerm))
        );
    }

    // Apply date range filter
    if (dateRange.value && dateRange.value.length > 0) {
        const [startDate, endDate] = dateRange.value;
        
        if (startDate) {
            filtered = filtered.filter((sale) => {
                const saleDate = new Date(sale.created_at);
                saleDate.setHours(0, 0, 0, 0);
                
                const start = new Date(startDate);
                start.setHours(0, 0, 0, 0);
                
                if (endDate) {
                    const end = new Date(endDate);
                    end.setHours(23, 59, 59, 999);
                    return saleDate >= start && saleDate <= end;
                }
                
                return saleDate.getTime() === start.getTime();
            });
        }
    }

    // Apply payment method filter
    if (filters.value.payment_method.value) {
        filtered = filtered.filter((sale) => sale.payment_method === filters.value.payment_method.value);
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
watch([() => filters.value.global.value, () => filters.value.payment_method.value, () => dateRange.value], () => {
    currentPage.value = 1;
});

// Computed property to calculate total amount from filtered data
const totalAmount = computed(() => {
    return filteredData.value.reduce((sum, sale) => sum + (sale.total_amount || 0), 0);
});

const progressiveLoad = useProgressiveLoad();

const loadPrimaryData = async () => {
    await loadService.get("admin/sales").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load sales");
        }
    });
};

const loadSecondaryData = async () => {
    return Promise.resolve();
};

const load = () => {
    progressiveLoad.loadData({
        primaryLoader: loadPrimaryData,
        secondaryLoader: loadSecondaryData,
    });
};

const handleRetry = async () => {
    try {
        await progressiveLoad.retry();
        toast.success("Sales data reloaded successfully");
    } catch (error) {
        toast.error("Retry failed. Please try again later.");
    }
};

const selectedSale = ref<ISale | null>(null);
const viewSaleModal = ref(false);

const onRowClick = (event: any) => {
    selectedSale.value = event.data;
    viewSaleModal.value = true;
}

const handleViewInvoice = (sale: ISale) => {
    if (sale.sale_id) {
        router.push({
            name: 'admin.pos.find',
            params: {
                id: sale.sale_id
            }
        });
    }
}

onMounted(() => {
    load();
});
</script>
