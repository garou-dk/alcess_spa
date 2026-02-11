<template>
    <div class="profile-view min-h-screen bg-gradient-to-b from-slate-50 via-slate-50/80 to-white pb-20">
        <!-- Hero Section with Cover Image -->
        <div class="relative group h-64 md:h-80 w-full overflow-hidden bg-slate-900">
            <img 
                v-if="Page.user?.cover_image" 
                :src="UrlUtil.getBaseAppUrl(`storage/images/cover/${Page.user.cover_image}`)" 
                class="w-full h-full object-cover opacity-90 transition-transform duration-700 group-hover:scale-105"
            >
            <!-- Fallback -->
            <div v-else class="w-full h-full flex items-center justify-center">
                <img :src="Logo" alt="logo" class="w-24 h-24 opacity-10 filter invert" />
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-transparent"></div>

            <div class="absolute inset-x-0 bottom-0 pb-4">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-end justify-between gap-4">
                    <div>
                        <h1 class="text-lg md:text-xl font-semibold text-white/90">My Profile</h1>
                        <p class="text-xs md:text-sm text-slate-200/80">Manage your personal information, address, and security settings.</p>
                    </div>
            <button 
                @click="triggerCoverUpload" 
                class="absolute top-6 right-6 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl shadow-lg border border-blue-500/50 flex items-center gap-2 text-xs font-bold uppercase tracking-wider transition-all active:scale-95 z-20"
            >
                <i class="pi pi-camera text-lg"></i>
                <span class="hidden sm:inline">Upload Cover</span>
            </button>
            <input type="file" ref="coverInput" class="hidden" @change="onCoverChange" accept="image/*">
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Column: User Profile Card -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Main Profile Card -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 relative">
                        <div class="flex flex-col items-center text-center -mt-12 mb-6">
                            <div class="relative group cursor-pointer" @click="triggerAvatarUpload">
                                <div class="w-36 h-36 md:w-44 md:h-44 rounded-full border-[6px] border-white shadow-2xl overflow-hidden bg-white flex items-center justify-center relative z-10">
                                    <img 
                                        v-if="Page.user?.image" 
                                        :src="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.image}`)" 
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    >
                                    <span v-else class="text-6xl font-black text-slate-200">{{ Page.user?.full_name?.charAt(0) }}</span>
                                </div>
                                <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity z-20 rounded-full border-[6px] border-transparent">
                                    <i class="pi pi-camera text-white text-2xl"></i>
                                </div>
                                <button 
                                    class="absolute bottom-2 right-2 z-30 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all hover:scale-110 active:scale-95 border-[3px] border-white"
                                >
                                    <i class="pi pi-pencil text-sm"></i>
                                </button>
                                <input type="file" ref="avatarInput" class="hidden" @change="onAvatarChange" accept="image/*">
                            </div>

                            <div class="mt-4">
                                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">{{ Page.user?.full_name }}</h1>
                                <p class="text-slate-500 font-medium text-sm mt-1 flex items-center justify-center gap-1.5">
                                    <i class="pi pi-envelope text-slate-400 text-xs"></i>
                                    {{ Page.user?.email }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-colors hover:bg-slate-100/80">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                        <i class="pi pi-id-card"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Role</span>
                                </div>
                                <span class="text-sm font-bold text-slate-900 bg-white px-3 py-1 rounded-full shadow-sm border border-slate-100">{{ Page.user?.role?.role_name }}</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-colors hover:bg-slate-100/80">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                                        <i class="pi pi-verified"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                                    <i class="pi pi-check-circle text-[10px] font-bold"></i>
                                    <span class="text-xs font-bold uppercase">Verified</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-colors hover:bg-slate-100/80">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                                        <i class="pi pi-calendar"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Joined</span>
                                </div>
                                <span class="text-sm font-bold text-slate-700">{{ Page.user?.created_at ? DateUtil.formatToMonthDayYear(Page.user.created_at) : '--' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Security Summary (neutral) -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="pi pi-lock text-blue-500 text-xs"></i>
                            <span>Account security</span>
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-slate-600">Password</p>
                                    <p class="text-xs text-slate-500">Use a strong, unique password.</p>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-2.5 py-1 text-[10px] font-semibold text-slate-600 uppercase tracking-wide">
                                    <i class="pi pi-check-circle text-green-500 text-[10px]"></i>
                                    OK
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-slate-600">Recovery question</p>
                                    <p class="text-xs text-slate-500">Helps you recover access if you forget your password.</p>
                                </div>
                                <span
                                    :class="Page.user?.security_question ? 'text-green-600 bg-green-50 border-green-100' : 'text-amber-600 bg-amber-50 border-amber-100'"
                                    class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide"
                                >
                                    <i :class="Page.user?.security_question ? 'pi pi-check-circle' : 'pi pi-exclamation-circle'" class="text-[10px]"></i>
                                    {{ Page.user?.security_question ? 'Configured' : 'Recommended' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Settings & Forms -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- 0. Order Timeline -->
                    <div class="bg-white rounded-3xl p-6 md:p-7 shadow-sm border border-slate-100 order-section">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-blue-50 text-blue-500 mr-1">
                                        <i class="pi pi-shopping-bag text-sm"></i>
                                    </span>
                                    Order History
                                </h2>
                                <p class="text-slate-500 text-sm mt-1">Track the status of your recent purchases.</p>
                            </div>
                            <span v-if="recentOrders.length > 0" class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-full">
                                {{ recentOrders.length }} order{{ recentOrders.length !== 1 ? 's' : '' }}
                            </span>
                        </div>

                        <!-- Order Status Tabs -->
                        <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2 scrollbar-hide scroll-smooth snap-x snap-mandatory">
                            <button 
                                v-for="tab in orderTabs" 
                                :key="tab"
                                @click="activeTab = tab"
                                class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wide whitespace-nowrap transition-all duration-300 border snap-start"
                                :class="activeTab === tab ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-200 scale-105' : 'bg-white text-slate-500 border-slate-200 hover:border-blue-300 hover:text-blue-600 hover:shadow-sm'"
                            >
                                {{ tab }}
                                <span v-if="getTabCount(tab) > 0" class="ml-1 inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full text-[10px] font-bold" :class="activeTab === tab ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'">
                                    {{ getTabCount(tab) }}
                                </span>
                            </button>
                        </div>

                        <!-- Loading Skeleton -->
                        <div v-if="loadingOrders" class="space-y-6">
                            <div v-for="n in 3" :key="n" class="animate-pulse flex gap-6">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex-shrink-0"></div>
                                <div class="flex-1 bg-slate-100 rounded-2xl p-5 space-y-3">
                                    <div class="flex justify-between">
                                        <div class="space-y-2">
                                            <div class="h-3 w-20 bg-slate-200 rounded"></div>
                                            <div class="h-4 w-36 bg-slate-200 rounded"></div>
                                        </div>
                                        <div class="h-5 w-16 bg-slate-200 rounded-full"></div>
                                    </div>
                                    <div class="flex gap-2">
                                        <div v-for="i in 3" :key="i" class="w-16 h-16 bg-slate-200 rounded-lg"></div>
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                                        <div class="h-5 w-24 bg-slate-200 rounded"></div>
                                        <div class="h-8 w-24 bg-slate-200 rounded-xl"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="filteredOrders.length > 0" class="relative pl-4 space-y-8 before:absolute before:inset-0 before:ml-4 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
                            <div v-for="(order, index) in filteredOrders" :key="order.order_id" class="relative flex gap-6 group order-card" :style="{ animationDelay: `${index * 80}ms` }">
                                <!-- Dot -->
                                <div class="absolute -left-4 mt-1.5 w-8 h-8 rounded-full border-4 border-white shadow-sm flex items-center justify-center z-10" :class="getStatusTimelineColor(order.status)">
                                    <div class="w-2.5 h-2.5 rounded-full bg-white opacity-90"></div>
                                </div>
                                
                                <div class="flex-1 bg-white rounded-2xl p-5 border border-slate-100 hover:border-blue-100 hover:shadow-md hover:shadow-blue-50 transition-all duration-300">
                                    <div class="flex flex-wrap justify-between items-start gap-4 mb-3">
                                        <div>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">
                                                {{ DateUtil.formatToMonthDayYear(order.created_at) }}
                                            </span>
                                            <h4 class="text-base font-bold text-slate-800">Order #{{ String(order.order_id || '').substring(0, 8) }}</h4>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border" :class="getStatusBadgeClass(order.status)">
                                            {{ order.status }}
                                        </span>
                                    </div>

                                    <!-- Product Images -->
                                    <div class="flex gap-2 overflow-x-auto pb-3 mb-2 scrollbar-hide">
                                        <div v-for="item in order.product_orders" :key="item.product_id" class="w-16 h-16 rounded-lg border border-slate-200 bg-slate-50 flex-shrink-0 overflow-hidden relative group/img">
                                            <img :src="UrlUtil.getBaseAppUrl(`storage/images/product/${item.product.product_image}`)" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/5 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition-opacity"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                        <span class="font-bold text-slate-900">₱{{ (Number(order.total_amount) || 0).toLocaleString() }}</span>
                                        <Button 
                                            @click="buyAgain(order)" 
                                            label="Buy again" 
                                            icon="pi pi-refresh" 
                                            class="!bg-blue-600 hover:!bg-blue-700 text-white border-0 font-bold rounded-xl text-xs px-4 py-2" 
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div v-else class="text-center py-14">
                            <div class="w-20 h-20 bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-slate-100 empty-icon-float">
                                <i class="pi pi-shopping-bag text-3xl text-slate-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900">No Orders Found</h3>
                            <p class="text-slate-500 text-sm mt-1.5 mb-8 max-w-xs mx-auto">{{ activeTab === 'All' ? 'You haven\'t placed any orders yet. Start shopping!' : `No ${activeTab.toLowerCase()} orders at the moment.` }}</p>
                            <router-link :to="{ name: 'customer.browse-products' }">
                                <Button 
                                    label="Browse products" 
                                    icon="pi pi-shopping-cart" 
                                    class="!bg-blue-600 hover:!bg-blue-700 text-white border-0 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all hover:shadow-xl hover:shadow-blue-500/30" 
                                />
                            </router-link>
                        </div>
                    </div>
                    
                    <!-- 1. Contact & Address -->
                    <div class="bg-white rounded-3xl p-6 md:p-7 shadow-sm border border-slate-100">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                            <div>
                                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                    <i class="pi pi-map-marker text-slate-400 text-sm"></i>
                                    Delivery Address
                                </h2>
                                <p class="text-slate-500 text-sm mt-1">Manage your shipping details for faster checkout.</p>
                            </div>
                            <Button 
                                @click="openAddressForm" 
                                label="Edit address" 
                                icon="pi pi-pencil" 
                                class="!bg-blue-600 hover:!bg-blue-700 text-white border-0 rounded-xl font-bold" 
                            />
                        </div>

                        <div class="bg-slate-50/60 rounded-2xl p-6 border border-slate-100">
                             <div v-if="Page.user?.address" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-5">
                                    <div>
                                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Contact Number</label>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-600">
                                                <i class="pi pi-phone"></i>
                                            </div>
                                            <span class="text-lg font-bold text-slate-800">{{ Page.user.address.contact_number || 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Postal Code</label>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-600">
                                                <i class="pi pi-envelope"></i>
                                            </div>
                                            <span class="text-lg font-bold text-slate-800">{{ Page.user.address.postal_code || 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Full Address</label>
                                    <div class="bg-white rounded-xl p-4 border border-slate-200 h-full">
                                        <div class="flex gap-3">
                                            <i class="pi pi-home text-blue-500 mt-1"></i>
                                            <div>
                                                <p class="text-sm font-bold text-slate-800 leading-relaxed">{{ Page.user.address.other_details }}</p>
                                                <div v-if="Page.user.address.barangay" class="mt-3 pt-3 border-t border-slate-100">
                                                     <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">
                                                        {{ Page.user.address.barangay.barangay_name }}, 
                                                        {{ Page.user.address.barangay.municity?.municity_name }}
                                                    </p>
                                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-0.5">
                                                        {{ Page.user.address.barangay.municity?.province?.province_name }}
                                                    </p>
                                                    <div v-if="Page.user.address.barangay?.municity?.province?.region" class="mt-2 inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-slate-100 text-[10px] font-semibold text-slate-600 uppercase">
                                                        <i class="pi pi-map text-[10px]"></i>
                                                        {{ Page.user.address.barangay.municity.province.region.region_name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-else class="text-center py-10 bg-white rounded-2xl">
                                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-500">
                                    <i class="pi pi-map-marker text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900">No Address Set</h3>
                                <p class="text-slate-500 text-sm mb-6 max-w-sm mx-auto">Add your delivery address to make checkout faster.</p>
                                <Button @click="openAddressForm" label="Add address" icon="pi pi-plus" class="!bg-blue-600 hover:!bg-blue-700 text-white border-0 rounded-xl font-bold shadow-lg shadow-blue-500/30" />
                            </div>
                        </div>
                    </div>

                    <!-- 2. Personal Information -->
                    <div class="bg-white rounded-3xl p-6 md:p-7 shadow-sm border border-slate-100">
                        <div class="mb-8 border-b border-slate-100 pb-4">
                            <h2 class="text-xl font-bold text-slate-900">Personal Details</h2>
                            <p class="text-slate-500 text-sm font-medium mt-1">Update how your name appears on your profile.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Full Name</label>
                                <div class="relative group">
                                    <input v-model="profileForm.full_name" type="text" class="form-input pl-4 pr-10" placeholder="Enter your full name">
                                    <i class="pi pi-user absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Email (Read Only)</label>
                                <div class="relative">
                                    <input :value="Page.user?.email" type="email" readonly class="form-input bg-slate-50 text-slate-500 cursor-not-allowed border-slate-200" placeholder="email@example.com">
                                    <i class="pi pi-lock absolute right-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <Button @click="updateName" :loading="savingName" label="Save changes" icon="pi pi-check" class="!bg-blue-600 hover:!bg-blue-700 text-white border-0 rounded-xl font-bold w-full md:w-auto px-6" />
                        </div>
                    </div>

                    <!-- 3. Security Settings -->
                    <div class="space-y-4">
                        <!-- Password Update -->
                        <div class="bg-white rounded-3xl p-6 md:p-7 shadow-sm border border-slate-100">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                        <i class="pi pi-key text-slate-400 text-sm"></i>
                                        Change password
                                    </h2>
                                    <p class="text-slate-500 text-xs mt-1">Update your password regularly to keep your account secure.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                                <div class="md:col-span-4">
                                     <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1 mb-2 block">Current Password</label>
                                     <Password v-model="passwordForm.current_password" toggleMask fluid :feedback="false" placeholder="Required" pt:input:class="form-input" />
                                </div>
                                <div class="md:col-span-4">
                                     <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1 mb-2 block">New Password</label>
                                     <Password v-model="passwordForm.password" toggleMask fluid placeholder="Min. 8 chars" pt:input:class="form-input" />
                                </div>
                                <div class="md:col-span-4">
                                     <label class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1 mb-2 block">Confirm</label>
                                     <Password v-model="passwordForm.password_confirmation" toggleMask fluid :feedback="false" placeholder="Repeat" pt:input:class="form-input" />
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end">
                                <Button @click="updatePassword" :loading="savingPassword" label="Update password" icon="pi pi-lock" class="!bg-blue-600 hover:!bg-blue-700 text-white border-0 rounded-xl font-bold w-full md:w-auto px-6" />
                            </div>
                        </div>

                         <!-- Recovery Options -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <div class="bg-white rounded-3xl p-6 border border-slate-100 hover:bg-slate-50 transition-colors group">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 group-hover:scale-110 transition-transform">
                                        <i class="pi pi-question-circle"></i>
                                    </div>
                                    <span :class="Page.user?.security_question ? 'text-emerald-700 bg-emerald-50 border border-emerald-100' : 'text-amber-700 bg-amber-50 border border-amber-100'" class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                        {{ Page.user?.security_question ? 'Configured' : 'Recommended' }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-base mb-1">Security Question</h4>
                                <p class="text-slate-600 text-xs font-medium leading-relaxed mb-6 h-10">
                                    {{ Page.user?.security_question ? 'Your custom question is set. Click to update.' : 'Set a question for account recovery.' }}
                                </p>
                                <Button @click="showQuestionDialog = true" :label="Page.user?.security_question ? 'Update Question' : 'Set Up Now'" class="w-full rounded-xl font-bold" :outlined="!!Page.user?.security_question" />
                             </div>

                             <div class="bg-white rounded-3xl p-6 border border-slate-100 hover:bg-slate-50 transition-colors group">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 group-hover:scale-110 transition-transform">
                                        <i class="pi pi-shield"></i>
                                    </div>
                                    <span v-if="recoveryCodes.length > 0" class="text-emerald-700 bg-emerald-50 border border-emerald-100 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Active</span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-base mb-1">Backup Codes</h4>
                                <p class="text-slate-600 text-xs font-medium leading-relaxed mb-6 h-10">
                                    Generate one-time codes in case you lose access to your email.
                                </p>
                                <Button @click="showCodesDialog = true" label="Manage Codes" class="!bg-blue-600 hover:!bg-blue-700 text-white border-0 rounded-xl font-bold w-full" />
                             </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Dialogs (Styled consistently) -->
        <Dialog v-model:visible="showQuestionDialog" modal header="Set Security Question" :style="{ width: '30rem' }" :breakpoints="{ '575px': '90vw' }" pt:root:class="rounded-3xl shadow-2xl border-0" pt:header:class="p-6 border-b border-gray-100 bg-gray-50/50 rounded-t-3xl" pt:content:class="p-6" pt:footer:class="p-6 border-t border-gray-100 rounded-b-3xl">
            <div class="space-y-5">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Your Question</label>
                    <input v-model="questionForm.customQuestion" type="text" class="form-input" placeholder="e.g. What was your first pet's name?">
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Your Answer</label>
                    <Password v-model="questionForm.answer" toggleMask fluid :feedback="false" placeholder="Enter secure answer" pt:input:class="form-input" />
                    <p class="text-[10px] text-slate-400 font-medium">Answers are case-insensitive and encrypted.</p>
                </div>
            </div>
            <template #footer>
                <div class="flex gap-3">
                    <Button @click="showQuestionDialog = false" label="Cancel" text severity="secondary" class="rounded-xl font-bold" />
                    <Button @click="saveQuestion" :loading="savingQuestion" label="Save Security Question" class="!bg-blue-600 hover:!bg-blue-700 text-white border-0 rounded-xl font-bold flex-1 shadow-lg shadow-blue-500/20" />
                </div>
            </template>
        </Dialog>

        <Dialog v-model:visible="showCodesDialog" modal header="Recovery Codes" :style="{ width: '36rem' }" :breakpoints="{ '575px': '90vw' }" pt:root:class="rounded-3xl shadow-2xl border-0" pt:header:class="p-6 border-b border-gray-100 bg-gray-50/50 rounded-t-3xl" pt:content:class="p-6" pt:footer:class="p-6 border-t border-gray-100 rounded-b-3xl">
            <div class="space-y-6">
                <div v-if="recoveryCodes.length > 0" class="grid grid-cols-2 gap-3">
                    <div v-for="(code, index) in recoveryCodes" :key="index" class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-xl group hover:border-blue-200 transition-colors">
                        <code class="text-sm font-bold text-slate-700 font-mono tracking-wider">{{ code }}</code>
                        <button @click="copyToClipboard(code)" class="text-slate-400 hover:text-blue-600 transition-colors">
                            <i class="pi pi-copy"></i>
                        </button>
                    </div>
                </div>
                <div v-else class="text-center py-10">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                        <i class="pi pi-lock text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">No Codes Generated</h3>
                    <p class="text-slate-500 text-sm font-medium mt-1 mb-8">Generate codes to start securing your account.</p>
                    <Button @click="generateCodes" :loading="generatingCodes" label="Generate Codes" icon="pi pi-key" class="!bg-blue-600 hover:!bg-blue-700 text-white border-0 rounded-xl font-bold px-8" />
                </div>

                <div v-if="recoveryCodes.length > 0" class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex gap-3">
                    <i class="pi pi-info-circle text-amber-600 text-lg flex-shrink-0 mt-0.5"></i>
                    <p class="text-xs text-amber-800 font-medium leading-relaxed">
                        <strong>Important:</strong> Save these codes in a safe place. Each code can be used once. We recommend downloading them immediately.
                    </p>
                </div>
            </div>
            <template #footer v-if="recoveryCodes.length > 0">
                <div class="flex gap-3 w-full">
                    <Button @click="downloadCodes" label="Download .txt" icon="pi pi-download" severity="secondary" outlined class="rounded-xl font-bold flex-1" />
                    <Button @click="confirmRegenerate" label="Regenerate" icon="pi pi-refresh" severity="danger" class="rounded-xl font-bold flex-1" />
                </div>
            </template>
        </Dialog>

        <Dialog v-model:visible="showCropper" modal :header="cropMode === 'avatar' ? 'Crop Profile Photo' : 'Crop Cover Photo'" :style="{ width: '34rem' }" :breakpoints="{ '575px': '90vw' }" pt:root:class="rounded-3xl shadow-2xl border-0 !p-0" pt:header:class="p-6 border-b border-gray-100 rounded-t-3xl" pt:content:class="!p-0" pt:footer:class="p-6 border-t border-gray-100 rounded-b-3xl">
            <div class="bg-slate-100 flex items-center justify-center py-4" v-if="selectedFile">
                <VuePictureCropper
                    :boxStyle="{ width: '100%', height: '400px', backgroundColor: '#f1f5f9', margin: 'auto' }"
                    :img="selectedFile"
                    :options="{ 
                        viewMode: 1, 
                        dragMode: 'move', 
                        aspectRatio: cropMode === 'avatar' ? 1 : 16/6, 
                        cropBoxResizable: true, 
                        guides: false,
                        background: false
                    }"
                />
            </div>
            <template #footer>
                <div class="flex gap-3">
                    <Button @click="showCropper = false" label="Cancel" text severity="secondary" class="rounded-xl font-bold" />
                    <Button @click="uploadCroppedImage" :loading="uploadingImage" label="Save & Update" icon="pi pi-check" class="!bg-blue-600 hover:!bg-blue-700 text-white border-0 rounded-xl font-bold flex-1 shadow-lg shadow-blue-500/20" />
                </div>
            </template>
        </Dialog>

        <ConfirmDialog pt:root:class="rounded-3xl shadow-2xl border-0 w-96" pt:header:class="hidden" pt:content:class="p-8 text-center" pt:footer:class="p-6 border-t border-gray-50 flex gap-3 justify-center bg-gray-50 rounded-b-3xl">
             <template #message="slotProps">
                <div class="flex flex-col items-center w-full">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-4 text-red-500">
                        <i :class="slotProps.message.icon" class="text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-2">{{ slotProps.message.header }}</h4>
                    <p class="text-slate-500 text-sm leading-relaxed">{{ slotProps.message.message }}</p>
                </div>
            </template>
        </ConfirmDialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import Page from '@/stores/Page'
import UrlUtil from '@/utils/UrlUtil'
import useAxiosUtil from '@/utils/AxiosUtil'
import { useToast } from 'vue-toastification'
import { useConfirm } from 'primevue/useconfirm'
import VuePictureCropper, { cropper } from 'vue-picture-cropper'
import { 
    Button, Dialog, Password, 
    ConfirmDialog
} from 'primevue'
import DateUtil from '@/utils/DateUtil'
import Logo from "@/../img/logo.png"

const toast = useToast()
const confirm = useConfirm()
const profileService = useAxiosUtil()

// State
const orderTabs = ['All', 'Pending', 'Confirmed', 'Processing', 'Shipped', 'Completed', 'Cancelled']
const activeTab = ref('All')

const profileForm = reactive({ full_name: Page.user?.full_name || '' })
const passwordForm = reactive({ current_password: '', password: '', password_confirmation: '' })
const questionForm = reactive({ customQuestion: Page.user?.security_question || '', answer: '' })
const recoveryCodes = ref<string[]>([])

const savingName = ref(false)
const savingPassword = ref(false)
const savingQuestion = ref(false)
const generatingCodes = ref(false)
const uploadingImage = ref(false)

const showQuestionDialog = ref(false)
const showCodesDialog = ref(false)
const showCropper = ref(false)
const cropMode = ref<'avatar' | 'cover'>('avatar')
const selectedFile = ref<string | null>(null)

const avatarInput = ref<HTMLInputElement | null>(null)
const coverInput = ref<HTMLInputElement | null>(null)

// Methods
const triggerAvatarUpload = () => avatarInput.value?.click()
const triggerCoverUpload = () => coverInput.value?.click()

const openAddressForm = () => {
    window.dispatchEvent(new CustomEvent('open-address-form'))
}

const onAvatarChange = (e: Event) => {
    cropMode.value = 'avatar'
    handleFileSelect(e)
}

const onCoverChange = (e: Event) => {
    cropMode.value = 'cover'
    handleFileSelect(e)
}

const handleFileSelect = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (file) {
        const reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onload = () => {
            selectedFile.value = reader.result as string
            showCropper.value = true
        }
    }
}

const uploadCroppedImage = async () => {
    if (!cropper) return
    uploadingImage.value = true
    const blob = await cropper.getBlob()
    if (blob) {
        const file = new File([blob], `cropped-${cropMode.value}.png`, { type: 'image/png' })
        const formData = new FormData()
        formData.append('image', file)
        
        const endpoint = cropMode.value === 'avatar' 
            ? 'customer/profile/image' 
            : 'customer/profile/cover'

        await profileService.patchFormData(endpoint, formData).then(() => {
            uploadingImage.value = false
            if (profileService.request.status === 200) {
                toast.success(`${cropMode.value === 'avatar' ? 'Profile' : 'Cover'} updated successfully.`)
                showCropper.value = false
                if (Page.user) {
                    if (cropMode.value === 'avatar') {
                        Page.user.image = profileService.request.data.image
                    } else {
                        Page.user.cover_image = profileService.request.data.cover_image
                    }
                }
            } else {
                toast.error(profileService.request.message || 'Failed to upload image.')
            }
        })
    }
}

const updateName = async () => {
    if (!profileForm.full_name) return toast.error('Full name is required.')
    savingName.value = true
    await profileService.patch('customer/profile/name', { full_name: profileForm.full_name }).then(() => {
        savingName.value = false
        if (profileService.request.status === 200) {
            toast.success('Profile updated.')
            if (Page.user) Page.user.full_name = profileForm.full_name
        } else {
            toast.error(profileService.request.message || 'Failed to update name.')
        }
    })
}

const updatePassword = async () => {
    if (!passwordForm.current_password || !passwordForm.password) return toast.error('Check your password inputs.')
    if (passwordForm.password !== passwordForm.password_confirmation) return toast.error('Passwords do not match.')
    
    savingPassword.value = true
    await profileService.patch('customer/profile/password', passwordForm).then(() => {
        savingPassword.value = false
        if (profileService.request.status === 200) {
            toast.success('Password changed successfully.')
            passwordForm.current_password = ''
            passwordForm.password = ''
            passwordForm.password_confirmation = ''
        } else {
            toast.error(profileService.request.message || 'Failed to change password.')
        }
    })
}

const saveQuestion = async () => {
    if (!questionForm.customQuestion || !questionForm.answer) return toast.error('Both question and answer are required.')
    
    savingQuestion.value = true
    await profileService.post('security/settings', {
        security_question: questionForm.customQuestion,
        security_answer: questionForm.answer
    }).then(() => {
        savingQuestion.value = false
        if (profileService.request.status === 200) {
            toast.success('Security question saved.')
            showQuestionDialog.value = false
            if (Page.user) Page.user.security_question = questionForm.customQuestion
        } else {
            toast.error(profileService.request.message || 'Failed to save question.')
        }
    })
}

const generateCodes = async () => {
    generatingCodes.value = true
    await profileService.post('security/settings', { generate_codes: true }).then(() => {
        generatingCodes.value = false
        if (profileService.request.status === 200) {
            recoveryCodes.value = profileService.request.data.recovery_codes
            toast.success('New codes generated.')
        }
    })
}

const confirmRegenerate = () => {
    confirm.require({
        header: 'Regenerate Codes?',
        message: 'Existing codes will be replaced with new ones. Continue?',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Generate New Codes',
        rejectLabel: 'Cancel',
        accept: () => generateCodes()
    })
}

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text)
    toast.info('Copied to clipboard.')
}

const downloadCodes = () => {
    const content = recoveryCodes.value.join('\n')
    const blob = new Blob([content], { type: 'text/plain' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `recovery-codes-${new Date().getTime()}.txt`
    a.click()
    toast.success('Codes downloaded.')
}

const recentOrders = ref<any[]>([]); // Using any[] for now or IOrder if imported
const loadingOrders = ref(false);

const loadOrders = async () => {
    loadingOrders.value = true;
    await profileService.get('customer/orders').then(() => {
        loadingOrders.value = false;
        if (profileService.request.status === 200) {
            const data = profileService.request.data;
            if (Array.isArray(data)) {
                // Store all orders
                recentOrders.value = data;
            } else {
                recentOrders.value = [];
            }
        }
    });
};

const filteredOrders = computed(() => {
    if (activeTab.value === 'All') return recentOrders.value;
    
    return recentOrders.value.filter(order => {
        if (activeTab.value === 'Completed') {
            return ['Completed', 'Delivered'].includes(order.status);
        }
        if (activeTab.value === 'Cancelled') {
            return ['Cancelled', 'Declined'].includes(order.status);
        }
        return order.status === activeTab.value;
    });
});

const getTabCount = (tab: string) => {
    if (tab === 'All') return recentOrders.value.length;
    if (tab === 'Completed') return recentOrders.value.filter(o => ['Completed', 'Delivered'].includes(o.status)).length;
    if (tab === 'Cancelled') return recentOrders.value.filter(o => ['Cancelled', 'Declined'].includes(o.status)).length;
    return recentOrders.value.filter(o => o.status === tab).length;
};



const getStatusTimelineColor = (status: string) => {
    switch (status) {
        case 'Completed':
        case 'Delivered': return 'bg-emerald-500 text-white';
        case 'Processing': return 'bg-blue-500 text-white';
        case 'Shipped': return 'bg-indigo-500 text-white';
        case 'Confirmed': return 'bg-sky-500 text-white';
        case 'Pending': return 'bg-amber-400 text-white';
        case 'Cancelled': 
        case 'Declined': return 'bg-red-500 text-white';
        default: return 'bg-slate-300 text-white';
    }
};

const getStatusBadgeClass = (status: string) => {
    switch (status) {
        case 'Completed':
        case 'Delivered': return 'bg-emerald-50 text-emerald-600 border-emerald-100';
        case 'Processing': return 'bg-blue-50 text-blue-600 border-blue-100';
        case 'Shipped': return 'bg-indigo-50 text-indigo-600 border-indigo-100';
        case 'Confirmed': return 'bg-sky-50 text-sky-600 border-sky-100';
        case 'Pending': return 'bg-amber-50 text-amber-600 border-amber-100';
        case 'Cancelled': 
        case 'Declined': return 'bg-red-50 text-red-600 border-red-100';
        default: return 'bg-slate-50 text-slate-600 border-slate-100';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'Completed':
        case 'Delivered': return 'pi-check';
        case 'Processing': return 'pi-cog pi-spin';
        case 'Shipped': return 'pi-truck';
        case 'Confirmed': return 'pi-thumbs-up';
        case 'Pending': return 'pi-clock';
        case 'Cancelled': 
        case 'Declined': return 'pi-times';
        default: return 'pi-circle-fill';
    }
};

const buyAgain = async (order: any) => {
    await profileService.post(`customer/orders/buy-again/${order.order_id}`, {});
    if (profileService.request.status === 200) {
        toast.success('Items added to cart!');
    } else {
        toast.error(profileService.request.message || 'Failed to add items to cart.');
    }
};

onMounted(() => {
    loadOrders();
    if (Page.user?.recovery_codes) recoveryCodes.value = Page.user.recovery_codes
});
</script>

<style scoped>
.form-input {
    width: 100%; 
    background-color: #ffffff; 
    border: 1px solid #e2e8f0; 
    border-radius: 0.75rem;
    padding: 0.75rem 1rem; 
    font-weight: 500; 
    color: #0f172a; 
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.form-input:focus { 
    outline: none; 
    border-color: #3b82f6; 
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08), 0 1px 2px rgba(0,0,0,0.05); 
    background-color: #fafbff;
}
.form-input:hover:not(:focus) {
    border-color: #cbd5e1;
}
:deep(.p-password-input) { border-radius: 0.75rem !important; }

/* Order card entrance animation */
.order-card {
    animation: orderSlideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
}
@keyframes orderSlideIn {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Floating empty state icon */
.empty-icon-float {
    animation: gentleFloat 3s ease-in-out infinite;
}
@keyframes gentleFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

/* Order section entrance */
.order-section {
    animation: sectionFadeIn 0.6s ease-out;
}
@keyframes sectionFadeIn {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hide scrollbar for tabs */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
