<template>
    <header :class="['w-full z-50 transition-all duration-300', transparent && !isScrolled ? 'absolute top-0 left-0 bg-transparent' : 'sticky top-0 bg-white shadow-md']">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Admin Sidebar Toggle & Logo -->
                <div class="flex items-center gap-4">
                    <button v-if="mode === 'admin'" @click="toggleSidebar" 
                        :class="['p-2 rounded-lg hover:bg-gray-100 transition-colors', transparent && !isScrolled ? 'text-white hover:bg-white/10' : 'text-gray-600']">
                        <i class="pi pi-bars text-2xl"></i>
                    </button>
                    
                    <div class="flex-shrink-0 flex items-center cursor-pointer" @click="goHome">
                        <img :src="Logo" alt="Alcess Logo" class="h-10 w-auto sm:h-12" />
                        <span :class="['ml-3 text-xl font-bold tracking-tight hidden sm:block', transparent && !isScrolled ? 'text-white' : 'text-gray-900']">
                            Alcess
                        </span>
                    </div>
                </div>

                <!-- Deskstop Navigation (Search & Links) -->
                <div class="hidden md:flex flex-1 items-center justify-center px-8 lg:ml-6 lg:justify-end">
                     <div v-if="(mode === 'customer' || mode === 'guest') && showSearch" class="max-w-lg w-full">
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="pi pi-search text-gray-400"></i>
                            </div>
                            <input id="search" name="search" v-model="searchQuery" @keyup.enter="handleSearch"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                                placeholder="Search products..." type="search" />
                        </div>
                    </div>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-8 ml-4">
                    <template v-if="mode === 'customer' || mode === 'guest'">
                         <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" 
                           :class="['flex items-center gap-2 font-medium hover:opacity-80 transition-colors', 
                                     transparent && !isScrolled ? 'text-white' : 'text-gray-700 hover:text-blue-600']">
                            <i class="pi pi-facebook text-xl"></i>
                            <span class="hidden lg:inline">Message Us</span>
                        </a>
                        <button @click="goToBrowse" 
                            :class="['flex items-center gap-2 font-medium hover:opacity-80 transition-colors', 
                                      transparent && !isScrolled ? 'text-white' : 'text-gray-700 hover:text-blue-600']">
                            <i class="pi pi-th-large text-xl"></i>
                            <span class="hidden lg:inline">Browse</span>
                        </button>
                    </template>

                    <!-- Auth/User Actions -->
                    <div class="flex items-center gap-4">
                        <template v-if="Page.user">
                            <!-- User Logged In -->
                            
                            <!-- Notifications -->
                            <div v-if="mode === 'customer' || mode === 'admin'" class="relative">
                                <button @click="toggleNotification" class="relative p-2 rounded-full hover:bg-gray-100 transition-colors">
                                    <i :class="['pi pi-bell text-xl', transparent && !isScrolled ? 'text-white hover:text-gray-700' : 'text-gray-600']"></i>
                                    <span v-if="unreadCount > 0" class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full animate-pulse">
                                        {{ unreadCount }}
                                    </span>
                                </button>
                                
                                <!-- Notification Popover -->
                                <Popover ref="notificationElement" class="w-80 sm:w-96 shadow-xl border-0 !p-0">
                                    <div class="flex flex-col max-h-[400px]">
                                        <!-- Header -->
                                        <div class="flex items-center justify-between p-3 border-b bg-gray-50 rounded-t-lg">
                                            <h3 class="font-bold text-gray-800">Notifications</h3>
                                            <Button v-if="unreadCount > 0" @click="markAllAsRead" label="Mark all read" link size="small" class="!p-0 text-sm" />
                                        </div>
                                        
                                        <!-- Tabs -->
                                        <div class="flex border-b text-sm font-medium text-center text-gray-500 bg-white">
                                            <button @click="handleTabClick('all')" :class="['flex-1 py-2', activeTab === 'all' ? 'text-blue-600 border-b-2 border-blue-600' : 'hover:bg-gray-50']">
                                                All
                                            </button>
                                            <button @click="handleTabClick('unread')" :class="['flex-1 py-2', activeTab === 'unread' ? 'text-blue-600 border-b-2 border-blue-600' : 'hover:bg-gray-50']">
                                                Unread
                                            </button>
                                        </div>

                                        <!-- List -->
                                        <div class="overflow-y-auto flex-1">
                                            <div v-if="filteredNotifications.length === 0" class="p-8 text-center text-gray-500">
                                                <i class="pi pi-bell-slash text-2xl mb-2"></i>
                                                <p class="text-xs">No notifications</p>
                                            </div>
                                            <div v-else v-for="note in filteredNotifications" :key="note.order_notification_id" 
                                                @click="markAsRead(note)"
                                                :class="['p-3 border-b hover:bg-gray-50 cursor-pointer transition-colors', !note.is_read ? 'bg-blue-50/50' : '']">
                                                <div class="flex gap-3">
                                                    <div class="flex-shrink-0 mt-1">
                                                        <i :class="getNotificationIcon(note.notification_type || 'info') + ' text-blue-500'"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-800">{{ note.message }}</p>
                                                        <p class="text-xs text-gray-500 mt-1">{{ formatTime(note.created_at) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </Popover>
                            </div>

                             <button v-if="mode === 'customer'" @click="goToCart" class="relative group">
                                <i :class="['pi pi-shopping-cart text-2xl', transparent && !isScrolled ? 'text-white' : 'text-gray-700 group-hover:text-blue-600']"></i>
                                <span v-if="cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full">
                                    {{ cartCount }}
                                </span>
                            </button>

                            <div class="relative ml-2" ref="userMenuRef">
                                <button @click="toggleUserMenu" class="flex items-center gap-2 focus:outline-none">
                                    <Avatar 
                                        :image="userImage" 
                                        :label="userInitials" 
                                        shape="circle" 
                                        class="cursor-pointer border-2 border-white shadow-sm"
                                    />
                                    <div class="hidden lg:block text-left">
                                        <p :class="['text-sm font-semibold', transparent && !isScrolled ? 'text-white' : 'text-gray-900']">
                                            {{ Page.user.full_name }}
                                        </p>
                                        <p :class="['text-xs', transparent && !isScrolled ? 'text-blue-100' : 'text-gray-500']">
                                            {{ Page.user.role?.role_name || 'User' }}
                                        </p>
                                    </div>
                                    <i :class="['pi pi-chevron-down text-xs ml-1', transparent && !isScrolled ? 'text-white' : 'text-gray-500']"></i>
                                </button>
                                
                                <!-- Dropdown -->
                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95"
                                >
                                    <div v-if="isUserMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-1 ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden z-50">
                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <p class="text-sm font-bold text-gray-900 truncate">{{ Page.user.full_name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ Page.user.email }}</p>
                                        </div>
                                        <template v-if="mode === 'admin'">
                                             <router-link :to="{ name: 'admin.dashboard.index' }" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="pi pi-home mr-2 text-blue-500"></i> Dashboard
                                            </router-link>
                                        </template>
                                        <template v-else>
                                            <router-link :to="{ name: 'customer.profile' }" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="pi pi-user mr-2 text-blue-500"></i> Profile
                                            </router-link>
                                            <router-link :to="{ name: 'customer.orders' }" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="pi pi-shopping-bag mr-2 text-blue-500"></i> My Orders
                                            </router-link>
                                        </template>
                                        <button @click="handleLogout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class="pi pi-sign-out mr-2"></i> Logout
                                        </button>
                                    </div>
                                </transition>
                            </div>
                        </template>
                        <template v-else>
                            <!-- Guest -->
                            <!-- <router-link :to="{ name: 'auth.login' }" 
                                :class="['font-semibold', transparent && !isScrolled ? 'text-white hover:text-blue-100' : 'text-gray-700 hover:text-blue-600']">
                                Log in
                            </router-link> -->
                            <router-link :to="{ name: 'auth.login' }" 
                                class="btn-primary px-5 py-2.5 rounded-full font-bold shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5 transition-all text-sm">
                                Login / Sign up
                            </router-link>
                        </template>
                    </div>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen" 
                        :class="['p-2 rounded-md focus:outline-none', transparent && !isScrolled && !isMobileMenuOpen ? 'text-white' : 'text-gray-600']">
                        <i :class="isMobileMenuOpen ? 'pi pi-times text-2xl' : 'pi pi-bars text-2xl'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform -translate-y-4 opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform -translate-y-4 opacity-0"
        >
            <div v-if="isMobileMenuOpen" class="md:hidden bg-white shadow-xl border-t border-gray-100">
                <div class="px-4 py-6 space-y-4">
                    <template v-if="Page.user">
                        <div class="flex items-center gap-3 px-2 pb-4 border-b border-gray-100">
                            <Avatar :image="userImage" :label="userInitials" shape="circle" size="large" />
                            <div>
                                <p class="font-bold text-gray-900">{{ Page.user.full_name }}</p>
                                <p class="text-sm text-gray-500">{{ Page.user.email }}</p>
                            </div>
                        </div>
                         <template v-if="mode === 'admin'">
                             <router-link :to="{ name: 'admin.dashboard.index' }" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                Dashboard
                            </router-link>
                         </template>
                         <template v-else>
                            <router-link :to="{ name: 'home' }" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                Home
                            </router-link>
                            <router-link :to="{ name: 'customer.profile' }" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                My Profile
                            </router-link>
                            <router-link :to="{ name: 'customer.orders' }" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                My Orders
                            </router-link>
                         </template>
                         <button @click="handleLogout" class="w-full text-left block px-3 py-2 rounded-lg text-base font-medium text-red-600 hover:bg-red-50">
                            Logout
                        </button>
                    </template>
                    <template v-else>
                        <router-link :to="{ name: 'auth.login' }" class="block w-full text-center btn-primary py-3 rounded-lg font-bold">
                            Login / Sign up
                        </router-link>
                        <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="pi pi-facebook mr-2"></i> Message Us
                        </a>
                        <button @click="goToBrowse" class="w-full text-left px-3 py-2 rounded-lg text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="pi pi-th-large mr-2"></i> Browse Products
                        </button>
                    </template>
                </div>
            </div>
        </transition>
    </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, defineProps, inject, watch } from 'vue';
import { useRouter } from 'vue-router';
import Page from "@/stores/Page";
import UrlUtil from "@/utils/UrlUtil";
import Logo from "@/../img/logo.png";
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import Popover from 'primevue/popover';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useToast } from 'vue-toastification';
import { useEcho } from "@laravel/echo-vue";
import { IOrderNotification } from "@/interfaces/IOrderNotification";
import DateUtil from '@/utils/DateUtil'; // Ensure you have this util

