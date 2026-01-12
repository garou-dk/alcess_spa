<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Stripe-Style Header -->
        <header class="fixed top-0 left-0 right-0 z-[100] transition-all duration-300">
            <nav :class="[
                'mx-auto transition-all duration-300',
                isScrolled ? 'bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 py-3' : 'bg-transparent py-5'
            ]">
                <div class="container mx-auto px-6 flex items-center justify-between">
                    <!-- Logo -->
                    <RouterLink :to="{ name: 'home' }" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                            <img :src="Icon" class="w-7 h-7" alt="Logo" />
                        </div>
                        <h1 :class="[
                            'font-bold text-xl tracking-tight transition-colors',
                            isScrolled ? 'text-gray-900' : 'text-white'
                        ]">{{ appName }}</h1>
                    </RouterLink>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center gap-8">
                        <RouterLink v-for="link in ['Products', 'About', 'Contact']" :key="link" to="#" 
                            :class="[
                                'font-semibold text-sm transition-colors hover:opacity-70',
                                isScrolled ? 'text-gray-600' : 'text-white/90'
                            ]">{{ link }}</RouterLink>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-4">
                        <button v-if="!Page.user" @click="openLoginForm()" 
                            :class="[
                                'stripe-btn text-sm py-2 px-5 transition-all',
                                isScrolled ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-white/10 text-white backdrop-blur-sm hover:bg-white/20'
                            ]">
                            Sign In
                        </button>
                        <button v-else @click="goRoute('customer.dashboard')" 
                            :class="[
                                'stripe-btn text-sm py-2 px-5 transition-all',
                                isScrolled ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-white/10 text-white backdrop-blur-sm hover:bg-white/20'
                            ]">
                            Dashboard
                        </button>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Stripe Mesh Hero Section -->
        <section class="relative stripe-mesh min-h-screen flex items-center overflow-hidden pt-20">
            <!-- Background Orbs -->
            <div class="absolute inset-0 z-0">
                <div class="absolute top-20 left-10 w-[500px] h-[500px] bg-blue-400/20 rounded-full blur-[120px] animate-stripe-float"></div>
                <div class="absolute bottom-10 right-10 w-[600px] h-[600px] bg-indigo-500/20 rounded-full blur-[120px]" style="animation-delay: 2s;"></div>
            </div>

            <!-- Content -->
            <div class="container mx-auto px-6 relative z-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="animate-fade-in">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/20 mb-8">
                            <span class="flex h-2 w-2 rounded-full bg-blue-400"></span>
                            <span class="text-white text-xs font-bold uppercase tracking-widest">Premium Tech Store</span>
                        </div>
                        <h1 class="text-5xl lg:text-7xl font-bold text-white leading-tight mb-8">
                            Next-gen tech, <br/>
                            <span class="text-blue-200">now accessible.</span>
                        </h1>
                        <p class="text-xl text-blue-100/80 mb-10 max-w-xl leading-relaxed">
                            Upgrade your workflow with our premium selection of laptops and peripherals. Trusted by over 1,000+ professionals nationwide.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <button @click="goToBrowseProducts" class="stripe-btn-white stripe-btn text-lg py-4 px-8 shadow-xl">
                                Start Shopping
                                <i class="pi pi-arrow-right"></i>
                            </button>
                            <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="stripe-btn text-lg py-4 px-8 text-white border border-white/30 hover:bg-white/10 transition-all">
                                Contact Sales
                            </a>
                        </div>
                    </div>

                    <!-- Hero Visual -->
                    <div v-if="showCarousel" class="relative lg:-mr-32 animate-fade-in" style="animation-delay: 0.2s;">
                        <div class="relative stripe-glass rounded-[32px] p-8 lg:p-12 shadow-2xl border border-white/20">
                            <TransitionGroup name="hero-slide">
                                <div v-for="(product, index) in [products.slice(0, 1)[0]]" :key="index" class="relative">
                                    <div class="absolute -top-20 -right-20 w-40 h-40 bg-blue-500/20 rounded-full blur-3xl"></div>
                                    <img :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" 
                                        class="w-full h-auto object-contain drop-shadow-[0_35px_35px_rgba(0,0,0,0.5)] transform -rotate-6 hover:rotate-0 transition-transform duration-700"
                                        alt="Feature Product" />
                                    
                                    <!-- Floating Detail Card -->
                                    <div class="absolute -bottom-10 -left-10 bg-white p-6 rounded-2xl shadow-2xl animate-stripe-float hidden lg:block">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="pi pi-bolt text-blue-600 text-xl font-bold"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Flash Deal</p>
                                                <p class="text-lg font-bold text-gray-900">{{ CurrencyUtil.formatCurrency(product.product_price) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </TransitionGroup>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stripe Slanted Divider -->
            <div class="stripe-divider"></div>
        </section>


        <!-- Trust Indicators (Highlighting Quality) -->
        <section class="py-16 bg-white relative z-10">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="(item, idx) in [
                        { icon: 'pi pi-verified', title: '100% Genuine', sub: 'Verified quality' },
                        { icon: 'pi pi-truck', title: 'Fast Delivery', sub: 'Reliable shipping' },
                        { icon: 'pi pi-shield', title: 'Secure Warranty', sub: 'Buyer protection' },
                        { icon: 'pi pi-headphones', title: 'Expert Support', sub: 'Always here to help' }
                    ]" :key="idx" class="flex flex-col gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                            <i :class="item.icon"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 border-l-2 border-blue-600 pl-3 leading-none mb-1">{{ item.title }}</h3>
                            <p class="text-sm text-gray-500 pl-3">{{ item.sub }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic Browse Section (Categories & Products) -->
        <section id="browse-products" class="py-24 bg-[#f6f9fc] relative overflow-hidden">
            <div class="stripe-divider-inverse"></div>
            
            <div class="container mx-auto px-6 relative z-10 pt-16">
                <div class="flex flex-col md:flex-row items-baseline justify-between gap-6 mb-16">
                    <div>
                        <h2 class="text-3xl lg:text-5xl font-bold text-[#0a2540] mb-4">Precision gear for <br/>digital pioneers.</h2>
                        <div class="h-1 w-20 bg-blue-600 rounded-full mb-6"></div>
                    </div>
                    
                    <!-- View Toggle -->
                    <div class="inline-flex bg-gray-200/50 p-1 rounded-full backdrop-blur-sm self-start">
                        <button @click="activeView = 'categories'" 
                            :class="['px-6 py-2 rounded-full text-sm font-bold transition-all', activeView === 'categories' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700']">
                            Categories
                        </button>
                        <button @click="activeView = 'products'" 
                            :class="['px-6 py-2 rounded-full text-sm font-bold transition-all', activeView === 'products' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700']">
                            All Products
                        </button>
                    </div>
                </div>

                <Transition name="hero-slide" mode="out-in">
                    <!-- Categories Display -->
                    <div v-if="activeView === 'categories'" key="cats" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div v-if="CategoryStore.categories && CategoryStore.categories.length > 0" 
                             v-for="category in CategoryStore.categories" 
                             :key="category.category_id" 
                             @click="goRoute('customer.product-category', { id: category.category_id })"
                             class="product-card-stripe p-8 cursor-pointer group bg-white shadow-sm hover:shadow-xl transition-all h-full flex flex-col">
                            <div class="w-full aspect-square bg-[#f8fafc] rounded-2xl mb-8 flex items-center justify-center p-8 transition-colors group-hover:bg-blue-50">
                                <img v-if="category.category_image" 
                                    :src="UrlUtil.getBaseAppUrl(`storage/images/category/${category.category_image}`)" 
                                    class="h-full w-full object-contain transform group-hover:scale-110 transition-transform duration-500" />
                                <i v-else class="pi pi-image text-4xl text-gray-200"></i>
                            </div>
                            <h4 class="text-xl font-bold text-[#0a2540] mb-2 group-hover:text-blue-600 transition-colors">{{ category.category_name }}</h4>
                            <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Collection</span>
                                <i class="pi pi-arrow-right text-blue-600 opacity-0 group-hover:opacity-100 transition-all"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Products Display -->
                    <div v-else key="prods" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div v-if="products && products.length > 0" v-for="product in products" :key="product.product_id" 
                             class="product-card-stripe group bg-white p-6 shadow-sm hover:shadow-xl transition-all h-full flex flex-col">
                            <div class="relative w-full aspect-square bg-gray-50 rounded-2xl mb-6 flex items-center justify-center p-6 overflow-hidden">
                                <img :src="UrlUtil.getBaseAppUrl(`storage/images/product/${product.product_image}`)" 
                                    class="h-full w-full object-contain transform group-hover:scale-110 transition-transform duration-500" />
                                <div class="absolute top-4 left-4">
                                    <span class="bg-white/80 backdrop-blur-sm shadow-sm border border-gray-100 text-[10px] font-bold px-3 py-1 rounded-full text-blue-600 uppercase">
                                        {{ product.category?.category_name || 'Electronics' }}
                                    </span>
                                </div>
                            </div>
                            <h4 class="font-bold text-[#0a2540] mb-2 line-clamp-2 min-h-[3rem] group-hover:text-blue-600 transition-colors">{{ product.product_name }}</h4>
                            <div class="flex items-center justify-between mb-8">
                                <p class="text-2xl font-extrabold text-[#0a2540]">{{ CurrencyUtil.formatCurrency(product.product_price) }}</p>
                                <div class="flex items-center gap-1">
                                    <i class="pi pi-star-fill text-yellow-400 text-xs"></i>
                                    <span class="text-xs font-bold text-gray-400">4.9</span>
                                </div>
                            </div>
                            <div class="mt-auto grid grid-cols-4 gap-2">
                                <button @click="addToCart(product.product_id)" 
                                    class="col-span-3 bg-blue-600 text-white py-3 rounded-xl text-sm font-bold active:scale-95 transition-all hover:bg-black">
                                    Add to Cart
                                </button>
                                <button @click="goToProductDetails(product.product_id)" 
                                    class="bg-gray-100 text-gray-400 py-3 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-all">
                                    <i class="pi pi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </section>

            <!-- Premium Features List -->
            <section class="py-24 relative">
                <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-200 to-transparent mb-24"></div>
                
                <div class="grid lg:grid-cols-2 gap-24 items-center mb-24">
                    <div>
                        <span class="text-blue-600 font-bold uppercase tracking-widest text-sm mb-6 block">Our Commitment</span>
                        <h2 class="text-4xl lg:text-5xl font-bold text-[#0a2540] leading-tight mb-8">Engineering trust in every transaction.</h2>
                        <p class="text-lg text-gray-600 mb-10 leading-relaxed">We don't just sell computers; we provide the foundation for your digital growth. Every product is selected for performance and reliability.</p>
                        <div class="space-y-6">
                            <div v-for="(feat, i) in [
                                { t: 'Authenticity First', d: 'We source directly from manufacturers to guarantee genuine hardware.' },
                                { t: 'Global Standards', d: 'All devices meet international quality and safety certifications.' },
                                { t: 'Customer Centric', d: 'Flexible return policies and dedicated technical support teams.' }
                            ]" :key="i" class="flex gap-4">
                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="pi pi-check text-[10px] text-blue-600 font-bold"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#0a2540]">{{ feat.t }}</h4>
                                    <p class="text-gray-500 text-sm">{{ feat.d }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Visual Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-600 rounded-[32px] p-8 aspect-square flex flex-col justify-end text-white relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-8 transform group-hover:rotate-12 transition-transform">
                                <i class="pi pi-shield text-6xl opacity-20"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Secure</h3>
                            <p class="text-blue-100 text-sm">Enterprise security</p>
                        </div>
                        <div class="bg-gray-100 rounded-[32px] p-8 aspect-square flex flex-col justify-end text-[#0a2540] mt-12">
                            <h3 class="text-2xl font-bold mb-2">Fast</h3>
                            <p class="text-gray-500 text-sm">Optimized delivery</p>
                        </div>
                        <div class="bg-[#f6f9fc] rounded-[32px] p-8 aspect-square flex flex-col justify-end text-[#0a2540] -mt-12">
                            <h3 class="text-2xl font-bold mb-2">Global</h3>
                            <p class="text-gray-500 text-sm">Major brands</p>
                        </div>
                        <div class="bg-black rounded-[32px] p-8 aspect-square flex flex-col justify-end text-white">
                            <h3 class="text-2xl font-bold mb-2">Support</h3>
                            <p class="text-gray-400 text-sm">24/7 Assistance</p>
                        </div>
                    </div>
                </div>
            </section>

        <!-- Global Call to Action (Mesh Redesign) -->
        <section class="stripe-mesh-dark py-32 relative overflow-hidden">
            <div class="container mx-auto px-6 relative z-10">
                <div class="max-w-4xl mx-auto text-center">
                    <h2 class="text-4xl lg:text-7xl font-bold text-white mb-8 tracking-tight">Ready to build your <br/><span class="stripe-text-gradient">future setup?</span></h2>
                    <p class="text-xl text-indigo-100/70 mb-12 leading-relaxed">Join over 10,000 satisfied professionals who trust us with their high-performance computing needs. Start your journey today.</p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                        <button @click="goToBrowseProducts" class="stripe-btn stripe-btn-white text-lg py-5 px-12 w-full sm:w-auto">
                            Explore Catalog
                        </button>
                        <a href="https://www.facebook.com/alcesslaptopstore" target="_blank" class="stripe-btn bg-white/10 text-white border border-white/20 text-lg py-5 px-12 w-full sm:w-auto hover:bg-white/20">
                            Speak to Expert
                        </a>
                    </div>
                    
                    <div class="mt-20 grid grid-cols-3 gap-8 border-t border-white/10 pt-12 max-w-2xl mx-auto">
                        <div v-for="stat in [{v:'1k+', l:'Happy Users'}, {v:'500+', l:'Products'}, {v:'24h', l:'Response'}]" :key="stat.l">
                            <p class="text-3xl font-bold text-white mb-1">{{ stat.v }}</p>
                            <p class="text-indigo-200/50 text-sm uppercase tracking-widest font-bold">{{ stat.l }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dynamic Subscription & Footer -->
        <footer class="bg-white pt-32 pb-12 border-t border-gray-100">
            <div class="container mx-auto px-6">
                <!-- Newsletter -->
                <div class="stripe-card p-8 lg:p-12 bg-[#0a2540] rounded-[32px] mb-24 flex flex-col lg:flex-row items-center justify-between gap-12">
                    <div class="max-w-md">
                        <h3 class="text-3xl font-bold text-white mb-4">Stay ahead of the curve.</h3>
                        <p class="text-blue-200/60 leading-relaxed">Sign up for our newsletter to receive exclusive tech reviews, pre-order alerts, and special price drops.</p>
                    </div>
                    <form @submit.prevent class="relative w-full lg:max-w-md bg-white/5 p-2 rounded-2xl flex border border-white/10">
                        <input type="email" placeholder="email@address.com" class="bg-transparent text-white px-4 py-3 flex-1 outline-none" />
                        <button class="bg-white text-[#0a2540] px-8 py-3 rounded-xl font-bold hover:bg-blue-300 transition-colors">
                            Join Now
                        </button>
                    </form>
                </div>

                <!-- Main Footer Links -->
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-12 mb-20">
                    <div class="col-span-2">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                                <img :src="Icon" class="w-6 h-6" alt="Logo" />
                            </div>
                            <h1 class="font-bold text-2xl text-[#0a2540]">{{ appName }}</h1>
                        </div>
                        <p class="text-gray-500 max-w-sm mb-8 leading-relaxed">Premium technology solutions for modern professionals. We deliver excellence in every hardware component.</p>
                        <div class="flex gap-4">
                            <a v-for="s in ['facebook', 'twitter', 'instagram']" :key="s" href="#" class="w-10 h-10 rounded-full border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:border-blue-100 transition-all">
                                <i :class="`pi pi-${s}`"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-bold text-[#0a2540] mb-8">Ecosystem</h4>
                        <ul class="space-y-4 text-gray-500 text-sm font-medium">
                            <li v-for="l in ['Computers', 'Laptops', 'Smartphones', 'Graphic Cards']" :key="l">
                                <a href="#" class="hover:text-blue-600 transition-colors">{{ l }}</a>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-bold text-[#0a2540] mb-8">Community</h4>
                        <ul class="space-y-4 text-gray-500 text-sm font-medium">
                            <li v-for="l in ['About Us', 'Contact', 'Track Order']" :key="l">
                                <button @click="l === 'Track Order' ? handleTrackOrder() : openFooterModal(l.toLowerCase().replace(' ', ''))" class="hover:text-blue-600 transition-colors">{{ l }}</button>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-bold text-[#0a2540] mb-8">Legal</h4>
                        <ul class="space-y-4 text-gray-500 text-sm font-medium">
                            <li v-for="l in ['Privacy', 'Terms', 'Shipping', 'Returns']" :key="l">
                                <button @click="openFooterModal(l.toLowerCase())" class="hover:text-blue-600 transition-colors">{{ l }} Policy</button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center gap-6">
                    <p class="text-gray-400 text-sm">© {{ new Date().getFullYear() }} {{ appName }}. Powered by excellence.</p>
                    <div class="flex items-center gap-6 text-gray-300">
                        <i class="pi pi-credit-card text-lg"></i>
                        <i class="pi pi-shield text-lg"></i>
                        <i class="pi pi-lock text-lg"></i>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Auth and Informational Modals (Logic Retained) -->
        <Dialog v-model:visible="loginFormVisible" modal header="Login" :style="{ width: '28rem' }" :breakpoints="{ '1199px': '75vw' }" :dismissableMask="true" pt:header:class="bg-blue-600! text-white! rounded-t-3xl! rounded-b-none!">
            <LoginForm @success="loginFormVisible = false">
                <template #footer>
                    <div class="flex flex-col p-4 border-t border-gray-50 mt-4 rounded-b-3xl">
                        <div class="flex justify-between items-center mb-4">
                            <Button type="button" variant="link" label="Forgot password?" class="p-0! text-gray-500! text-sm!" @click="openResetPasswordForm()" />
                            <div class="text-right">
                                <span class="text-xs text-gray-400 block">No account?</span>
                                <Button type="button" variant="link" label="Register Now" class="p-0! text-sm! text-blue-600!" @click="openRegisterForm()" />
                            </div>
                        </div>
                        <div class="text-center text-xs text-gray-400">
                            Administrator? <RouterLink :to="{ name: 'admin.login' }" class="text-blue-600 hover:underline">Login here</RouterLink>
                        </div>
                    </div>
                </template>
            </LoginForm>
        </Dialog>

        <Dialog v-model:visible="registerFormVisible" modal header="Register" :style="{ width: '32rem' }" :dismissableMask="true" pt:header:class="bg-blue-600! text-white! rounded-t-3xl!">
            <RegisterForm>
                <template #footer>
                    <div class="p-4 border-t border-gray-50 text-center">
                        <span class="text-gray-400 text-sm">Already have an account?</span>
                        <Button type="button" variant="link" label="Login" class="p-0! ml-2 font-bold!" @click="openLoginForm()" />
                    </div>
                </template>
            </RegisterForm>
        </Dialog>

        <Dialog v-model:visible="resetPasswordFormVisible" modal header="Reset Password" :style="{ width: '28rem' }" :dismissableMask="true" pt:header:class="bg-blue-600! text-white! rounded-t-3xl!">
            <ResetPasswordForm @code-sent="handleCodeSent" />
        </Dialog>

        <Dialog v-model:visible="verifyCodeFormVisible" modal header="Verify Identity" :style="{ width: '28rem' }" :dismissableMask="true" pt:header:class="bg-blue-600! text-white! rounded-t-3xl!">
            <VerifyCodeForm :email="resetEmail" :current-password="resetCurrentPassword" :new-password="resetNewPassword" :new-password-confirmation="resetNewPasswordConfirmation" @success="handleResetSuccess" />
        </Dialog>

        <Dialog v-model:visible="footerModalVisible" modal :header="footerModalTitle" :style="{ width: '50rem' }" :breakpoints="{ '1199px': '90vw' }" :dismissableMask="true" pt:header:class="bg-blue-600! text-white! rounded-t-3xl!">
            <div class="p-8">
                <div v-if="footerModalType === 'about'" class="space-y-6">
                    <p class="text-lg text-[#0a2540] font-bold leading-tight">Empowering your digital world since 2018.</p>
                    <p class="text-gray-600 leading-relaxed">{{ appName }} started as a small boutique for high-end laptops. Today, we supply thousands of professionals with the tools they need to succeed. Our focus remains on quality over quantity.</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-6 bg-blue-50 rounded-2xl">
                            <p class="text-3xl font-bold text-blue-600 mb-1">100%</p>
                            <p class="text-xs text-blue-400 font-bold uppercase tracking-wider">Original</p>
                        </div>
                        <div class="p-6 bg-green-50 rounded-2xl">
                            <p class="text-3xl font-bold text-green-600 mb-1">Local</p>
                            <p class="text-xs text-green-400 font-bold uppercase tracking-wider">Warranty</p>
                        </div>
                    </div>
                </div>
                <div v-if="footerModalType === 'contact'" class="space-y-8">
                    <div v-for="c in [{i:'pi-facebook', t:'Facebook', v:'alcesslaptopstore', l:'https://facebook.com/alcesslaptopstore'}, {i:'pi-envelope', t:'Email', v:'support@alcess.com', l:'mailto:support@alcess.com'}]" :key="c.t" class="flex items-center gap-6 p-6 bg-gray-50 rounded-2xl hover:bg-blue-50 transition-colors">
                        <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-blue-600">
                            <i :class="`pi ${c.i}`"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase">{{ c.t }}</p>
                            <a :href="c.l" target="_blank" class="text-lg font-bold text-[#0a2540] hover:text-blue-600">{{ c.v }}</a>
                        </div>
                    </div>
                </div>
                <div v-if="footerModalType === 'faqs'" class="space-y-4">
                    <div v-for="q in [{q:'Shipping?', a:'3-7 business days nationwide.'}, {q:'Original?', a:'Yes, all items are 100% genuine with seals.'}, {q:'Payment?', a:'Bank transfer, GCash, and COD available.'}]" :key="q.q" class="p-6 border border-gray-100 rounded-2xl">
                        <p class="font-bold text-[#0a2540] mb-2">{{ q.q }}</p>
                        <p class="text-gray-500">{{ q.a }}</p>
                    </div>
                </div>
                <!-- Other content (Privacy, Terms, Shipping, Returns) displayed generically -->
                <div v-if="['privacy', 'terms', 'shipping', 'returns'].includes(footerModalType)" class="prose prose-blue prose-sm max-w-none">
                    <p class="text-gray-600 leading-relaxed">Please refer to our standard operational procedures for {{ footerModalType }}. We handle all processed data and returns with maximum transparency and focus on user satisfaction in accordance with local laws.</p>
                </div>
            </div>
            <template #footer>
                <div class="p-4 flex justify-end">
                    <Button label="Clear" class="bg-gray-100! text-gray-600! border-none! px-8!" @click="footerModalVisible = false" />
                </div>
            </template>
        </Dialog>
    </div>
</template>
<script setup lang="ts">
import Icon from "@/../img/logo.png";
import LoginForm from "@/components/forms/LoginForm.vue";
import RegisterForm from "@/components/forms/RegisterForm.vue";
import ResetPasswordForm from "@/components/forms/ResetPasswordForm.vue";
import VerifyCodeForm from "@/components/forms/VerifyCodeForm.vue";
import {
    SearchErrorInterface,
    SearchProductInterface,
} from "@/interfaces/SearchProductInterface";
import Page from "@/stores/Page";
import { computed, onMounted, onUnmounted, reactive, ref, watch } from "vue";
import { useResponsive } from "@/composables/useResponsive";
import HeroBg from "@/../img/hero-bg.jpg";
import { useCategoryStore } from "@/stores/CategoryState";
import UrlUtil from "@/utils/UrlUtil";
import useAxiosUtil from "@/utils/AxiosUtil";
import { ProductInterface } from "@/interfaces/ProductInterface";
import CurrencyUtil from "@/utils/CurrencyUtil";
import { useRouter } from "vue-router";
import { CartFormInterface } from "@/interfaces/CartInterface";

const { isMobile, isTablet, isDesktop } = useResponsive();
const appName = import.meta.env.VITE_APP_NAME;
const loginFormVisible = ref<boolean>(false);
const registerFormVisible = ref<boolean>(false);
const resetPasswordFormVisible = ref<boolean>(false);
const verifyCodeFormVisible = ref<boolean>(false);
const resetEmail = ref<string | null>(null);
const resetCurrentPassword = ref<string | null>(null);
const resetNewPassword = ref<string | null>(null);
const resetNewPasswordConfirmation = ref<string | null>(null);
const CategoryStore = useCategoryStore();
const loadBestSellingService = useAxiosUtil<null, ProductInterface[]>();
const addToCartService = useAxiosUtil<CartFormInterface, null>();
const products = ref<ProductInterface[]>([]);
const currentSlide = ref<number>(0);
let carouselInterval: number | null = null;
const activeView = ref<'categories' | 'products'>('categories');

// Footer modal state
const footerModalVisible = ref<boolean>(false);
const footerModalType = ref<string>('about');
const footerModalTitle = computed(() => {
    const titles: Record<string, string> = {
        'about': 'About Us',
        'contact': 'Contact Us',
        'faqs': 'Frequently Asked Questions',
        'shipping': 'Shipping Information',
        'returns': 'Returns & Warranty',
        'privacy': 'Privacy Policy',
        'terms': 'Terms & Conditions'
    };
    return titles[footerModalType.value] || 'Information';
});

const openFooterModal = (type: string) => {
    footerModalType.value = type;
    footerModalVisible.value = true;
};

const handleTrackOrder = () => {
    if (!Page.user) {
        openLoginForm();
        return;
    }
    // Navigate to customer orders page
    router.push({ name: 'customer.order.index' });
};

// Computed property to dynamically change the category heading
const categoriesHeading = computed(() => {
    if (CategoryStore.categories && CategoryStore.categories.length > 0) {
        // Check if categories have the is_popular flag (must be explicitly true)
        const hasPopularCategories = CategoryStore.categories.some((cat: any) => cat.is_popular === true || cat.is_popular === 1);
        return hasPopularCategories ? 'Browse Popular Categories' : 'Browse Categories';
    }
    return 'Browse Categories';
});

// Computed property to dynamically change the products heading
const productsHeading = computed(() => {
    if (products.value && products.value.length > 0) {
        // Check if products have the is_best_selling flag (must be explicitly true)
        const hasBestSellingProducts = products.value.some((prod: any) => prod.is_best_selling === true || prod.is_best_selling === 1);
        return hasBestSellingProducts ? 'Best Selling Products' : 'Current Products';
    }
    return 'Current Products';
});

// Computed property to check if we should show carousel (only for best selling products)
const showCarousel = computed(() => {
    if (products.value && products.value.length > 0) {
        // Only show carousel if products are truly best selling
        return products.value.some((prod: any) => prod.is_best_selling === true || prod.is_best_selling === 1);
    }
    return false;
});

const form: SearchProductInterface = reactive({
    search: null,
});
const errors: SearchErrorInterface = reactive({
    search: [],
});

// Check if current route is a product-related page or home page
const isProductPage = computed(() => {
    const productRoutes = [
        'customer.product-search',
        'customer.product-info.index',
        'customer.product-category',
        'customer.browse-products',
        'customer.home.index',
        'customer.cart',
        'home'
    ];
    return productRoutes.includes(router.currentRoute.value.name as string);
});

const goToBrowseProducts = () => {
    router.push({ name: "customer.browse-products" });
};

const openLoginForm = () => {
    registerFormVisible.value = false;
    resetPasswordFormVisible.value = false;
    loginFormVisible.value = true;
};

const openRegisterForm = () => {
    loginFormVisible.value = false;
    resetPasswordFormVisible.value = false;
    registerFormVisible.value = true;
};

const openResetPasswordForm = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
    resetPasswordFormVisible.value = true;
};

const handleSearch = () => {
    if (form.search && form.search.trim()) {
        router.push({
            name: 'customer.search-product',
            query: { q: form.search }
        });
    } else if (form.search === '' || form.search === null) {
        // If search is cleared, navigate to search page without query
        if (router.currentRoute.value.name === 'customer.search-product') {
            router.push({
                name: 'customer.search-product'
            });
        }
    }
};

// Watch for search input changes with debounce
let searchTimeout: number | null = null;
watch(() => form.search, (newValue, oldValue) => {
    // Only trigger if on search page
    if (router.currentRoute.value.name === 'customer.search-product') {
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        searchTimeout = window.setTimeout(() => {
            if (newValue && newValue.trim()) {
                router.push({
                    name: 'customer.search-product',
                    query: { q: newValue }
                });
            } else if (newValue === '' || newValue === null) {
                // Clear search results when input is empty
                router.push({
                    name: 'customer.search-product'
                });
            }
        }, 500); // 500ms debounce
    }
});

const handleCodeSent = (data: { email: string; current_password: string; new_password: string; new_password_confirmation: string }) => {
    resetEmail.value = data.email;
    resetCurrentPassword.value = data.current_password;
    resetNewPassword.value = data.new_password;
    resetNewPasswordConfirmation.value = data.new_password_confirmation;
    resetPasswordFormVisible.value = false;
    verifyCodeFormVisible.value = true;
};

const handleResetSuccess = () => {
    verifyCodeFormVisible.value = false;
    resetPasswordFormVisible.value = false;
    loginFormVisible.value = true;
};

const loadBestSellingProducts = async () => {
    await loadBestSellingService.get("best-selling").then(() => {
        if (
            loadBestSellingService.request.status === 200 &&
            loadBestSellingService.request.data
        ) {
            products.value = loadBestSellingService.request.data;
            startCarousel();
        }
    });
};

const startCarousel = () => {
    const carouselProducts = Math.min(products.value.length, 3);
    if (carouselProducts > 1) {
        carouselInterval = window.setInterval(() => {
            currentSlide.value = (currentSlide.value + 1) % carouselProducts;
        }, 5000);
    }
};

const stopCarousel = () => {
    if (carouselInterval) {
        clearInterval(carouselInterval);
        carouselInterval = null;
    }
};

const router = useRouter();

const goRoute = (route: string, params: Record<string, string>) => {
    router.push({ name: route, params: params });
};

const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    target.style.display = 'none';
    const fallback = target.nextElementSibling || target.parentElement?.querySelector('.fallback-image');
    if (fallback) {
        (fallback as HTMLElement).style.display = 'flex';
    }
};

const addToCart = async (productId: number) => {
    if (!Page.user) {
        openLoginForm();
        return;
    }

    const cartData: CartFormInterface = {
        product_id: productId,
        quantity: 1
    };

    await addToCartService.post('customer/carts', cartData).then(() => {
        if (addToCartService.request.status === 200) {
            // Product added successfully - cart count will update via websocket
        }
    }).catch(() => {
        // Error adding to cart
        console.error('Failed to add product to cart:', addToCartService.request.message);
    });
};

const buyNow = (productId: number) => {
    if (!Page.user) {
        openLoginForm();
        return;
    }
    
    // Navigate to product page if logged in
    router.push({
        name: 'customer.product-info.index',
        params: { id: productId }
    });
};

const goToProductDetails = (productId: number) => {
    router.push({
        name: 'customer.product-info.index',
        params: { id: productId }
    });
};

const isScrolled = ref(false);
const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
    CategoryStore.fetchCategories();
    loadBestSellingProducts();
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    stopCarousel();
    window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 1s ease-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Hero Carousel Transitions */
.hero-slide-enter-active {
    transition: all 0.8s ease;
}

.hero-slide-leave-active {
    transition: all 0.8s ease;
}

.hero-slide-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.hero-slide-leave-to {
    opacity: 0;
    transform: translateX(-100%);
}

/* Blue scrollbar styling */
.category-scroll::-webkit-scrollbar {
    height: 8px;
}

.category-scroll::-webkit-scrollbar-track {
    background: #e5e7eb;
    border-radius: 10px;
}

.category-scroll::-webkit-scrollbar-thumb {
    background: #2563eb;
    border-radius: 10px;
}

.category-scroll::-webkit-scrollbar-thumb:hover {
    background: #1d4ed8;
}
</style>