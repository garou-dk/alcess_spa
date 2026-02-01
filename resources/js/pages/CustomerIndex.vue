<template>
    <div>
        <header class="bg-blue-600 sticky top-0 z-50 shadow-md">
            <!-- Mobile Header (< 768px) -->
            <nav v-if="isMobile">
                <!-- Top row: Logo and Menu Toggle -->
                <div class="flex items-center justify-between p-3">
                    <RouterLink :to="{ name: 'home' }" class="flex items-center gap-2">
                        <div class="rounded-full bg-white p-1">
                            <img :src="Icon" class="h-10 w-10" alt="Store Icon" />
                        </div>
                        <h1 class="text-lg font-bold text-white">{{ appName }}</h1>
                    </RouterLink>
                    
                    <!-- Mobile action buttons -->
                    <div class="flex items-center gap-2">
                        <Button
                            icon="pi pi-facebook"
                            rounded
                            as="a"
                            href="https://www.facebook.com/alcesslaptopstore"
                            target="_blank"
                            class="!bg-white !text-blue-600"
                            size="small"
                        />
                        <Button
                            icon="pi pi-th-large"
                            rounded
                            severity="secondary"
                            size="small"
                            @click="goToBrowseProducts"
                            v-tooltip.bottom="'Browse Products'"
                        />
                        <div v-if="Page.user" class="flex items-center gap-2">
                            <CartButton size="small" />
                            <!-- Notification Bell -->
                            <OverlayBadge 
                                v-if="unreadCount > 0"
                                :value="unreadCount"
                                pt:pcBadge:root:class="bg-red-500! text-white !rounded-full !w-4 !h-4 !flex !items-center !justify-center !text-xs !font-semibold !leading-none"
                            >
                                <Button
                                    icon="pi pi-bell"
                                    rounded
                                    severity="secondary"
                                    size="small"
                                    @click="openNotifications"
                                />
                            </OverlayBadge>
                            <Button
                                v-else
                                icon="pi pi-bell"
                                rounded
                                severity="secondary"
                                size="small"
                                @click="openNotifications"
                            />
                            <!-- User Avatar -->
                            <button @click="openAvatar" class="cursor-pointer">
                                <Avatar
                                    v-if="Page.user && Page.user.image"
                                    :image="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.image}`)"
                                    shape="circle"
                                    style="width: 32px; height: 32px;"
                                />
                                <Avatar
                                    v-else
                                    :label="Page.user && Page.user.full_name[0]"
                                    shape="circle"
                                    class="bg-white! text-black!"
                                    style="width: 32px; height: 32px;"
                                />
                            </button>
                        </div>
                        <Button
                            v-else
                            icon="pi pi-user"
                            rounded
                            severity="secondary"
                            size="small"
                            @click="openLoginForm()"
                            v-tooltip.bottom="'Login'"
                        />
                    </div>
                </div>
                
                <!-- Bottom row: Search bar (only on product pages) -->
                <div v-if="isProductPage" class="px-3 pb-3">
                    <form @submit.prevent="handleSearch">
                        <div class="relative">
                            <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="form.search"
                                type="text"
                                placeholder="Search products..."
                                class="w-full pl-10 pr-4 py-2 rounded-lg border-2 border-white bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-white/50 transition-all text-sm"
                            />
                        </div>
                    </form>
                </div>
            </nav>

            <!-- Tablet Header (768px - 1023px) -->
            <nav v-if="isTablet">
                <div class="flex items-center justify-between p-3">
                    <!-- Left: Logo -->
                    <RouterLink :to="{ name: 'home' }" class="flex items-center gap-3">
                        <div class="rounded-full bg-white p-1">
                            <img :src="Icon" class="h-12 w-12" alt="Store Icon" />
                        </div>
                        <h1 class="text-xl font-bold text-white">{{ appName }}</h1>
                    </RouterLink>
                    
                    <!-- Center: Search (only on product pages) -->
                    <div v-if="isProductPage" class="flex-1 max-w-md mx-6">
                        <form @submit.prevent="handleSearch">
                            <div class="relative">
                                <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="Search for products..."
                                    class="w-full pl-10 pr-4 py-2 rounded-lg border-2 border-white bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-white/50 transition-all"
                                />
                            </div>
                        </form>
                    </div>
                    
                    <!-- Right: Action buttons -->
                    <div class="flex items-center gap-3">
                        <Button
                            icon="pi pi-facebook"
                            rounded
                            as="a"
                            href="https://www.facebook.com/alcesslaptopstore"
                            target="_blank"
                            class="!bg-white !text-blue-600"
                            v-tooltip.bottom="'Facebook'"
                        />
                        <Button
                            icon="pi pi-th-large"
                            rounded
                            severity="secondary"
                            @click="goToBrowseProducts"
                            v-tooltip.bottom="'Browse Products'"
                        />
                        <div v-if="Page.user" class="flex items-center gap-3">
                            <CartButton size="small" />
                            <!-- Notification Bell -->
                            <OverlayBadge 
                                v-if="unreadCount > 0"
                                :value="unreadCount"
                                pt:pcBadge:root:class="bg-red-500! text-white !rounded-full !w-5 !h-5 !flex !items-center !justify-center !text-xs !font-semibold !leading-none"
                            >
                                <Button
                                    icon="pi pi-bell"
                                    rounded
                                    severity="secondary"
                                    @click="openNotifications"
                                />
                            </OverlayBadge>
                            <Button
                                v-else
                                icon="pi pi-bell"
                                rounded
                                severity="secondary"
                                @click="openNotifications"
                            />
                            <!-- User Avatar -->
                            <button @click="openAvatar" class="cursor-pointer">
                                <Avatar
                                    v-if="Page.user && Page.user.image"
                                    :image="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.image}`)"
                                    shape="circle"
                                    style="width: 40px; height: 40px;"
                                />
                                <Avatar
                                    v-else
                                    :label="Page.user && Page.user.full_name[0]"
                                    shape="circle"
                                    class="bg-white! text-black!"
                                    style="width: 40px; height: 40px;"
                                />
                            </button>
                        </div>
                        <Button
                            v-else
                            icon="pi pi-user"
                            rounded
                            severity="secondary"
                            @click="openLoginForm()"
                            v-tooltip.bottom="'Login'"
                        />
                    </div>
                </div>
            </nav>

            <!-- Desktop Header (≥ 1024px) - Original Layout -->
            <nav v-if="isDesktop" class="p-2">
                <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2">
                            <RouterLink :to="{ name: 'home' }">
                                <div class="flex">
                                    <div class="px-2">
                                        <div class="rounded-full bg-white">
                                            <img
                                                :src="Icon"
                                                class="h-16 w-16"
                                                alt="Store Icon"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex items-center px-2">
                                        <h1 class="text-3xl font-bold text-white">
                                            {{ appName }}
                                        </h1>
                                    </div>
                                </div>
                            </RouterLink>
                        </div>

                        <div class="flex items-center w-full flex-1 justify-end">
                            <div v-if="Page.user" class="flex items-center gap-3 w-full">
                                <!-- Search Bar - Only show on product pages -->
                                <div v-if="isProductPage" class="flex-1 max-w-4xl mx-6">
                                    <form @submit.prevent="handleSearch">
                                        <div class="relative">
                                            <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                                            <input
                                                v-model="form.search"
                                                type="text"
                                                placeholder="Search for products..."
                                                class="w-full pl-11 pr-4 py-2.5 rounded-lg border-2 border-white bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-white/50 transition-all"
                                            />
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Right side icons -->
                                <div class="flex items-center" :class="isProductPage ? 'ml-6' : 'ml-auto'">
                                    <div class="flex gap-4 items-center">
                                        <!-- Facebook Icon -->
                                        <Button
                                            icon="pi pi-facebook"
                                            rounded
                                            as="a"
                                            href="https://www.facebook.com/alcesslaptopstore"
                                            target="_blank"
                                            class="!bg-white !text-blue-600"
                                        />
                                        <!-- Browse All Products -->
                                        <Button
                                            icon="pi pi-th-large"
                                            rounded
                                            severity="secondary"
                                            @click="goToBrowseProducts"
                                            v-tooltip.bottom="'Browse All Products'"
                                        />
                                        <CartButton />
                                        <!-- Notification Bell -->
                                        <OverlayBadge 
                                            v-if="unreadCount > 0"
                                            :value="unreadCount"
                                            pt:pcBadge:root:class="bg-red-500! text-white !rounded-full !w-5 !h-5 !flex !items-center !justify-center !text-xs !font-semibold !leading-none"
                                        >
                                            <Button
                                                icon="pi pi-bell"
                                                rounded
                                                severity="secondary"
                                                @click="openNotifications"
                                            />
                                        </OverlayBadge>
                                        <Button
                                            v-else
                                            icon="pi pi-bell"
                                            rounded
                                            severity="secondary"
                                            @click="openNotifications"
                                            />
                                        <!-- User Avatar -->
                                        <button @click="openAvatar" class="cursor-pointer">
                                            <Avatar
                                                v-if="Page.user && Page.user.image"
                                                :image="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.image}`)"
                                                shape="circle"
                                                style="width: 40px; height: 40px;"
                                            />
                                            <Avatar
                                                v-else
                                                :label="Page.user && Page.user.full_name[0]"
                                                shape="circle"
                                                class="bg-white! text-black!"
                                                style="width: 40px; height: 40px;"
                                            />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex items-center w-full">
                                <!-- Search Bar - Only show on product pages -->
                                <div v-if="isProductPage" class="flex-1 max-w-4xl mx-6">
                                    <form @submit.prevent="handleSearch">
                                        <div class="relative">
                                            <i class="pi pi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                                            <input
                                                v-model="form.search"
                                                type="text"
                                                placeholder="Search for products..."
                                                class="w-full pl-11 pr-4 py-2.5 rounded-lg border-2 border-white bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:border-white focus:ring-2 focus:ring-white/50 transition-all"
                                            />
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Right side icons -->
                                <div class="flex items-center" :class="isProductPage ? 'ml-6' : 'ml-auto'">
                                    <div class="flex gap-4 items-center">
                                        <!-- Facebook Icon -->
                                        <Button
                                            icon="pi pi-facebook"
                                            rounded
                                            as="a"
                                            href="https://www.facebook.com/alcesslaptopstore"
                                            target="_blank"
                                            class="!bg-white !text-blue-600"
                                        />
                                        <!-- Browse All Products -->
                                        <Button
                                            icon="pi pi-th-large"
                                            rounded
                                            severity="secondary"
                                            @click="goToBrowseProducts"
                                            v-tooltip.bottom="'Browse All Products'"
                                        />
                                        <Button
                                            icon="pi pi-user"
                                            rounded
                                            severity="secondary"
                                            @click="openLoginForm()"
                                            v-tooltip.bottom="'Login'"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </nav>
        </header>

        <!-- Notification Popover (shared across all layouts) -->
        <Popover
            ref="notificationElement"
            :dismissable="false"
            @show="onNotificationShow"
            @hide="onNotificationHide"
            :pt="{
                root: { 
                    class: 'notification-popover',
                    style: isMobile ? 'width: calc(100vw - 32px); max-width: calc(100vw - 32px); z-index: 9999; right: 16px !important; left: auto !important;' : isTablet ? 'width: 300px; max-width: 300px; z-index: 9999; right: 0 !important; left: auto !important;' : 'width: 350px; max-width: 350px; z-index: 9999; right: 0 !important; left: auto !important;'
                }
            }"
        >
            <div class="w-full flex flex-col overflow-hidden" style="max-width: 320px;">
                <!-- Notification Header -->
                <div :class="[
                    'flex items-center justify-between border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50 flex-shrink-0',
                    isMobile ? 'p-2' : 'p-3'
                ]">
                    <div class="flex items-center gap-2">
                        <i :class="[
                            'pi pi-bell text-sky-800',
                            isMobile ? 'text-base' : 'text-lg'
                        ]"></i>
                        <h3 :class="[
                            'font-bold text-gray-800',
                            isMobile ? 'text-sm' : 'text-base'
                        ]">Notifications</h3>
                        <span 
                            v-if="unreadCount > 0" 
                            :class="[
                                'bg-red-500 text-white font-semibold px-2 py-1 rounded-full',
                                isMobile ? 'text-xs' : 'text-xs'
                            ]"
                        >
                            {{ unreadCount }}
                        </span>
                    </div>
                    <Button
                        v-if="unreadCount > 0"
                        type="button"
                        label="Mark all read"
                        text
                        size="small"
                        :class="[
                            'text-sky-700 hover:text-sky-900 font-semibold',
                            isMobile ? 'text-xs' : 'text-xs'
                        ]"
                        @click.stop="markAllAsRead"
                    />
                </div>

                <!-- Notification Tabs -->
                <div class="flex border-b border-gray-200 bg-gray-50">
                    <button
                        @click.stop="activeTab = 'all'"
                        :class="[
                            'flex-1 py-2 font-semibold transition-all duration-200',
                            isMobile ? 'px-2 text-xs' : 'px-3 text-xs',
                            activeTab === 'all' 
                                ? 'text-sky-800 border-b-2 border-sky-800 bg-white' 
                                : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'
                        ]"
                    >
                        All ({{ notifications.length }})
                    </button>
                    <button
                        @click.stop="activeTab = 'unread'"
                        :class="[
                            'flex-1 py-2 font-semibold transition-all duration-200',
                            isMobile ? 'px-2 text-xs' : 'px-3 text-xs',
                            activeTab === 'unread' 
                                ? 'text-sky-800 border-b-2 border-sky-800 bg-white' 
                                : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'
                        ]"
                    >
                        Unread ({{ unreadCount }})
                    </button>
                </div>

                <!-- Notifications List -->
                <div :class="[
                    'overflow-y-auto',
                    isMobile ? 'max-h-[200px]' : isTablet ? 'max-h-[220px]' : 'max-h-[250px]'
                ]">
                    <div v-if="filteredNotifications.length === 0" :class="[
                        'text-center',
                        isMobile ? 'p-3' : 'p-4'
                    ]">
                        <i :class="[
                            'pi pi-bell-slash text-gray-300 mb-2',
                            isMobile ? 'text-xl' : 'text-2xl'
                        ]"></i>
                        <p :class="[
                            'text-gray-500 font-medium',
                            isMobile ? 'text-xs' : 'text-sm'
                        ]">No notifications</p>
                        <p :class="[
                            'text-gray-400 mt-1',
                            isMobile ? 'text-xs' : 'text-xs'
                        ]">
                            {{ activeTab === 'unread' ? 'All caught up!' : 'You have no notifications yet' }}
                        </p>
                    </div>

                    <div v-else class="divide-y divide-gray-100">
                        <div
                            v-for="notification in filteredNotifications"
                            :key="notification.order_notification_id"
                            @click.stop="markAsRead(notification.order_notification_id)"
                            :class="[
                                'cursor-pointer transition-all duration-200 hover:bg-gray-50',
                                isMobile ? 'p-2' : 'p-3',
                                !notification.is_read ? 'bg-blue-50/50' : ''
                            ]"
                        >
                            <div class="flex gap-2">
                                <!-- Notification Icon -->
                                <div :class="[
                                    'flex-shrink-0 rounded-full flex items-center justify-center bg-blue-100 text-blue-600',
                                    isMobile ? 'w-6 h-6' : 'w-8 h-8'
                                ]">
                                    <i :class="[
                                        'pi pi-shopping-cart',
                                        isMobile ? 'text-xs' : 'text-sm'
                                    ]"></i>
                                </div>

                                <!-- Notification Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <h4 :class="[
                                            'font-semibold text-gray-800 leading-tight',
                                            isMobile ? 'text-xs' : 'text-xs'
                                        ]">
                                            {{ notification.message }}
                                        </h4>
                                        <span 
                                            v-if="!notification.is_read"
                                            :class="[
                                                'flex-shrink-0 bg-blue-600 rounded-full mt-1',
                                                isMobile ? 'w-1.5 h-1.5' : 'w-2 h-2'
                                            ]"
                                        ></span>
                                    </div>
                                    <div :class="[
                                        'flex items-center gap-2 text-gray-500',
                                        isMobile ? 'text-[9px]' : 'text-[10px]'
                                    ]">
                                        <span class="flex items-center gap-1">
                                            <i :class="[
                                                'pi pi-clock',
                                                isMobile ? 'text-[9px]' : 'text-[10px]'
                                            ]"></i>
                                            {{ DateUtil.formatToMonthDayYear(notification.created_at) }} {{ DateUtil.timeStringAMPM(DateUtil.timeString(new Date(notification.created_at))) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Popover>

        <!-- Avatar Popover (shared across all layouts) -->
        <Popover
            ref="avatarElement"
            :dismissable="true"
            class="mr-5 inline-flex"
        >
            <div class="flex flex-col">
                <div>
                    <Button
                        type="button"
                        :class="[
                            '!bg-transparent !text-black !font-normal !border-none hover:!bg-gray-100 !transition-colors',
                            isMobile || isTablet ? '!text-sm' : ''
                        ]"
                        label="My Profile"
                        icon="pi pi-user"
                        fluid
                        @click="goToProfile()"
                    />
                </div>
                <div class="border-t border-gray-200"></div>
                <div>
                    <Button
                        type="button"
                        :class="[
                            '!bg-transparent !text-black !font-normal !border-none hover:!bg-gray-100 !transition-colors',
                            isMobile || isTablet ? '!text-sm' : ''
                        ]"
                        label="Orders"
                        icon="pi pi-truck"
                        fluid
                        @click="goToOrder()"
                    />
                </div>
                <div class="border-t border-gray-200"></div>
                <div>
                    <Button
                        type="button"
                        :class="[
                            '!bg-transparent !text-black !font-normal !border-none hover:!bg-gray-100 !transition-colors',
                            isMobile || isTablet ? '!text-sm' : ''
                        ]"
                        label="Invoice"
                        icon="pi pi-file-pdf"
                        fluid
                        @click="goToInvoice()"
                    />
                </div>
                <div class="border-t border-gray-200"></div>
                <div>
                    <Button
                        type="button"
                        :class="[
                            '!bg-transparent !text-black !font-normal !border-none hover:!bg-gray-100 !transition-colors',
                            isMobile || isTablet ? '!text-sm' : ''
                        ]"
                        label="Address"
                        icon="pi pi-map-marker"
                        fluid
                        @click="openAddressForm()"
                    />
                </div>
                <div class="border-t border-gray-200"></div>
                <div>
                    <Button
                        type="button"
                        :class="[
                            '!bg-transparent !text-black !font-normal !border-none hover:!bg-gray-100 !transition-colors',
                            isMobile || isTablet ? '!text-sm' : ''
                        ]"
                        label="Security"
                        icon="pi pi-shield"
                        fluid
                        @click.stop="openSecurityForm()"
                    />
                </div>
                <div class="border-t border-gray-200"></div>
                <div>
                    <LogoutButton :class="isMobile || isTablet ? '!text-sm' : ''" />
                </div>
            </div>
        </Popover>
        <div>
            <RouterView />
        </div>
        
        <Dialog
            v-model:visible="loginFormVisible"
            modal
            header="Login"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <LoginForm @success="loginFormVisible = false">
                <template #footer>
                    <div class="flex flex-col">
                        <div class="flex">
                            <div class="w-1/2 p-2">
                                <Button
                                    type="button"
                                    variant="link"
                                    label="Forgot password?"
                                    class="p-0! text-black! text-sm!"
                                    pt:label:class="font-normal!"
                                    @click="openResetPasswordForm()"
                                />
                            </div>
                            <div class="flex w-1/2 flex-col p-2">
                                <span class="text-end text-sm">Don't have an account?</span>
                                <div class="flex justify-end">
                                    <Button
                                        type="button"
                                        variant="link"
                                        label="Register"
                                        class="p-0! text-sm! text-blue-600!"
                                        pt:label:class="font-normal!"
                                        @click="openRegisterForm()"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center p-2 text-sm text-gray-600">
                            <span>Are you an administrator? </span>
                            <RouterLink 
                                :to="{ name: 'admin.login' }"
                                class="ml-1 text-green-600 hover:underline cursor-pointer"
                            >
                                Login
                            </RouterLink>
                        </div>
                    </div>
                </template>
            </LoginForm>
        </Dialog>
        <Dialog
            v-model:visible="registerFormVisible"
            modal
            header="Register"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <RegisterForm>
                <template #footer>
                    <div class="flex justify-center p-2">
                        <span>Already have an account?</span>
                        &nbsp;
                        <Button
                            type="button"
                            variant="link"
                            label="Login"
                            class="p-0!"
                            pt:label:class="font-normal!"
                            @click="openLoginForm()"
                        />
                    </div>
                </template>
            </RegisterForm>
        </Dialog>
        <Dialog
            v-model:visible="resetPasswordFormVisible"
            modal
            header="Reset Account"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <ResetPasswordForm @code-sent="handleCodeSent" />
        </Dialog>
        <Dialog
            v-model:visible="verifyCodeFormVisible"
            modal
            header="Verify Code"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <VerifyCodeForm 
                :email="resetEmail"
                :current-password="resetCurrentPassword"
                :new-password="resetNewPassword"
                :new-password-confirmation="resetNewPasswordConfirmation"
                @success="handleResetSuccess" 
            />
        </Dialog>
        <Dialog
            v-model:visible="addressForm"
            modal
            header="Address"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <AddressForm />
        </Dialog>
        <Dialog
            v-model:visible="securityForm"
            modal
            header="Security Settings"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
            :dismissableMask="true"
            pt:header:class="bg-blue-600! text-white! rounded-t-lg! rounded-b-none!"
        >
            <SecurityForm />
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import {
    SearchErrorInterface,
    SearchProductInterface,
} from "@/interfaces/SearchProductInterface";
import { reactive, ref, computed, onMounted, onUnmounted, provide, watch, nextTick } from "vue";
import { useResponsive } from "@/composables/useResponsive";
import Icon from "@/../img/logo.png";
import Page from "@/stores/Page";
import UrlUtil from "@/utils/UrlUtil";
import { Popover, Badge, OverlayBadge } from "primevue";
import LogoutButton from "@/components/LogoutButton.vue";
import CartButton from "@/components/CartButton.vue";
import RegisterForm from "@/components/forms/RegisterForm.vue";
import LoginForm from "@/components/forms/LoginForm.vue";
import ResetPasswordForm from "@/components/forms/ResetPasswordForm.vue";
import VerifyCodeForm from "@/components/forms/VerifyCodeForm.vue";
import AddressForm from "@/components/forms/AddressForm.vue";
import SecurityForm from "@/components/forms/SecurityForm.vue";
import { useRouter } from "vue-router";
import useAxiosUtil from "@/utils/AxiosUtil";
import { IOrderNotification } from "@/interfaces/IOrderNotification";
import { useToast } from "vue-toastification";
import DateUtil from "@/utils/DateUtil";
import { useEcho } from "@laravel/echo-vue";

