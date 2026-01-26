<template>
    <div :class="getResponsiveClasses({
        mobile: 'p-3',
        tablet: 'p-4',
        desktop: 'p-0'
    })">
        <BoxShadow :class="getResponsiveClasses({
            mobile: 'p-3',
            tablet: 'p-4',
            desktop: 'p-5'
        })">
            <div class="w-full">
                <div v-if="!loadService.request.loading">
                    <div v-if="data">
                        <!-- Desktop DataTable -->
                        <DataTable v-if="isDesktop" :value="data.sale_items" show-gridlines>
                            <template #header>
                                <div class="flex flex-wrap justify-around">
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Purchase Code</p>
                                        <p>{{ data.sale_code }}</p>
                                    </div>
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Date Created</p>
                                        <p>{{ DateUtil.formatToMonthDayYear(data.created_at) }}</p>
                                    </div>
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Payment Method</p>
                                        <p>{{ data.payment_method }}</p>
                                    </div>
                                    <div class="p-2 grow">
                                        <p class="font-semibold text-gray-600">Prepared By</p>
                                        <p>Blademier S. Canalita</p>
                                    </div>
                                    <div class="flex items-center justify-center">
                                        <Button type="button" label="Print" icon="pi pi-print" class="!bg-blue-600 hover:!bg-blue-700 !text-white"
                                            @click="printReport()" />
                                    </div>
                                </div>
                            </template>
                            <Column field="product.product_name" header="Product Name">
                                <template #body="{ data }">
                                    <div class="flex items-center">
                                        <Avatar v-if="data.product.product_image" shape="circle" :image="UrlUtil.getBaseAppUrl(
                                            `storage/images/product/${data.product.product_image}`,
                                        )
                                            " class="aspect-square!" />
                                        <Avatar v-else shape="circle" icon="pi pi-camera" class="aspect-square!" />
                                        <div class="ml-2 shrink">
                                            {{ data.product.product_name }}
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            <Column field="price" header="Price">
                                <template #body="{ data }">
                                    {{ CurrencyUtil.formatCurrency(data.price) }}
                                </template>
                            </Column>
                            <Column field="quantity" header="Quantity" />
                            <Column field="total" header="Total">
                                <template #body="{ data }">
                                    {{ CurrencyUtil.formatCurrency(data.price * data.quantity) }}
                                </template>
                            </Column>
                            <template #footer>
                                <p class="text-end">
                                    <span class="font-semibold">Total:</span> {{
                                        CurrencyUtil.formatCurrency(data.total_amount) }}
                                </p>
                            </template>
                        </DataTable>

                        <!-- Mobile/Tablet Layout -->
                        <div v-else>
                            <!-- Receipt Header -->
                            <div :class="getResponsiveClasses({
                                mobile: 'mb-4 border border-gray-200 rounded-lg overflow-hidden bg-white',
                                tablet: 'mb-4 border border-gray-200 rounded-lg overflow-hidden bg-white',
                                desktop: 'mb-4 border border-gray-200 rounded-lg overflow-hidden bg-white'
                            })">
                                <div :class="getResponsiveClasses({
                                    mobile: 'px-3 py-2 bg-blue-600 text-white',
                                    tablet: 'px-4 py-3 bg-blue-600 text-white',
                                    desktop: 'px-4 py-3 bg-blue-600 text-white'
                                })">
                                    <h2 :class="getResponsiveClasses({
                                        mobile: 'text-sm font-semibold',
                                        tablet: 'text-base font-semibold',
                                        desktop: 'text-lg font-semibold'
                                    })">Receipt Details</h2>
                                </div>
                                <div :class="getResponsiveClasses({
                                    mobile: 'p-3 space-y-3',
                                    tablet: 'p-4 grid grid-cols-2 gap-4',
                                    desktop: 'p-4 grid grid-cols-2 gap-4'
                                })">
                                    <div :class="getResponsiveClasses({
                                        mobile: 'w-full',
                                        tablet: 'col-span-1',
                                        desktop: 'col-span-1'
                                    })">
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs font-medium text-gray-700 mb-1',
                                            tablet: 'text-sm font-medium text-gray-700 mb-1',
                                            desktop: 'text-sm font-medium text-gray-700 mb-1'
                                        })">Purchase Code</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-sm font-semibold text-gray-900',
                                            tablet: 'text-base font-semibold text-gray-900',
                                            desktop: 'text-base font-semibold text-gray-900'
                                        })">{{ data.sale_code }}</p>
                                    </div>
                                    <div :class="getResponsiveClasses({
                                        mobile: 'w-full',
                                        tablet: 'col-span-1',
                                        desktop: 'col-span-1'
                                    })">
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs font-medium text-gray-700 mb-1',
                                            tablet: 'text-sm font-medium text-gray-700 mb-1',
                                            desktop: 'text-sm font-medium text-gray-700 mb-1'
                                        })">Date Created</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-sm text-gray-900',
                                            tablet: 'text-base text-gray-900',
                                            desktop: 'text-base text-gray-900'
                                        })">{{ DateUtil.formatToMonthDayYear(data.created_at) }}</p>
                                    </div>
                                    <div :class="getResponsiveClasses({
                                        mobile: 'w-full',
                                        tablet: 'col-span-1',
                                        desktop: 'col-span-1'
                                    })">
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs font-medium text-gray-700 mb-1',
                                            tablet: 'text-sm font-medium text-gray-700 mb-1',
                                            desktop: 'text-sm font-medium text-gray-700 mb-1'
                                        })">Payment Method</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-sm text-gray-900',
                                            tablet: 'text-base text-gray-900',
                                            desktop: 'text-base text-gray-900'
                                        })">{{ data.payment_method }}</p>
                                    </div>
                                    <div :class="getResponsiveClasses({
                                        mobile: 'w-full',
                                        tablet: 'col-span-1',
                                        desktop: 'col-span-1'
                                    })">
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-xs font-medium text-gray-700 mb-1',
                                            tablet: 'text-sm font-medium text-gray-700 mb-1',
                                            desktop: 'text-sm font-medium text-gray-700 mb-1'
                                        })">Prepared By</p>
                                        <p :class="getResponsiveClasses({
                                            mobile: 'text-sm text-gray-900',
                                            tablet: 'text-base text-gray-900',
                                            desktop: 'text-base text-gray-900'
                                        })">Blademier S. Canalita</p>
                                    </div>
                                </div>
                                <!-- Print Button for Mobile/Tablet -->
                                <div :class="getResponsiveClasses({
                                    mobile: 'p-3 border-t border-gray-200 bg-gray-50',
                                    tablet: 'p-4 border-t border-gray-200 bg-gray-50',
                                    desktop: 'p-4 border-t border-gray-200 bg-gray-50'
                                })">
                                    <Button 
                                        type="button" 
                                        label="Print Receipt" 
                                        icon="pi pi-print" 
                                        :class="[
                                            '!bg-blue-600 hover:!bg-blue-700 !text-white',
                                            getResponsiveClasses({
                                                mobile: 'w-full',
                                                tablet: 'w-full',
                                                desktop: 'w-auto'
                                            })
                                        ]"
                                        @click="printReport()" 
                                    />
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
                                        tablet: 'text-base font-semibold text-gray-900',
                                        desktop: 'text-base font-semibold text-gray-900'
                                    })">Purchased Items</h3>
                                </div>
                                
                                <!-- Items Cards -->
                                <div :class="getResponsiveClasses({
                                    mobile: 'space-y-3 p-3',
                                    tablet: 'space-y-3 p-4',
                                    desktop: 'space-y-4 p-4'
                                })">
                                    <div 
                                        v-for="(item, index) in data.sale_items" 
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

                                        <!-- Quantity and Total -->
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
                                                })">Total:</p>
                                                <p :class="getResponsiveClasses({
                                                    mobile: 'text-sm font-bold text-blue-600',
                                                    tablet: 'text-base font-bold text-blue-600',
                                                    desktop: 'text-base font-bold text-blue-600'
                                                })">{{ CurrencyUtil.formatCurrency(item.price * item.quantity) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Grand Total Section -->
                                <div :class="getResponsiveClasses({
                                    mobile: 'border-t border-gray-200 p-3 bg-blue-50',
                                    tablet: 'border-t border-gray-200 p-4 bg-blue-50',
                                    desktop: 'border-t border-gray-200 p-4 bg-blue-50'
                                })">
                                    <div class="flex justify-between items-center">
                                        <span :class="getResponsiveClasses({
                                            mobile: 'text-base font-semibold text-gray-900',
                                            tablet: 'text-lg font-semibold text-gray-900',
                                            desktop: 'text-xl font-semibold text-gray-900'
                                        })">Grand Total:</span>
                                        <span :class="getResponsiveClasses({
                                            mobile: 'text-lg font-bold text-blue-600',
                                            tablet: 'text-xl font-bold text-blue-600',
                                            desktop: 'text-2xl font-bold text-blue-600'
                                        })">
                                            {{ CurrencyUtil.formatCurrency(data.total_amount) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else :class="getResponsiveClasses({
                        mobile: 'flex justify-center p-4',
                        tablet: 'flex justify-center p-5',
                        desktop: 'flex justify-center p-5'
                    })">
                        <p :class="getResponsiveClasses({
                            mobile: 'text-sm text-gray-600',
                            tablet: 'text-base text-gray-600',
                            desktop: 'text-base text-gray-600'
                        })">No sales found</p>
                    </div>
                </div>
                <div v-else :class="getResponsiveClasses({
                    mobile: 'flex justify-center p-4',
                    tablet: 'flex justify-center p-5',
                    desktop: 'flex justify-center p-5'
                })">
                    <PageLoader />
                </div>
            </div>
        </BoxShadow>
        <div v-if="data">
            <div id="sales-report" class="hidden">
                <!-- Header -->
                <div style="
                        display: flex;
                        align-items: flex-start;
                        gap: 20px;
                        width: 100%;
                    ">
                    <div style="flex-shrink: 0;">
                        <img src="LOGO_PLACEHOLDER" alt="ALCESS Logo" style="width: 100px; height: auto;" />
                    </div>
                    <div style="text-align: left; flex-grow: 1;">
                        <h1 style="color: #00598a; font-size: 20px; margin: 0 0 5px 0; font-weight: bold;">
                            Alcess Laptop and Computer Accessories
                        </h1>
                        <p style="margin: 0; line-height: 1.6; font-size: 14px;">
                            DOOR 1, Yong BLDG PICHON MAGALLANES STREET<br />
                            DAVAO CITY<br />
                            Tel. 09531852360 / 09651934705<br />
                            Email: tiny.wenie1234@gmail.com
                        </p>
                    </div>
                </div>

                <h2 style="margin-top: 20px; margin-bottom: 20px; font-size: 18px; text-align: center; font-weight: bold;">SALES INVOICE</h2>
                
                <div style="margin-bottom: 15px; font-size: 14px; display: flex; justify-content: space-between;">
                    <div>
                        <p style="margin: 2px 0;"><strong>Customer:</strong> {{ data.customer_name || 'N/A' }}</p>
                        <p style="margin: 2px 0;"><strong>Address:</strong> {{ data.customer_address || 'N/A' }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 2px 0;"><strong>Date:</strong> {{ DateUtil.formatToMonthDayYear(data.created_at) }}</p>
                    </div>
                </div>

                <h3 style="margin-top: 20px; margin-bottom: 10px; font-size: 16px; font-weight: bold;">Sales Overview</h3>
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px">
                    <thead>
                        <tr>
                            <th style="
                                    border: 1px solid #ccc;
                                    background-color: #00598a;
                                    color: white;
                                    padding: 8px;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                ">
                                Product
                            </th>
                            <th style="
                                    border: 1px solid #ccc;
                                    background-color: #00598a;
                                    color: white;
                                    padding: 8px;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                ">
                                Quantity
                            </th>
                            <th style="
                                    border: 1px solid #ccc;
                                    background-color: #00598a;
                                    color: white;
                                    padding: 8px;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                ">
                                Unit Price
                            </th>
                            <th style="
                                    border: 1px solid #ccc;
                                    background-color: #00598a;
                                    color: white;
                                    padding: 8px;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                ">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sale in data.sale_items">
                            <td style="border: 1px solid #ccc; padding: 8px; vertical-align: middle">
                                {{ sale.product.product_name }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px; vertical-align: middle; text-align: center;">
                                {{ sale.quantity }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px; vertical-align: middle">
                                {{ CurrencyUtil.formatCurrency(sale.price) }}
                            </td>
                            <td style="border: 1px solid #ccc; padding: 8px; vertical-align: middle">
                                {{ CurrencyUtil.formatCurrency(sale.price * sale.quantity) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="border: 1px solid #ccc; padding: 8px; text-align: right">
                                <strong>Total: {{ CurrencyUtil.formatCurrency(data.total_amount) }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <div style="position: relative; margin-top: 30px;">
                    <div style="float: left; width: 45%;">
                        <h2 style="font-size: 16px;">Prepared by:</h2>
                        <div style="text-align: center;">
                            <span style="display: block; margin-top: 5px;">Blademier S. Canalita</span>
                        </div>
                    </div>
                    <div style="float: right; width: 45%;">
                        <h2 style="font-size: 16px;">Approved by:</h2>
                        <div style="text-align: center;">
                            <span style="display: block; margin-top: 5px;">Albert Von Daligdigan</span>
                        </div>
                    </div>
                    <div style="clear: both;"></div>
                </div>

                <div style="margin-top: 40px; text-align: center; font-size: 12px; line-height: 1.6;">
                    <p style="margin: 5px 0; font-weight: bold;">"LCD, misuse, negligence NOT INCLUDED for Warrantys"</p>
                    <p style="margin: 5px 0; font-weight: bold;">"1 MONTH WARRANTY - 1 WEEK REPLACEMENT"</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ISale } from '@/interfaces/ISale';
import useAxiosUtil from '@/utils/AxiosUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import DateUtil from '@/utils/DateUtil';
import UrlUtil from '@/utils/UrlUtil';
import { useResponsive } from '@/composables/useResponsive';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import Logo from "@/../img/logo.jpg";

const route = useRoute();
const router = useRouter();
const loadService = useAxiosUtil<null, ISale>();
const toast = useToast();
const data = ref<ISale | null>(null);

// Use responsive composable
const { getResponsiveClasses, isDesktop } = useResponsive();

const load = async () => {
    await loadService.get(`admin/sales/${route.params["id"]}`).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load sales");
            router.push({ name: "admin.pos.index" });
        }
    });
};

const printReport = () => {
    const printElement = document.getElementById("sales-report");
    if (!printElement) return;

    const logoUrl = Logo;
    const html = printElement.innerHTML.replace('src="LOGO_PLACEHOLDER"', `src="${logoUrl}"`);

    const printWindow = window.open("", "_blank", "width=1000,height=600");
    if (!printWindow) return;

    printWindow.document.write(`
    <html>
      <head>
        <title>Sales Report</title>
        <base href="${window.location.origin}/">
        <style>
          body { font-family: Arial, sans-serif; }
          img { width: 100px; height: auto; display: block; }
        </style>
      </head>
      <body>${html}</body>
    </html>
  `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.onload = () => {
        printWindow.print();
        printWindow.close();
    };
};



onMounted(() => {
    load();
});
</script>