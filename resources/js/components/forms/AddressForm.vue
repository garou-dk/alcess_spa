<template>
    <div class="py-3 sm:py-4 px-2 sm:px-0">
        <form v-if="!loadAddressService.request.loading" class="space-y-4 sm:space-y-5" @submit.prevent="handleSubmit()">
            <!-- Location Selection Card -->
            <div class="rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-3 sm:px-4 py-3">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-map text-blue-600 text-lg"></i>
                        <h2 class="text-base sm:text-lg font-bold text-gray-800">Location Details</h2>
                    </div>
                </div>
                <div class="p-3 sm:p-4 space-y-4">
                    <div>
                        <InputForm
                            :errors="[]"
                            label-name="Island Group*"
                            id="island_group"
                            tag="span"
                        >
                            <Select
                                v-model="selectedIslandGroup"
                                :options="islandGroups"
                                option-label="island_group_name"
                                placeholder="Select Island Group"
                                filter
                                show-clear
                                :loading="loadIslandGroupService.request.loading"
                                class="w-full"
                            />
                        </InputForm>
                    </div>
                    <div>
                        <InputForm
                            :errors="[]"
                            label-name="Region*"
                            id="region"
                            tag="span"
                        >
                            <Select
                                v-model="selectedRegion"
                                :options="regions"
                                option-label="region_name"
                                placeholder="Select Region"
                                filter
                                show-clear
                                :loading="loadRegionService.request.loading"
                                class="w-full"
                            />
                        </InputForm>
                    </div>
                    <div>
                        <InputForm
                            :errors="[]"
                            label-name="Province*"
                            id="province"
                            tag="span"
                        >
                            <Select
                                v-model="selectedProvince"
                                :options="provinces"
                                option-label="province_name"
                                placeholder="Select Province"
                                filter
                                show-clear
                                :loading="loadProvinceService.request.loading"
                                class="w-full"
                            />
                        </InputForm>
                    </div>
                    <div>
                        <InputForm
                            :errors="[]"
                            label-name="Municipality/City*"
                            id="municity"
                            tag="span"
                        >
                            <Select
                                v-model="selectedMunicity"
                                :options="municities"
                                option-label="municity_name"
                                placeholder="Select Municipality/City"
                                filter
                                show-clear
                                :loading="loadMunicityService.request.loading"
                                class="w-full"
                            />
                        </InputForm>
                    </div>
                    <div>
                        <InputForm
                            :errors="errors.barangay_id"
                            label-name="Barangay*"
                            id="barangay"
                            tag="span"
                        >
                            <Select
                                v-model="selectedBarangay"
                                :options="barangays"
                                option-label="barangay_name"
                                placeholder="Select Barangay"
                                filter
                                show-clear
                                :invalid="errors.barangay_id.length > 0"
                                :loading="loadBarangayService.request.loading"
                                class="w-full"
                            />
                        </InputForm>
                    </div>
                </div>
            </div>

            <!-- Address Details Card -->
            <div class="rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-3 sm:px-4 py-3">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-home text-blue-600 text-lg"></i>
                        <h2 class="text-base sm:text-lg font-bold text-gray-800">Address Details</h2>
                    </div>
                </div>
                <div class="p-3 sm:p-4 space-y-4">
                    <div>
                        <InputForm
                            :errors="errors.other_details"
                            label-name="Unit / Block / Street / Purok / Sitio / Subdivision*"
                            id="other_details"
                            tag="label"
                        >
                            <Textarea
                                v-model="form.other_details"
                                id="other_details"
                                placeholder="Enter your unit / block / street / purok / sitio / subdivision"
                                :rows="3"
                                :invalid="errors.other_details.length > 0"
                                class="w-full"
                            />
                        </InputForm>
                    </div>
                    <div>
                        <InputForm
                            :errors="errors.postal_code"
                            label-name="Postal Code*"
                            id="postal_code"
                            tag="label"
                        >
                            <InputText
                                v-model="form.postal_code"
                                id="postal_code"
                                placeholder="Enter your postal code"
                                :invalid="errors.postal_code.length > 0"
                                class="w-full"
                            />
                        </InputForm>
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-3 sm:px-4 py-3">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-phone text-blue-600 text-lg"></i>
                        <h2 class="text-base sm:text-lg font-bold text-gray-800">Contact Information</h2>
                    </div>
                </div>
                <div class="p-3 sm:p-4">
                    <InputForm
                        :errors="errors.contact_number"
                        label-name="Contact Number*"
                        id="contact_number"
                        tag="label"
                    >
                        <InputText
                            v-model="form.contact_number"
                            id="contact_number"
                            placeholder="Enter your contact number (e.g., 09123456789)"
                            :invalid="errors.contact_number.length > 0"
                            pattern="^09\d{9}$"
                            inputmode="numeric"
                            class="w-full"
                        />
                    </InputForm>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center pt-2">
                <Button 
                    type="submit" 
                    label="Save Address" 
                    icon="pi pi-save" 
                    :loading="submitService.request.loading"
                    class="w-full sm:w-auto !bg-blue-600 hover:!bg-blue-700 !border-blue-600 text-white font-bold text-base sm:text-lg px-6 sm:px-8 py-2.5 sm:py-3 shadow-lg hover:shadow-xl transition-all duration-200"
                    :class="{ 'opacity-50': submitService.request.loading }"
                />
            </div>

            <!-- Info Notice -->
            <div class="text-center text-xs sm:text-sm text-gray-500">
                <span>Make sure your address details are accurate for delivery</span>
            </div>
        </form>
        <div v-else class="flex justify-center items-center py-12 sm:py-16">
            <div class="text-center">
                <PageLoader />
                <p class="text-gray-600 mt-4 text-sm sm:text-base">Loading address information...</p>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { IAddress } from '@/interfaces/IAddress';
