<template>
    <div class="product-bot-container">
        <!-- Chat Window -->
        <div v-show="chatStore.isOpen" class="chat-window animate__animated animate__fadeInUp">
            <div class="chat-header">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="pi pi-bolt text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-sm">Alcess Assistant</h3>
                        <p class="text-blue-100 text-xs opacity-80">Always here to help!</p>
                    </div>
                </div>
                <button @click="chatStore.toggleChat" class="text-white hover:bg-white/10 p-1 rounded transition-colors">
                    <i class="pi pi-times"></i>
                </button>
            </div>

            <div class="chat-body" ref="chatBody">
                <div v-for="(msg, index) in chatStore.messages" :key="index" :class="['message-wrapper', msg.type]">
                    <!-- Bot Avatar -->
                    <div v-if="msg.type === 'bot' || msg.type === 'product_list'" class="bot-avatar">
                        <i class="pi pi-bolt"></i>
                    </div>

                    <!-- Message Content -->
                    <div class="message-content" :class="{ 'w-full': msg.type === 'product_list' }">
                        
                        <!-- Text Bubble -->
                        <div v-if="msg.text" class="message-bubble" :class="{ 'mb-2': msg.type === 'product_list' }">
                            <p v-html="msg.text"></p>
                        </div>

                        <!-- Product List Carousel -->
                        <div v-if="msg.type === 'product_list'" class="product-carousel">
                            <div v-for="product in msg.data" :key="product.product_id" class="chat-product-card" @click="goToProduct(product.product_id)">
                                <div class="chat-prod-img">
                                     <img v-if="product.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" :alt="product.product_name" />
                                     <i v-else class="pi pi-image text-2xl text-slate-300"></i>
                                </div>
                                <div class="chat-prod-info">
                                    <h4 class="line-clamp-2 text-xs font-semibold text-slate-800 mb-1">{{ product.product_name }}</h4>
                                    <span class="text-blue-600 font-bold text-xs">{{ CurrencyUtil.formatCurrency(product.product_price) }}</span>
                                </div>
                            </div>
                        </div>

                        <span class="message-time">{{ msg.time }}</span>
                    </div>
                </div>
                
                <div v-if="chatStore.isTyping" class="message-wrapper bot">
                    <div class="bot-avatar"><i class="pi pi-bolt"></i></div>
                    <div class="message-bubble typing-indicator">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>

            <div class="chat-footer">
                <div class="suggestions-container" v-if="chatStore.currentOptions.length > 0">
                    <p class="text-xs text-slate-500 mb-2 font-medium">Suggested topics:</p>
                    <div class="flex flex-wrap gap-2">
                        <button 
                            v-for="(option, idx) in chatStore.currentOptions" 
                            :key="idx" 
                            @click="chatStore.handleOption(option)"
                            class="suggestion-chip"
                            :disabled="chatStore.isTyping"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Toggle Button -->
        <button 
            @click="chatStore.toggleChat" 
            class="chat-toggle-btn group"
            :class="{ 'active': chatStore.isOpen }"
        >
            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
            </span>
            <i :class="chatStore.isOpen ? 'pi pi-chevron-down' : 'pi pi-comments'" class="text-xl transition-transform duration-300"></i>
            <span class="tooltip-text">Chat with AI</span>
        </button>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick, onMounted } from 'vue';
import { useChatStore } from '@/stores/ChatStore';
import UrlUtil from '@/utils/UrlUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import { useRouter } from 'vue-router';

const chatStore = useChatStore();
const chatBody = ref<HTMLElement | null>(null);
const router = useRouter();

const scrollToBottom = () => {
    nextTick(() => {
        if (chatBody.value) {
            chatBody.value.scrollTop = chatBody.value.scrollHeight;
        }
    });
};

watch(() => chatStore.messages.length, () => {
    scrollToBottom();
});

watch(() => chatStore.isTyping, () => {
    scrollToBottom();
});

watch(() => chatStore.isOpen, (val) => {
    if (val) scrollToBottom();
});

