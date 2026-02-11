<template>
    <header :class="['w-full z-50 transition-all duration-300', transparent ? 'fixed top-0 left-0' : 'sticky top-0', transparent && !isScrolled ? 'bg-transparent' : 'bg-white/95 backdrop-blur-xl border-b border-slate-200 shadow-sm']">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Admin Sidebar Toggle & Logo -->
                <div class="flex items-center gap-4">
                    <button v-if="mode === 'admin'" @click="toggleSidebar" 
                        :class="['p-2 rounded-lg transition-all focus:outline-none border-2', transparent && !isScrolled ? 'text-white border-white/30 hover:bg-white hover:text-blue-600 hover:border-white' : 'text-blue-600 border-blue-500/30 hover:bg-blue-600 hover:text-white hover:border-blue-600']">
                        <i class="pi pi-bars text-lg md:text-xl font-bold"></i>
                    </button>
                    
                    <div class="flex-shrink-0 flex items-center cursor-pointer group" @click="goHome">
                        <img :src="Logo" alt="Alcess Logo" class="h-8 w-auto sm:h-10 transition-transform group-hover:scale-110" />
                        <span :class="['ml-3 text-xl font-extrabold tracking-tight hidden sm:block transition-colors', (transparent && !isScrolled) ? 'text-white' : 'text-slate-900']">
                            Alcess
                        </span>
                    </div>
                </div>

                <!-- Middle: Navigation Links (Restored) -->
                <div class="hidden md:flex flex-1 items-center justify-center">
                    <div v-if="isHomePage && !isAuthPage" class="flex items-center gap-8 lg:gap-12">
                        <a href="#home" :class="['text-sm font-bold transition-colors', (transparent && !isScrolled) ? 'text-white/90 hover:text-white' : 'text-slate-600 hover:text-blue-600']">Home</a>
                        <a href="#products" :class="['text-sm font-bold transition-colors', (transparent && !isScrolled) ? 'text-white/90 hover:text-white' : 'text-slate-600 hover:text-blue-600']">Products</a>
                        <a href="#features" :class="['text-sm font-bold transition-colors', (transparent && !isScrolled) ? 'text-white/90 hover:text-white' : 'text-slate-600 hover:text-blue-600']">Features</a>
                        <a href="#footer" :class="['text-sm font-bold transition-colors', (transparent && !isScrolled) ? 'text-white/90 hover:text-white' : 'text-slate-600 hover:text-blue-600']">Contact</a>
                    </div>
                </div>

                <!-- Navigation Actions (Visible on Mobile & Desktop) -->
                <div class="flex items-center gap-3 md:gap-4 ml-auto lg:ml-0">
                    <!-- Search Input (Repositioned to right) -->
                    <div v-if="(mode === 'customer' || mode === 'guest') && showSearch" class="hidden lg:block w-96 xl:w-[32rem]">
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i :class="['pi pi-search transition-colors', (transparent && !isScrolled) ? 'text-white/60' : 'text-slate-400 group-focus-within:text-blue-600']"></i>
                            </div>
                            <input id="search" name="search" v-model="searchQuery" @keyup.enter="handleSearch"
                                :class="['block w-full pl-9 pr-3 py-2 border-0 rounded-xl leading-5 focus:outline-none focus:ring-2 sm:text-sm transition-all duration-300 backdrop-blur-md', 
                                    (transparent && !isScrolled) ? 'bg-white/10 text-white placeholder-white/60 focus:ring-white/30' : 'bg-slate-100 text-slate-900 placeholder-slate-500 focus:bg-white focus:ring-blue-500/20 ring-1 ring-blue-500/40']"
                                placeholder="Search..." type="search" />
                        </div>
                    </div>
                    
                    <template v-if="Page.user">
                        <!-- Notifications -->
                        <div v-if="mode === 'customer' || mode === 'admin'" class="relative">
                            <button @click="toggleNotification" 
                                :class="['relative p-2 rounded-full transition-all border-2 flex items-center justify-center w-10 h-10', 
                                    transparent && !isScrolled ? 'text-white border-white/30 hover:bg-white hover:text-blue-600 hover:border-white' : 'text-blue-600 border-blue-500/30 hover:bg-blue-600 hover:text-white hover:border-blue-600']">
                                <i class="pi pi-bell text-lg font-bold"></i>
                                <span v-if="unreadCount > 0" :class="['absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-black w-4 h-4 flex items-center justify-center rounded-full border-2 shadow-sm', transparent && !isScrolled ? 'border-[#0b1426]' : 'border-white']">
                                    {{ unreadCount }}
                                </span>
                            </button>
                            
                            <!-- Notification Popover -->
                            <Popover ref="notificationElement" appendTo="body" class="w-80 sm:w-96 !bg-white !shadow-2xl !border-0 !p-0 overflow-hidden !rounded-2xl mt-2">
                                <div class="flex flex-col max-h-[480px]">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between p-4 bg-gray-50/80 border-b border-gray-100">
                                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                            <i class="pi pi-bell text-blue-600"></i>
                                            Notifications
                                        </h3>
                                        <Button v-if="unreadCount > 0" @click="markAllAsRead" label="Mark all read" link size="small" class="!px-0 !py-0 !text-blue-600 !font-bold hover:!underline" />
                                    </div>
                                    
                                    <!-- Tabs -->
                                    <div class="flex p-1 gap-1 text-sm font-bold text-center bg-gray-100/50 m-2 rounded-xl">
                                        <button @click="handleTabClick('all')" :class="['flex-1 py-1.5 rounded-lg transition-all', activeTab === 'all' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700']">
                                            All
                                        </button>
                                        <button @click="handleTabClick('unread')" :class="['flex-1 py-1.5 rounded-lg transition-all', activeTab === 'unread' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700']">
                                            Unread
                                            <span v-if="unreadCount > 0" class="ml-1 bg-red-500 text-white px-1.5 rounded-full text-[10px]">{{ unreadCount }}</span>
                                        </button>
                                    </div>

                                    <!-- List -->
                                    <div class="overflow-y-auto flex-1 custom-scrollbar">
                                        <div v-if="filteredNotifications.length === 0" class="p-10 text-center text-gray-400">
                                            <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="pi pi-bell-slash text-2xl"></i>
                                            </div>
                                            <p class="text-sm font-medium">No notifications yet</p>
                                            <p class="text-xs text-gray-400 mt-1">We'll let you know when something happens</p>
                                        </div>
                                        <div v-else v-for="note in filteredNotifications" :key="note.order_notification_id" 
                                            @click="markAsRead(note.order_notification_id)"
                                            :class="['group p-4 border-b border-gray-50 hover:bg-blue-50/50 cursor-pointer transition-all duration-200', !note.is_read ? 'bg-blue-50/30' : '']">
                                            <div class="flex gap-4">
                                                <div :class="['flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110', getNotificationColor(note.notification_type)]">
                                                    <i :class="[getNotificationIcon(note.notification_type), 'text-lg']"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex justify-between items-start mb-1">
                                                        <p class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ note.notification_type }}</p>
                                                        <span class="text-[10px] text-gray-400 font-medium">{{ formatTime(note.created_at) }}</span>
                                                    </div>
                                                    <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">{{ note.message }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Popover>
                        </div>

                        <!-- Cart -->
                        <button v-if="Page.user && Page.user.role?.role_name === 'Customer'" @click="goToCart" 
                            :class="['relative group p-2 rounded-full transition-all border-2 flex items-center justify-center w-10 h-10', 
                                transparent && !isScrolled ? 'text-white border-white/30 hover:bg-white hover:text-blue-600 hover:border-white' : 'text-blue-600 border-blue-500/30 hover:bg-blue-600 hover:text-white hover:border-blue-600']">
                            <i class="pi pi-shopping-cart text-lg font-bold"></i>
                            <span v-if="cartCount > 0" :class="['absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-black w-4 h-4 flex items-center justify-center rounded-full border-2 shadow-sm', transparent && !isScrolled ? 'border-[#0b1426]' : 'border-white']">
                                {{ cartCount }}
                            </span>
                        </button>
                    </template>

                    <!-- Mobile Menu Button (Only visible on Mobile/Tablet) -->
                    <div class="md:hidden flex items-center gap-2" v-if="(!Page.user && !isAuthPage) || (isHomePage && !Page.user)">
                        <button @click="isMobileMenuOpen = !isMobileMenuOpen" 
                            :class="['p-2 rounded-full border-2 focus:outline-none transition-all flex items-center justify-center w-10 h-10', 
                                transparent && !isScrolled && !isMobileMenuOpen ? 'text-white border-white/30 hover:bg-white hover:text-blue-600 hover:border-white' : 'text-blue-600 border-blue-500/30 hover:bg-blue-600 hover:text-white hover:border-blue-600']">
                            <i :class="isMobileMenuOpen ? 'pi pi-times text-xl' : 'pi pi-bars text-xl'"></i>
                        </button>
                    </div>



                        <template v-if="Page.user">
                            <div class="relative ml-2" ref="userMenuRef">
                                <button @click="toggleUserMenu" 
                                    :class="['flex items-center gap-3 p-1 pr-3 rounded-full transition-all border-2 focus:outline-none', 
                                        transparent && !isScrolled ? 'text-white border-white/30 hover:bg-white hover:text-blue-600 hover:border-white' : 'text-blue-600 border-blue-500/30 hover:bg-blue-50/50 hover:border-blue-500']">
                                    <Avatar 
                                        shape="circle" 
                                        class="cursor-pointer shadow-sm !w-8 !h-8 bg-blue-500 text-white overflow-hidden"
                                    >
                                        <img v-if="userImage" :src="userImage" class="w-full h-full object-cover" />
                                        <span v-else>{{ userInitials }}</span>
                                    </Avatar>
                                    <div class="hidden lg:block text-left">
                                        <p class="text-sm font-bold leading-tight">
                                            {{ Page.user.full_name }}
                                        </p>
                                    </div>
                                    <i :class="['pi pi-chevron-down text-[10px]', transparent && !isScrolled ? 'text-white/70' : 'text-blue-400']"></i>
                                </button>
                                
                                <!-- Dropdown -->
                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform -translate-y-2 opacity-0"
                                >
                                    <div v-if="isUserMenuOpen" class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-2xl py-2 ring-1 ring-black/5 focus:outline-none overflow-hidden z-50">
                                        <div class="px-4 py-4 border-b border-gray-100 bg-gray-50/50">
                                            <div class="flex items-center gap-3">
                                                <Avatar 
                                                    shape="circle" 
                                                    class="!w-10 !h-10 shadow-sm ring-2 ring-white bg-blue-500 text-white overflow-hidden"
                                                >
                                                    <img v-if="userImage" :src="userImage" class="w-full h-full object-cover" />
                                                    <span v-else>{{ userInitials }}</span>
                                                </Avatar>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-gray-900 truncate">{{ Page.user.full_name }}</p>
                                                    <p class="text-[10px] font-bold text-blue-600 uppercase tracking-tighter mb-0.5">{{ Page.user.role?.role_name || 'User' }}</p>
                                                    <p class="text-[11px] text-gray-500 truncate font-medium">{{ Page.user.email }}</p>
                                                </div>
                                            </div>
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
                             <!-- Guest Buttons Desktop -->
                             <div class="flex items-center gap-3">
                                <router-link :to="{ name: 'auth.login' }" 
                                    :class="['font-bold text-sm transition-colors', (transparent && !isScrolled) ? 'text-white hover:text-white/80' : 'text-blue-600 hover:text-blue-700']">
                                    Login
                                </router-link>
                                <router-link :to="{ name: 'auth.register' }" 
                                    class="btn-primary px-5 py-2.5 rounded-full font-bold shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5 transition-all text-sm">
                                    Get Started
                                </router-link>
                             </div>
                        </template>
            </div>
        </div>
                
        <!-- Mobile Search Bar (Appears below header on mobile) -->
        <div v-if="(mode === 'customer' || mode === 'guest') && showSearch" class="lg:hidden pb-4 px-2">
            <div class="container mx-auto px-4 sm:px-6"> <!-- Added container for alignment -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i :class="['pi pi-search transition-colors', (transparent && !isScrolled) ? 'text-white/60' : 'text-slate-400 group-focus-within:text-blue-600']"></i>
                    </div>
                    <input id="mobile-search" name="search" v-model="searchQuery" @keyup.enter="handleSearch"
                        :class="['block w-full pl-9 pr-3 py-2 border-0 rounded-xl leading-5 focus:outline-none focus:ring-2 text-sm transition-all duration-300 backdrop-blur-md', 
                            (transparent && !isScrolled) ? 'bg-white/10 text-white placeholder-white/60 focus:ring-white/30' : 'bg-slate-100 text-slate-900 placeholder-slate-500 focus:bg-white focus:ring-blue-500/20 ring-1 ring-blue-500/40 shadow-sm']"
                        placeholder="Search products..." type="search" />
                </div>
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
            <div v-if="isMobileMenuOpen" class="md:hidden bg-white shadow-xl border-t border-slate-100">
                <div class="px-4 py-6 space-y-4">
                    <template v-if="Page.user">
                        <div class="flex items-center gap-3 px-2 pb-4 border-b border-slate-100">
                            <Avatar shape="circle" size="large" class="bg-blue-500 text-white overflow-hidden">
                                <img v-if="userImage" :src="userImage" class="w-full h-full object-cover" />
                                <span v-else>{{ userInitials }}</span>
                            </Avatar>
                            <div>
                                <p class="font-bold text-slate-900">{{ Page.user.full_name }}</p>
                                <p class="text-sm text-slate-500">{{ Page.user.email }}</p>
                            </div>
                        </div>
                         <template v-if="mode === 'admin'">
                             <router-link :to="{ name: 'admin.dashboard.index' }" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                                Dashboard
                            </router-link>
                         </template>
                         <template v-else>
                            <router-link :to="{ name: 'home' }" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                                Home
                            </router-link>
                            <router-link :to="{ name: 'customer.profile' }" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                                My Profile
                            </router-link>
                            <router-link :to="{ name: 'customer.orders' }" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                                My Orders
                            </router-link>
                         </template>
                         <button @click="handleLogout" class="w-full text-left block px-3 py-2 rounded-lg text-base font-medium text-red-600 hover:bg-red-50">
                            Logout
                        </button>
                    </template>
                    <template v-else>
                        <!-- Mobile Section Links -->
                        <div class="grid grid-cols-2 gap-2 pt-2">
                             <a href="#home" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50">Home</a>
                             <a href="#products" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50">Products</a>
                             <a href="#features" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50">Features</a>
                             <a href="#footer" class="block px-3 py-2 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50">Contact</a>
                        </div>
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
import { RoleEnum, getStoreRoles } from "@/enums/RoleEnum";
import { useChatStore } from "@/stores/ChatStore";

