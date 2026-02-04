<template>
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-4" v-click-outside="closeMenu">
        
        <!-- Embedded Chat Window (Controlled by Store) -->
        <ProductChatBot :externalControl="true" />

        <!-- Floating Options List -->
        <div class="flex flex-col gap-3 items-end transition-all duration-300 transform origin-bottom"
             :class="isExpanded ? 'opacity-100 scale-100 translate-y-0' : 'opacity-0 scale-95 translate-y-10 pointer-events-none'">
            
            <!-- Browse Products -->
            <button @click="handleAction('browse')" 
                class="flex items-center gap-3 group">
                <span class="bg-gray-900 text-white text-xs py-1.5 px-3 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                    Browse Products
                </span>
                <div class="w-12 h-12 rounded-full bg-white text-blue-600 shadow-lg shadow-blue-500/20 flex items-center justify-center hover:bg-blue-50 transition-all border border-blue-100">
                    <i class="pi pi-th-large text-xl"></i>
                </div>
            </button>

            <!-- Message Us (FB) -->
            <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" 
                class="flex items-center gap-3 group no-underline">
                <span class="bg-gray-900 text-white text-xs py-1.5 px-3 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                    Message Us
                </span>
                <div class="w-12 h-12 rounded-full bg-blue-600 text-white shadow-lg shadow-blue-500/30 flex items-center justify-center hover:bg-blue-700 transition-all">
                    <i class="pi pi-facebook text-xl"></i>
                </div>
            </a>

            <!-- Chat AI (Bottom of list) -->
            <button @click="handleAction('chat')" 
                class="flex items-center gap-3 group">
                <span class="bg-gray-900 text-white text-xs py-1.5 px-3 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                    Ask AI Assistant
                </span>
                <div class="w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg shadow-indigo-500/30 flex items-center justify-center hover:bg-indigo-700 transition-all relative">
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    <i class="pi pi-bolt text-xl"></i>
                </div>
            </button>
        </div>

        <!-- Master Toggle Button -->
        <button @click="toggleMenu" 
            class="w-14 h-14 rounded-full bg-gray-900 text-white shadow-xl shadow-gray-900/30 flex items-center justify-center transition-all hover:scale-110 z-50 relative focus:outline-none"
            :class="{ 'rotate-45': isExpanded }">
            <i class="pi pi-plus text-2xl transition-transform duration-300"></i>
        </button>

    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useChatStore } from '@/stores/ChatStore';
import ProductChatBot from "@/components/ProductChatBot.vue";

const isExpanded = ref(false);
const router = useRouter();
const chatStore = useChatStore();

const toggleMenu = () => {
    isExpanded.value = !isExpanded.value;
};

const closeMenu = () => {
    if (isExpanded.value) isExpanded.value = false;
    if (chatStore.isOpen) chatStore.isOpen = false;
};

const handleAction = (action: string) => {
    if (action === 'browse') {
        // As requested: Chat should show links instead of redirecting directly.
        // We will open the Chat and trigger "Browse All" flow.
        if (!chatStore.isOpen) chatStore.toggleChat();
        chatStore.handleOption({ label: 'Browse Store', value: 'all', action: 'browse_all' });
        // Optional: Close menu after selection? 
        // isExpanded.value = false;
    } 
    else if (action === 'chat') {
        chatStore.toggleChat();
        // isExpanded.value = false;
    }
};

// Custom directive for v-click-outside if not globally available
const vClickOutside = {
    mounted(el: any, binding: any) {
        el.clickOutsideEvent = function(event: Event) {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value(event);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el: any) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
    }
};
</script>

<style scoped>
.rotate-45 {
    transform: rotate(45deg);
}
</style>
