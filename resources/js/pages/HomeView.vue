<template>
    <div>
        <header>
            <nav class="p-2">
                <Toolbar>
                    <template #start>
                        <div class="flex">
                            <img :src="Icon" class="w-16 h-16" alt="Store Icon">
                        </div>
                    </template>
                </Toolbar>
            </nav>
        </header>
    </div>
</template>
<script setup lang="ts">
import Icon from '@/../img/logo.png';
</script>