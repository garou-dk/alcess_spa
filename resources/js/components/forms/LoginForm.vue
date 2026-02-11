<template>
    <form @submit.prevent="handleSubmit()">
        <!-- Unverified Account Notice -->
        <transition name="notice-slide">
            <div v-if="showUnverifiedNotice" class="mx-2 mb-4 rounded-xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center">
                            <i class="pi pi-envelope text-amber-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-amber-800 mb-1">Account Not Verified</h4>
                        <p class="text-xs text-amber-700 leading-relaxed">
                            {{ unverifiedMessage }}
                        </p>
                    </div>
                    <button 
                        type="button" 
                        @click="showUnverifiedNotice = false" 
                        class="flex-shrink-0 text-amber-400 hover:text-amber-600 transition-colors"
                    >
                        <i class="pi pi-times text-sm"></i>
                    </button>
                </div>
            </div>
        </transition>

        <!-- Recent Logins (Facebook-like quick profiles) -->
        <div v-if="!props.admin && recentLogins.length" class="px-2 mb-1">
            <p class="text-xs font-semibold text-gray-500 mb-2">Recent logins</p>
            <div class="flex gap-3 overflow-x-auto pb-1">
                <button
                    v-for="user in recentLogins"
                    :key="user.user_id"
                    type="button"
                    @click="selectRecentLogin(user)"
                    class="flex items-center gap-2 px-2.5 py-1.5 bg-gray-50 hover:bg-gray-100 rounded-xl border border-gray-200 transition-colors cursor-pointer min-w-[180px]"
                >
                    <div class="relative">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold overflow-hidden">
                            <img
                                v-if="user.image"
                                :src="user.image"
                                alt="Avatar"
                                class="w-full h-full object-cover"
                            />
                            <span v-else>{{ user.full_name.charAt(0).toUpperCase() }}</span>
                        </div>
                        <button
                            type="button"
                            @click.stop="removeRecentLogin(user)"
                            class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-white text-gray-400 hover:text-red-500 border border-gray-200 flex items-center justify-center text-[10px] cursor-pointer"
                        >
                            ×
                        </button>
                    </div>
                    <div class="flex-1 min-w-0 text-left">
                        <p class="text-xs font-semibold text-gray-800 truncate">{{ user.full_name }}</p>
                        <p class="text-[11px] text-gray-500 truncate">••••••••••</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Quick login modal for recent accounts -->
        <Dialog
            v-model:visible="showLoginModal"
            modal
            header="Welcome back"
            :style="{ width: '25rem' }"
            :draggable="false"
            class="p-0"
        >
            <div class="flex flex-col items-center pt-4">
                <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl font-bold overflow-hidden mb-4 shadow-sm border-2 border-white">
                    <img
                        v-if="selectedRecentUser?.image"
                        :src="selectedRecentUser.image"
                        alt="Avatar"
                        class="w-full h-full object-cover"
                    />
                    <span v-else>{{ selectedRecentUser?.full_name?.charAt(0).toUpperCase() }}</span>
                </div>
                
                <h3 class="text-lg font-bold text-gray-800">{{ selectedRecentUser?.full_name }}</h3>
                <p class="text-xs text-gray-500 mb-4">Enter password to continue</p>

                <form @submit.prevent="handleSubmit" class="w-full">
                    <div class="mb-4">
                        <Password
                            v-model="form.password"
                            :invalid="errors.password.length > 0"
                            placeholder="Password"
                            fluid
                            :feedback="false"
                            :toggleMask="true"
                            input-id="modal-password"
                            class="w-full"
                            :autofocus="true"
                        />
                        <small v-if="errors.password.length > 0" class="text-red-500 text-xs mt-1 block">
                            {{ errors.password[0] }}
                        </small>
                    </div>

                    <Button
                        type="submit"
                        label="Log In"
                        :loading="authService.request.loading"
                        fluid
                        pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-xl!"
                    />
                </form>
            </div>
        </Dialog>

        <div class="p-2">
            <InputForm
                :errors="errors.email"
                id="email"
                labelName="Email"
                tag="label"
            >
                <InputText
                    type="email"
                    v-model="form.email"
                    :invalid="errors.email.length > 0"
                    placeholder="Email"
                    fluid
                    id="email"
                    autocomplete="off"
                />
            </InputForm>
        </div>
        <div class="p-2">
            <InputForm
                :errors="errors.password"
                id="password"
                labelName="Password"
                tag="label"
            >
                <Password
                    v-model="form.password"
                    :invalid="errors.password.length > 0"
                    placeholder="Password"
                    fluid
                    :feedback="false"
                    :toggleMask="true"
                    input-id="password"
                />
            </InputForm>
        </div>
        <div class="flex justify-center p-2">
            <Button
                type="submit"
                label="Login"
                :loading="authService.request.loading"
                fluid
                pt:root:class="bg-blue-600! hover:bg-blue-700! rounded-3xl!"
            />
        </div>
        <slot name="footer" />
    </form>
</template>
<script setup lang="ts">
import { getStoreRoles, RoleEnum } from "@/enums/RoleEnum";
import {
    LoginFormErrorInterface,
    LoginFormInterface,
} from "@/interfaces/LoginFormInterface";
import { UserInterface } from "@/interfaces/UserInterface";
import Page from "@/stores/Page";
import useAxiosUtil from "@/utils/AxiosUtil";
import { reactive, ref, onMounted, nextTick } from "vue";
import Dialog from "primevue/dialog";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";

interface Props {
    admin?: boolean;
}

const router = useRouter();
const props = defineProps<Props>();
const toast = useToast();
const authService = useAxiosUtil<LoginFormInterface, UserInterface>();

