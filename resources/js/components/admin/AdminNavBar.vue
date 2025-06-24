<template>
    <header>
        <nav class="relative shadow-bottom-float z-20 bg-white p-5 flex">
            <div class="grow">
                <Button type="button" variant="link" raised icon="pi pi-bars" class="text-2xl text-gray-500!"
                    @click="sideBar = !sideBar" />
            </div>
            <div class="flex items-center justify-end grow px-2 gap-3">
                <div>
                    <OverlayBadge
                        :value="0"
                        pt:pcBadge:root:class="bg-sky-800! text-white"
                    >
                        <Button 
                            type="button"
                            @click="openNotification"
                            icon="pi pi-bell"
                            class="bg-sky-800! text-white"
                        />
                    </OverlayBadge>
                    <Popover 
                        ref="notificationElement" 
                        :dismissable="true"
                        class="inline-flex mr-5"
                    >
                        <div>
                            Notifications
                        </div>
                    </Popover>
                </div>
                <div>
                    <button 
                        type="button"
                        class="cursor-pointer"
                        @click="openAvatar"
                    >
                        <Avatar
                            v-if="Page.user && Page.user.image"
                            :image="Page.user.image"
                            size="large"
                            shape="circle"
                        />
                        <Avatar
                            v-else
                            :label="Page.user && Page.user.full_name[0]"
                            size="large"
                            shape="circle"
                            class="bg-sky-800! text-white!"
                        />
                    </button>
                    <Popover 
                        ref="avatarElement" 
                        :dismissable="true"
                        class="inline-flex mr-5"
                    >
                        <div>
                            <LogoutButton />
                        </div>
                    </Popover>
                </div>
            </div>
        </nav>
    </header>
</template>
<script setup lang="ts">
import Page from '@/stores/Page';
import { Popover } from 'primevue';
import { inject, ref } from 'vue';
import LogoutButton from '@/components/LogoutButton.vue';

const sideBar = inject('sideBar');
const notificationElement = ref<null | InstanceType<typeof Popover>>();
const avatarElement = ref<null | InstanceType<typeof Popover>>();

const openNotification = (event: Event) => {
    if (notificationElement.value) {
        notificationElement.value.toggle(event);
    }
}

const openAvatar = (event: Event) => {
    if (avatarElement.value) {
        avatarElement.value.toggle(event);
    }
}
</script>