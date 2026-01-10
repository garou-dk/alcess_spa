<template>
    <div :class="getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 p-3',
        tablet: 'min-h-screen bg-gray-50 p-4',
        desktop: 'min-h-screen bg-gray-50 p-5'
    })">
        <BoxShadow :class="getResponsiveClasses({
            mobile: 'p-3',
            tablet: 'p-4',
            desktop: 'p-5'
        })">
            <div class="w-full">
                <div v-if="!loadService.request.loading">
                    <div v-if="data">
                        <!-- Invoice Header - Responsive -->
                        <div :class="getResponsiveClasses({
                            mobile: 'mb-4 p-3 bg-gray-50 rounded-lg',
                            tablet: 'mb-6 p-4 bg-gray-50 rounded-lg',
                            desktop: 'mb-6 p-4 bg-gray-50 rounded-lg'
                        })">
                            <div :class="getResponsiveClasses({
                                mobile: 'space-y-3',
                                tablet: 'grid grid-cols-2 gap-4',
                                desktop: 'flex flex-wrap justify-around'
                            })">
                                <div :class="getResponsiveClasses({
                                    mobile: 'p-2',
                                    tablet: 'p-2',
                                    desktop: 'p-2 grow'
                                })">
                                    <p :class="getResponsiveClasses({
                                        mobile: 'font-semibold text-gray-600 text-sm',
                                        tablet: 'font-semibold text-gray-600',
                                        desktop: 'font-semibold text-gray-600'
                                    })">Order ID</p>
                                    <p :class="getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })">{{ data.sale_code }}</p>
                                </div>
                                <div :class="getResponsiveClasses({
                                    mobile: 'p-2',
                                    tablet: 'p-2',
                                    desktop: 'p-2 grow'
                                })">
                                    <p :class="getResponsiveClasses({
                                        mobile: 'font-semibold text-gray-600 text-sm',
                                        tablet: 'font-semibold text-gray-600',
                                        desktop: 'font-semibold text-gray-600'
                                    })">Date Created</p>
                                    <p :class="getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })">{{ DateUtil.formatToMonthDayYear(data.created_at) }}</p>
                                </div>
                                <div :class="getResponsiveClasses({
                                    mobile: 'p-2',
                                    tablet: 'p-2',
                                    desktop: 'p-2 grow'
                                })">
                                    <p :class="getResponsiveClasses({
                                        mobile: 'font-semibold text-gray-600 text-sm',
                                        tablet: 'font-semibold text-gray-600',
                                        desktop: 'font-semibold text-gray-600'
                                    })">Payment Method</p>
                                    <p :class="getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })">{{ data.payment_method }}</p>
                                </div>
                                <div :class="getResponsiveClasses({
                                    mobile: 'p-2',
                                    tablet: 'p-2',
                                    desktop: 'p-2 grow'
                                })">
                                    <p :class="getResponsiveClasses({
                                        mobile: 'font-semibold text-gray-600 text-sm',
                                        tablet: 'font-semibold text-gray-600',
                                        desktop: 'font-semibold text-gray-600'
                                    })">Prepared By</p>
                                    <p :class="getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })">Albert Von Daligdigan</p>
                                </div>
                                <div :class="getResponsiveClasses({
                                    mobile: 'flex justify-center pt-2 border-t border-gray-200',
                                    tablet: 'col-span-2 flex justify-center pt-2 border-t border-gray-200',
                                    desktop: 'flex items-center justify-center'
                                })">
                                    <Button 
                                        type="button" 
                                        label="Print" 
                                        icon="pi pi-print" 
                                        :class="[
                                            '!bg-blue-600 hover:!bg-blue-700 !text-white',
                                            getResponsiveClasses({
                                                mobile: '!text-sm !px-4 !py-2',
                                                tablet: '!text-base !px-6 !py-3',
                                                desktop: '!text-base !px-6 !py-3'
                                            })
                                        ]"
                                        @click="printReport()" 
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Desktop DataTable -->
                        <DataTable 
                            v-if="isDesktop"
                            :value="data.sale_items" 
                            show-gridlines
                        >
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
                                <div class="text-end space-y-2">
                                    <div v-if="orderData && orderData.shipping_fee" class="text-sm">
                                        <span>Shipping Fee:</span>
                                        <span class="font-semibold ml-2">{{ CurrencyUtil.formatCurrency(orderData.shipping_fee) }}</span>
                                    </div>
                                    <p class="font-semibold text-lg pt-2 border-t border-gray-200">
                                        <span>Total:</span>
                                        <span class="ml-2">{{ CurrencyUtil.formatCurrency(data.total_amount) }}</span>
                                    </p>
                                </div>
                            </template>
                        </DataTable>

                        <!-- Mobile/Tablet Card Layout -->
                        <div v-else>
                            <div :class="getResponsiveClasses({
                                mobile: 'space-y-3',
                                tablet: 'space-y-4',
                                desktop: 'space-y-4'
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
                                                mobile: 'w-12 h-12 flex-shrink-0 bg-gray-100',
                                                tablet: 'w-16 h-16 flex-shrink-0 bg-gray-100',
                                                desktop: 'w-16 h-16 flex-shrink-0 bg-gray-100'
                                            })"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <h3 :class="getResponsiveClasses({
                                                mobile: 'font-semibold text-gray-900 text-sm',
                                                tablet: 'font-semibold text-gray-900 text-base',
                                                desktop: 'font-semibold text-gray-900 text-base'
                                            })">
                                                {{ item.product.product_name }}
                                            </h3>
                                        </div>
                                    </div>

                                    <!-- Item Details -->
                                    <div :class="getResponsiveClasses({
                                        mobile: 'grid grid-cols-2 gap-3 pt-3 border-t border-gray-100',
                                        tablet: 'grid grid-cols-3 gap-4 pt-3 border-t border-gray-100',
                                        desktop: 'grid grid-cols-3 gap-4 pt-3 border-t border-gray-100'
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
                                            })">Total</p>
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

                            <!-- Mobile/Tablet Summary -->
                            <div :class="getResponsiveClasses({
                                mobile: 'mt-4 bg-gray-50 rounded-lg p-3 border border-gray-200',
                                tablet: 'mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200',
                                desktop: 'mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200'
                            })">
                                <div class="space-y-2">
                                    <div v-if="orderData && orderData.shipping_fee" class="flex justify-between items-center">
                                        <span :class="getResponsiveClasses({
                                            mobile: 'text-sm text-gray-600',
                                            tablet: 'text-base text-gray-600',
                                            desktop: 'text-base text-gray-600'
                                        })">Shipping Fee:</span>
                                        <span :class="getResponsiveClasses({
                                            mobile: 'text-sm font-semibold text-gray-900',
                                            tablet: 'text-base font-semibold text-gray-900',
                                            desktop: 'text-base font-semibold text-gray-900'
                                        })">{{ CurrencyUtil.formatCurrency(orderData.shipping_fee) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                                        <span :class="getResponsiveClasses({
                                            mobile: 'text-base font-bold text-gray-900',
                                            tablet: 'text-lg font-bold text-gray-900',
                                            desktop: 'text-lg font-bold text-gray-900'
                                        })">Total:</span>
                                        <span :class="getResponsiveClasses({
                                            mobile: 'text-lg font-bold text-blue-600',
                                            tablet: 'text-xl font-bold text-blue-600',
                                            desktop: 'text-xl font-bold text-blue-600'
                                        })">{{ CurrencyUtil.formatCurrency(data.total_amount) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else :class="getResponsiveClasses({
                        mobile: 'flex justify-center p-4',
                        tablet: 'flex justify-center p-6',
                        desktop: 'flex justify-center p-5'
                    })">
                        <p :class="getResponsiveClasses({
                            mobile: 'text-sm text-gray-600',
                            tablet: 'text-base text-gray-600',
                            desktop: 'text-base text-gray-600'
                        })">No invoice found</p>
                    </div>
                </div>
                <div v-else :class="getResponsiveClasses({
                    mobile: 'flex justify-center p-4',
                    tablet: 'flex justify-center p-6',
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
                        <p style="margin: 2px 0;"><strong>Order ID:</strong> {{ orderData?.order_public_id || 'N/A' }}</p>
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
                        <tr v-if="orderData && orderData.shipping_fee">
                            <td colspan="4" style="border: 1px solid #ccc; padding: 8px; text-align: right">
                                Shipping Fee: {{ CurrencyUtil.formatCurrency(orderData.shipping_fee) }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="border: 1px solid #ccc; padding: 8px; text-align: right; border-top: 2px solid #000;">
                                <strong>Total: {{ CurrencyUtil.formatCurrency(data.total_amount) }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <div style="position: relative; margin-top: 30px;">
                    <div style="float: left; width: 45%;">
                        <h2 style="font-size: 16px;">Approved by:</h2>
                        <div style="text-align: center;">
                            <span style="display: block; margin-top: 5px;">Blademier S. Canalita</span>
                        </div>
                    </div>
                    <div style="float: right; width: 45%;">
                        <h2 style="font-size: 16px;">Prepared by:</h2>
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
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useResponsive } from '@/composables/useResponsive';
import Logo from "@/../img/logo.jpg";

// Use responsive composable
const { getResponsiveClasses, isDesktop } = useResponsive();

const route = useRoute();
const router = useRouter();
const loadService = useAxiosUtil<null, ISale>();
const orderService = useAxiosUtil<null, any>();
const toast = useToast();
const data = ref<ISale | null>(null);
const orderData = ref<any | null>(null);

const load = async () => {
    const saleId = route.params["saleId"];
    const orderId = route.params["orderId"];
    
    // Load sale data
    await loadService.get(`admin/sales/${saleId}`).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            data.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load invoice");
            router.push({ name: "admin.order.index" });
        }
    });
    
    // Load order data to get shipping fee
    if (orderId) {
        await orderService.get(`admin/orders`).then(() => {
            if (orderService.request.status === 200 && orderService.request.data) {
                // Find the specific order
                const orders = orderService.request.data as any[];
                orderData.value = orders.find((o: any) => o.order_id == orderId);
            }
        });
    }
};

