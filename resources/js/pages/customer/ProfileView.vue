<template>
    <div class="profile-page min-h-screen bg-slate-50/50 pb-12">
        <!-- Profile Header -->
        <div class="relative h-64 bg-slate-900 overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <div class="hero-pattern"></div>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-50/50 to-transparent"></div>
        </div>

        <div class="container mx-auto px-4 -mt-32 relative z-10">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <!-- Sidebar / Basic Info -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 text-center">
                        <div class="relative inline-block group mb-6">
                            <div class="w-32 h-32 rounded-full border-4 border-white shadow-xl overflow-hidden bg-slate-100 flex items-center justify-center">
                                <img v-if="Page.user?.image" :src="UrlUtil.getBaseAppUrl(`storage/images/profile/${Page.user.image}`)" class="w-full h-full object-cover">
                                <span v-else class="text-4xl font-black text-slate-300">{{ Page.user?.full_name?.charAt(0) }}</span>
                            </div>
                            <button @click="triggerFileUpload" class="absolute bottom-0 right-0 bg-blue-600 text-white p-3 rounded-2xl shadow-lg hover:bg-blue-700 transition-all group-hover:scale-110 active:scale-95">
                                <i class="pi pi-camera"></i>
                            </button>
                            <input type="file" ref="fileInput" class="hidden" @change="onFileChange" accept="image/*">
                        </div>

                        <h2 class="text-2xl font-black text-slate-900">{{ Page.user?.full_name }}</h2>
                        <p class="text-slate-500 font-medium mb-6">{{ Page.user?.email }}</p>
                        
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Role</span>
                                <span class="text-sm font-black text-blue-600 uppercase">{{ Page.user?.role?.role_name }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Status</span>
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-sm font-black text-emerald-600 uppercase">Verified</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Overview -->
                    <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-xl shadow-slate-200">
                        <h3 class="text-lg font-bold mb-6 italic">Security Health</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-400 font-medium">Password</span>
                                <span class="text-emerald-400 font-bold uppercase tracking-tighter">Strong</span>
                            </div>
                            <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full w-[85%]"></div>
                            </div>
                            
                            <div class="pt-4 flex items-center justify-between text-sm">
                                <span class="text-slate-400 font-medium">Security Question</span>
                                <span :class="Page.user?.security_question ? 'text-emerald-400' : 'text-amber-400'" class="font-bold uppercase tracking-tighter">
                                    {{ Page.user?.security_question ? 'Enabled' : 'Missing' }}
                                </span>
                            </div>
                            <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                                <div :class="Page.user?.security_question ? 'bg-emerald-500 w-[100%]' : 'bg-amber-500 w-[20%]'" class="h-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- Profile Management Card -->
                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-2xl font-black text-slate-900 italic">Account Matrix</h2>
                                <p class="text-slate-400 text-sm font-medium">Manage your core identity data</p>
                            </div>
                            <i class="pi pi-user-edit text-blue-600 text-2xl"></i>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                                <div class="relative">
                                    <input v-model="profileForm.full_name" type="text" class="profile-input" placeholder="Enter your full name">
                                    <i class="pi pi-user absolute right-4 top-4 text-slate-300"></i>
                                </div>
                            </div>
                            <div class="space-y-2 opacity-60 cursor-not-allowed">
                                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Email Address (Registry Only)</label>
                                <div class="relative">
                                    <input :value="Page.user?.email" type="email" readonly class="profile-input bg-slate-50" placeholder="email@example.com">
                                    <i class="pi pi-envelope absolute right-4 top-4 text-slate-300"></i>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end">
                            <Button @click="updateName" :loading="savingName" label="Synchronize Identity" icon="pi pi-sync" class="p-button-lg rounded-2xl font-black uppercase tracking-widest px-8 py-4" />
                        </div>
                    </div>

                    <!-- Security Matrix -->
                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-2xl font-black text-slate-900 italic">Security Decryption</h2>
                                <p class="text-slate-400 text-sm font-medium">Update authentication credentials</p>
                            </div>
                            <i class="pi pi-shield text-purple-600 text-2xl"></i>
                        </div>

                        <div class="space-y-6 max-w-xl">
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Current Password</label>
                                <Password v-model="passwordForm.current_password" toggleMask fluid :feedback="false" placeholder="Required for verification" pt:input:class="profile-input" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">New Password</label>
                                    <Password v-model="passwordForm.password" toggleMask fluid placeholder="New hash key" pt:input:class="profile-input" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Confirm New</label>
                                    <Password v-model="passwordForm.password_confirmation" toggleMask fluid :feedback="false" placeholder="Sync hash" pt:input:class="profile-input" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end">
                            <Button @click="updatePassword" :loading="savingPassword" label="Update Credentials" icon="pi pi-lock" severity="help" class="p-button-lg rounded-2xl font-black uppercase tracking-widest px-8 py-4" />
                        </div>
                    </div>

                    <!-- Recovery Matrix -->
                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-2xl font-black text-slate-900 italic">Recovery Protocols</h2>
                                <p class="text-slate-400 text-sm font-medium">Configure fallback access methods</p>
                            </div>
                            <i class="pi pi-key text-amber-600 text-2xl"></i>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="p-6 rounded-3xl bg-amber-50 border border-amber-100 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-black text-amber-900 uppercase tracking-widest text-xs mb-2">Security Question</h4>
                                    <p class="text-amber-800/70 text-sm font-medium leading-relaxed">
                                        {{ Page.user?.security_question || 'No security protocol established for manual recovery.' }}
                                    </p>
                                </div>
                                <Button @click="showQuestionDialog = true" :label="Page.user?.security_question ? 'Reconfigure' : 'Establish Protocol'" severity="warning" text class="font-black uppercase tracking-widest text-xs mt-6 text-left p-0 justify-start" />
                            </div>

                            <div class="p-6 rounded-3xl bg-purple-50 border border-purple-100 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-black text-purple-900 uppercase tracking-widest text-xs mb-2">Recovery Codes</h4>
                                    <p class="text-purple-800/70 text-sm font-medium leading-relaxed">
                                        Offline backup keys for administrative override in case of logic failure.
                                    </p>
                                </div>
                                <Button @click="showCodesDialog = true" label="Access Manifest" severity="help" text class="font-black uppercase tracking-widest text-xs mt-6 text-left p-0 justify-start" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Question Dialog -->
        <Dialog v-model:visible="showQuestionDialog" modal header="Security Protocol Setup" :style="{ width: '32rem' }" :breakpoints="{ '575px': '95vw' }" pt:header:class="p-8 border-b border-slate-100" pt:content:class="p-8" pt:footer:class="p-8 border-t border-slate-100">
            <div class="space-y-8">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Select Question</label>
                    <Select v-model="questionForm.question" :options="predefinedQuestions" placeholder="Choose a protocol question" fluid class="rounded-2xl border-slate-200" />
                </div>
                
                <div class="relative py-2">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
                    <div class="relative flex justify-center"><span class="bg-white px-4 text-xs font-black text-slate-300 uppercase italic">Or create manual</span></div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Custom Question</label>
                    <input v-model="questionForm.customQuestion" type="text" class="profile-input" placeholder="Override with manual question">
                </div>
                
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Secure Answer</label>
                    <Password v-model="questionForm.answer" toggleMask fluid :feedback="false" placeholder="Define secret hash" pt:input:class="profile-input" />
                    <p class="text-[10px] text-slate-400 font-medium italic mt-2">* Answers are case-insensitive and hashed before transport.</p>
                </div>
            </div>
            <template #footer>
                <div class="flex gap-4">
                    <Button @click="showQuestionDialog = false" label="Abort" text severity="secondary" class="rounded-2xl font-black uppercase tracking-widest" />
                    <Button @click="saveQuestion" :loading="savingQuestion" label="Secure Protocol" class="rounded-2xl font-black uppercase tracking-widest px-10 flex-1 py-4" />
                </div>
            </template>
        </Dialog>

        <!-- Recovery Codes Dialog -->
        <Dialog v-model:visible="showCodesDialog" modal header="Recovery Manifest" :style="{ width: '40rem' }" :breakpoints="{ '575px': '95vw' }" pt:header:class="p-8 border-b border-slate-100" pt:content:class="p-8" pt:footer:class="p-8 border-t border-slate-100">
            <div class="space-y-6">
                <div v-if="recoveryCodes.length > 0" class="grid grid-cols-2 gap-3">
                    <div v-for="(code, index) in recoveryCodes" :key="index" class="flex items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl group transition-all hover:border-slate-300">
                        <code class="text-sm font-black text-slate-600 font-mono tracking-wider">{{ code }}</code>
                        <button @click="copyToClipboard(code)" class="text-slate-300 hover:text-blue-600 transition-colors">
                            <i class="pi pi-copy"></i>
                        </button>
                    </div>
                </div>
                <div v-else class="text-center py-12">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="pi pi-lock text-3xl text-slate-300"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase">No Overflow Codes</h3>
                    <p class="text-slate-400 text-sm font-medium mt-2 mb-8">Generate unique recovery keys for emergency bypass.</p>
                    <Button @click="generateCodes" :loading="generatingCodes" label="Init Manifest Generation" icon="pi pi-key" class="rounded-2xl font-black uppercase tracking-widest px-8" />
                </div>

                <div v-if="recoveryCodes.length > 0" class="p-6 bg-amber-50 rounded-3xl border border-amber-100 flex gap-4">
                    <i class="pi pi-info-circle text-amber-600 text-xl flex-shrink-0"></i>
                    <p class="text-xs text-amber-800 font-medium leading-relaxed italic">
                        Warning: Each code is authorized for a single execution cycle only. Once utilized, it will be purged from the security lattice.
                    </p>
                </div>
            </div>
            <template #footer v-if="recoveryCodes.length > 0">
                <div class="flex gap-4">
                    <Button @click="downloadCodes" label="Export PDF" severity="secondary" outlined class="rounded-2xl font-black uppercase tracking-widest flex-1 py-4" />
                    <Button @click="confirmRegenerate" label="Regenerate Keys" severity="danger" class="rounded-2xl font-black uppercase tracking-widest flex-1 py-4" />
                </div>
            </template>
        </Dialog>

        <ConfirmDialog pt:header:class="font-black italic px-8 pt-8" pt:content:class="px-8 pb-4" pt:footer:class="px-8 pb-8" />
        
        <!-- Image Cropper Dialog -->
        <Dialog v-model:visible="showCropper" modal header="Avatar Matrix" :style="{ width: '32rem' }" :breakpoints="{ '575px': '95vw' }" pt:footer:class="p-8 border-t border-slate-100">
            <div class="p-4" v-if="selectedFile">
                <VuePictureCropper
                    :boxStyle="{ width: '100%', height: '300px', backgroundColor: '#f8fafc', margin: 'auto' }"
                    :img="selectedFile"
                    :options="{ viewMode: 1, dragMode: 'move', aspectRatio: 1, cropBoxResizable: true, }"
                />
            </div>
            <template #footer>
                <div class="flex gap-4">
                    <Button @click="showCropper = false" label="Cancel" text severity="secondary" class="rounded-2xl font-black uppercase tracking-widest" />
                    <Button @click="uploadCroppedImage" :loading="uploadingImage" label="Apply Avatar" class="rounded-2xl font-black uppercase tracking-widest px-10 flex-1 py-4" />
                </div>
            </template>
        </Dialog>
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
    Button, Dialog, Select, Password, 
    ConfirmDialog, Tag, Divider 
} from 'primevue'

