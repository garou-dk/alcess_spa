<template>
    <footer class="bg-slate-900 text-slate-400 py-12 px-4 border-t border-slate-800">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- Branding -->
                <div>
                    <h3 class="text-white text-lg font-bold mb-4">{{ appName }}</h3>
                    <p class="text-xs leading-relaxed max-w-xs">Your trusted destination for quality laptops, phones, and computers with genuine products and warranty.</p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-white text-sm font-semibold mb-3">Quick Links</h4>
                    <ul class="space-y-2">
                        <li>
                            <RouterLink :to="{ name: 'help-center' }" class="text-xs hover:text-white transition-colors block py-0.5">About Us</RouterLink>
                        </li>
                        <li>
                            <RouterLink :to="{ name: 'help-center' }" class="text-xs hover:text-white transition-colors block py-0.5">Contact</RouterLink>
                        </li>
                        <li>
                            <RouterLink :to="{ name: 'help-center' }" class="text-xs hover:text-white transition-colors block py-0.5">FAQs</RouterLink>
                        </li>
                        <li>
                            <RouterLink :to="{ name: 'help-center' }" class="text-xs hover:text-white transition-colors block py-0.5">Shipping Info</RouterLink>
                        </li>
                    </ul>
                </div>
                
                <!-- Customer Service -->
                <div>
                    <h4 class="text-white text-sm font-semibold mb-3">Customer Service</h4>
                    <ul class="space-y-2">
                        <li>
                            <RouterLink :to="{ name: 'help-center' }" class="text-xs hover:text-white transition-colors block py-0.5">Returns & Warranty</RouterLink>
                        </li>
                        <li>
                            <RouterLink :to="{ name: 'privacy-policy' }" class="text-xs hover:text-white transition-colors block py-0.5">Privacy Policy</RouterLink>
                        </li>
                        <li>
                            <RouterLink :to="{ name: 'terms-of-service' }" class="text-xs hover:text-white transition-colors block py-0.5">Terms & Conditions</RouterLink>
                        </li>
                    </ul>
                </div>
                
                <!-- Connect -->
                <div>
                    <h4 class="text-white text-sm font-semibold mb-3">Connect With Us</h4>
                    <div class="flex gap-3 mb-4">
                         <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="w-8 h-8 bg-white/10 rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-colors">
                            <i class="pi pi-facebook"></i>
                        </a>
                    </div>
                    <p class="text-xs flex items-center gap-2">
                        <i class="pi pi-envelope"></i> support@alcess.com
                    </p>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-8 text-center">
                 <p class="text-xs text-slate-500">&copy; {{ new Date().getFullYear() }} {{ appName }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
const appName = import.meta.env.VITE_APP_NAME || 'Alcess';
</script>