const printReport = () => {
    const printElement = document.getElementById("sales-report");
    if (!printElement) return;

    // Convert logo to absolute URL
    const logoUrl = new URL(Logo, window.location.origin).href;
    const html = printElement.innerHTML.replace('src="LOGO_PLACEHOLDER"', `src="${logoUrl}"`);

    const printWindow = window.open("", "_blank", "width=1000,height=600");
    if (!printWindow) return;

    printWindow.document.write(`
    <html>
      <head>
        <title>Sales Invoice</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          img { width: 100px; height: auto; display: block; }
          @media print {
            body { margin: 0; }
          }
        </style>
      </head>
      <body>${html}</body>
    </html>
  `);

    printWindow.document.close();
    printWindow.focus();
    
    // Wait for images to load before printing
    printWindow.onload = () => {
        // Small delay to ensure images are loaded
        setTimeout(() => {
            printWindow.print();
        }, 250);
    };
    
    // Handle after print - redirect back to orders with auto-open modal
    printWindow.onafterprint = () => {
        printWindow.close();
        handleAfterPrint();
    };
    
    // Fallback for browsers that don't support onafterprint
    printWindow.onbeforeunload = () => {
        handleAfterPrint();
    };
};

const handleAfterPrint = () => {
    // Check if we should return to order view with auto-open
    if (route.query.returnToOrder === 'true') {
        const orderId = route.query.orderId;
        router.push({ 
            name: 'admin.order.index',
            query: { 
                openOrderId: orderId,
                showDeliveryModal: 'true'
            }
        });
    }
};

onMounted(() => {
    load();
});
</script>
