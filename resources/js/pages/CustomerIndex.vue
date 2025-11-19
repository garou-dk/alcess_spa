<template>
    <div>
        <header class="bg-sky-800">
            <nav class="p-2">
                <Toolbar style="background-color: transparent; border: none" pt:end:class="max-lg:w-full">
                    <template #start>
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
                            <Button
                                icon="pi pi-facebook"
                                rounded
                                as="a"
                                href="https://www.facebook.com/alcesslaptopstore"
                                target="_blank"
                            />
                        </div>
                    </template>
                    <template #end>
                        <div v-if="Page.user" class="flex gap-5 justify-evenly w-full">
                            <div class="flex items-center">
                                <RouterLink
                                    :to="{ name: 'customer.search-product' }"
                                >
                                    <Button
                                        type="button"
                                        icon="pi pi-search"
                                        severity="secondary"
                                        rounded
                                    />
                                </RouterLink>
                            </div>
                            <!-- Notification Bell -->
                            <div class="flex items-center">
                                <button
                                    type="button"
                                    class="relative cursor-pointer"
                                    @click="openNotifications"
                                >
                                    <Button
                                        type="button"
                                        icon="pi pi-bell"
                                        severity="secondary"
                                        rounded
                                    />
                                    <Badge 
                                        v-if="unreadCount > 0"
                                        :value="unreadCount"
                                        severity="danger"
                                        class="absolute -right-1 -top-1"
                                    />
                                </button>
                                <Popover
                                    ref="notificationElement"
                                    :dismissable="true"
                                >
                                    <div class="w-80 sm:w-96">
                                        <div class="border-b p-3">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-base sm:text-lg font-semibold">Notifications</h3>
                                                <Button
                                                    v-if="unreadCount > 0"
                                                    type="button"
                                                    label="Mark all read"
                                                    text
                                                    size="small"
                                                    class="text-xs sm:text-sm"
                                                    @click="markAllAsRead"
                                                />
                                            </div>
                                        </div>
                                        <div class="max-h-96 overflow-y-auto">
                                            <div
                                                v-if="notifications.length === 0"
                                                class="p-4 text-center text-gray-500 text-sm"
                                            >
                                                No notifications
                                            </div>
                                            <div
                                                v-for="notification in notifications"
                                                :key="notification.order_notification_id"
                                                class="border-b p-3 hover:bg-gray-50 cursor-pointer transition-colors"
                                                :class="{ 'bg-blue-50': !notification.is_read }"
                                                @click="markAsRead(notification.order_notification_id)"
                                            >
                                                <div class="flex gap-2 sm:gap-3">
                                                    <div class="flex-shrink-0">
                                                        <i
                                                            class="text-lg sm:text-xl pi pi-cart"
                                                        />
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-medium text-sm sm:text-base truncate">{{ notification.message }}</p>
                                                        <p class="text-xs text-gray-400 mt-1">{{ DateUtil.formatToMonthDayYear(notification.created_at) }} {{ DateUtil.timeStringAMPM(DateUtil.timeString(new Date(notification.created_at))) }}</p>
                                                    </div>
                                                    <div v-if="!notification.is_read" class="flex-shrink-0">
                                                        <Badge value=" " severity="info" class="w-2 h-2" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </Popover>
                            </div>
                            <div class="flex items-center">
                                <CartButton />
                            </div>
                            <div>
                                <button
                                    type="button"
                                    class="cursor-pointer"
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
                                        :label="
                                            Page.user && Page.user.full_name[0]
                                        "
                                        size="large"
                                        shape="circle"
                                        class="bg-white! text-black!"
                                    />
                                </button>
                                <Popover
                                    ref="avatarElement"
                                    :dismissable="true"
                                    class="mr-5 inline-flex"
                                >
                                    <div class="flex flex-col gap-2">
                                        <div>
                                            <Button
                                                type="button"
                                                class="bg-sky-800! text-white"
                                                label="Orders"
                                                icon="pi pi-truck"
                                                fluid
                                                @click="goToOrder()"
                                            />
                                        </div>
                                        <div>
                                            <Button
                                                type="button"
                                                class="bg-sky-800! text-white"
                                                label="Address"
                                                icon="pi pi-map-marker"
                                                @click="openAddressForm()"
                                            />
                                        </div>
                                        <div>
                                            <LogoutButton />
                                        </div>
                                    </div>
                                </Popover>
                            </div>
                        </div>
                        <div v-else class="flex">
                            <div v-if="!Page.user" class="px-2">
                                <button
                                    type="button"
                                    class="cursor-pointer text-white"
                                    @click="openLoginForm()"
                                >
                                    <i
                                        class="pi pi-user"
                                        style="font-size: 1.5rem"
                                    />
                                </button>
                            </div>
                        </div>
                    </template>
                </Toolbar>
            </nav>
        </header>
        <div>
            <RouterView />
        </div>
        
        <Dialog
            v-model:visible="loginFormVisible"
            modal
            header="Login"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <LoginForm>
                <template #footer>
                    <div class="flex">
                        <div class="w-1/2 p-2">
                            <Button
                                type="button"
                                variant="link"
                                label="Forgot password?"
                                class="p-0! text-black!"
                                pt:label:class="font-normal!"
                            />
                        </div>
                        <div class="flex w-1/2 flex-col p-2">
                            <span class="text-end">Don't have an account?</span>
                            <div class="flex justify-end">
                                <Button
                                    type="button"
                                    variant="link"
                                    label="Register"
                                    class="p-0!"
                                    pt:label:class="font-normal!"
                                    @click="openRegisterForm()"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </LoginForm>
        </Dialog>
        <Dialog
            v-model:visible="registerFormVisible"
            modal
            header="Register"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <RegisterForm>
                <template #footer>
                    <div class="flex justify-center p-2">
                        <span>Already have an account?</span>
                        &nbsp;
                        <Button
                            type="button"
                            variant="link"
                            label="Login"
                            class="p-0!"
                            pt:label:class="font-normal!"
                            @click="openLoginForm()"
                        />
                    </div>
                </template>
            </RegisterForm>
        </Dialog>
        <Dialog
            v-model:visible="addressForm"
            modal
            header="Address"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <AddressForm />
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import {
    SearchErrorInterface,
    SearchProductInterface,
} from "@/interfaces/SearchProductInterface";
import { reactive, ref, computed, onMounted, onUnmounted } from "vue";
import Icon from "@/../img/logo.png";
import Page from "@/stores/Page";
import UrlUtil from "@/utils/UrlUtil";
import { Popover, Badge } from "primevue";
import LogoutButton from "@/components/LogoutButton.vue";
import CartButton from "@/components/CartButton.vue";
import RegisterForm from "@/components/forms/RegisterForm.vue";
import LoginForm from "@/components/forms/LoginForm.vue";
import AddressForm from "@/components/forms/AddressForm.vue";
import { useRouter } from "vue-router";
import useAxiosUtil from "@/utils/AxiosUtil";
import { IOrderNotification } from "@/interfaces/IOrderNotification";
import { useToast } from "vue-toastification";
import DateUtil from "@/utils/DateUtil";
import { useEcho } from "@laravel/echo-vue";