const { isMobile, isTablet, isDesktop } = useResponsive();
const appName = import.meta.env.VITE_APP_NAME;
const form: SearchProductInterface = reactive({
    search: null,
});
const errors: SearchErrorInterface = reactive({
    search: [],
});

// Watch for manual changes to form.search (typing in navbar) with debounce
let navbarSearchTimeout: number | null = null;
let isUpdatingFromRouteNavbar = false;

watch(() => form.search, (newValue, oldValue) => {
    console.log('⌨️ CustomerIndex form.search changed (user typing):', {
        newValue,
        oldValue,
        currentRoute: router.currentRoute.value.name,
        currentQuery: router.currentRoute.value.query.q,
        isUpdatingFromRoute: isUpdatingFromRouteNavbar
    });
    
    // Skip if we're updating from route watcher to avoid circular updates
    if (isUpdatingFromRouteNavbar) {
        console.log('⏭️ CustomerIndex: Skipping - updating from route');
        isUpdatingFromRouteNavbar = false;
        return;
    }
    
    // Clear existing timeout
    if (navbarSearchTimeout) {
        clearTimeout(navbarSearchTimeout);
    }
    
    // Set new timeout for debounced search
    navbarSearchTimeout = window.setTimeout(() => {
        const trimmedValue = newValue ? newValue.trim() : '';
        const currentQuery = router.currentRoute.value.query.q as string | undefined;
        
        console.log('⏰ CustomerIndex debounce executed:', {
            trimmedValue,
            currentQuery,
            willTriggerSearch: trimmedValue !== currentQuery
        });
        
        // Only trigger if value changed from current query
        if (trimmedValue && trimmedValue !== currentQuery) {
            console.log('🚀 CustomerIndex: Auto-triggering search');
            handleSearch();
        } else if (!trimmedValue && currentQuery) {
            console.log('🚀 CustomerIndex: Auto-clearing search');
            handleSearch();
        }
    }, 500); // 500ms debounce
});

