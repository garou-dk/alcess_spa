<template>
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Cover Section -->
        <div class="relative group h-56 md:h-72 w-full overflow-hidden bg-gray-900">
            <img 
                v-if="Page.user?.cover_image" 
                :src="UrlUtil.getBaseAppUrl(`storage/images/cover/${Page.user.cover_image}`)" 
                class="w-full h-full object-cover opacity-90 transition-transform duration-700 group-hover:scale-105"
            >
            <div v-else class="w-full h-full flex items-center justify-center">
                <img :src="Logo" alt="logo" class="w-20 h-20 opacity-10 filter invert" />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 via-gray-900/30 to-transparent"></div>

            <!-- Cover upload button - top right corner -->
            <button 
                @click="triggerCoverUpload" 
                class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-3 py-1.5 rounded-lg flex items-center gap-1.5 text-[11px] font-medium transition-all cursor-pointer z-20 border border-white/20"
            >
                <i class="pi pi-camera text-xs"></i>
                <span class="hidden sm:inline">Change Cover</span>
            </button>
            <input type="file" ref="coverInput" class="hidden" @change="onCoverChange" accept="image/*">

            <div class="absolute inset-x-0 bottom-0 pb-5">
                <div class="max-w-[1200px] mx-auto px-4 sm:px-6">
                    <h1 class="text-lg md:text-xl font-bold text-white">My Profile</h1>
                    <p class="text-sm text-white/70 mt-0.5">Manage your account settings and preferences</p>
                </div>
            </div>
        </div>

        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 -mt-16 relative z-10 pb-20 flex-grow">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left Column -->
                <div class="lg:col-span-4 space-y-5">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 relative">
                        <div class="flex flex-col items-center text-center -mt-16 mb-5">
                            <div class="relative group cursor-pointer" @click="triggerAvatarUpload">
                                <div class="w-28 h-28 md:w-32 md:h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white flex items-center justify-center">
                                    <img 
                                        v-if="Page.user?.image" 
                                        :src="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.image}`)" 
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                    >
                                    <span v-else class="text-5xl font-bold text-gray-200">{{ Page.user?.full_name?.charAt(0) }}</span>
                                </div>
                                <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity border-4 border-transparent">
                                    <i class="pi pi-camera text-white text-xl"></i>
                                </div>
                                <button 
                                    class="absolute bottom-1 right-1 z-30 bg-blue-600 text-white p-2 rounded-full shadow-md hover:bg-blue-700 transition-colors border-2 border-white cursor-pointer"
                                >
                                    <i class="pi pi-pencil text-[10px]"></i>
                                </button>
                                <input type="file" ref="avatarInput" class="hidden" @change="onAvatarChange" accept="image/*">
                            </div>

                            <div class="mt-3">
                                <h2 class="text-xl font-bold text-gray-900">{{ Page.user?.full_name }}</h2>
                                <p class="text-sm text-gray-500 mt-0.5">{{ Page.user?.email }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-2.5">
                                    <i class="pi pi-id-card text-sm text-gray-400"></i>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-800 bg-white px-2.5 py-1 rounded-full border border-gray-200">{{ Page.user?.role?.role_name }}</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-2.5">
                                    <i class="pi pi-verified text-sm text-green-500"></i>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</span>
                                </div>
                                <span class="text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full border border-green-100">Verified</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-2.5">
                                    <i class="pi pi-calendar text-sm text-gray-400"></i>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Joined</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-700">{{ Page.user?.created_at ? DateUtil.formatToMonthDayYear(Page.user.created_at) : '--' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Security Summary -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                        <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="pi pi-lock text-blue-600 text-xs"></i>
                            Account Security
                        </h3>
                        <div class="space-y-3.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-700">Password</p>
                                    <p class="text-[11px] text-gray-400">Use a strong, unique password.</p>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 border border-green-100 px-2 py-0.5 text-[10px] font-semibold text-green-700 uppercase tracking-wide">
                                    <i class="pi pi-check-circle text-[10px]"></i> OK
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-700">Recovery question</p>
                                    <p class="text-[11px] text-gray-400">Helps recover your account.</p>
                                </div>
                                <span
                                    :class="Page.user?.security_question ? 'text-green-700 bg-green-50 border-green-100' : 'text-amber-700 bg-amber-50 border-amber-100'"
                                    class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide"
                                >
                                    <i :class="Page.user?.security_question ? 'pi pi-check-circle' : 'pi pi-exclamation-circle'" class="text-[10px]"></i>
                                    {{ Page.user?.security_question ? 'Set' : 'Recommended' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password (moved here to fill space) -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <i class="pi pi-key text-blue-600 text-xs"></i>
                                Change Password
                            </h2>
                            <p class="text-[11px] text-gray-400 mt-0.5">Keep your account secure with a strong password</p>
                        </div>

                        <div class="p-5 space-y-4">
                            <div class="space-y-1.5">
                                    <label class="text-[11px] font-semibold text-gray-600">Current Password</label>
                                    <Password v-model="passwordForm.current_password" toggleMask fluid :feedback="false" placeholder="Required" pt:input:class="form-input-minimal" />
                            </div>
                            <div class="space-y-1.5">
                                    <label class="text-[11px] font-semibold text-gray-600">New Password</label>
                                    <Password v-model="passwordForm.password" toggleMask fluid placeholder="Min. 8 chars" pt:input:class="form-input-minimal" />
                            </div>
                            <div class="space-y-1.5">
                                    <label class="text-[11px] font-semibold text-gray-600">Confirm Password</label>
                                    <Password v-model="passwordForm.password_confirmation" toggleMask fluid :feedback="false" placeholder="Repeat" pt:input:class="form-input-minimal" />
                            </div>
                            <button @click="updatePassword" :disabled="savingPassword" class="w-full mt-2 px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer flex items-center justify-center gap-2">
                                <i v-if="savingPassword" class="pi pi-spin pi-spinner text-xs"></i>
                                <i v-else class="pi pi-lock text-xs"></i>
                                Update Password
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Delivery Address -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div>
                                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <i class="pi pi-map-marker text-gray-400 text-sm"></i>
                                    Delivery Address
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">Manage your shipping details for faster checkout</p>
                            </div>
                            <button 
                                @click="openAddressForm" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition-colors flex items-center gap-1.5 cursor-pointer"
                            >
                                <i class="pi pi-pencil text-[10px]"></i> Edit Address
                            </button>
                        </div>

                        <div class="p-5">
                             <div v-if="Page.user?.address" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Contact Number</label>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-500">
                                                <i class="pi pi-phone text-xs"></i>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-800">{{ Page.user.address.contact_number || 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Postal Code</label>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-500">
                                                <i class="pi pi-envelope text-xs"></i>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-800">{{ Page.user.address.postal_code || 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Full Address</label>
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 h-full">
                                        <div class="flex gap-2.5">
                                            <i class="pi pi-home text-blue-600 text-xs mt-0.5"></i>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800 leading-relaxed">{{ Page.user.address.other_details }}</p>
                                                <div v-if="Page.user.address.barangay" class="mt-2 pt-2 border-t border-gray-200">
                                                     <p class="text-xs font-medium text-gray-600">
                                                        {{ Page.user.address.barangay.barangay_name }}, 
                                                        {{ Page.user.address.barangay.municity?.municity_name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        {{ Page.user.address.barangay.municity?.province?.province_name }}
                                                    </p>
                                                    <div v-if="Page.user.address.barangay?.municity?.province?.region" class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 text-[10px] font-medium text-gray-500">
                                                        <i class="pi pi-map text-[10px]"></i>
                                                        {{ Page.user.address.barangay.municity.province.region.region_name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-else class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-50 mb-3">
                                    <i class="pi pi-map-marker text-xl text-blue-500"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-800">No Address Set</h3>
                                <p class="text-xs text-gray-500 mb-5 max-w-xs mx-auto">Add your delivery address to make checkout faster.</p>
                                <button @click="openAddressForm" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition-colors cursor-pointer">
                                    Add Address
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Details -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h2 class="text-base font-bold text-gray-900">Personal Details</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Update how your name appears on your profile</p>
                        </div>

                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-gray-600">Full Name</label>
                                    <div class="relative">
                                        <input v-model="profileForm.full_name" type="text" class="form-input pl-3.5 pr-10" placeholder="Enter your full name">
                                        <i class="pi pi-user absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-semibold text-gray-600">Email (Read Only)</label>
                                    <div class="relative">
                                        <input :value="Page.user?.email" type="email" readonly class="form-input bg-gray-50 text-gray-500 cursor-not-allowed pl-3.5 pr-10" placeholder="email@example.com">
                                        <i class="pi pi-lock absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 flex justify-end">
                                <button @click="updateName" :disabled="savingName" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer flex items-center gap-2">
                                    <i v-if="savingName" class="pi pi-spin pi-spinner text-sm"></i>
                                    <i v-else class="pi pi-check text-sm"></i>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Recovery Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 hover:border-gray-300 transition-colors group">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                    <i class="pi pi-question-circle text-sm"></i>
                                </div>
                                <span :class="Page.user?.security_question ? 'text-green-700 bg-green-50 border-green-100' : 'text-amber-700 bg-amber-50 border-amber-100'" class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider border">
                                    {{ Page.user?.security_question ? 'Set' : 'Recommended' }}
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm mb-0.5">Security Question</h4>
                            <p class="text-gray-500 text-xs leading-relaxed mb-4">
                                {{ Page.user?.security_question ? 'Your question is set. Click to update.' : 'Set a question for account recovery.' }}
                            </p>
                            <button @click="showQuestionDialog = true" class="w-full py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition-colors cursor-pointer">
                                {{ Page.user?.security_question ? 'Update Question' : 'Set Up Now' }}
                            </button>
                            </div>

                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 hover:border-gray-300 transition-colors group">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                    <i class="pi pi-shield text-sm"></i>
                                </div>
                                <span v-if="recoveryCodes.length > 0" class="text-green-700 bg-green-50 border border-green-100 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Active</span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm mb-0.5">Backup Codes</h4>
                            <p class="text-gray-500 text-xs leading-relaxed mb-4">
                                Generate one-time codes in case you lose access to your email.
                            </p>
                            <button @click="showCodesDialog = true" class="w-full py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition-colors cursor-pointer">
                                Manage Codes
                            </button>
                            </div>
                    </div>

                    <!-- Order History (below recovery) -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                    <i class="pi pi-shopping-bag text-gray-400 text-xs"></i>
                                    Order History
                                </h3>
                                <p class="text-[11px] text-gray-400 mt-0.5">Track the status of your purchases</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span v-if="recentOrders.length > 0" class="text-[10px] font-medium text-gray-400">
                                    {{ filteredOrders.length }} result{{ filteredOrders.length !== 1 ? 's' : '' }}
                                </span>
                                <div class="relative">
                                    <select
                                        v-model="activeTab"
                                        @change="orderPage = 1"
                                        class="appearance-none bg-gray-50 border border-gray-200 rounded-lg text-xs font-medium text-gray-700 pl-3 pr-8 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 cursor-pointer hover:border-gray-300 transition-colors"
                                    >
                                        <option v-for="tab in orderTabs" :key="tab" :value="tab">
                                            {{ tab }}{{ getTabCount(tab) > 0 ? ` (${getTabCount(tab)})` : '' }}
                                        </option>
                                    </select>
                                    <i class="pi pi-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" style="font-size: 0.6rem;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <!-- Loading -->
                            <div v-if="loadingOrders" class="space-y-2">
                                <div v-for="n in 4" :key="n" class="animate-pulse flex items-center gap-3 py-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex-shrink-0"></div>
                                    <div class="flex-1 space-y-1.5">
                                        <div class="h-3 bg-gray-100 rounded w-2/5"></div>
                                        <div class="h-2.5 bg-gray-100 rounded w-1/4"></div>
                                    </div>
                                    <div class="h-5 bg-gray-100 rounded-full w-16"></div>
                                </div>
                            </div>

                            <!-- Order List -->
                            <div v-else-if="filteredOrders.length > 0">
                                <div class="divide-y divide-gray-100">
                                    <div 
                                        v-for="order in paginatedOrders" 
                                        :key="order.order_id" 
                                        class="flex items-center gap-3 py-3 first:pt-0 last:pb-0 group"
                                    >
                                        <!-- Product thumbnail -->
                                        <div class="w-10 h-10 rounded-lg border border-gray-200 bg-gray-50 flex-shrink-0 overflow-hidden">
                                            <img 
                                                v-if="order.product_orders?.[0]?.product?.product_image" 
                                                :src="UrlUtil.getBaseAppUrl(`storage/images/product/${order.product_orders[0].product.product_image}`)" 
                                                class="w-full h-full object-cover"
                                            >
                                        </div>

                                        <!-- Order info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-black truncate">
                                                {{ getOrderProductNames(order) }}
                                            </p>
                                            <p class="text-[11px] text-gray-800 mt-0.5 font-semibold">
                                                {{ DateUtil.formatToMonthDayYear(order.created_at) }}
                                                <span class="text-gray-300 mx-1">·</span>
                                                ₱{{ (Number(order.total_amount) || 0).toLocaleString() }}
                                            </p>
                                        </div>

                                        <!-- Status + action -->
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold" :class="getStatusBadgeClass(order.status)">
                                                {{ order.status }}
                                            </span>
                                            <button
                                                @click="buyAgain(order)" 
                                                class="px-2 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-[10px] font-bold transition-all cursor-pointer whitespace-nowrap"
                                            >
                                                Order again
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pagination -->
                                <div v-if="orderTotalPages > 1" class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">
                                    <p class="text-[11px] text-gray-400">
                                        Page {{ orderPage }} of {{ orderTotalPages }}
                                    </p>
                                    <div class="flex items-center gap-1">
                                        <button
                                            @click="orderPage > 1 && orderPage--"
                                            :disabled="orderPage === 1"
                                            class="w-7 h-7 flex items-center justify-center rounded-md text-xs transition-colors cursor-pointer"
                                            :class="orderPage === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-500 hover:bg-gray-100'"
                                        >
                                            <i class="pi pi-chevron-left" style="font-size: 0.6rem;"></i>
                                        </button>
                                        <button
                                            v-for="page in orderPaginationRange"
                                            :key="page"
                                            @click="orderPage = page"
                                            class="w-7 h-7 flex items-center justify-center rounded-md text-[11px] font-medium transition-colors cursor-pointer"
                                            :class="orderPage === page ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                                        >
                                            {{ page }}
                                        </button>
                                        <button
                                            @click="orderPage < orderTotalPages && orderPage++"
                                            :disabled="orderPage === orderTotalPages"
                                            class="w-7 h-7 flex items-center justify-center rounded-md text-xs transition-colors cursor-pointer"
                                            :class="orderPage === orderTotalPages ? 'text-gray-300 cursor-not-allowed' : 'text-gray-500 hover:bg-gray-100'"
                                        >
                                            <i class="pi pi-chevron-right" style="font-size: 0.6rem;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty -->
                            <div v-else class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                    <i class="pi pi-shopping-bag text-lg text-gray-400"></i>
                                </div>
                                <h4 class="text-xs font-bold text-gray-700 mb-0.5">No orders found</h4>
                                <p class="text-[11px] text-gray-400 max-w-xs mx-auto mb-4">
                                    {{ activeTab === 'All' ? 'You haven\'t placed any orders yet.' : `No ${activeTab.toLowerCase()} orders.` }}
                                </p>
                                <router-link :to="{ name: 'customer.browse-products' }">
                                    <button class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-[11px] font-semibold hover:bg-blue-700 transition-colors cursor-pointer">
                                        Browse Products
                                    </button>
                                </router-link>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Dialogs -->
        <Dialog v-model:visible="showQuestionDialog" modal header="Set Security Question" :style="{ width: '28rem' }" :breakpoints="{ '575px': '90vw' }" :pt="{ root: { class: 'rounded-2xl overflow-hidden' }, header: { class: 'px-6 py-4 border-b border-gray-100' }, content: { class: 'px-6 py-5' }, footer: { class: 'px-6 pb-5 pt-0' } }">
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-600">Your Question</label>
                    <input v-model="questionForm.customQuestion" type="text" class="form-input" placeholder="e.g. What was your first pet's name?">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-600">Your Answer</label>
                    <Password v-model="questionForm.answer" toggleMask fluid :feedback="false" placeholder="Enter secure answer" pt:input:class="form-input" />
                    <p class="text-[10px] text-gray-400">Answers are case-insensitive and encrypted.</p>
                </div>
            </div>
            <template #footer>
                <div class="flex gap-3 mt-3">
                    <button @click="showQuestionDialog = false" class="flex-1 px-4 py-2.5 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold transition-colors cursor-pointer">Cancel</button>
                    <button @click="saveQuestion" :disabled="savingQuestion" class="flex-1 px-4 py-2.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-xl font-semibold transition-colors disabled:opacity-50 cursor-pointer flex items-center justify-center gap-1.5">
                        <i v-if="savingQuestion" class="pi pi-spin pi-spinner text-sm"></i>
                        Save
                    </button>
                </div>
            </template>
        </Dialog>

        <Dialog v-model:visible="showCodesDialog" modal header="Recovery Codes" :style="{ width: '34rem' }" :breakpoints="{ '575px': '90vw' }" :pt="{ root: { class: 'rounded-2xl overflow-hidden' }, header: { class: 'px-6 py-4 border-b border-gray-100' }, content: { class: 'px-6 py-5' }, footer: { class: 'px-6 pb-5 pt-0' } }">
            <div class="space-y-5">
                <div v-if="recoveryCodes.length > 0" class="grid grid-cols-2 gap-2.5">
                    <div v-for="(code, index) in recoveryCodes" :key="index" class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-xl group hover:border-blue-200 transition-colors">
                        <code class="text-sm font-semibold text-gray-700 font-mono tracking-wider">{{ code }}</code>
                        <button @click="copyToClipboard(code)" class="text-gray-400 hover:text-blue-600 transition-colors cursor-pointer">
                            <i class="pi pi-copy text-xs"></i>
                        </button>
                    </div>
                </div>
                <div v-else class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gray-100 mb-3">
                        <i class="pi pi-lock text-xl text-gray-400"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">No Codes Generated</h3>
                    <p class="text-xs text-gray-500 mt-1 mb-5">Generate codes to start securing your account.</p>
                    <button @click="generateCodes" :disabled="generatingCodes" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 cursor-pointer flex items-center justify-center gap-1.5 mx-auto">
                        <i v-if="generatingCodes" class="pi pi-spin pi-spinner text-sm"></i>
                        <i v-else class="pi pi-key text-sm"></i>
                        Generate Codes
                    </button>
                </div>

                <div v-if="recoveryCodes.length > 0" class="p-3.5 bg-amber-50 rounded-xl border border-amber-100 flex gap-2.5">
                    <i class="pi pi-info-circle text-amber-600 text-sm flex-shrink-0 mt-0.5"></i>
                    <p class="text-xs text-amber-800 leading-relaxed">
                        <strong>Important:</strong> Save these codes in a safe place. Each code can be used once.
                    </p>
                </div>
            </div>
            <template #footer v-if="recoveryCodes.length > 0">
                <div class="flex gap-3 mt-3">
                    <button @click="downloadCodes" class="flex-1 px-4 py-2.5 text-sm text-gray-700 border border-gray-200 hover:bg-gray-50 rounded-xl font-semibold transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                        <i class="pi pi-download text-sm"></i> Download
                    </button>
                    <button @click="confirmRegenerate" class="flex-1 px-4 py-2.5 text-sm text-white bg-red-500 hover:bg-red-600 rounded-xl font-semibold transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                        <i class="pi pi-refresh text-sm"></i> Regenerate
                    </button>
                </div>
            </template>
        </Dialog>

        <Dialog v-model:visible="showCropper" modal :header="cropMode === 'avatar' ? 'Crop Profile Photo' : 'Crop Cover Photo'" :style="{ width: '34rem' }" :breakpoints="{ '575px': '90vw' }" :pt="{ root: { class: 'rounded-2xl overflow-hidden !p-0' }, header: { class: 'px-6 py-4 border-b border-gray-100' }, content: { class: '!p-0' }, footer: { class: 'px-6 py-4 border-t border-gray-100' } }">
            <div class="bg-gray-100 flex items-center justify-center py-4" v-if="selectedFile">
                <VuePictureCropper
                    :boxStyle="{ width: '100%', height: '400px', backgroundColor: '#f3f4f6', margin: 'auto' }"
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
                    <button @click="showCropper = false" class="flex-1 px-4 py-2.5 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold transition-colors cursor-pointer">Cancel</button>
                    <button @click="uploadCroppedImage" :disabled="uploadingImage" class="flex-1 px-4 py-2.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-xl font-semibold transition-colors disabled:opacity-50 cursor-pointer flex items-center justify-center gap-1.5">
                        <i v-if="uploadingImage" class="pi pi-spin pi-spinner text-sm"></i>
                        <i v-else class="pi pi-check text-sm"></i>
                        Save & Update
                    </button>
                </div>
            </template>
        </Dialog>

        <ConfirmDialog :pt="{ root: { class: 'rounded-2xl overflow-hidden w-96' }, header: { class: 'hidden' }, content: { class: 'p-8 text-center' }, footer: { class: 'p-5 border-t border-gray-100 flex gap-3 justify-center bg-gray-50' } }">
             <template #message="slotProps">
                <div class="flex flex-col items-center w-full">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mb-3 text-red-500">
                        <i :class="slotProps.message.icon" class="text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-1.5">{{ slotProps.message.header }}</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ slotProps.message.message }}</p>
                </div>
            </template>
        </ConfirmDialog>

        <!-- Footer -->
        <Footer class="mt-8" />
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
import Footer from '@/components/Footer.vue'

const toast = useToast()
const confirm = useConfirm()
const profileService = useAxiosUtil()

// State
const orderTabs = ['All', 'Processing', 'Confirmed', 'Shipped', 'Completed', 'Cancelled']
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

// Pagination for orders
const ordersPerPage = 6
const orderPage = ref(1)

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

const recentOrders = ref<any[]>([]);
const loadingOrders = ref(false);

const loadOrders = async () => {
    loadingOrders.value = true;
    await profileService.get('customer/orders').then(() => {
        loadingOrders.value = false;
        if (profileService.request.status === 200) {
            const data = profileService.request.data;
            if (Array.isArray(data)) {
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
            return order.status === 'Cancelled';
        }
        return order.status === activeTab.value;
    });
});

const paginatedOrders = computed(() => {
    const start = (orderPage.value - 1) * ordersPerPage;
    return filteredOrders.value.slice(start, start + ordersPerPage);
});

const orderTotalPages = computed(() => {
    return Math.ceil(filteredOrders.value.length / ordersPerPage);
});

const orderPaginationRange = computed(() => {
    const range: number[] = [];
    const delta = 2;
    const left = orderPage.value - delta;
    const right = orderPage.value + delta + 1;
    for (let i = 1; i <= orderTotalPages.value; i++) {
        if (i === 1 || i === orderTotalPages.value || (i >= left && i < right)) {
            range.push(i);
        }
    }
    return range;
});

const getTabCount = (tab: string) => {
    if (tab === 'All') return recentOrders.value.length;
    if (tab === 'Completed') return recentOrders.value.filter(o => ['Completed', 'Delivered'].includes(o.status)).length;
    if (tab === 'Cancelled') return recentOrders.value.filter(o => o.status === 'Cancelled').length;
    return recentOrders.value.filter(o => o.status === tab).length;
};

const getOrderProductNames = (order: any) => {
    if (!order.product_orders || order.product_orders.length === 0) return 'Order';
    const names = order.product_orders.map((po: any) => po.product?.product_name || 'Item').slice(0, 2);
    const remaining = order.product_orders.length - 2;
    if (remaining > 0) {
        return names.join(', ') + ` +${remaining} more`;
    }
    return names.join(', ');
};

const getStatusBadgeClass = (status: string) => {
    switch (status) {
        case 'Completed':
        case 'Delivered': return 'bg-green-50 text-green-700';
        case 'Processing': return 'bg-blue-50 text-blue-700';
        case 'Shipped': return 'bg-purple-50 text-purple-700';
        case 'Confirmed': return 'bg-sky-50 text-sky-700';
        case 'Pending': return 'bg-amber-50 text-amber-700';
        case 'Cancelled': return 'bg-red-50 text-red-700';
        default: return 'bg-gray-50 text-gray-700';
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
    border: 1px solid #e5e7eb; 
    border-radius: 0.75rem;
    padding: 0.625rem 0.875rem; 
    font-size: 0.875rem;
    font-weight: 500; 
    color: #111827; 
    transition: all 0.2s ease;
}
.form-input:focus { 
    outline: none; 
    border-color: #3b82f6; 
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15); 
    background-color: #fff;
}
.form-input:hover:not(:focus):not(:read-only) {
    border-color: #d1d5db;
}

/* Minimalist form input for card sections */
.form-input-minimal {
    width: 100%; 
    background-color: #f9fafb; 
    border: 1px solid #e5e7eb; 
    border-radius: 0.625rem;
    padding: 0.5rem 0.75rem; 
    font-size: 0.8125rem;
    font-weight: 500; 
    color: #111827; 
    transition: all 0.2s ease;
}
.form-input-minimal:focus { 
    outline: none; 
    border-color: #3b82f6; 
    background-color: #fff;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.08);
}
.form-input-minimal::placeholder {
    font-size: 0.8125rem;
    color: #9ca3af;
    font-weight: 400;
}

:deep(.p-password-input) { border-radius: 0.625rem !important; }

/* Scrollbar hide */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
