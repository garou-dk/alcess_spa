<template>
    <div class="min-h-screen bg-gray-50">
        <NavBar mode="customer" :transparent="false" />
        
        <div class="container mx-auto px-4 py-8 max-w-4xl">
            <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12 mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
                <p class="text-gray-500 mb-8">Last updated: {{ new Date().toLocaleDateString() }}</p>
                
                <div class="prose prose-blue max-w-none text-gray-700">
                    <p>At Alcess, we take your privacy seriously. This Privacy Policy describes how your personal information is collected, used, and shared when you visit or make a purchase from our store.</p>
                    
                    <h3>Information We Collect</h3>
                    <p>When you visit the Site, we automatically collect certain information about your device, including information about your web browser, IP address, time zone, and some of the cookies that are installed on your device.</p>
                    
                    <h3>How We Use Your Information</h3>
                    <p>We use the Order Information that we collect generally to fulfill any orders placed through the Site (including processing your payment information, arranging for shipping, and providing you with invoices and/or order confirmations).</p>
                    
                    <h3>Sharing Your Personal Information</h3>
                    <p>We share your Personal Information with third parties to help us use your Personal Information, as described above. We also use Google Analytics to help us understand how our customers use the Site.</p>
                    
                    <h3>Data Retention</h3>
                    <p>When you place an order through the Site, we will maintain your Order Information for our records unless and until you ask us to delete this information.</p>
                    
                    <h3>Contact Us</h3>
                    <p>For more information about our privacy practices, if you have questions, or if you would like to make a complaint, please contact us by e-mail at support@alcess.com.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import NavBar from "@/components/NavBar.vue";
</script>

<style scoped>
.prose h3 {
    color: #1e293b;
    font-weight: 700;
    margin-top: 1.5em;
    margin-bottom: 0.5em;
}
.prose p {
    margin-bottom: 1em;
    line-height: 1.6;
}
</style>
