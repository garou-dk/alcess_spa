<template>
    <header class="sticky top-0 z-10">
        <nav class="shadow-bottom-float relative z-20 flex bg-white p-5">
            <div class="grow">
                <Button
                    type="button"
                    variant="link"
                    raised
                    icon="pi pi-bars"
                    class="text-2xl text-gray-500!"
                    @click="sideBar = !sideBar"
                />
            </div>
            <div class="flex grow items-center justify-end gap-3 px-2">
                <!-- Notification Button -->
                <div>
                    <OverlayBadge
                        :value="notifications.filter(n => !n.read).length"
                        :severity="notifications.filter(n => !n.read).length > 0 ? 'danger' : 'secondary'"
                        pt:pcBadge:root:class="bg-sky-800! text-white"
                    >
                        <Button
                            type="button"
                            @click="openNotification"
                            icon="pi pi-bell"
                            :class="[
                                'bg-sky-800! text-white transition-all duration-200',
                                notifications.filter(n => !n.read).length > 0 ? 'animate-pulse' : ''
                            ]"
                        />
                    </OverlayBadge>
                    <Popover
                        ref="notificationElement"
                        :dismissable="true"
                        class="inline-flex"
                        :pt="{
                            root: { class: 'max-sm:!left-0 max-sm:!right-0 max-sm:!w-screen max-sm:!max-w-none' }
                        }"
                    >
                        <div class="w-full sm:w-[400px] max-h-[600px] max-sm:max-h-[calc(100vh-80px)] flex flex-col">
                            <!-- Notification Header -->
                            <div class="flex items-center justify-between p-3 sm:p-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-bell text-sky-800 text-lg sm:text-xl"></i>
                                    <h3 class="text-base sm:text-lg font-bold text-gray-800">Notifications</h3>
                                    <span 
                                        v-if="unreadCount > 0" 
                                        class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full"
                                    >
                                        {{ unreadCount }}
                                    </span>
                                </div>
                                <Button
                                    v-if="unreadCount > 0"
                                    type="button"
                                    label="Mark all read"
                                    text
                                    size="small"
                                    class="text-sky-700 hover:text-sky-900 text-xs font-semibold max-sm:!text-[10px] max-sm:!px-1"
                                    @click="markAllAsRead"
                                />
                            </div>

                            <!-- Notification Tabs -->
                            <div class="flex border-b border-gray-200 bg-gray-50">
                                <button
                                    @click="activeTab = 'all'"
                                    :class="[
                                        'flex-1 px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition-all duration-200',
                                        activeTab === 'all' 
                                            ? 'text-sky-800 border-b-2 border-sky-800 bg-white' 
                                            : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'
                                    ]"
                                >
                                    All ({{ notifications.length }})
                                </button>
                                <button
                                    @click="activeTab = 'unread'"
                                    :class="[
                                        'flex-1 px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-semibold transition-all duration-200',
                                        activeTab === 'unread' 
                                            ? 'text-sky-800 border-b-2 border-sky-800 bg-white' 
                                            : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'
                                    ]"
                                >
                                    Unread ({{ unreadCount }})
                                </button>
                            </div>

                            <!-- Notifications List -->
                            <div class="overflow-y-auto flex-1">
                                <div v-if="filteredNotifications.length === 0" class="p-6 sm:p-8 text-center">
                                    <i class="pi pi-bell-slash text-3xl sm:text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 font-medium text-sm sm:text-base">No notifications</p>
                                    <p class="text-gray-400 text-xs sm:text-sm mt-1">
                                        {{ activeTab === 'unread' ? 'All caught up!' : 'You have no notifications yet' }}
                                    </p>
                                </div>

                                <div v-else class="divide-y divide-gray-100">
                                    <div
                                        v-for="notification in filteredNotifications"
                                        :key="notification.id"
                                        @click="markAsRead(notification)"
                                        :class="[
                                            'p-3 sm:p-4 cursor-pointer transition-all duration-200 hover:bg-gray-50',
                                            !notification.read ? 'bg-blue-50/50' : ''
                                        ]"
                                    >
                                        <div class="flex gap-2 sm:gap-3">
                                            <!-- Notification Icon -->
                                            <div 
                                                :class="[
                                                    'flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center',
                                                    getNotificationColor(notification.type)
                                                ]"
                                            >
                                                <i :class="getNotificationIcon(notification.type)" class="text-base sm:text-lg"></i>
                                            </div>

                                            <!-- Notification Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-2 mb-1">
                                                    <h4 class="font-semibold text-gray-800 text-xs sm:text-sm leading-tight">
                                                        {{ notification.title }}
                                                    </h4>
                                                    <span 
                                                        v-if="!notification.read"
                                                        class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-1"
                                                    ></span>
                                                </div>
                                                <p class="text-xs sm:text-sm text-gray-600 line-clamp-2 mb-2">
                                                    {{ notification.message }}
                                                </p>
                                                <div class="flex items-center gap-2 sm:gap-3 text-[10px] sm:text-xs text-gray-500">
                                                    <span class="flex items-center gap-1">
                                                        <i class="pi pi-clock text-[10px] sm:text-xs"></i>
                                                        {{ formatTime(notification.time) }}
                                                    </span>
                                                    <span 
                                                        v-if="notification.category"
                                                        class="px-1.5 sm:px-2 py-0.5 sm:py-1 bg-gray-100 rounded text-gray-700 font-medium"
                                                    >
                                                        {{ notification.category }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notification Footer -->
                            <div v-if="notifications.length > 0" class="border-t border-gray-200 p-2 sm:p-3 bg-gray-50">
                                <Button
                                    type="button"
                                    label="View All Notifications"
                                    text
                                    class="w-full text-sky-700 hover:text-sky-900 font-semibold text-xs sm:text-sm"
                                    icon="pi pi-arrow-right"
                                    iconPos="right"
                                    @click="viewAllNotifications"
                                />
                            </div>
                        </div>
                    </Popover>
                </div>

                <!-- Avatar -->
                <div>
                    <button
                        type="button"
                        class="cursor-pointer hover:opacity-80 transition-opacity"
                        @click="openAvatar"
                    >
                        <Avatar
                            v-if="Page.user && Page.user.image"
                            :image="
                                UrlUtil.getBaseApiUrl(
                                    `profile/${Page.user.image}`,
                                )
                            "
                            size="large"
                            shape="circle"
                        />
                        <Avatar
                            v-else
                            :label="Page.user && Page.user.full_name[0]"
                            size="large"
                            shape="circle"
                            class="bg-sky-800! text-white!"
                        />
                    </button>
                    <Popover
                        ref="avatarElement"
                        :dismissable="true"
                        class="inline-flex"
                        :pt="{
                            root: { class: 'max-sm:!right-0' }
                        }"
                    >
                        <div class="p-2 min-w-[180px] sm:min-w-[200px]">
                            <!-- User Info -->
                            <div class="px-2 sm:px-3 py-2 mb-2 border-b border-gray-200">
                                <p class="font-semibold text-gray-800 text-sm sm:text-base truncate">{{ Page.user?.full_name }}</p>
                                <p class="text-xs sm:text-sm text-gray-600 truncate">{{ Page.user?.email }}</p>
                            </div>
                            <LogoutButton />
                        </div>
                    </Popover>
                </div>
            </div>
        </nav>
    </header>
