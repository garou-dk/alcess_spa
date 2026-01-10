<template>
    <header class="sticky top-0 z-10">
        <nav 
            :class="[
                'relative z-20 flex items-center justify-between bg-gray-100 transition-all duration-200',
                getResponsiveClasses({
                    mobile: 'p-3',
                    tablet: 'p-4',
                    desktop: 'p-5'
                }),
                { 'border-b border-gray-200': isScrolled }
            ]"
        >
            <!-- Left Side: Hamburger Menu and Page Info -->
            <div :class="getResponsiveClasses({
                mobile: 'flex items-center gap-3',
                tablet: 'flex items-center gap-4',
                desktop: 'flex items-center gap-6'
            })">
                <!-- Hamburger Menu -->
                <div class="flex-shrink-0">
                    <button
                        type="button"
                        :class="[
                            'hamburger-menu transition-all duration-200 rounded-lg flex items-center justify-center',
                            getResponsiveClasses({
                                mobile: 'text-xl',
                                tablet: 'text-xl',
                                desktop: 'text-2xl'
                            })
                        ]"
                        :style="getResponsiveButtonStyles()"
                        @click="sideBar = !sideBar"
                        @mouseenter="$event.currentTarget.style.backgroundColor = '#DBEAFE'"
                        @mouseleave="$event.currentTarget.style.backgroundColor = 'transparent'"
                    >
                        <i class="pi pi-bars"></i>
                    </button>
                </div>

                <!-- Page Info -->
                <div :class="getResponsiveClasses({
                    mobile: 'hidden',
                    tablet: 'block',
                    desktop: 'block'
                })">
                    <h1 :class="getResponsiveClasses({
                        mobile: 'text-lg font-bold text-gray-800',
                        tablet: 'text-lg font-bold text-gray-800',
                        desktop: 'text-xl font-bold text-gray-800'
                    })">{{ route.meta["pageName"] }}</h1>
                    <p :class="getResponsiveClasses({
                        mobile: 'text-xs text-gray-600',
                        tablet: 'text-xs text-gray-600',
                        desktop: 'text-sm text-gray-600'
                    })">{{ route.meta["pageSubName"] }}</p>
                </div>
            </div>

            <!-- Right Side: Notifications and Avatar -->
            <div :class="getResponsiveClasses({
                mobile: 'flex flex-shrink-0 items-center justify-end gap-3 px-1',
                tablet: 'flex flex-shrink-0 items-center justify-end gap-4 px-2',
                desktop: 'flex flex-shrink-0 items-center justify-end gap-6 px-2'
            })">
                <!-- Notification Button -->
                <div class="relative">
                    <div 
                        class="rounded-lg transition-all duration-200 inline-block"
                        @mouseenter="$event.currentTarget.style.backgroundColor = '#DBEAFE'"
                        @mouseleave="$event.currentTarget.style.backgroundColor = 'transparent'"
                    >
                        <OverlayBadge
                            :value="notifications.filter(n => !n.is_read).length"
                            :severity="notifications.filter(n => !n.is_read).length > 0 ? 'danger' : 'secondary'"
                        >
                            <button
                                type="button"
                                @click="openNotification"
                                :class="[
                                    'transition-all duration-200 flex items-center justify-center relative',
                                    getResponsiveClasses({
                                        mobile: 'text-lg',
                                        tablet: 'text-xl',
                                        desktop: 'text-2xl'
                                    }),
                                    notifications.filter(n => !n.is_read).length > 0 ? 'animate-pulse' : ''
                                ]"
                                :style="getResponsiveButtonStyles()"
                            >
                                <i class="pi pi-bell"></i>
                            </button>
                        </OverlayBadge>
                    </div>
                    <Popover
                        ref="notificationElement"
                        :dismissable="false"
                        @show="onPopoverShow"
                        @hide="onPopoverHide"
                        :pt="{
                            root: { 
                                class: getResponsiveClasses({
                                    mobile: '!left-0 !right-0 !w-screen !max-w-none',
                                    tablet: '!right-0 !left-auto',
                                    desktop: '!right-0 !left-auto'
                                }),
                                style: getResponsivePopoverStyles()
                            }
                        }"
                    >
                        <div :class="getResponsiveClasses({
                            mobile: 'w-full flex flex-col overflow-hidden',
                            tablet: 'w-full flex flex-col overflow-hidden',
                            desktop: 'w-full flex flex-col overflow-hidden'
                        })" :style="getResponsivePopoverContentStyles()">
                            <!-- Notification Header -->
                            <div :class="[
                                'flex items-center justify-between border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50 flex-shrink-0',
                                getResponsiveClasses({
                                    mobile: 'p-2',
                                    tablet: 'p-3',
                                    desktop: 'p-3 sm:p-4'
                                })
                            ]">
                                <div class="flex items-center gap-2">
                                    <i :class="[
                                        'pi pi-bell text-sky-800',
                                        getResponsiveClasses({
                                            mobile: 'text-base',
                                            tablet: 'text-lg',
                                            desktop: 'text-lg sm:text-xl'
                                        })
                                    ]"></i>
                                    <h3 :class="[
                                        'font-bold text-gray-800',
                                        getResponsiveClasses({
                                            mobile: 'text-sm',
                                            tablet: 'text-base',
                                            desktop: 'text-base sm:text-lg'
                                        })
                                    ]">Notifications</h3>
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
                                    :class="[
                                        'text-sky-700 hover:text-sky-900 font-semibold',
                                        getResponsiveClasses({
                                            mobile: 'text-xs !px-1',
                                            tablet: 'text-xs',
                                            desktop: 'text-xs'
                                        })
                                    ]"
                                    @click.stop="markAllAsRead"
                                    @mousedown.stop
                                />
                            </div>

                            <!-- Notification Tabs -->
                            <div class="flex border-b border-gray-200 bg-gray-50">
                                <button
                                    @click="handleTabClick('all', $event)"
                                    :class="[
                                        'flex-1 py-2 font-semibold transition-all duration-200',
                                        getResponsiveClasses({
                                            mobile: 'px-2 text-xs',
                                            tablet: 'px-3 text-xs',
                                            desktop: 'px-3 sm:px-4 text-xs sm:text-sm'
                                        }),
                                        activeTab === 'all' 
                                            ? 'text-sky-800 border-b-2 border-sky-800 bg-white' 
                                            : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'
                                    ]"
                                >
                                    All ({{ notifications.length }})
                                </button>
                                <button
                                    @click="handleTabClick('unread', $event)"
                                    :class="[
                                        'flex-1 py-2 font-semibold transition-all duration-200',
                                        getResponsiveClasses({
                                            mobile: 'px-2 text-xs',
                                            tablet: 'px-3 text-xs',
                                            desktop: 'px-3 sm:px-4 text-xs sm:text-sm'
                                        }),
                                        activeTab === 'unread' 
                                            ? 'text-sky-800 border-b-2 border-sky-800 bg-white' 
                                            : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'
                                    ]"
                                >
                                    Unread ({{ unreadCount }})
                                </button>
                            </div>

                            <!-- Notifications List -->
                            <div :class="getResponsiveClasses({
                                mobile: 'overflow-y-auto max-h-[200px]',
                                tablet: 'overflow-y-auto max-h-[250px]',
                                desktop: 'overflow-y-auto max-h-[300px]'
                            })">
                                <div v-if="filteredNotifications.length === 0" :class="[
                                    'text-center',
                                    getResponsiveClasses({
                                        mobile: 'p-4',
                                        tablet: 'p-5',
                                        desktop: 'p-6 sm:p-8'
                                    })
                                ]">
                                    <i :class="[
                                        'pi pi-bell-slash text-gray-300 mb-3',
                                        getResponsiveClasses({
                                            mobile: 'text-2xl',
                                            tablet: 'text-3xl',
                                            desktop: 'text-3xl sm:text-4xl'
                                        })
                                    ]"></i>
                                    <p :class="[
                                        'text-gray-500 font-medium',
                                        getResponsiveClasses({
                                            mobile: 'text-xs',
                                            tablet: 'text-sm',
                                            desktop: 'text-sm sm:text-base'
                                        })
                                    ]">No notifications</p>
                                    <p :class="[
                                        'text-gray-400 mt-1',
                                        getResponsiveClasses({
                                            mobile: 'text-xs',
                                            tablet: 'text-xs',
                                            desktop: 'text-xs sm:text-sm'
                                        })
                                    ]">
                                        {{ activeTab === 'unread' ? 'All caught up!' : 'You have no notifications yet' }}
                                    </p>
                                </div>

                                <div v-else class="divide-y divide-gray-100">
                                    <div
                                        v-for="notification in filteredNotifications"
                                        :key="notification.order_notification_id"
                                        @click="markAsRead(notification)"
                                        :class="[
                                            'cursor-pointer transition-all duration-200 hover:bg-gray-50',
                                            getResponsiveClasses({
                                                mobile: 'p-2',
                                                tablet: 'p-3',
                                                desktop: 'p-3 sm:p-4'
                                            }),
                                            !notification.is_read ? 'bg-blue-50/50' : ''
                                        ]"
                                    >
                                        <div :class="getResponsiveClasses({
                                            mobile: 'flex gap-2',
                                            tablet: 'flex gap-2',
                                            desktop: 'flex gap-2 sm:gap-3'
                                        })">
                                            <!-- Notification Icon -->
                                            <div 
                                                :class="[
                                                    'flex-shrink-0 rounded-full flex items-center justify-center',
                                                    getResponsiveClasses({
                                                        mobile: 'w-6 h-6',
                                                        tablet: 'w-8 h-8',
                                                        desktop: 'w-8 h-8 sm:w-10 sm:h-10'
                                                    }),
                                                    getNotificationColor(notification.notification_type)
                                                ]"
                                            >
                                                <i :class="[
                                                    getNotificationIcon(notification.notification_type),
                                                    getResponsiveClasses({
                                                        mobile: 'text-xs',
                                                        tablet: 'text-sm',
                                                        desktop: 'text-base sm:text-lg'
                                                    })
                                                ]"></i>
                                            </div>

                                            <!-- Notification Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-2 mb-1">
                                                    <h4 :class="[
                                                        'font-semibold text-gray-800 leading-tight',
                                                        getResponsiveClasses({
                                                            mobile: 'text-xs',
                                                            tablet: 'text-xs',
                                                            desktop: 'text-xs sm:text-sm'
                                                        })
                                                    ]">
                                                        {{ notification.message }}
                                                    </h4>
                                                    <span 
                                                        v-if="!notification.is_read"
                                                        :class="[
                                                            'flex-shrink-0 bg-blue-600 rounded-full mt-1',
                                                            getResponsiveClasses({
                                                                mobile: 'w-1.5 h-1.5',
                                                                tablet: 'w-2 h-2',
                                                                desktop: 'w-2 h-2'
                                                            })
                                                        ]"
                                                    ></span>
                                                </div>
                                                <div :class="[
                                                    'flex items-center gap-2 text-gray-500',
                                                    getResponsiveClasses({
                                                        mobile: 'text-[9px]',
                                                        tablet: 'text-[10px]',
                                                        desktop: 'text-[10px] sm:text-xs'
                                                    })
                                                ]">
                                                    <span class="flex items-center gap-1">
                                                        <i :class="[
                                                            'pi pi-clock',
                                                            getResponsiveClasses({
                                                                mobile: 'text-[9px]',
                                                                tablet: 'text-[10px]',
                                                                desktop: 'text-[10px] sm:text-xs'
                                                            })
                                                        ]"></i>
                                                        {{ formatTime(notification.created_at) }}
                                                    </span>
                                                    <span 
                                                        v-if="notification.notification_type"
                                                        :class="[
                                                            'bg-gray-100 rounded text-gray-700 font-medium',
                                                            getResponsiveClasses({
                                                                mobile: 'px-1 py-0.5',
                                                                tablet: 'px-1.5 py-0.5',
                                                                desktop: 'px-1.5 sm:px-2 py-0.5 sm:py-1'
                                                            })
                                                        ]"
                                                    >
                                                        {{ notification.notification_type }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notification Footer -->
                            <!-- <div v-if="notifications.length > 0" class="border-t border-gray-200 p-2 sm:p-3 bg-gray-50">
                                <Button
                                    type="button"
                                    label="View All Notifications"
                                    text
                                    class="w-full text-sky-700 hover:text-sky-900 font-semibold text-xs sm:text-sm"
                                    icon="pi pi-arrow-right"
                                    iconPos="right"
                                    @click="viewAllNotifications"
                                />
                            </div> -->
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
                            :size="getResponsiveClasses({
                                mobile: 'normal',
                                tablet: 'large',
                                desktop: 'large'
                            })"
                            shape="circle"
                        />
                        <Avatar
                            v-else
                            :label="Page.user && Page.user.full_name[0]"
                            :size="getResponsiveClasses({
                                mobile: 'normal',
                                tablet: 'large',
                                desktop: 'large'
                            })"
                            shape="circle"
                            style="background-color: #2563EB; color: white;"
                        />
                    </button>
                    <Popover
                        ref="avatarElement"
                        :dismissable="true"
                        class="inline-flex"
                        :pt="{
                            root: { class: getResponsiveClasses({
                                mobile: '!right-0',
                                tablet: '!right-0',
                                desktop: '!right-0'
                            }) }
                        }"
                    >
                        <div :class="getResponsiveClasses({
                            mobile: 'p-2 min-w-[160px]',
                            tablet: 'p-2 min-w-[180px]',
                            desktop: 'p-2 min-w-[180px] sm:min-w-[200px]'
                        })">
                            <!-- User Info -->
                            <div :class="[
                                'border-b border-gray-200 mb-2',
                                getResponsiveClasses({
                                    mobile: 'px-2 py-2',
                                    tablet: 'px-2 py-2',
                                    desktop: 'px-2 sm:px-3 py-2'
                                })
                            ]">
                                <p :class="[
                                    'font-semibold text-gray-800 truncate',
                                    getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-sm',
                                        desktop: 'text-sm sm:text-base'
                                    })
                                ]">{{ Page.user?.full_name }}</p>
                                <p :class="[
                                    'text-gray-600 truncate',
                                    getResponsiveClasses({
                                        mobile: 'text-xs',
                                        tablet: 'text-xs',
                                        desktop: 'text-xs sm:text-sm'
                                    })
                                ]">{{ Page.user?.email }}</p>
                            </div>
                            <LogoutButton :class="getResponsiveClasses({
                                mobile: '!text-sm',
                                tablet: '!text-sm',
                                desktop: ''
                            })" />
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
import { computed, inject, onMounted, onUnmounted, ref } from "vue";
import LogoutButton from "@/components/LogoutButton.vue";
import UrlUtil from "@/utils/UrlUtil";
import { useEcho } from "@laravel/echo-vue";
import { IOrderNotification } from "@/interfaces/IOrderNotification";
import useAxiosUtil from "@/utils/AxiosUtil";
import { useToast } from "vue-toastification";
import { useRouter, useRoute } from "vue-router";
import { useResponsive } from "@/composables/useResponsive";