const avatarElement = ref<null | InstanceType<typeof Popover>>();
const notificationElement = ref<null | InstanceType<typeof Popover>>();
const loginFormVisible = ref<boolean>(false);
const registerFormVisible = ref<boolean>(false);
const resetPasswordFormVisible = ref<boolean>(false);
const verifyCodeFormVisible = ref<boolean>(false);
const resetEmail = ref<string | null>(null);
const resetCurrentPassword = ref<string | null>(null);
const resetNewPassword = ref<string | null>(null);
const resetNewPasswordConfirmation = ref<string | null>(null);
const addressForm = ref<boolean>(false);
const securityForm = ref<boolean>(false);
const router = useRouter();

// Check if current route is a product-related page or home page
const isProductPage = computed(() => {
    const productRoutes = [
        'customer.search-product',
        'customer.product-info.index',
        'customer.product-category',
        'customer.browse-products',
        'customer.home.index',
        'customer.cart.index',
        'customer.order.index',
        'home'
    ];
    return productRoutes.includes(router.currentRoute.value.name as string);
});

interface Notification {
    id: number;
    title: string;
    message: string;
    time: string;
    read: boolean;
    icon: string;
    color: string;
}

const activeTab = ref<'all' | 'unread'>('all');

const notifications = ref<IOrderNotification[]>([
]);

