<template>
    <header>
        <nav class="relative shadow-bottom-float z-20 bg-white p-5 flex">
            <div class="grow">
                <Button type="button" variant="link" raised icon="pi pi-bars" class="text-2xl text-gray-500!"
                    @click="sideBar = !sideBar" />
            </div>
            <div class="flex items-center justify-end grow px-2">
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
            </div>
        </nav>
    </header>
</template>
<script setup lang="ts">
import { Popover } from 'primevue';
import { inject, ref } from 'vue';

const sideBar = inject('sideBar');
const notificationElement = ref<null | InstanceType<typeof Popover>>();
const openNotification = (event: Event) => {
    if (notificationElement.value) {
        notificationElement.value.toggle(event);
    }
}
</script>