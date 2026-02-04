<template>
    <div class="product-bot-container">
        <!-- Chat Window -->
        <div v-show="chatStore.isOpen" class="chat-window animate__animated animate__fadeInUp" @click.stop>
            <div class="chat-header">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center p-1">
                        <img :src="Logo" alt="Alcess" class="w-full h-full object-contain" />
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-sm">Alcess Assistant</h3>
                        <p class="text-blue-100 text-xs opacity-80">Always here to help!</p>
                    </div>
                </div>
                <!-- Only show close button if not externally controlled, or maybe always allowing close is fine but toggle is external -->
                <button @click="chatStore.toggleChat" class="text-white hover:bg-white/10 p-1 rounded transition-colors">
                    <i class="pi pi-times"></i>
                </button>
            </div>

            <div class="chat-body" ref="chatBody">
                <div v-for="(msg, index) in chatStore.messages" :key="index" :class="['message-wrapper', msg.type]">
                    <!-- Bot Avatar -->
                    <div v-if="['bot', 'product_list', 'product_detail'].includes(msg.type)" class="bot-avatar">
                        <img :src="Logo" alt="Bot" class="w-full h-full object-contain p-1" />
                    </div>

                    <!-- Message Content -->
                    <div class="message-content" :class="{ 'w-full': msg.type === 'product_list' || msg.type === 'product_detail' }">
                        
                        <!-- Text Bubble -->
                        <div v-if="msg.text" class="message-bubble" :class="{ 'mb-2': msg.type !== 'user' && msg.type !== 'bot' }">
                            <p v-html="msg.text"></p>
                        </div>

                        <!-- Product List Carousel -->
                        <div v-if="msg.type === 'product_list'" class="product-carousel">
                            <div v-for="product in msg.data" :key="product.product_id" class="chat-product-card" @click="handleProductClick(product.product_id)">
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

                        <!-- Product Detail View (Enhanced) -->
                        <div v-if="msg.type === 'product_detail'" class="product-detail-card">
                            <div class="detail-header">
                                <img v-if="msg.data.product_image" :src="UrlUtil.getBaseAppUrl(`storage/images/product/${msg.data.product_image}`)" class="detail-img" />
                                <div class="detail-info">
                                    <h4 class="font-bold text-slate-800 text-sm leading-tight line-clamp-2">{{ msg.data.product_name }}</h4>
                                    <p class="text-blue-600 font-bold mt-1">{{ CurrencyUtil.formatCurrency(msg.data.product_price) }}</p>
                                </div>
                            </div>
                            
                            <div class="detail-content custom-scrollbar">
                                 <!-- Description Section -->
                                <div class="mb-3">
                                    <p class="text-xs font-bold text-slate-700 mb-1">About this Item:</p>
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ msg.data.description || 'No description available.' }}</p>
                                </div>

                                <!-- Specs Section -->
                                <div>
                                    <p class="text-xs font-bold text-slate-700 mb-1">Technical Specifications:</p>
                                    <div v-if="msg.data.specifications && msg.data.specifications.length" class="space-y-1 bg-slate-50 p-2 rounded">
                                        <div v-for="spec in msg.data.specifications" :key="spec.id" class="text-xs text-slate-600 flex flex-col border-b border-slate-100 last:border-0 pb-1 last:pb-0">
                                            <span class="opacity-70 text-[10px] uppercase tracking-wide">{{ spec.specification_name }}</span>
                                            <span class="font-medium pl-1">{{ spec.specification_value }}</span>
                                        </div>
                                    </div>
                                    <p v-else class="text-xs text-slate-400 italic">No detailed specifications listed.</p>
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
                            class="suggestion-chip flex items-center gap-2"
                            :disabled="chatStore.isTyping"
                        >
                            <i v-if="option.icon" :class="option.icon" class="text-xs"></i>
                            {{ option.label }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Toggle Button (Hidden if externally controlled) -->
        <button 
            v-if="!externalControl"
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
import { ref, watch, nextTick, defineProps } from 'vue';
import { useChatStore } from '@/stores/ChatStore';
import UrlUtil from '@/utils/UrlUtil';
import CurrencyUtil from '@/utils/CurrencyUtil';
import { useRouter } from 'vue-router';
import Logo from "@/../img/logo.png";

const props = defineProps({
    externalControl: {
        type: Boolean,
        default: false
    }
});

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

const handleProductClick = (id: number) => {
    // Instead of navigating, we ask the bot for details
    chatStore.handleOption({ 
        label: `View Details`, 
        value: 'view_details', 
        action: 'view_details', 
        payload: id 
    });
};
</script>

<style scoped>
.product-bot-container {
    position: relative;
    /* Removed z-index: 9999 to allow Menu container to manage stacking if needed, 
       but keeping relative implies it flows naturally in the DOM of Menu */
}

/* Chat Window - Adjusted for new positioning logic relative to parent */
.chat-window {
    position: fixed;
    bottom: 0;
    right: 6rem; /* Left of the toggle button */
    left: auto;
    width: 380px;
    height: 550px;
    max-height: 85vh;
    background: #ffffff;
    border-top-left-radius: 1.5rem;
    border-top-right-radius: 1.5rem;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    box-shadow: 0 -5px 40px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform-origin: bottom right;
    border: 1px solid #e2e8f0;
    z-index: 1050; /* Above menu */
}

/* Header */
.chat-header {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
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
    max-width: 90%;
    animation: fadeIn 0.3s ease;
}

.message-wrapper.user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-wrapper.product_list, .message-wrapper.product_detail {
    max-width: 100%; 
}

.bot-avatar {
    width: 32px;
    height: 32px;
    background: #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.75rem;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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

.bot .message-bubble, .product_list .message-bubble, .product_detail .message-bubble {
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
    max-width: 280px;
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

/* Product Details Card */
.product-detail-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    overflow: hidden;
    width: 300px; /* Wider for details */
    max-width: 100%;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.detail-header {
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.detail-img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    background: #fff;
    border-radius: 0.5rem;
    padding: 0.25rem;
    border: 1px solid #e2e8f0;
}

.detail-content {
    padding: 1rem;
    background: #fff;
    max-height: 250px; /* Limit height */
    overflow-y: auto; /* Scroll internally */
}

/* Custom Scrollbar for details */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
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
        width: 300px;
        right: 1rem;
        bottom: 5rem;
        height: 500px;
    }
}
</style>
