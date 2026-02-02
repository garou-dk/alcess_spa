<template>
    <div>
        <header class="bg-blue-600 sticky top-0 z-50 shadow-md">
            <!-- Mobile Header (< 768px) -->
            <nav v-if="isMobile">
                <!-- Top row: Logo and Menu Toggle -->
                <div class="flex items-center justify-between p-3">
                    <RouterLink :to="{ name: 'home' }" class="flex items-center gap-2">
                        <div class="rounded-full bg-white p-1">
                            <img :src="Icon" class="h-10 w-10" alt="Store Icon" />
                        </div>
                        <h1 class="text-lg font-bold text-white">{{ appName }}</h1>
                    </RouterLink>
                    
                    <!-- Mobile action buttons -->
                    <div class="flex items-center gap-2">
                        <Button
                            icon="pi pi-facebook"
                            rounded
                            as="a"
                            href="https://www.facebook.com/alcesslaptopstore"
                            target="_blank"
                            class="!bg-white !text-blue-600"
                            size="small"
                        />
                        <Button
                            icon="pi pi-th-large"
                            rounded
                            severity="secondary"
                            size="small"
                            @click="goToBrowseProducts"
                            v-tooltip.bottom="'Browse Products'"
                        />
                        <div v-if="Page.user" class="flex items-center gap-2">
                            <CartButton size="small" />
                            <!-- Notification Bell -->
                            <OverlayBadge 
                                v-if="unreadCount > 0"
                                :value="unreadCount"
                                pt:pcBadge:root:class="bg-red-500! text-white !rounded-full !w-4 !h-4 !flex !items-center !justify-center !text-xs !font-semibold !leading-none"
                            >
                                <Button
                                    icon="pi pi-bell"
                                    rounded
                                    severity="secondary"
                                    size="small"
                                    @click="openNotifications"
                                />
                            </OverlayBadge>
                            <Button
                                v-else
                                icon="pi pi-bell"
                                rounded
                                severity="secondary"
                                size="small"
                                @click="openNotifications"
                            />
                            <!-- User Avatar -->
                            <button @click="openAvatar" class="cursor-pointer">
                                <Avatar
                                    v-if="Page.user && Page.user.image"
                                    :image="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.image}`)"
                                    shape="circle"
                                    style="width: 32px; height: 32px;"
                                />
                                <Avatar
                                    v-else
                                    :label="Page.user && Page.user.full_name[0]"
                                    shape="circle"
                                    class="bg-white! text-black!"
                                    style="width: 32px; height: 32px;"
                                />
                            </button>
                        </div>
                        <Button
                            v-else
                            icon="pi pi-user"
                            rounded
                            severity="secondary"
                            size="small"
                            as="router-link"
                            to="/login"
                            v-tooltip.bottom="'Login'"
                        />
                    </div>
                </div>
                
                <!-- Bottom row: Search bar (only on product pages) -->
                <div v-if="isProductPage" class="px-3 pb-3">
                    <form @submit.prevent="handleSearch">
                        <div class="relative">
                            <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="form.search"
                                type="text"
                                placeholder="Search products..."
                                class="w-full pl-10 pr-4 py-2 rounded-lg border-2 border-white bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-white/50 transition-all text-sm"
                            />
                        </div>
                    </form>
                </div>
            </nav>

            <!-- Tablet Header (768px - 1023px) -->
            <nav v-if="isTablet">
                <div class="flex items-center justify-between p-3">
                    <!-- Left: Logo -->
                    <RouterLink :to="{ name: 'home' }" class="flex items-center gap-3">
                        <div class="rounded-full bg-white p-1">
                            <img :src="Icon" class="h-12 w-12" alt="Store Icon" />
                        </div>
                        <h1 class="text-xl font-bold text-white">{{ appName }}</h1>
                    </RouterLink>
                    
                    <!-- Center: Search (only on product pages) -->
                    <div v-if="isProductPage" class="flex-1 max-w-md mx-6">
                        <form @submit.prevent="handleSearch">
                            <div class="relative">
                                <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="Search for products..."
                                    class="w-full pl-10 pr-4 py-2 rounded-lg border-2 border-white bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-white/50 transition-all"
                                />
                            </div>
                        </form>
                    </div>
                    
                    <!-- Right: Action buttons -->
                    <div class="flex items-center gap-3">
                        <Button
                            icon="pi pi-facebook"
                            rounded
                            as="a"
                            href="https://www.facebook.com/alcesslaptopstore"
                            target="_blank"
                            class="!bg-white !text-blue-600"
                            v-tooltip.bottom="'Facebook'"
                        />
                        <Button
                            icon="pi pi-th-large"
                            rounded
                            severity="secondary"
                            @click="goToBrowseProducts"
                            v-tooltip.bottom="'Browse Products'"
                        />
                        <div v-if="Page.user" class="flex items-center gap-3">
                            <CartButton size="small" />
                            <!-- Notification Bell -->
                            <OverlayBadge 
                                v-if="unreadCount > 0"
                                :value="unreadCount"
                                pt:pcBadge:root:class="bg-red-500! text-white !rounded-full !w-5 !h-5 !flex !items-center !justify-center !text-xs !font-semibold !leading-none"
                            >
                                <Button
                                    icon="pi pi-bell"
                                    rounded
                                    severity="secondary"
                                    @click="openNotifications"
                                />
                            </OverlayBadge>
                            <Button
                                v-else
                                icon="pi pi-bell"
                                rounded
                                severity="secondary"
                                @click="openNotifications"
                            />
                            <!-- User Avatar -->
                            <button @click="openAvatar" class="cursor-pointer">
                                <Avatar
                                    v-if="Page.user && Page.user.image"
                                    :image="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.image}`)"
                                    shape="circle"
                                    style="width: 40px; height: 40px;"
                                />
                                <Avatar
                                    v-else
                                    :label="Page.user && Page.user.full_name[0]"
                                    shape="circle"
                                    class="bg-white! text-black!"
                                    style="width: 40px; height: 40px;"
                                />
                            </button>
                        </div>
                        <Button
                            v-else
                            icon="pi pi-user"
                            rounded
                            severity="secondary"
                            as="router-link"
                            to="/login"
                            v-tooltip.bottom="'Login'"
                        />
                    </div>
                </div>
            </nav>

            <!-- Desktop Header (≥ 1024px) - Original Layout -->
            <nav v-if="isDesktop" class="p-2">
                <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2">
                            <RouterLink :to="{ name: 'home' }">
                                <div class="flex">
                                    <div class="px-2">
                                        <div class="rounded-full bg-white">
                                            <img
                                                :src="Icon"
                                                class="h-16 w-16"
                                                alt="Store Icon"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex items-center px-2">
                                        <h1 class="text-3xl font-bold text-white">
                                            {{ appName }}
                                        </h1>
                                    </div>
                                </div>
                            </RouterLink>
                        </div>

                        <div class="flex items-center w-full flex-1 justify-end">
                            <div v-if="Page.user" class="flex items-center gap-3 w-full">
                                <!-- Search Bar - Only show on product pages -->
                                <div v-if="isProductPage" class="flex-1 max-w-4xl mx-6">
                                    <form @submit.prevent="handleSearch">
                                        <div class="relative">
                                            <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                                            <input