</template>

<script setup lang="ts">
import Page from "@/stores/Page";
import { Popover } from "primevue";
import { computed, inject, ref } from "vue";
import LogoutButton from "@/components/LogoutButton.vue";
import UrlUtil from "@/utils/UrlUtil";

const sideBar = inject("sideBar");
const notificationElement = ref<null | InstanceType<typeof Popover>>();
const avatarElement = ref<null | InstanceType<typeof Popover>>();
const activeTab = ref<'all' | 'unread'>('all');

// Sample notifications - replace with your actual data source
const notifications = ref([
    {
        id: 1,
        type: 'order',
        title: 'Order Confirmed',
        message: 'Your order #12345 has been confirmed and is being processed.',
        time: new Date(Date.now() - 5 * 60000), // 5 minutes ago
        read: false,
        category: 'Orders'
    },
    {
        id: 2,
        type: 'delivery',
        title: 'Out for Delivery',
        message: 'Your package is out for delivery and will arrive today.',
        time: new Date(Date.now() - 30 * 60000), // 30 minutes ago
        read: false,
        category: 'Delivery'
    },
    {
        id: 3,
        type: 'promotion',
        title: 'Special Offer',
        message: 'Get 20% off on your next purchase. Limited time offer!',
        time: new Date(Date.now() - 2 * 60 * 60000), // 2 hours ago
        read: true,
        category: 'Promotions'
    },
    {
        id: 4,
        type: 'info',
        title: 'Account Updated',
        message: 'Your profile information has been successfully updated.',
        time: new Date(Date.now() - 24 * 60 * 60000), // 1 day ago
        read: true,
        category: 'Account'
    },
]);

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const filteredNotifications = computed(() => {
    if (activeTab.value === 'unread') {
        return notifications.value.filter(n => !n.read);
    }
    return notifications.value;
});

const openNotification = (event: Event) => {
    if (notificationElement.value) {
        notificationElement.value.toggle(event);
    }
};

const openAvatar = (event: Event) => {
    if (avatarElement.value) {
        avatarElement.value.toggle(event);
    }
};

const markAsRead = (notification: any) => {
    notification.read = true;
    // Add your API call here to mark as read in backend
};

const markAllAsRead = () => {
    notifications.value.forEach(n => n.read = true);
    // Add your API call here to mark all as read in backend
};

const viewAllNotifications = () => {
    // Navigate to notifications page or close popover
    notificationElement.value?.hide();
    // router.push({ name: 'notifications' });
};

const getNotificationIcon = (type: string) => {
    const icons = {
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
    const colors = {
        order: 'bg-blue-100 text-blue-600',
        delivery: 'bg-green-100 text-green-600',
        promotion: 'bg-purple-100 text-purple-600',
        info: 'bg-gray-100 text-gray-600',
        warning: 'bg-amber-100 text-amber-600',
        success: 'bg-emerald-100 text-emerald-600'
    };
    return colors[type] || 'bg-gray-100 text-gray-600';
};

const formatTime = (date: Date) => {
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
</script>