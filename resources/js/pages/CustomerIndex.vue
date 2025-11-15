<template>
    <div>
        <header class="bg-sky-800">
            <nav class="p-2">
                <Toolbar style="background-color: transparent; border: none">
                    <template #start>
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
                    </template>
                    <template #end>
                        <div v-if="Page.user" class="flex gap-5">
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
import { reactive, ref } from "vue";
import Icon from "@/../img/logo.png";
import Page from "@/stores/Page";
import UrlUtil from "@/utils/UrlUtil";
import { Popover } from "primevue";
import LogoutButton from "@/components/LogoutButton.vue";
import CartButton from "@/components/CartButton.vue";
import RegisterForm from "@/components/forms/RegisterForm.vue";
import LoginForm from "@/components/forms/LoginForm.vue";
import AddressForm from "@/components/forms/AddressForm.vue";
import { useRouter } from "vue-router";

const appName = import.meta.env.VITE_APP_NAME;
const form: SearchProductInterface = reactive({
    search: null,
});
const errors: SearchErrorInterface = reactive({
    search: [],
});
const avatarElement = ref<null | InstanceType<typeof Popover>>();
const loginFormVisible = ref<boolean>(false);
const registerFormVisible = ref<boolean>(false);
const addressForm = ref<boolean>(false);
const router = useRouter();

const openAvatar = (event: Event) => {
    if (avatarElement.value) {
        avatarElement.value.toggle(event);
    }
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
}
</script>
