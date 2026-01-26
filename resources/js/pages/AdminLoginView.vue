<template>
    <div class="admin-login-page">
        <!-- Background Decoration -->
        <div class="bg-decoration">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
        </div>

        <div class="login-container">
            <!-- Left Side: Branding/Welcome -->
            <div class="login-brand-panel">
                <div class="brand-content">
                    <div class="logo-wrapper">
                        <div class="logo-pulse"></div>
                        <img :src="Logo" alt="Store Logo" class="brand-logo" />
                    </div>
                    <h2 class="welcome-title">Welcome Back</h2>
                    <p class="welcome-subtitle">Gear up for greatness</p>
                    <div class="divider"></div>
                    <p class="welcome-text">Sign in to access your admin dashboard and manage your store with powerful tools.</p>
                    
                    <div class="feature-list">
                        <div class="feature-item">
                            <i class="pi pi-chart-line"></i>
                            <span>Real-time Analytics</span>
                        </div>
                        <div class="feature-item">
                            <i class="pi pi-box"></i>
                            <span>Inventory Management</span>
                        </div>
                        <div class="feature-item">
                            <i class="pi pi-shield"></i>
                            <span>Secure Access</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="login-form-panel">
                <div class="form-content">
                    <header class="form-header">
                        <div class="header-icon">
                            <i class="pi pi-lock"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="form-title">Admin Login</h1>
                            <p class="form-subtitle">Enter your credentials to continue</p>
                        </div>
                    </header>

                    <div class="form-body">
                        <LoginForm :admin="true" />
                    </div>

                    <footer class="form-footer">
                        <div class="security-notice">
                            <i class="pi pi-info-circle"></i>
                            <div class="notice-text">
                                <strong>Security Notice</strong>
                                <p>Make sure you're on the official portal. Never share your credentials.</p>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>

        <div class="page-footer">
            <p>&copy; {{ new Date().getFullYear() }} All rights reserved. Powered by Your Store Management System.</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import Logo from "@/../img/logo.png";
import LoginForm from "@/components/forms/LoginForm.vue";
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap');

.admin-login-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #f8fafc;
    font-family: 'Inter', sans-serif;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

/* Background Decoration */
.bg-decoration {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
}

.blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.4;
}

.blob-1 {
    width: 500px;
    height: 500px;
    background-color: #bfdbfe;
    top: -100px;
    right: -100px;
    animation: move 20s infinite alternate;
}

.blob-2 {
    width: 400px;
    height: 400px;
    background-color: #dbeafe;
    bottom: -50px;
    left: -50px;
    animation: move 15s infinite alternate-reverse;
}

@keyframes move {
    from { transform: translate(0, 0); }
    to { transform: translate(50px, 50px); }
}

/* Main Container */
.login-container {
    width: 100%;
    max-width: 1000px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    display: flex;
    overflow: hidden;
    position: relative;
    z-index: 1;
    min-height: 600px;
}

/* Left Panel */
.login-brand-panel {
    width: 45%;
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    padding: 3rem;
    color: #fff;
    display: flex;
    align-items: center;
}

.brand-content {
    width: 100%;
}

.logo-wrapper {
    position: relative;
    width: 100px;
    height: 100px;
    background: #fff;
    border-radius: 20px;
    padding: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.brand-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.logo-pulse {
    position: absolute;
    inset: -5px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 24px;
    z-index: -1;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.1; transform: scale(1.1); }
}

.welcome-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    font-family: 'Poppins', sans-serif;
}

.welcome-subtitle {
    font-size: 1.125rem;
    color: #93c5fd;
    font-weight: 500;
    margin-bottom: 2rem;
}

.divider {
    width: 60px;
    height: 4px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
    margin-bottom: 2rem;
}

.welcome-text {
    font-size: 1rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 3rem;
}

.feature-list {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.feature-item i {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
}

.feature-item span {
    font-size: 0.9375rem;
    font-weight: 500;
}

/* Right Panel */
.login-form-panel {
    width: 55%;
    padding: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-content {
    width: 100%;
    max-width: 400px;
}

.form-header {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    margin-bottom: 2.5rem;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: #eff6ff;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2563eb;
    font-size: 1.25rem;
}

.form-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
}

.form-subtitle {
    font-size: 0.875rem;
    color: #64748b;
}

.form-body {
    margin-bottom: 2rem;
}

.security-notice {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #2563eb;
}

.security-notice i {
    color: #2563eb;
    margin-top: 0.25rem;
}

.notice-text strong {
    display: block;
    font-size: 0.875rem;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.notice-text p {
    font-size: 0.75rem;
    color: #64748b;
    line-height: 1.4;
}

.page-footer {
    margin-top: 2rem;
    color: #64748b;
    font-size: 0.875rem;
    text-align: center;
    position: relative;
    z-index: 1;
}

/* Mobile Responsive */
@media (max-width: 1024px) {
    .login-container {
        flex-direction: column;
        max-width: 500px;
    }

    .login-brand-panel {
        width: 100%;
        padding: 2.5rem;
        min-height: auto;
    }

    .login-form-panel {
        width: 100%;
        padding: 2.5rem;
    }

    .welcome-title {
        font-size: 2rem;
    }

    .welcome-text {
        margin-bottom: 1.5rem;
    }

    .feature-list {
        display: none;
    }
}

@media (max-width: 640px) {
    .admin-login-page {
        padding: 1rem;
    }
    
    .login-container {
        border-radius: 16px;
    }
    
    .login-brand-panel, .login-form-panel {
        padding: 2rem 1.5rem;
    }
}
</style>