const toast = useToast()
const confirm = useConfirm()
const profileService = useAxiosUtil()

// State
const profileForm = reactive({ full_name: Page.user?.full_name || '' })
const passwordForm = reactive({ current_password: '', password: '', password_confirmation: '' })
const questionForm = reactive({ question: null as string | null, customQuestion: '', answer: '' })
const recoveryCodes = ref<string[]>([])

const savingName = ref(false)
const savingPassword = ref(false)
const savingQuestion = ref(false)
const generatingCodes = ref(false)
const uploadingImage = ref(false)

const showQuestionDialog = ref(false)
const showCodesDialog = ref(false)
const showCropper = ref(false)
const selectedFile = ref<string | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)

const predefinedQuestions = [
    "What was the name of your first pet?",
    "In what city were you born?",
    "What is your mother's maiden name?",
    "What was the name of your first school?",
    "What is the name of your favorite childhood friend?",
    "What was the make of your first car?",
    "What is your favorite book?",
]

// Methods
const triggerFileUpload = () => fileInput.value?.click()

const onFileChange = (e: Event) => {
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
        const file = new File([blob], 'cropped-avatar.png', { type: 'image/png' })
        const formData = new FormData()
        formData.append('image', file)
        
        // Axios util doesn't support FormData directly in patch sometimes, but let's try
        await profileService.post(`customer/profile/image?_method=PATCH`, formData).then(() => {
            uploadingImage.value = false
            if (profileService.request.status === 200) {
                toast.success('Avatar matrix updated.')
                showCropper.value = false
                if (Page.user) Page.user.image = profileService.request.data.image
            } else {
                toast.error(profileService.request.message || 'Logic failure in upload.')
            }
        })
    }
}