interface RecentLogin {
    user_id: number;
    full_name: string;
    email: string;
    image: string | null;
    last_login_at?: string;
}

const RECENT_LOGIN_KEY = "recent_logins";
const recentLogins = ref<RecentLogin[]>([]);
const selectedRecentUser = ref<RecentLogin | null>(null);
const showLoginModal = ref(false);

const form: LoginFormInterface = reactive({
    email: null,
    password: null,
});
const errors: LoginFormErrorInterface = reactive({
    email: [],
    password: [],
});

const showUnverifiedNotice = ref(false);
const unverifiedMessage = ref('');
const showLoginModal = ref(false);
const selectedRecentUser = ref<RecentLogin | null>(null);

const maskEmail = (email: string) => {
    if (!email) return '';
    const [name, domain] = email.split('@');
    if (!name || !domain) return email;
    const maskedName = name.length > 2 
        ? name[0] + '*'.repeat(name.length - 2) + name[name.length - 1] 
        : name + '***';
    return `${maskedName}@${domain}`;
};

const loadRecentLogins = () => {
    try {
        const raw = localStorage.getItem(RECENT_LOGIN_KEY);
        if (!raw) {
            recentLogins.value = [];
            return;
        }
        const parsed = JSON.parse(raw) as RecentLogin[];
        recentLogins.value = Array.isArray(parsed) ? parsed : [];
    } catch {
        recentLogins.value = [];
    }
};

const saveRecentLogin = (user: UserInterface) => {
    try {
        const raw = localStorage.getItem(RECENT_LOGIN_KEY);
        const existing: RecentLogin[] = raw ? JSON.parse(raw) : [];
        const filtered = existing.filter(
            (u) => u.user_id !== user.user_id && u.email !== user.email
        );
        const entry: RecentLogin = {
            user_id: user.user_id,
            full_name: user.full_name,
            email: user.email,
            image: user.image,
            last_login_at: new Date().toISOString(),
        };
        const updated = [entry, ...filtered].slice(0, 5);
        localStorage.setItem(RECENT_LOGIN_KEY, JSON.stringify(updated));
        recentLogins.value = updated;
    } catch {
        // ignore storage errors
    }
};

const selectRecentLogin = (user: RecentLogin) => {
    selectedRecentUser.value = user;
    form.password = null;
    clearErrors();
    showLoginModal.value = true;
    
    nextTick(() => {
        const el = document.getElementById("modal-password") as HTMLInputElement | null;
        el?.focus();
    });
};

const removeRecentLogin = (user: RecentLogin) => {
    const updated = recentLogins.value.filter(
        (u) => u.user_id !== user.user_id || u.email !== user.email
    );
    recentLogins.value = updated;
    try {
        localStorage.setItem(RECENT_LOGIN_KEY, JSON.stringify(updated));
    } catch {
        // ignore
    }
};

const clearErrors = () => {
    Object.keys(errors).forEach((key) => {
        errors[key] = [];
    });
};

const validate = () => {
    clearErrors();
    
    const effectiveEmail = selectedRecentUser.value
        ? selectedRecentUser.value.email
        : form.email;

    if (!effectiveEmail) {
        errors.email.push("Email is required");
    }
    if (!form.password) {
        errors.password.push("Password is required");
    }

    const hasErrors = [errors.email.length > 0, errors.password.length > 0];

    if (hasErrors.includes(true)) {
        return false;
    }

    return {
        email: effectiveEmail,
        password: form.password,
    } as LoginFormInterface;
};

const emit = defineEmits(['success']);

onMounted(() => {
    if (!props.admin) {
        loadRecentLogins();
    }
});

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        // Reset notice on new attempt
        showUnverifiedNotice.value = false;

        await authService
            .post(props.admin ? "admin/login" : "login", data)
            .then(async () => {
                if (
                    authService.request.status === 200 &&
                    authService.request.data
                ) {
                    // Update recent login with successful login time and ensure it's saved/moved to top
                    if (!props.admin) {
                        saveRecentLogin(authService.request.data);
                    }

                    Page.user = authService.request.data;
                    
                    // Navigate first
                    if (
                        getStoreRoles().includes(
                            Page.user.role.role_name as RoleEnum,
                        )
                    ) {
                        await router.push({ name: "admin.app" });
                    } else {
                        await router.push({ name: "customer.home.index" });
                    }
                    
                    // Then close modal after navigation completes
                    emit('success');
                } else {
                    const message = authService.request.message ?? "Please try again.";
                    
                    // Check if it's an email verification issue
                    if (
                        authService.request.status === 401 &&
                        message.toLowerCase().includes('verify')
                    ) {
                        showUnverifiedNotice.value = true;
                        unverifiedMessage.value = message;
                    } else {
                        toast.error(message);
                    }
                    
                    if (authService.request.errors) {
                        Object.keys(authService.request.errors).forEach(
                            (key) => {
                                errors[key] = authService.request.errors[key];
                            },
                        );
                    }
                }
            });
    } else {
        toast.error("Please fill in the required fields.");
    }
};
</script>
<style scoped>
.notice-slide-enter-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.notice-slide-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.notice-slide-enter-from {
    opacity: 0;
    transform: translateY(-12px);
    max-height: 0;
}
.notice-slide-enter-to {
    opacity: 1;
    transform: translateY(0);
    max-height: 200px;
}
.notice-slide-leave-from {
    opacity: 1;
    transform: translateY(0);
    max-height: 200px;
}
.notice-slide-leave-to {
    opacity: 0;
    transform: translateY(-8px);
    max-height: 0;
}
</style>
