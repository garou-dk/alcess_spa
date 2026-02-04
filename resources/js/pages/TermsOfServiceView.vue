<template>
    <div class="min-h-screen bg-gray-50">
        <NavBar mode="customer" :transparent="false" />
        
        <div class="container mx-auto px-4 py-8 max-w-4xl">
            <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12 mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Terms of Service</h1>
                <p class="text-gray-500 mb-8">Last updated: {{ new Date().toLocaleDateString() }}</p>
                
                <div class="prose prose-blue max-w-none text-gray-700">
                    <p>This website is operated by Alcess. Throughout the site, the terms "we", "us" and "our" refer to Alcess. Alcess offers this website, including all information, tools and services available from this site to you, the user, conditioned upon your acceptance of all terms, conditions, policies and notices stated here.</p>
                    
                    <h3>1. Online Store Terms</h3>
                    <p>By agreeing to these Terms of Service, you represent that you are at least the age of majority in your state or province of residence.</p>
                    
                    <h3>2. General Conditions</h3>
                    <p>We reserve the right to refuse service to anyone for any reason at any time. You understand that your content (not including credit card information), may be transferred unencrypted and involve (a) transmissions over various networks; and (b) changes to conform and adapt to technical requirements of connecting networks or devices.</p>
                    
                    <h3>3. Accuracy, Completeness and Timeliness of Information</h3>
                    <p>We are not responsible if information made available on this site is not accurate, complete or current. The material on this site is provided for general information only and should not be relied upon or used as the sole basis for making decisions without consulting primary, more accurate, more complete or more timely sources of information.</p>
                    
                    <h3>4. Modifications to the Service and Prices</h3>
                    <p>Prices for our products are subject to change without notice. We reserve the right at any time to modify or discontinue the Service (or any part or content thereof) without notice at any time.</p>
                    
                    <h3>5. Products or Services</h3>
                    <p>Certain products or services may be available exclusively online through the website. These products or services may have limited quantities and are subject to return or exchange only according to our Return Policy.</p>
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