const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.is_read).length;
});

const filteredNotifications = computed(() => {
    if (activeTab.value === 'unread') {
        return notifications.value.filter(n => !n.is_read);
    }
    return notifications.value;
});

const openAvatar = (event: Event) => {
    if (avatarElement.value) {
        avatarElement.value.toggle(event);
    }
};

const openNotifications = (event: Event) => {
    if (notificationElement.value) {
        if (isNotificationOpen.value) {
            notificationElement.value.hide();
            isNotificationOpen.value = false;
        } else {
            notificationElement.value.show(event);
            isNotificationOpen.value = true;
        }
    }
};

const isNotificationOpen = ref(false);
let clickOutsideListener: ((event: MouseEvent) => void) | null = null;

const onNotificationShow = () => {
    isNotificationOpen.value = true;
    // Add click outside listener
    setTimeout(() => {
        clickOutsideListener = (event: MouseEvent) => {
            const popoverEl = notificationElement.value?.$el;
            const target = event.target as Node;
            
            // Check if click is inside the popover
            if (popoverEl && popoverEl.contains(target)) {
                return;
            }
            
            // Click is outside, close the popover
            if (popoverEl && !popoverEl.contains(target)) {
                notificationElement.value?.hide();
                isNotificationOpen.value = false;
                // Remove listener
                if (clickOutsideListener) {
                    document.removeEventListener('click', clickOutsideListener);
                    clickOutsideListener = null;
                }
            }
        };
        document.addEventListener('click', clickOutsideListener);
    }, 100);
};