const props = defineProps<{
    mode?: 'guest' | 'auth' | 'customer' | 'admin';
    transparent?: boolean;
    showSearch?: boolean; // New prop
}>();

const router = useRouter();
const toast = useToast();
const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);
const isUserMenuOpen = ref(false);
const userMenuRef = ref(null);

// Admin Sidebar Injection
const sideBar = inject('sideBar', ref(false));

const cartCount = ref(0); // This should ideally come from a store or API for customers

// Search
const searchQuery = ref('');

// Notifications State
const notifications = ref<IOrderNotification[]>([]);
const notificationElement = ref<any>(null); // Popover ref
const activeTab = ref<'all' | 'unread'>('all');
const isNotificationOpen = ref(false);

const unreadCount = computed(() => notifications.value.filter(n => !n.is_read).length);

const filteredNotifications = computed(() => {
    if (activeTab.value === 'unread') {
        return notifications.value.filter(n => !n.is_read);
    }
    return notifications.value;
});

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

const handleSearch = () => {
    if (!searchQuery.value.trim()) return;
    router.push({ name: 'product.search', query: { q: searchQuery.value } });
};

const toggleSidebar = () => {
    sideBar.value = !sideBar.value;
};

const goHome = () => {
    if (props.mode === 'admin') {
        router.push({ name: 'admin.dashboard.index' });
    } else {
        router.push({ name: 'home' });
    }
};