<NavBar 
            mode="customer" 
            :show-search="isProductPage" 
        />

        <!-- Popovers removed as they are now handled by NavBar component -->
        <div>
            <RouterView />
        </div>
        

        <Dialog
            v-model:visible="addressForm"
            modal
            header="Address"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <AddressForm />
        </Dialog>
        <Dialog
            v-model:visible="securityForm"
            modal
            header="Security Settings"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <SecurityForm />
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import {
    SearchErrorInterface,
    SearchProductInterface,
} from "@/interfaces/SearchProductInterface";
import { reactive, ref, computed, onMounted, onUnmounted, provide, watch, nextTick } from "vue";
import { useResponsive } from "@/composables/useResponsive";
import Icon from "@/../img/logo.png";
import Page from "@/stores/Page";
import UrlUtil from "@/utils/UrlUtil";
import { Popover, Badge, OverlayBadge } from "primevue";
import LogoutButton from "@/components/LogoutButton.vue";
import NavBar from "@/components/NavBar.vue";
import CartButton from "@/components/CartButton.vue";

import AddressForm from "@/components/forms/AddressForm.vue";
import SecurityForm from "@/components/forms/SecurityForm.vue";
import { useRouter } from "vue-router";
import useAxiosUtil from "@/utils/AxiosUtil";
import { IOrderNotification } from "@/interfaces/IOrderNotification";
import { useToast } from "vue-toastification";
import DateUtil from "@/utils/DateUtil";
import { useEcho } from "@laravel/echo-vue";

