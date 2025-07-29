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
                                        <img :src="Icon" class="h-16 w-16" alt="Store Icon" />
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
                    <template #center>
                        <div class="w-[500px] max-lg:hidden">
                            <form>
                                <InputGroup>
                                    <InputGroupAddon>
                                        <i class="pi pi-search" />
                                    </InputGroupAddon>
                                    <InputText v-model="form.search" :invalid="errors.search.length > 0"
                                        placeholder="Search product" fluid name="search" autocomplete="off" />
                                </InputGroup>
                            </form>
                        </div>
                    </template>
                    <template #end>
                        <div v-if="Page.user" class="flex gap-5">
                            <div class="flex items-center">
                                <CartButton />
                            </div>
                            <div>
                                <button type="button" class="cursor-pointer" @click="openAvatar">
                                    <Avatar v-if="Page.user && Page.user.image" :image="UrlUtil.getBaseApiUrl(
                                        `profile/${Page.user.image}`,
                                    )
                                        " size="large" shape="circle" />
                                    <Avatar v-else :label="Page.user && Page.user.full_name[0]" size="large"
                                        shape="circle" class="bg-white! text-black!" />
                                </button>
                                <Popover ref="avatarElement" :dismissable="true" class="mr-5 inline-flex">
                                    <div>
                                        <LogoutButton />
                                    </div>
                                </Popover>
                            </div>
                        </div>
                    </template>
                </Toolbar>
            </nav>
        </header>
        <div>
            <RouterView />
        </div>
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

const appName = import.meta.env.VITE_APP_NAME;
const form: SearchProductInterface = reactive({
    search: null,
});
const errors: SearchErrorInterface = reactive({
    search: [],
});
const avatarElement = ref<null | InstanceType<typeof Popover>>();

const openAvatar = (event: Event) => {
    if (avatarElement.value) {
        avatarElement.value.toggle(event);
    }
};
</script>