const onNotificationHide = () => {
    isNotificationOpen.value = false;
    if (clickOutsideListener) {
        document.removeEventListener('click', clickOutsideListener);
        clickOutsideListener = null;
    }
};

const submitMarkReadService = useAxiosUtil();

const markAsRead = async (id: number) => {
    const notification = notifications.value.find(n => n.order_notification_id === id);
    if (notification && !notification.is_read) {
        notification.is_read = true;
    }
    notificationElement.value?.hide();
    isNotificationOpen.value = false;
    router.push({ name: 'customer.order.index' });
    await submitMarkReadService.patch(`customer/order-notifications/mark-as-read/${notification.order_notification_id}`, null).then(() => {
    });
};

const markAllAsRead = () => {
    notifications.value.forEach(n => n.is_read = true);
};

const openRegisterForm = () => {
    loginFormVisible.value = false;
    resetPasswordFormVisible.value = false;
    addressForm.value = false;
    registerFormVisible.value = true;
};

const openLoginForm = () => {
    registerFormVisible.value = false;
    resetPasswordFormVisible.value = false;
    addressForm.value = false;
    loginFormVisible.value = true;
};

// Provide openLoginForm to child components
provide('openLoginForm', openLoginForm);

const openResetPasswordForm = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
    resetPasswordFormVisible.value = true;
};

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