const updateName = async () => {
    if (!profileForm.full_name) return toast.error('Full name is mission critical.')
    savingName.value = true
    await profileService.patch('customer/profile/name', { full_name: profileForm.full_name }).then(() => {
        savingName.value = false
        if (profileService.request.status === 200) {
            toast.success('Identity synchronized.')
            if (Page.user) Page.user.full_name = profileForm.full_name
        } else {
            toast.error(profileService.request.message || 'Sync failure.')
        }
    })
}

const updatePassword = async () => {
    if (!passwordForm.current_password || !passwordForm.password) return toast.error('Hash requirements not met.')
    if (passwordForm.password !== passwordForm.password_confirmation) return toast.error('Hash mismatch.')
    
    savingPassword.value = true
    await profileService.patch('customer/profile/password', passwordForm).then(() => {
        savingPassword.value = false
        if (profileService.request.status === 200) {
            toast.success('Credentials decrypted and updated.')
            passwordForm.current_password = ''
            passwordForm.password = ''
            passwordForm.password_confirmation = ''
        } else {
            toast.error(profileService.request.message || 'Credential failure.')
        }
    })
}

const saveQuestion = async () => {
    const finalQuestion = questionForm.customQuestion || questionForm.question
    if (!finalQuestion || !questionForm.answer) return toast.error('Protocol requires both question and hash answer.')
    
    savingQuestion.value = true
    await profileService.post('security/settings', {
        security_question: finalQuestion,
        security_answer: questionForm.answer
    }).then(() => {
        savingQuestion.value = false
        if (profileService.request.status === 200) {
            toast.success('Protocol established.')
            showQuestionDialog.value = false
            if (Page.user) Page.user.security_question = finalQuestion
        } else {
            toast.error(profileService.request.message || 'Protocol failure.')
        }
    })
}