const appName = import.meta.env.VITE_APP_NAME;
const form: SearchProductInterface = reactive({
    search: null,
});
const errors: SearchErrorInterface = reactive({
    search: [],
});

const avatarElement = ref<null | InstanceType<typeof Popover>>();
const notificationElement = ref<null | InstanceType<typeof Popover>>();
const loginFormVisible = ref<boolean>(false);
const registerFormVisible = ref<boolean>(false);
const addressForm = ref<boolean>(false);
const router = useRouter();

interface Notification {
    id: number;
    title: string;
    message: string;
    time: string;
    read: boolean;
    icon: string;
    color: string;
}

const notifications = ref<IOrderNotification[]>([
]);

const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.is_read).length;
});

const openAvatar = (event: Event) => {
    if (avatarElement.value) {
        avatarElement.value.toggle(event);
    }
};

const openNotifications = (event: Event) => {
    if (notificationElement.value) {
        notificationElement.value.toggle(event);
    }
};

const submitMarkReadService = useAxiosUtil();

const markAsRead = async (id: number) => {
    const notification = notifications.value.find(n => n.order_notification_id === id);
    if (notification && !notification.is_read) {
        notification.is_read = true;
    }
    notificationElement.value?.hide();
    router.push({ name: 'customer.order.index' });
    await submitMarkReadService.patch(`customer/order-notifications/mark-as-read/${notification.order_notification_id}`, null).then(() => {
    });
};

const markAllAsRead = () => {
    notifications.value.forEach(n => n.is_read = true);
};

const openRegisterForm = () => {
    loginFormVisible.value = false;
    addressForm.value = false;
    registerFormVisible.value = true;
};

const openLoginForm = () => {
    registerFormVisible.value = false;
    addressForm.value = false;
    loginFormVisible.value = true;
};

const openAddressForm = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
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

const { leave } = useEcho(
    `order.${Page.user.user_id}`,
    [".customer-order.event"],
    (value: IOrderNotification) => {
        notifications.value.unshift(value);
    },
);

onMounted(() => {
    loadNotifications();
});

onUnmounted(() => {
    leave();
});
</script>