const openAddressForm = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
    addressForm.value = true;
    avatarElement.value?.hide();
};

const goToOrder = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
    addressForm.value = false;
    router.push({ name: "customer-order" });
    avatarElement.value?.hide();
};

const goToInvoice = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
    addressForm.value = false;
    router.push({ name: "customer-invoice" });
    avatarElement.value?.hide();
};

const openSecurityForm = () => {
    loginFormVisible.value = false;
    registerFormVisible.value = false;
    addressForm.value = false;
    avatarElement.value?.hide();
    nextTick(() => {
        securityForm.value = true;
    });
};

const goToBrowseProducts = () => {
    router.push({ name: "customer.browse-products" });
};

const goToProfile = () => {
    router.push({ name: "customer.profile" });
    avatarElement.value?.hide();
};

const handleSearch = () => {
    console.log('🔎 CustomerIndex handleSearch called:', {
        formSearch: form.search,
        trimmed: form.search ? form.search.trim() : '',
        currentRoute: router.currentRoute.value.name,
        currentQuery: router.currentRoute.value.query.q
    });
    
    if (form.search && form.search.trim()) {
        console.log('➡️ CustomerIndex: Navigating to search with query:', form.search);
        router.push({
            name: 'customer.search-product',
            query: { q: form.search }
        });
    } else {
        console.log('⬅️ CustomerIndex: Navigating to browse (empty search)');
        // If search is empty, redirect to browse all products
        router.push({
            name: 'customer.browse-products'
        });
    }
};

