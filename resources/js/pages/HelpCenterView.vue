<template>
    <div class="min-h-screen bg-gray-50">
        <NavBar mode="customer" :transparent="false" />
        
        <div class="container mx-auto px-4 py-8 max-w-4xl">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How can we help you?</h1>
                <div class="relative max-w-lg mx-auto">
                    <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        placeholder="Search for help..." 
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                    >
                </div>
            </div>

            <!-- FAQ Categories -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-center">
                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
                        <i class="pi pi-shopping-cart text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Orders & Shipping</h3>
                    <p class="text-sm text-gray-500">Track orders, shipping rates, and delivery times.</p>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-center">
                    <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 text-green-600">
                        <i class="pi pi-refresh text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Returns & Refunds</h3>
                    <p class="text-sm text-gray-500">Return policy, refund status, and exchanges.</p>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow text-center">
                    <div class="w-12 h-12 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-600">
                        <i class="pi pi-user text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Account & Setting</h3>
                    <p class="text-sm text-gray-500">Profile settings, password reset, and security.</p>
                </div>
            </div>

            <!-- Frequently Asked Questions -->
            <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
                
                <div class="space-y-4">
                    <div v-for="(faq, index) in faqs" :key="index" class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <button @click="faq.isOpen = !faq.isOpen" class="flex items-center justify-between w-full text-left group">
                            <span class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ faq.question }}</span>
                            <i :class="['pi text-sm transition-transform duration-300', faq.isOpen ? 'pi-minus text-blue-600' : 'pi-plus text-gray-400']"></i>
                        </button>
                        <div v-show="faq.isOpen" class="mt-3 text-sm text-gray-600 leading-relaxed">
                            {{ faq.answer }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="mt-12 text-center">
                <p class="text-gray-600 mb-4">Still need help?</p>
                <button class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                    <i class="pi pi-envelope"></i>
                    Contact Support
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import NavBar from "@/components/NavBar.vue";
import { ref } from 'vue';

const faqs = ref([
    {
        question: "How do I track my order?",
        answer: "Once your order has been shipped, you will receive an email with a tracking number. You can also track your order status in your account dashboard under 'My Orders'.",
        isOpen: false
    },
    {
        question: "What payment methods do you accept?",
        answer: "We accept all major credit cards (Visa, Mastercard, Amex), PayPal, and GCash. We also offer Cash on Delivery (COD) for select locations.",
        isOpen: false
    },
    {
        question: "Can I cancel my order?",
        answer: "You can cancel your order within 1 hour of placing it directly from your account. If the order has already been processed or shipped, you may need to initiate a return instead.",
        isOpen: false
    },
    {
        question: "Do you offer warranty on products?",
        answer: "Yes, all our electronic products come with a standard 1-year manufacturer warranty. Some premium items may have extended warranty coverage.",
        isOpen: false
    }
]);
</script>