const { isMobile, isTablet, isDesktop } = useResponsive();
const appName = import.meta.env.VITE_APP_NAME;
const form: SearchProductInterface = reactive({
    search: null,
});
const errors: SearchErrorInterface = reactive({
    search: [],
});

// Watch for manual changes to form.search (typing in navbar) with debounce
let navbarSearchTimeout: number | null = null;
let isUpdatingFromRouteNavbar = false;

watch(() => form.search, (newValue, oldValue) => {
    console.log('⌨️ CustomerIndex form.search changed (user typing):', {
        newValue,
        oldValue,
        currentRoute: router.currentRoute.value.name,
        currentQuery: router.currentRoute.value.query.q,
        isUpdatingFromRoute: isUpdatingFromRouteNavbar
    });
    
    // Skip if we're updating from route watcher to avoid circular updates
    if (isUpdatingFromRouteNavbar) {
        console.log('⏭️ CustomerIndex: Skipping - updating from route');
        isUpdatingFromRouteNavbar = false;
        return;
    }
    
    // Clear existing timeout
    if (navbarSearchTimeout) {
        clearTimeout(navbarSearchTimeout);
    }
    
    // Set new timeout for debounced search
    navbarSearchTimeout = window.setTimeout(() => {
        const trimmedValue = newValue ? newValue.trim() : '';
        const currentQuery = router.currentRoute.value.query.q as string | undefined;
        
        console.log('⏰ CustomerIndex debounce executed:', {
            trimmedValue,
            currentQuery,
            willTriggerSearch: trimmedValue !== currentQuery
        });
        
        // Only trigger if value changed from current query
        if (trimmedValue && trimmedValue !== currentQuery) {
            console.log('🚀 CustomerIndex: Auto-triggering search');
            handleSearch();
        } else if (!trimmedValue && currentQuery) {
            console.log('🚀 CustomerIndex: Auto-clearing search');
            handleSearch();
        }
    }, 500); // 500ms debounce
});

const avatarElement = ref<null | InstanceType<typeof Popover>>();
const notificationElement = ref<null | InstanceType<typeof Popover>>();
const addressForm = ref<boolean>(false);
const securityForm = ref<boolean>(false);
const router = useRouter();

// Check if current route is a product-related page or home page
const isProductPage = computed(() => {
    const productRoutes = [
        'customer.search-product',
        'customer.product-info.index',
        'customer.product-category',
        'customer.browse-products',
        'customer.home.index',
        'customer.cart.index',
        'customer.order.index',
        'home'
    ];
    return productRoutes.includes(router.currentRoute.value.name as string);
});

interface Notification {
    id: number;
    title: string;
    message: string;
    time: string;
    read: boolean;
    icon: string;
    color: string;
}

const activeTab = ref<'all' | 'unread'>('all');

const notifications = ref<IOrderNotification[]>([
]);

const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.is_read).length;
});

const filteredNotifications = computed(() => {
    if (activeTab.value === 'unread') {
        return notifications.value.filter(n => !n.is_read);
    }
    return notifications.value;
});

const openAvatar = (event: Event) => {
    if (avatarElement.value) {
        avatarElement.value.toggle(event);
    }
};

const openNotifications = (event: Event) => {
    if (notificationElement.value) {
        if (isNotificationOpen.value) {
            notificationElement.value.hide();
            isNotificationOpen.value = false;
        } else {
            notificationElement.value.show(event);
            isNotificationOpen.value = true;
        }
    }
};

const isNotificationOpen = ref(false);
let clickOutsideListener: ((event: MouseEvent) => void) | null = null;

const onNotificationShow = () => {
    isNotificationOpen.value = true;
    // Add click outside listener
    setTimeout(() => {
        clickOutsideListener = (event: MouseEvent) => {
            const popoverEl = notificationElement.value?.$el;
            const target = event.target as Node;
            
            // Check if click is inside the popover
            if (popoverEl && popoverEl.contains(target)) {
                return;
            }
            
            // Click is outside, close the popover
            if (popoverEl && !popoverEl.contains(target)) {
                notificationElement.value?.hide();
                isNotificationOpen.value = false;
                // Remove listener
                if (clickOutsideListener) {
                    document.removeEventListener('click', clickOutsideListener);
                    clickOutsideListener = null;
                }
            }
        };
        document.addEventListener('click', clickOutsideListener);
    }, 100);
};

