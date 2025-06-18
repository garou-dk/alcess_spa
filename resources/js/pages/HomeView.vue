<template>
    <div>
        <header class="bg-sky-800">
            <nav class="p-2">
                <Toolbar style="background-color: transparent; border: none;">
                    <template #start>
                        <RouterLink :to="{ name: 'home' }">
                            <div class="flex">
                                <div class="px-2">
                                    <div class="bg-white rounded-full">
                                        <img :src="Icon" class="w-16 h-16" alt="Store Icon">
                                    </div>
                                </div>
                                <div class="flex items-center px-2">
                                    <h1 class="text-3xl font-bold text-white">{{ appName }}</h1>
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
                                    <InputText 
                                        v-model="form.search"
                                        :invalid="errors.search.length > 0"
                                        placeholder="Search product"
                                        fluid
                                        name="search"
                                        autocomplete="off"
                                    />
                                </InputGroup>
                            </form>
                        </div>
                    </template>
                    <template #end>
                        <div class="flex">
                            <div v-if="!Page.user" class="px-2">
                                <button
                                    type="button"
                                    class="text-white cursor-pointer"
                                    @click="loginFormVisible = true"
                                >
                                    <i class="pi pi-user" style="font-size: 1.5rem;" />
                                </button>
                            </div>
                        </div>
                    </template>
                </Toolbar>
            </nav>
        </header>
        <Dialog v-model:visible="loginFormVisible" modal header="Login" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <LoginForm />
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import Icon from '@/../img/logo.png';
import LoginForm from '@/components/forms/LoginForm.vue';
import { SearchErrorInterface, SearchProductInterface } from '@/interfaces/SearchProductInterface';
import Page from '@/stores/Page';
import { reactive, ref } from 'vue';

const appName = import.meta.env.VITE_APP_NAME;
const loginFormVisible = ref<boolean>(false);
const form : SearchProductInterface = reactive({
    search: null,
});
const errors : SearchErrorInterface = reactive({
    search: [],
});
</script>