const loadService = useAxiosUtil<null, IOrderNotification[]>();
const toast = useToast();

const loadNotifications = async () => {
    await loadService.get("customer/order-notifications").then(() => {
        if (loadService.request.status === 200 && loadService.request.data) {
            notifications.value = loadService.request.data;
        } else {
            toast.error(loadService.request.message ?? "Failed to load notifications");
        }
    });
};

let leave: (() => void) | null = null;

if (Page.user) {
    const echoResult = useEcho(
        `order.${Page.user.user_id}`,
        [".customer-order.event"],
        (value: IOrderNotification) => {
            notifications.value.unshift(value);
        },
    );
    leave = echoResult.leave;
}

// Watch for route query changes to sync search input with URL
watch(() => router.currentRoute.value.query.q, (newQuery, oldQuery) => {
    console.log('🌐 CustomerIndex route.query.q watcher:', {
        newQuery,
        oldQuery,
        currentFormSearch: form.search,
        currentRoute: router.currentRoute.value.name
    });
    
    isUpdatingFromRouteNavbar = true;
    
    if (newQuery && typeof newQuery === 'string') {
        console.log('✅ CustomerIndex: Setting form.search to:', newQuery);
        form.search = newQuery;
    } else {
        console.log('🧹 CustomerIndex: Clearing form.search');
        form.search = '';
    }
}, { immediate: true });

onMounted(() => {
    if (Page.user) {
        loadNotifications();
    }
    window.addEventListener('open-security-form', openSecurityForm);
    window.addEventListener('open-address-form', openAddressForm);
    window.addEventListener('go-to-profile', goToProfile);
});

onUnmounted(() => {
    if (leave) {
        leave();
    }
    if (clickOutsideListener) {
        document.removeEventListener('click', clickOutsideListener);
        clickOutsideListener = null;
    }
    window.removeEventListener('open-security-form', openSecurityForm);
    window.removeEventListener('open-address-form', openAddressForm);
    window.removeEventListener('go-to-profile', goToProfile);
});
</script>