const generateCodes = async () => {
    generatingCodes.value = true
    await profileService.post('security/settings', { generate_codes: true }).then(() => {
        generatingCodes.value = false
        if (profileService.request.status === 200) {
            recoveryCodes.value = profileService.request.data.recovery_codes
            toast.success('Manifest generated.')
        }
    })
}

const confirmRegenerate = () => {
    confirm.require({
        header: 'Override Keys?',
        message: 'Logic suggests regenerating keys will purge all existing authorizations. Proceed?',
        acceptLabel: 'Confirm Purge',
        acceptClass: 'p-button-danger rounded-xl',
        rejectLabel: 'Abort',
        rejectClass: 'rounded-xl',
        accept: () => generateCodes()
    })
}

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text)
    toast.info('Hash copied to terminal.')
}

const downloadCodes = () => {
    const content = recoveryCodes.value.join('\n')
    const blob = new Blob([content], { type: 'text/plain' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `recovery-manifest-${new Date().getTime()}.txt`
    a.click()
    toast.success('Manifest exported.')
}

onMounted(() => {
    if (Page.user?.recovery_codes) recoveryCodes.value = Page.user.recovery_codes
})
</script>

<style scoped>
.hero-pattern { background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 24px 24px; width: 100%; height: 100%; }
.profile-input {
    width: 100%; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1.25rem;
    padding: 1rem 1.25rem; font-weight: 600; color: #1e293b; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.profile-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
:deep(.p-password-input) { border-radius: 1.25rem !important; }
</style>