const props = defineProps<{
    mode?: 'guest' | 'auth' | 'customer' | 'admin';
    transparent?: boolean;
    showSearch?: boolean; // New prop
}>();

const router = useRouter();
const toast = useToast();

const isAuthPage = computed(() => {
    const authRoutes = ['auth.login', 'auth.register', 'auth.forgot-password'];
    return authRoutes.includes(router.currentRoute.value.name as string);
});

const isHomePage = computed(() => {
    return router.currentRoute.value.name === 'home';
});

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
    router.push({ name: 'customer.search-product', query: { q: searchQuery.value } });
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
    router.push({ name: 'customer.browse-products' });
    isMobileMenuOpen.value = false;
};

const goToCart = () => {
    router.push({ name: 'customer.cart.index' });
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
        router.push({ name: 'auth.login' });
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
const submitMarkReadService = useAxiosUtil();
const loadNotifications = async () => {
    if (!Page.user) return;
    
    // Determine endpoint based on role
    const isAdmin = getStoreRoles().includes(Page.user.role.role_name as RoleEnum);
    const prefix = isAdmin ? 'admin' : 'customer';
    
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

const markAsRead = async (item: IOrderNotification | null = null) => {
    if (item) {
        if (!item.is_read) {
            await submitMarkReadService.patch(`order-notifications/mark-as-read/${item.order_notification_id}`, {});
            // Update local state
            const index = notifications.value.findIndex(n => n.order_notification_id === item.order_notification_id);
            if (index !== -1) {
                notifications.value[index].is_read = true;
            }
        }
        
        // Redirect based on role
        if (Page.user?.role?.role_id === RoleEnum.ADMIN || Page.user?.role?.role_id === RoleEnum.STAFF) {
            router.push({ name: 'admin.order.index' });
        } else {
             router.push({ name: 'customer.order.index' });
        }
    } else {
        await submitMarkReadService.patch("order-notifications/mark-all-as-read", {});
        notifications.value.forEach(n => n.is_read = true);
        
        // Redirect based on role
        if (Page.user?.role?.role_id === RoleEnum.ADMIN || Page.user?.role?.role_id === RoleEnum.STAFF) {
             router.push({ name: 'admin.order.index' });
        } else {
             router.push({ name: 'customer.order.index' });
        }
    }
    
    // Refresh notifications
    loadNotifications();
};
    
    // Hide popover
    if (notificationElement.value) {
        notificationElement.value.hide();
    }
};

const markAllAsRead = async () => {
    if (unreadCount.value === 0) return;
    
    notifications.value.forEach(n => n.is_read = true);
    
    const isAdmin = getStoreRoles().includes(Page.user?.role.role_name as RoleEnum);
    const prefix = isAdmin ? 'admin' : 'customer';
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
        'Pending Order': 'pi pi-clock',
        'Confirmed Order': 'pi pi-check-circle',
        'To Ship': 'pi pi-truck',
        'Paid': 'pi pi-wallet',
        'Cancelled Order': 'pi pi-times-circle',
        'Rejected Order': 'pi pi-ban'
    };
    return icons[type] || 'pi pi-bell';
};