const goToBrowse = () => {
    router.push({ name: 'product.search' });
    isMobileMenuOpen.value = false;
};

const goToCart = () => {
    router.push({ name: 'customer.cart' });
};

const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value;
};

const userImage = computed(() => {
    return Page.user?.image ? UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.image}`) : undefined;
});

const userInitials = computed(() => {
    return Page.user?.full_name ? Page.user.full_name[0].toUpperCase() : 'U';
});

const handleLogout = async () => {
    await useAxiosUtil().post('logout').then(() => {
        Page.user = null;
        router.push({ name: 'home' });
        toast.success("Logged out successfully");
    });
};

// Click outside to close user menu
const handleClickOutside = (event: MouseEvent) => {
    if (userMenuRef.value && !userMenuRef.value.contains(event.target as Node)) {
        isUserMenuOpen.value = false;
    }
};

// Notification Logic
const loadService = useAxiosUtil<null, IOrderNotification[]>();
const loadNotifications = async () => {
    if (!Page.user || (props.mode !== 'admin' && props.mode !== 'customer')) return;
    
    // Determine endpoint based on mode
    const prefix = props.mode === 'admin' ? 'admin' : 'customer';
    
    await loadService.get(`${prefix}/order-notifications`).then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            notifications.value = loadService.request.data;
        } else {
            console.error(loadService.request.message ?? "Failed to load notifications");
        }
    });
};

const toggleNotification = (event: Event) => {
    notificationElement.value?.toggle(event);
};

const markAsRead = async (notification: IOrderNotification) => {
    if (notification.is_read) return;
    
    notification.is_read = true;
    
    const prefix = props.mode === 'admin' ? 'admin' : 'customer';
    await useAxiosUtil().patch(`${prefix}/order-notifications/mark-as-read/${notification.order_notification_id}`, null);
    
    // Optional navigation based on type?
    // if (props.mode === 'admin') router.push({ name: 'admin.order.index' });
};

const markAllAsRead = async () => {
    if (unreadCount.value === 0) return;
    
    notifications.value.forEach(n => n.is_read = true);
    
    const prefix = props.mode === 'admin' ? 'admin' : 'customer';
    await useAxiosUtil().patch(`${prefix}/order-notifications/mark-all-as-read`, null);
    
    toast.success('All notifications marked as read');
};

const handleTabClick = (tab: 'all' | 'unread') => {
    activeTab.value = tab;
};

const formatTime = (value: string) => {
    const date = new Date(value);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days < 7) return `${days}d ago`;
    return date.toLocaleDateString();
};

const getNotificationIcon = (type: string) => {
    const icons: Record<string, string> = {
        order: 'pi pi-shopping-cart',
        delivery: 'pi pi-truck',
        promotion: 'pi pi-tag',
        info: 'pi pi-info-circle',
        warning: 'pi pi-exclamation-triangle',
        success: 'pi pi-check-circle'
    };
    return icons[type] || 'pi pi-bell';
};

const getNotificationColor = (type: string) => {
    // Only used for background colors logic if needed
    return 'bg-blue-100 text-blue-600'; 
};

// Echo Setup
const setupEcho = () => {
    if (!Page.user) return;
    
    // Channel name depends on backend implementation. 
    // Assuming 'admin-order-notification' for admin and something else for customer?
    // User specific channel is best.
    // If using public channel filtered by user, we need loop.
    // Assuming backend broadcasts to specific channels.
    
    // For now, mirroring AdminNavBar logic:
    if (props.mode === 'admin') {
         const { leave } = useEcho(
            "admin-order-notification",
            [".admin-order-notification.event"],
            (value: IOrderNotification) => {
                notifications.value.unshift(value);
                toast.info(`New Notification: ${value.message}`);
            },
        );
        onUnmounted(() => leave());
    } else if (props.mode === 'customer') {
        // Assuming customer channel logic exists. If not, skip for now.
        // Usually `App.Models.User.{id}` for private channels.
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    document.addEventListener('click', handleClickOutside);
    loadNotifications();
    setupEcho();
    
    // Fetch cart count if customer?
    // if (props.mode === 'customer') ...
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
.btn-primary {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: white;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}
</style>
