<template>
    <div class="profile-view min-h-screen bg-gray-50 pb-10">
        <!-- Profile Header with Cover Image -->
        <div class="relative h-48 md:h-64 bg-slate-800 overflow-hidden group">
            <img 
                v-if="Page.user?.cover_image" 
                :src="UrlUtil.getBaseAppUrl(`storage/images/cover/${Page.user.cover_image}`)" 
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            >
            <div v-else class="w-full h-full bg-gradient-to-r from-blue-600 to-indigo-700 opacity-80"></div>
            
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>
            
            <button 
                @click="triggerCoverUpload" 
                class="absolute top-4 right-4 bg-white/90 hover:bg-white text-gray-800 px-4 py-2 rounded-lg shadow-sm border border-gray-200 flex items-center gap-2 text-sm font-bold transition-all active:scale-95"
            >
                <i class="pi pi-camera"></i>
                <span>Update Cover</span>
            </button>
            <input type="file" ref="coverInput" class="hidden" @change="onCoverChange" accept="image/*">
        </div>

        <div class="max-w-6xl mx-auto px-4 -mt-16 md:-mt-24 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Sidebar: User Summary -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col items-center text-center">
                        <div class="relative group mb-4">
                            <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white shadow-md overflow-hidden bg-gray-100 flex items-center justify-center">
                                <img 
                                    v-if="Page.user?.image" 
                                    :src="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.image}`)" 
                                    class="w-full h-full object-cover"
                                >
                                <span v-else class="text-5xl font-bold text-gray-300">{{ Page.user?.full_name?.charAt(0) }}</span>
                            </div>
                            <button 
                                @click="triggerAvatarUpload" 
                                class="absolute bottom-1 right-1 bg-blue-600 text-white p-2.5 rounded-full shadow-lg hover:bg-blue-700 transition-transform hover:scale-110 active:scale-95 border-2 border-white"
                            >
                                <i class="pi pi-pencil text-xs"></i>
                            </button>
                            <input type="file" ref="avatarInput" class="hidden" @change="onAvatarChange" accept="image/*">
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 leading-tight mb-1">{{ Page.user?.full_name }}</h2>
                        <p class="text-gray-500 font-medium text-sm mb-6">{{ Page.user?.email }}</p>
                        
                        <div class="w-full space-y-2">
                            <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Account Role</span>
                                <span class="text-sm font-bold text-blue-600">{{ Page.user?.role?.role_name }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Verification Status</span>
                                <div class="flex items-center gap-1.5 text-emerald-600">
                                    <i class="pi pi-check-circle text-xs"></i>
                                    <span class="text-sm font-bold">Verified</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Member Since</span>
                                <span class="text-sm font-bold text-gray-700">{{ Page.user?.created_at ? DateUtil.formatToMonthDayYear(Page.user.created_at) : '--' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Security Status -->
                    <div class="bg-gray-900 rounded-2xl p-6 text-white shadow-lg overflow-hidden">
                        <h3 class="text-base font-bold mb-5 flex items-center gap-2">
                            <i class="pi pi-shield text-blue-400"></i>
                            <span>Security Health</span>
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1.5">
                                    <span class="text-gray-400">Password Strength</span>
                                    <span class="text-emerald-400 uppercase tracking-tight">Healthy</span>
                                </div>
                                <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-full w-[85%]"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1.5">
                                    <span class="text-gray-400">Recovery Setup</span>
                                    <span :class="Page.user?.security_question ? 'text-emerald-400' : 'text-amber-400'" class="uppercase tracking-tight">
                                        {{ Page.user?.security_question ? 'Configured' : 'Incomplete' }}
                                    </span>
                                </div>
                                <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                                    <div :class="Page.user?.security_question ? 'bg-emerald-500 w-[100%]' : 'bg-amber-500 w-[20%]'" class="h-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Section: Forms -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Basic Information -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100">
                        <div class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900">Personal Details</h2>
                            <p class="text-gray-500 text-sm font-medium">Update your profile name and basic information.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Full Name</label>
                                <div class="relative">
                                    <input v-model="profileForm.full_name" type="text" class="form-input" placeholder="Enter your full name">
                                    <i class="pi pi-user absolute right-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                                </div>
                            </div>
                            <div class="space-y-2 opacity-70">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Email (Read Only)</label>
                                <div class="relative">
                                    <input :value="Page.user?.email" type="email" readonly class="form-input bg-gray-50 cursor-not-allowed" placeholder="email@example.com">
                                    <i class="pi pi-envelope absolute right-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <Button @click="updateName" :loading="savingName" label="Save Changes" icon="pi pi-check" class="p-button-primary rounded-xl font-bold px-6" />
                        </div>
                    </div>

                    <!-- Contact & Location Registry -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Contact & Address</h2>
                                <p class="text-gray-500 text-sm font-medium">Your primary delivery and communication registry.</p>
                            </div>
                            <Button @click="openAddressForm" icon="pi pi-map-marker" label="Modify Address" severity="secondary" text class="font-bold text-sm" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Primary Contact</label>
                                    <div class="flex items-center gap-3 text-gray-900 font-bold">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                            <i class="pi pi-phone"></i>
                                        </div>
                                        <span>{{ Page.user?.address?.contact_number || 'No contact number provided' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Postal System</label>
                                    <div class="flex items-center gap-3 text-gray-900 font-bold">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                            <i class="pi pi-map"></i>
                                        </div>
                                        <span>{{ Page.user?.address?.postal_code || '--' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Detailed Location</label>
                                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 space-y-3">
                                    <div class="flex gap-3">
                                        <i class="pi pi-directions text-gray-400 mt-1"></i>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ Page.user?.address?.other_details || 'Address details missing' }}</p>
                                            <p v-if="Page.user?.address?.barangay" class="text-xs font-medium text-gray-500 mt-1 uppercase tracking-tight">
                                                Brgy. {{ Page.user.address.barangay.barangay_name }}, 
                                                {{ Page.user.address.barangay.municity?.municity_name }},
                                                {{ Page.user.address.barangay.municity?.province?.province_name }}
                                            </p>
                                            <p v-if="Page.user?.address?.barangay?.municity?.province?.region" class="text-[10px] font-bold text-blue-600/60 mt-2 uppercase">
                                                {{ Page.user.address.barangay.municity.province.region.region_name }} | 
                                                {{ Page.user.address.barangay.municity.province.region.island_group?.island_name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password Update -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100">
                        <div class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900">Change Password</h2>
                            <p class="text-gray-500 text-sm font-medium">Reset your authentication credentials to stay secure.</p>
                        </div>

                        <div class="space-y-6 max-w-2xl">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Current Password</label>
                                <Password v-model="passwordForm.current_password" toggleMask fluid :feedback="false" placeholder="Required for verification" pt:input:class="form-input" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">New Password</label>
                                    <Password v-model="passwordForm.password" toggleMask fluid placeholder="At least 8 characters" pt:input:class="form-input" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Confirm New Password</label>
                                    <Password v-model="passwordForm.password_confirmation" toggleMask fluid :feedback="false" placeholder="Repeat new password" pt:input:class="form-input" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <Button @click="updatePassword" :loading="savingPassword" label="Update Password" icon="pi pi-lock" class="p-button-secondary rounded-xl font-bold px-6" />
                        </div>
                    </div>

                    <!-- Account Recovery -->
                    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100">
                        <div class="mb-8">
                            <h2 class="text-xl font-bold text-gray-900">Account Recovery</h2>
                            <p class="text-gray-500 text-sm font-medium">Methods to regain access if you forget your password.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-5 rounded-xl bg-blue-50 border border-blue-100 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-bold text-blue-900 text-xs uppercase tracking-wider mb-2">Security Question</h4>
                                    <p class="text-blue-800/80 text-sm font-medium leading-relaxed mb-4">
                                        {{ Page.user?.security_question || 'Not yet configured. Recommended for manual account recovery.' }}
                                    </p>
                                </div>
                                <Button @click="showQuestionDialog = true" :label="Page.user?.security_question ? 'Update Question' : 'Setup Question'" text class="font-bold text-sm p-0 justify-start" />
                            </div>

                            <div class="p-5 rounded-xl bg-purple-50 border border-purple-100 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-bold text-purple-900 text-xs uppercase tracking-wider mb-2">Recovery Codes</h4>
                                    <p class="text-purple-800/80 text-sm font-medium leading-relaxed mb-4">
                                        One-time use backup codes for emergency access.
                                    </p>
                                </div>
                                <Button @click="showCodesDialog = true" label="View Codes" text class="font-bold text-sm p-0 justify-start" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Question Dialog -->
        <Dialog v-model:visible="showQuestionDialog" modal header="Set Security Question" :style="{ width: '30rem' }" :breakpoints="{ '575px': '90vw' }" pt:root:class="rounded-2xl" pt:header:class="p-6 border-b border-gray-100" pt:content:class="p-6" pt:footer:class="p-6 border-t border-gray-100">
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Your Question</label>
                    <input v-model="questionForm.customQuestion" type="text" class="form-input" placeholder="e.g. What was your first pet's name?">
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Your Secret Answer</label>
                    <Password v-model="questionForm.answer" toggleMask fluid :feedback="false" placeholder="Enter answer here" pt:input:class="form-input" />
                    <p class="text-[11px] text-gray-400 font-medium mt-1">Answers are case-insensitive and securely encrypted.</p>
                </div>
            </div>
            <template #footer>
                <div class="flex gap-3">
                    <Button @click="showQuestionDialog = false" label="Cancel" text severity="secondary" class="rounded-xl font-bold" />
                    <Button @click="saveQuestion" :loading="savingQuestion" label="Save Security Question" class="rounded-xl font-bold flex-1" />
                </div>
            </template>
        </Dialog>

        <!-- Recovery Codes Dialog -->
        <Dialog v-model:visible="showCodesDialog" modal header="Emergency Recovery Codes" :style="{ width: '36rem' }" :breakpoints="{ '575px': '90vw' }" pt:root:class="rounded-2xl" pt:header:class="p-6 border-b border-gray-100" pt:content:class="p-6" pt:footer:class="p-6 border-t border-gray-100">
            <div class="space-y-6">
                <div v-if="recoveryCodes.length > 0" class="grid grid-cols-2 gap-3">
                    <div v-for="(code, index) in recoveryCodes" :key="index" class="flex items-center justify-between p-3.5 bg-gray-50 border border-gray-100 rounded-xl">
                        <code class="text-sm font-bold text-gray-700 font-mono tracking-wider">{{ code }}</code>
                        <button @click="copyToClipboard(code)" class="text-gray-400 hover:text-blue-600 transition-colors">
                            <i class="pi pi-copy"></i>
                        </button>
                    </div>
                </div>
                <div v-else class="text-center py-10">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="pi pi-lock text-2xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Generate Backup Keys</h3>
                    <p class="text-gray-500 text-sm font-medium mt-1 mb-8">Each code can only be used once to recover your account.</p>
                    <Button @click="generateCodes" :loading="generatingCodes" label="Generate Codes" icon="pi pi-key" class="rounded-xl font-bold px-8" />
                </div>

                <div v-if="recoveryCodes.length > 0" class="p-4 bg-amber-50 rounded-xl border border-amber-100 flex gap-3">
                    <i class="pi pi-info-circle text-amber-600 text-lg flex-shrink-0"></i>
                    <p class="text-[11px] text-amber-800 font-medium leading-relaxed">
                        Store these codes securely. If you use a code, it becomes invalid. We recommend downloading or copying them to a safe location.
                    </p>
                </div>
            </div>
            <template #footer v-if="recoveryCodes.length > 0">
                <div class="flex gap-3 w-full">
                    <Button @click="downloadCodes" label="Save as TXT" icon="pi pi-download" severity="secondary" outlined class="rounded-xl font-bold flex-1" />
                    <Button @click="confirmRegenerate" label="Regenerate All" icon="pi pi-refresh" severity="danger" class="rounded-xl font-bold flex-1" />
                </div>
            </template>
        </Dialog>

        <!-- Image Cropper Dialog -->
        <Dialog v-model:visible="showCropper" modal :header="cropMode === 'avatar' ? 'Crop Profile Image' : 'Crop Cover Image'" :style="{ width: '34rem' }" :breakpoints="{ '575px': '90vw' }" pt:root:class="rounded-2xl" pt:footer:class="p-6 border-t border-gray-100">
            <div class="p-4 bg-gray-50" v-if="selectedFile">
                <VuePictureCropper
                    :boxStyle="{ width: '100%', height: '350px', backgroundColor: '#f8fafc', margin: 'auto' }"
                    :img="selectedFile"
                    :options="{ 
                        viewMode: 1, 
                        dragMode: 'move', 
                        aspectRatio: cropMode === 'avatar' ? 1 : 16/6, 
                        cropBoxResizable: true, 
                    }"
                />
            </div>
            <template #footer>
                <div class="flex gap-3">
                    <Button @click="showCropper = false" label="Cancel" text severity="secondary" class="rounded-xl font-bold" />
                    <Button @click="uploadCroppedImage" :loading="uploadingImage" label="Save Selection" class="rounded-xl font-bold flex-1" />
                </div>
            </template>
        </Dialog>

        <ConfirmDialog pt:root:class="rounded-2xl" pt:header:class="font-bold px-6 pt-6" pt:content:class="px-6 pb-4" pt:footer:class="px-6 pb-6" />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
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

const toast = useToast()
const confirm = useConfirm()
const profileService = useAxiosUtil()

// State
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

onMounted(() => {
    if (Page.user?.recovery_codes) recoveryCodes.value = Page.user.recovery_codes
})
</script>

<style scoped>
.form-input {
    width: 100%; background-color: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem;
    padding: 0.75rem 1rem; font-weight: 500; color: #111827; transition: all 0.2s ease;
}
.form-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
:deep(.p-password-input) { border-radius: 0.75rem !important; }
</style>