watch(() => router.currentRoute.value.name, (newRouteName) => {
    if (newRouteName) {
        chatStore.setContext(newRouteName.toString());
    }
}, { immediate: true });

const goToProduct = (id: number) => {
    router.push({ name: 'customer.product-info.index', params: { id } });
}
</script>

<style scoped>
.product-bot-container {
    position: relative;
    z-index: 9999;
}

/* Toggle Button */
.chat-toggle-btn {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    border: 2px solid rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 52; /* Above chat window when closed/open toggle */
}

.chat-toggle-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
}

.chat-toggle-btn.active {
    background: #0f172a;
    transform: rotate(180deg);
}

.tooltip-text {
    position: absolute;
    left: 100%;
    margin-left: 10px;
    background: #1e293b;
    color: white;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 4px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
    white-space: nowrap;
}

.chat-toggle-btn:hover .tooltip-text {
    opacity: 1;
}

/* Chat Window */
.chat-window {
    position: absolute;
    bottom: 5rem;
    left: 0;
    width: 360px;
    height: 550px; /* Fixed height for consistency */
    max-height: 80vh;
    background: #ffffff;
    border-radius: 1.5rem;
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform-origin: bottom left;
    border: 1px solid #e2e8f0;
    z-index: 51;
}

/* Header */
.chat-header {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}

/* Body */
.chat-body {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.message-wrapper {
    display: flex;
    gap: 0.75rem;
    max-width: 85%;
    animation: fadeIn 0.3s ease;
}

.message-wrapper.user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-wrapper.product_list {
    max-width: 100%; /* Product list takes full width minus padding */
}

.bot-avatar {
    width: 28px;
    height: 28px;
    background: #eff6ff;
    color: #2563eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.75rem;
    border: 1px solid #dbeafe;
}

.message-content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.user .message-content {
    align-items: flex-end;
}

.message-bubble {
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    line-height: 1.5;
    position: relative;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.bot .message-bubble, .product_list .message-bubble {
    background: #ffffff;
    color: #334155;
    border-bottom-left-radius: 0.25rem;
    border: 1px solid #e2e8f0;
}

.user .message-bubble {
    background: #2563eb;
    color: white;
    border-bottom-right-radius: 0.25rem;
}

.message-time {
    font-size: 0.65rem;
    color: #94a3b8;
    margin-top: 0.25rem;
}

/* Product Carousel */
.product-carousel {
    display: flex;
    gap: 0.75rem;
    overflow-x: auto;
    padding: 0.25rem;
    width: 100%;
    max-width: 270px; /* Constrain width within bubble area if needed, or let it flow */
    scrollbar-width: thin;
}

.chat-product-card {
    min-width: 140px;
    width: 140px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.chat-product-card:hover {
    border-color: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.chat-prod-img {
    height: 100px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
}

.chat-prod-img img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.chat-prod-info {
    padding: 0.75rem;
    border-top: 1px solid #f1f5f9;
}

/* Typing Indicator */
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 1rem !important;
    align-items: center;
}

.typing-indicator span {
    width: 6px;
    height: 6px;
    background: #94a3b8;
    border-radius: 50%;
    animation: bounce 1.4s infinite ease-in-out both;
}

.typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
.typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

@keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Footer / Suggestions */
.chat-footer {
    padding: 0.75rem 1rem 1rem;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    flex-shrink: 0;
}

.suggestion-chip {
    padding: 0.5rem 0.875rem;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 9999px;
    font-size: 0.75rem;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
}

.suggestion-chip:hover {
    background: #eff6ff;
    border-color: #2563eb;
    color: #2563eb;
    transform: translateY(-1px);
}

.suggestion-chip:disabled {
    opacity: 0.5;
    cursor: wait;
}

/* Mobile Tweaks */
@media (max-width: 640px) {
    .chat-window {
        width: 320px;
        bottom: 4.5rem;
        height: 500px;
    }
}
</style>
