<template>
    <div class="h-svh flex items-center">
        <div class="flex flex-wrap w-full lg:p-16">
            <div class="max-lg:p-2 lg:p-5 lg:w-1/2 max-lg:w-full">
                <div class="flex justify-center mb-2">
                    <div class="rounded-full border">
                        <img :src="Logo" class="lg:h-48 lg:w-48 max-lg:w-42" alt="Store Icon" />
                    </div>
                </div>
                <h2 class="text-center text-2xl font-semibold text-sky-800">Gear Up for Greatness</h2>
            </div>
            <div class="max-lg:p-2 lg:p-5 lg:w-1/2 max-lg:w-full">
                <h1 class="text-3xl font-semibold text-sky-800 p-3">Login</h1>
                <LoginForm :admin="true" />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import Logo from '@/../img/logo.png';
import LoginForm from '@/components/forms/LoginForm.vue';
</script>