const getNotificationColor = (type: string) => {
    const colors: Record<string, string> = {
        'Pending Order': 'bg-amber-100 text-amber-600',
        'Confirmed Order': 'bg-blue-100 text-blue-600',
        'To Ship': 'bg-indigo-100 text-indigo-600',
        'Paid': 'bg-green-100 text-green-600',
        'Cancelled Order': 'bg-red-100 text-red-600',
        'Rejected Order': 'bg-gray-100 text-gray-600'
    };
    return colors[type] || 'bg-blue-100 text-blue-600'; 
};

// Echo Setup
const setupEcho = () => {
    if (!Page.user) return;
    
    const isAdmin = getStoreRoles().includes(Page.user.role.role_name as RoleEnum);
    let leave: (() => void) | undefined;
    let echoResult: ReturnType<typeof useEcho> | undefined;
    
    if (isAdmin) {
         echoResult = useEcho(
            "admin-order-notification",
            [".admin-order-notification.event"],
            (value: IOrderNotification) => {
                notifications.value.unshift(value);
                toast.info(`New Notification: ${value.message}`);
            },
        );
        onUnmounted(() => leave?.());
    } else {
        // Customer listeners
        echoResult = useEcho(
            `order.${Page.user.user_id}`,
            [".customer-order.event"],
            (value: IOrderNotification) => {
                notifications.value.unshift(value);
                toast.info(`New Notification: ${value.message}`);
                // Also update chat store if needed
                useChatStore().handleNotification(value);
            },
        );
        
        // Cart listener
        const cartEcho = useEcho(
            `cart.${Page.user.user_id}`,
            [".cart.count"],
            (value: { count: number }) => {
                cartCount.value = value.count;
            }
        );
        
        // Combine leave functions
        const originalLeave = echoResult.leave;
        echoResult.leave = () => {
             originalLeave();
             cartEcho.leave();
        };
    }

    if (echoResult) {
        leave = echoResult.leave;
    }
};

const loadCartCount = async () => {
    if (!Page.user || Page.user.role?.role_name !== 'Customer') return;
    
    const cartService = useAxiosUtil();
    await cartService.get('customer/carts/count').then(() => {
        if (cartService.request.status === 200) {
            cartCount.value = Number(cartService.request.data);
        }
    });
};

watch(() => Page.user, (newUser) => {
    if (newUser) {
        loadNotifications();
        loadCartCount();
        setupEcho();
    } else {
        notifications.value = [];
        cartCount.value = 0;
    }
}, { immediate: true });

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    document.addEventListener('click', handleClickOutside);
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