const sideBar = inject("sideBar");
const isScrolled = ref(false);
const route = useRoute();

// Use responsive composable
const responsive = useResponsive();
const { 
    isMobile, 
    isTablet, 
    isDesktop 
} = responsive;

// Extract the function separately to ensure it's available
const getResponsiveClasses = responsive.getResponsiveClasses;

const getResponsiveButtonStyles = () => {
    const baseStyle = 'color: #2563EB; background: transparent; border: none; box-shadow: none; cursor: pointer;';
    
    if (isMobile.value) {
        return `${baseStyle} width: 36px; height: 36px;`;
    }
    if (isTablet.value) {
        return `${baseStyle} width: 40px; height: 40px;`;
    }
    return `${baseStyle} width: 44px; height: 44px;`;
};

const getResponsivePopoverStyles = () => {
    if (isMobile.value) {
        return 'width: calc(100vw - 16px); max-width: calc(100vw - 16px); z-index: 9999;';
    }
    if (isTablet.value) {
        return 'width: 350px; max-width: 350px; z-index: 9999;';
    }
    return 'width: 450px; max-width: 450px; z-index: 9999;';
};

const getResponsivePopoverContentStyles = () => {
    if (isMobile.value) {
        return 'max-width: calc(100vw - 16px); overflow: hidden;';
    }
    if (isTablet.value) {
        return 'max-width: 350px; overflow: hidden;';
    }
    return 'max-width: 450px; overflow: hidden;';
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 0;
};
const notificationElement = ref<null | InstanceType<typeof Popover>>();
const avatarElement = ref<null | InstanceType<typeof Popover>>();
const activeTab = ref<'all' | 'unread'>('all');