import { IBarangay } from '@/interfaces/IBarangay';
import { IIslandGroup } from '@/interfaces/IIslandGroup';
import { IMunicity } from '@/interfaces/IMunicity';
import { IProvince } from '@/interfaces/IProvince';
import { IRegion } from '@/interfaces/IRegion';
import Page from '@/stores/Page';
import useAxiosUtil from '@/utils/AxiosUtil';
import { useResponsive } from '@/composables/useResponsive';
import { onMounted, reactive, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';

interface IForm {
    barangay_id: number | null;
    other_details: string | null;
    postal_code: string | null;
    contact_number: string | null;
    user_id: number;
}

interface IFormError {
    barangay_id: string[];
    other_details: string[];
    postal_code: string[];
    contact_number: string[];
    user_id: string[];
}

const toast = useToast();

const form = reactive<IForm>({
    barangay_id: null,
    other_details: null,
    contact_number: null,
    postal_code: null,
    user_id: Page.user.user_id
});

const errors = reactive<IFormError>({
    barangay_id: [],
    other_details: [],
    contact_number: [],
    postal_code: [],
    user_id: []
});
const loaded = ref(false);
const defaultData = ref<IAddress | null>(null);
const completeLoaded = ref([false, false, false, false, false]);

const isgLoaded = ref(false);
const loadIslandGroupService = useAxiosUtil<null, IIslandGroup[]>();
const islandGroups = ref<IIslandGroup[]>([]);
const selectedIslandGroup = ref<IIslandGroup | null>(null);
const loadIslandGroups = async () => {
    await loadIslandGroupService.get('island-groups')
        .then(() => {
            if (loadIslandGroupService.request.status === 200 && loadIslandGroupService.request.data) {
                islandGroups.value = loadIslandGroupService.request.data;
                if (defaultData.value && (!isgLoaded.value || completeLoaded.value.includes(false))) {
                    selectedIslandGroup.value = defaultData.value.barangay.municity.province.region.island_group;
                }
            }
            else {
                islandGroups.value = [];
                toast.error(loadIslandGroupService.request.message ?? 'Failed to load island groups.');
            }
        })
        .finally(() => {
            isgLoaded.value = true;
            completeLoaded.value[0] = true;
        });
};

const rLoaded = ref(false);
const loadRegionService = useAxiosUtil<null, IRegion[]>();
const regions = ref<IRegion[]>([]);
const selectedRegion = ref<IRegion | null>(null);
const loadRegions = async () => {
    await loadRegionService.get(`regions/${selectedIslandGroup.value.island_group_id}`)
        .then(() => {
            if (loadRegionService.request.status === 200 && loadRegionService.request.data) {
                regions.value = loadRegionService.request.data;
                if (defaultData.value && (!rLoaded.value || completeLoaded.value.includes(false))) {
                    const findRegion = regions.value.find(region => region.region_id === defaultData.value!.barangay.municity.province.region.region_id);
                    if (findRegion) {
                        selectedRegion.value = findRegion;
                    }
                }
            }
            else {
                regions.value = [];
                toast.error(loadRegionService.request.message ?? 'Failed to load regions.');
            }
        })
        .finally(() => {
            rLoaded.value = true;
            completeLoaded.value[1] = true;
        });
};

const pLoaded = ref(false);
const loadProvinceService = useAxiosUtil<null, IProvince[]>();
const provinces = ref<IProvince[]>([]);
const selectedProvince = ref<IProvince | null>(null);
const loadProvinces = async () => {
    await loadProvinceService.get(`provinces/${selectedRegion.value.region_id}`)
        .then(() => {
            if (loadProvinceService.request.status === 200 && loadProvinceService.request.data) {
                provinces.value = loadProvinceService.request.data;
                if (defaultData.value && (!pLoaded.value || completeLoaded.value.includes(false))) {
                    const findProvince = provinces.value.find(province => province.province_id === defaultData.value!.barangay.municity.province.province_id);
                    if (findProvince) {
                        selectedProvince.value = findProvince;
                    }
                }
            }
            else {
                provinces.value = [];
                toast.error(loadProvinceService.request.message ?? 'Failed to load provinces.');
            }
        })
        .finally(() => {
            pLoaded.value = true;
            completeLoaded.value[2] = true;
        });
};

const mLoaded = ref(false);
const loadMunicityService = useAxiosUtil<null, IMunicity[]>();
const municities = ref<IMunicity[]>([]);
const selectedMunicity = ref<IMunicity | null>(null);
const loadMunicities = async () => {
    await loadMunicityService.get(`municities/${selectedProvince.value.province_id}`)
        .then(() => {
            if (loadMunicityService.request.status === 200 && loadMunicityService.request.data) {
                municities.value = loadMunicityService.request.data;
                if (defaultData.value && (!mLoaded.value || completeLoaded.value.includes(false))) {
                    const findMunicity = municities.value.find(municity => municity.municity_id === defaultData.value!.barangay.municity.municity_id);
                    if (findMunicity) {
                        selectedMunicity.value = findMunicity;
                    }
                }
            }
            else {
                municities.value = [];
                toast.error(loadMunicityService.request.message ?? 'Failed to load municities.');
            }
        })
        .finally(() => {
            mLoaded.value = true;
            completeLoaded.value[3] = true;
        });
};

const bLoaded = ref(false);
const loadBarangayService = useAxiosUtil<null, IBarangay[]>();
const barangays = ref<IBarangay[]>([]);
const selectedBarangay = ref<IBarangay | null>(null);
const loadBarangays = async () => {
    await loadBarangayService.get(`barangays/${selectedMunicity.value.municity_id}`)
        .then(() => {
            if (loadBarangayService.request.status === 200 && loadBarangayService.request.data) {
                barangays.value = loadBarangayService.request.data;
                if (defaultData.value && (!bLoaded.value || completeLoaded.value.includes(false))) {
                    const findBarangay = barangays.value.find(barangay => barangay.barangay_id === defaultData.value!.barangay.barangay_id);
                    if (findBarangay) {
                        selectedBarangay.value = findBarangay;
                    }
                }
            }
            else {
                barangays.value = [];
                toast.error(loadBarangayService.request.message ?? 'Failed to load barangays.');
            }
        })
        .finally(() => {
            bLoaded.value = true;
            completeLoaded.value[4] = true;
        });
};

watch(selectedIslandGroup, () => {
    if (completeLoaded.value.every((loaded) => loaded === true)) {
        selectedRegion.value = null;
    }
    if (selectedIslandGroup.value) {
        loadRegions();
    }
    else {
        regions.value = [];
    }
});

watch(selectedRegion, () => {
    if (completeLoaded.value.every((loaded) => loaded === true)) {
        selectedProvince.value = null;
    }
    if (selectedRegion.value) {
        loadProvinces();
    }
    else {
        provinces.value = [];
    }
});

watch(selectedProvince, () => {
    if (completeLoaded.value.every((loaded) => loaded === true)) {
        selectedMunicity.value = null;
    }
    if (selectedProvince.value) {
        loadMunicities();
    }
    else {
        municities.value = [];
    }
});

watch(selectedMunicity, () => {
    if (completeLoaded.value.every((loaded) => loaded === true)) {
        selectedBarangay.value = null;
    }
    if (selectedMunicity.value) {
        loadBarangays();
    }
    else {
        barangays.value = [];
    }
});

watch(selectedBarangay, () => {
    if (selectedBarangay.value) {
        form.barangay_id = selectedBarangay.value.barangay_id;
    }
    else {
        form.barangay_id = null;
    }
});

const clearErrors = () => {
    errors.barangay_id = [];
    errors.other_details = [];
    errors.contact_number = [];
    errors.user_id = [];
};

const validate = () => {
    clearErrors();
    if (!form.barangay_id) {
        errors.barangay_id.push('The island group, region, province, municipality/city and barangay field is required.');
    }
    if (!form.other_details) {
        errors.other_details.push('The unit / block / street / purok / sitio / subdivision field is required.');
    }
    if (!form.contact_number) {
        errors.contact_number.push('The contact number field is required.');
    }
    if (form.contact_number && !/^09\d{9}$/.test(form.contact_number)) {
        errors.contact_number.push('The contact number format is invalid. It should start with 09 followed by 9 digits.');
    }
    if (!form.postal_code) {
        errors.postal_code.push('The postal code field is required.');
    }
    const hasErrors = [errors.barangay_id.length > 0, errors.other_details.length > 0, errors.contact_number.length > 0];
    return hasErrors.includes(true) ? false : form;
};

const submitService = useAxiosUtil<IForm, null>();

const handleSubmit = async () => {
    const data = validate();
    if (data) {
        await submitService.post('customer/addresses', data)
            .then(() => {
                if (submitService.request.status === 200) {
                    toast.success(submitService.request.message ?? 'Address saved successfully.');
                }
                else {
                    toast.error(submitService.request.message ?? 'Failed to save address.');
                    if (submitService.request.errors) {
                        Object.keys(submitService.request.errors).forEach((key) => {
                            errors[key] = submitService.request.errors[key];
                        });
                    }
                }
            });
    }
    else {
        toast.error('Please fix the errors in the form.');
    }
};

const loadAddressService = useAxiosUtil<null, IAddress | null>();

const loadAddress = async () => {
    await loadAddressService.get(`customer/addresses/${Page.user.user_id}`)
        .then(() => {
            if (loadAddressService.request.status === 200) {
                if (loadAddressService.request.data) {
                    defaultData.value = loadAddressService.request.data;
                    selectedIslandGroup.value = loadAddressService.request.data.barangay.municity.province.region.island_group;
                    selectedRegion.value = loadAddressService.request.data.barangay.municity.province.region;
                    selectedProvince.value = loadAddressService.request.data.barangay.municity.province;
                    selectedMunicity.value = loadAddressService.request.data.barangay.municity;
                    selectedBarangay.value = loadAddressService.request.data.barangay;
                    form.other_details = loadAddressService.request.data.other_details;
                    form.contact_number = loadAddressService.request.data.contact_number;
                    form.postal_code = loadAddressService.request.data.postal_code;
                }
                else {
                    completeLoaded.value = [true, true, true, true, true];
                }
            }
            else {
                toast.error(loadAddressService.request.message ?? 'Failed to load address.');
            }
        })
        .finally(() => {
            loaded.value = true;
        });
};

watch(loaded, () => {
    if (loaded.value) {
        loadIslandGroups();
    }
});

onMounted(() => {
    loadAddress();
});
</script>