const onNotificationHide = () => {
    isNotificationOpen.value = false;
    if (clickOutsideListener) {
        document.removeEventListener('click', clickOutsideListener);
        clickOutsideListener = null;
    }
};

const submitMarkReadService = useAxiosUtil();

const markAsRead = async (id: number) => {
    const notification = notifications.value.find(n => n.order_notification_id === id);
    if (notification && !notification.is_read) {
        notification.is_read = true;
    }
    notificationElement.value?.hide();
    isNotificationOpen.value = false;
    router.push({ name: 'customer.order.index' });
    await submitMarkReadService.patch(`customer/order-notifications/mark-as-read/${notification.order_notification_id}`, null).then(() => {
    });
};

const markAllAsRead = () => {
    notifications.value.forEach(n => n.is_read = true);
};

const openAddressForm = () => {
    addressForm.value = true;
    avatarElement.value?.hide();
};

const goToOrder = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
    addressForm.value = false;
    router.push({ name: "customer-order" });
    avatarElement.value?.hide();
};

const goToInvoice = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
    addressForm.value = false;
    router.push({ name: "customer-invoice" });
    avatarElement.value?.hide();
};

const openSecurityForm = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
    addressForm.value = false;
    avatarElement.value?.hide();
    nextTick(() => {
        securityForm.value = true;
    });
};

const goToBrowseProducts = () => {
    router.push({ name: "customer.browse-products" });
};

const goToProfile = () => {
    router.push({ name: "customer.profile" });
    avatarElement.value?.hide();
};

const handleSearch = () => {
    console.log('🔎 CustomerIndex handleSearch called:', {
        formSearch: form.search,
        trimmed: form.search ? form.search.trim() : '',
        currentRoute: router.currentRoute.value.name,
        currentQuery: router.currentRoute.value.query.q
    });
    
    if (form.search && form.search.trim()) {
        console.log('➡️ CustomerIndex: Navigating to search with query:', form.search);
        router.push({
            name: 'customer.search-product',
            query: { q: form.search }
        });
    } else {
        console.log('⬅️ CustomerIndex: Navigating to browse (empty search)');
        // If search is empty, redirect to browse all products
        router.push({
            name: 'customer.browse-products'
        });
    }
};

const loadService = useAxiosUtil<null, IOrderNotification[]>();
const toast = useToast();

const loadNotifications = async () => {
    await loadService.get("customer/order-notifications").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            notifications.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load notifications");
        }
    });
};

let leave: (() => void) | null = null;

if (Page.user) {
    const echoResult = useEcho(
        `order.${Page.user.user_id}`,
        [".customer-order.event"],
        (value: IOrderNotification) => {
            notifications.value.unshift(value);
        },
    );
    leave = echoResult.leave;
}

// Watch for route query changes to sync search input with URL
watch(() => router.currentRoute.value.query.q, (newQuery, oldQuery) => {
    console.log('🌐 CustomerIndex route.query.q watcher:', {
        newQuery,
        oldQuery,
        currentFormSearch: form.search,
        currentRoute: router.currentRoute.value.name
    });
    
    isUpdatingFromRouteNavbar = true;
    
    if (newQuery && typeof newQuery === 'string') {
        console.log('✅ CustomerIndex: Setting form.search to:', newQuery);
        form.search = newQuery;
    } else {
        console.log('🧹 CustomerIndex: Clearing form.search');
        form.search = '';
    }
}, { immediate: true });

onMounted(() => {
    if (Page.user) {
        loadNotifications();
    }
    window.addEventListener('open-security-form', openSecurityForm);
    window.addEventListener('open-address-form', openAddressForm);
    window.addEventListener('go-to-profile', goToProfile);
});

onUnmounted(() => {
    if (leave) {
        leave();
    }
    if (clickOutsideListener) {
        document.removeEventListener('click', clickOutsideListener);
        clickOutsideListener = null;
    }
    window.removeEventListener('open-security-form', openSecurityForm);
    window.removeEventListener('open-address-form', openAddressForm);
    window.removeEventListener('go-to-profile', goToProfile);
});
</script>