// Sample notifications - replace with your actual data source
const notifications = ref<IOrderNotification[]>([

]);

const unreadCount = computed(() => notifications.value.filter(n => !n.is_read).length);

const filteredNotifications = computed(() => {
    if (activeTab.value === 'unread') {
        return notifications.value.filter(n => !n.is_read);
    }
    return notifications.value;
});

const isNotificationOpen = ref(false);

const openNotification = (event: Event) => {
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

const openAvatar = (event: Event) => {
    if (avatarElement.value) {
        avatarElement.value.toggle(event);
    }
};

const handleTabClick = (tab: 'all' | 'unread', event: Event) => {
    event.stopPropagation();
    event.preventDefault();
    activeTab.value = tab;
};

let clickOutsideListener: ((event: MouseEvent) => void) | null = null;

const onPopoverShow = () => {
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

const onPopoverHide = () => {
    isNotificationOpen.value = false;
    if (clickOutsideListener) {
        document.removeEventListener('click', clickOutsideListener);
        clickOutsideListener = null;
    }
};

const submitMarkReadService = useAxiosUtil();

const router = useRouter();

const markAsRead = async (notification: IOrderNotification) => {
    notification.is_read = true;
    router.push({ name: 'admin.order.index' });
    notificationElement.value?.hide();
    await submitMarkReadService.patch(`admin/order-notifications/mark-as-read/${notification.order_notification_id}`, null).then(() => {
    });
};

const markAllAsRead = () => {
    notifications.value.forEach(n => n.is_read = true);
    toast.success('All notifications marked as read successfully!');
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
        'Pending Order': 'bg-blue-100 text-blue-600',
        'Confirmed Order': 'bg-green-100 text-green-600',
        'To Ship': 'bg-orange-100 text-orange-600',
        'Paid': 'bg-emerald-100 text-emerald-600',
        'Cancelled Order': 'bg-red-100 text-red-600',
        'Rejected Order': 'bg-red-200 text-red-800'
    };
    return colors[type] || 'bg-gray-100 text-gray-600';
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

const loadService = useAxiosUtil<null, IOrderNotification[]>();
const toast = useToast();

const loadNotifications = async () => {
    await loadService.get("admin/order-notifications").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            notifications.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load notifications");
        }
    });
};

const { leave } = useEcho(
    "admin-order-notification",
    [".admin-order-notification.event"],
    (value: IOrderNotification) => {
        notifications.value.unshift(value);
    },
);

onMounted(() => {
    loadNotifications();
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    leave();
    window.removeEventListener('scroll', handleScroll);
});

</script>