<template>
    <div class="flex h-svh items-center">
        <div class="flex w-full flex-wrap lg:p-16">
            <div class="max-lg:w-full max-lg:p-2 lg:w-1/2 lg:p-5">
                <div class="mb-2 flex justify-center">
                    <div class="rounded-full border">
                        <img
                            :src="Logo"
                            class="max-lg:w-42 lg:h-48 lg:w-48"
                            alt="Store Icon"
                        />
                    </div>
                </div>
                <h2 class="text-center text-2xl font-semibold text-sky-800">
                    Gear Up for Greatness
                </h2>
            </div>
            <div class="max-lg:w-full max-lg:p-2 lg:w-1/2 lg:p-5">
                <h1 class="p-3 text-3xl font-semibold text-sky-800">Login</h1>
                <LoginForm :admin="true" />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import Logo from "@/../img/logo.png";
import LoginForm from "@/components/forms/LoginForm.vue";
</script>
