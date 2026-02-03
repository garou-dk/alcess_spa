<template>
    <div>
        <NavBar 
            mode="customer" 
            :show-search="isProductPage" 
            :transparent="false"
        />

        <div class="">
            <RouterView />
        </div>

        <!-- Sticky Icons -->
        <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-3">
            <button @click="goToBrowseProducts" 
                class="w-12 h-12 rounded-full bg-white text-blue-600 shadow-lg shadow-blue-500/20 flex items-center justify-center hover:bg-blue-50 transition-all hover:scale-110 tooltip-btn group relative"
                title="Browse Products">
                <i class="pi pi-th-large text-xl"></i>
                <span class="absolute right-full mr-2 bg-gray-900 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Browse Products</span>
            </button>
            <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" 
                class="w-12 h-12 rounded-full bg-blue-600 text-white shadow-lg shadow-blue-500/30 flex items-center justify-center hover:bg-blue-700 transition-all hover:scale-110 tooltip-btn group relative"
                title="Message Us">
                <i class="pi pi-facebook text-xl"></i>
                <span class="absolute right-full mr-2 bg-gray-900 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Message Us</span>
            </a>
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
import gsap from "gsap";

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

const isDashboard = computed(() => {
    return router.currentRoute.value.name === 'customer.home.index';
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

// Initialize GSAP animations for sticky buttons
const initStickyButtonsAnimation = () => {
    // Animate sticky floating action buttons with bouncy entrance
    gsap.from('.fixed.bottom-6.right-6 button, .fixed.bottom-6.right-6 a', {
        duration: 0.6,
        scale: 0,
        opacity: 0,
        stagger: 0.15,
        ease: 'back.out(1.7)',
        delay: 0.5
    });
};

onMounted(() => {
    if (Page.user) {
        loadNotifications();
    }
    window.addEventListener('open-security-form', openSecurityForm);
    window.addEventListener('open-address-form', openAddressForm);
    window.addEventListener('go-to-profile', goToProfile);
    
    // Initialize GSAP animations
    nextTick(() => {
        initStickyButtonsAnimation();
    });
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