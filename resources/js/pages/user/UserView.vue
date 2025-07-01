<template>
    <div>
        <BoxShadow class="mb-5">
            <div class="flex w-full">
                <form class="flex grow flex-wrap" @submit.prevent="load()">
                    <div class="grow p-2">
                        <InputForm
                            :errors="errors.search"
                            id="search"
                            label-name="Search"
                            tag="label"
                        >
                            <InputText
                                type="text"
                                v-model="form.search"
                                :invalid="errors.search.length > 0"
                                placeholder="Search"
                                fluid
                                id="search"
                            />
                        </InputForm>
                    </div>
                    <div class="p-2">
                        <InputForm
                            :errors="errors.role_id"
                            id="role_id"
                            label-name="Role*"
                            tag="span"
                        >
                            <Select
                                v-model="form.role_id"
                                :options="roleStore.roles"
                                :invalid="errors.role_id.length > 0"
                                option-label="role_name"
                                option-value="role_id"
                                :loading="
                                    roleStore.loadRoleService.request.loading
                                "
                                label-id="role_id"
                                placeholder="Select Role"
                            />
                        </InputForm>
                    </div>
                    <div class="p-2">
                        <InputForm
                            :errors="errors.status"
                            id="status"
                            label-name="Status"
                            tag="span"
                        >
                            <Select
                                v-model="form.status"
                                :options="statuses"
                                :invalid="errors.status.length > 0"
                                option-label="label"
                                option-value="value"
                                label-id="status"
                                placeholder="Select Status"
                                show-clear
                            />
                        </InputForm>
                    </div>
                    <div class="flex items-end p-2">
                        <Button
                            type="submit"
                            label="Filter"
                            icon="pi pi-filter"
                            class="primary-bg"
                        />
                    </div>
                    <div class="flex items-end p-2">
                        <Button
                            type="button"
                            @click="showUserForm = true"
                            label="Create User"
                            icon="pi pi-plus"
                            class="primary-bg"
                        />
                    </div>
                </form>
            </div>
        </BoxShadow>
        <BoxShadow>
            <div class="w-full p-5">
                <DataTable
                    :value="paginate.data"
                    :loading="
                        loadService.request.loading ||
                        roleStore.roles.length === 0
                    "
                    columnResizeMode="expand"
                >
                    <Column field="full_name" header="Name">
                        <template #body="{ data }">
                            <div class="flex items-center">
                                <Avatar
                                    v-if="data.image"
                                    shape="circle"
                                    :image="
                                        UrlUtil.getBaseApiUrl(
                                            `profile/${data.image}`,
                                        )
                                    "
                                    class="aspect-square!"
                                />
                                <Avatar
                                    v-else
                                    shape="circle"
                                    :label="data.full_name[0]"
                                />
                                <div class="ml-2 shrink">
                                    {{ data.full_name }}
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="role.role_name" header="Role" />
                    <Column field="status" header="Status">
                        <template #body="{ data }">
                            <span
                                class="rounded-full px-2 py-1 text-white"
                                :class="
                                    data.is_active
                                        ? 'bg-green-500'
                                        : 'bg-red-500'
                                "
                            >
                                {{ data.is_active ? "Active" : "Inactive" }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Action">
                        <template #body="{ data }">
                            <Button
                                type="button"
                                @click="openEditElement($event, data)"
                                icon="pi pi-pencil"
                            />
                        </template>
                    </Column>
                    <template #empty>
                        <p>No result found</p>
                    </template>

                    <template #footer>
                        <div
                            class="border-primary flex w-full items-center justify-between gap-4 rounded-full border bg-transparent px-2 py-1"
                        >
                            <Button
                                icon="pi pi-chevron-left"
                                rounded
                                text
                                @click="decrementPage()"
                                :disabled="
                                    paginate.current_page === 1 ||
                                    loadService.request.loading
                                "
                            />
                            <div class="text-color font-medium">
                                <span v-if="paginate.from && paginate.to"
                                    >Showing {{ paginate.from }} to
                                    {{ paginate.to }} of
                                    {{ paginate.total }}</span
                                >
                                <span v-else>No page results</span>
                            </div>
                            <Button
                                icon="pi pi-chevron-right"
                                rounded
                                text
                                @click="incrementPage()"
                                :disabled="
                                    paginate.last_page ===
                                        paginate.current_page ||
                                    loadService.request.loading
                                "
                            />
                        </div>
                    </template>
                </DataTable>
            </div>
        </BoxShadow>
        <Dialog
            v-model:visible="showUserForm"
            modal
            header="Create new user"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <UserForm @cb="load" />
        </Dialog>
        <Dialog
            v-model:visible="showChangeNameForm"
            modal
            header="Change name"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <ChangeNameForm
                v-if="selectedUser"
                @cb="changeNameFormCb"
                :data="selectedUser"
            />
        </Dialog>
        <Dialog
            v-model:visible="showChangePasswordForm"
            modal
            header="Change Password"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <ChangePasswordForm
                v-if="selectedUser"
                @cb="changePasswordFormCb"
                :data="selectedUser"
            />
        </Dialog>
        <Dialog
            v-model:visible="showChangeProfileForm"
            modal
            header="Change Profile Image"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <ChangeProfileForm
                v-if="selectedUser"
                @cb="changeProfileFormCb"
                :data="selectedUser"
            />
        </Dialog>
        <Dialog
            v-model:visible="showChangeRoleForm"
            modal
            header="Change Role"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <ChangeRoleForm
                v-if="selectedUser"
                @cb="changeRoleFormCb"
                :data="selectedUser"
            />
        </Dialog>
        <Popover ref="editElement" :dismissable="true" class="mr-5 inline-flex">
            <div v-if="selectedUser">
                <button
                    type="button"
                    @click="
                        showChangeNameForm = true;
                        editElement.hide();
                    "
                    class="flex w-full cursor-pointer items-center gap-2 rounded p-2 text-sm hover:bg-gray-300"
                >
                    <i class="pi pi-clipboard" />
                    Change Name
                </button>
                <button
                    type="button"
                    @click="
                        showChangePasswordForm = true;
                        editElement.hide();
                    "
                    class="flex w-full cursor-pointer items-center gap-2 rounded p-2 text-sm hover:bg-gray-300"
                >
                    <i class="pi pi-lock" />
                    Change Password
                </button>
                <button
                    type="button"
                    @click="
                        showChangeProfileForm = true;
                        editElement.hide();
                    "
                    class="flex w-full cursor-pointer items-center gap-2 rounded p-2 text-sm hover:bg-gray-300"
                >
                    <i class="pi pi-image" />
                    Change Profile
                </button>
                <button
                    type="button"
                    @click="
                        showChangeRoleForm = true;
                        editElement.hide();
                    "
                    class="flex w-full cursor-pointer items-center gap-2 rounded p-2 text-sm hover:bg-gray-300"
                >
                    <i class="pi pi-users" />
                    Change Role
                </button>
                <ChangeUserStatusForm
                    v-if="selectedUser"
                    @cb="changeStatusFormCb"
                    :data="selectedUser"
                />
            </div>
        </Popover>
    </div>
</template>
<script setup lang="ts">
import ChangeNameForm from "@/components/forms/ChangeNameForm.vue";
import ChangePasswordForm from "@/components/forms/ChangePasswordForm.vue";
import ChangeProfileForm from "@/components/forms/ChangeProfileForm.vue";
import ChangeRoleForm from "@/components/forms/ChangeRoleForm.vue";
import ChangeUserStatusForm from "@/components/forms/ChangeUserStatusForm.vue";
import UserForm from "@/components/forms/UserForm.vue";
import DataTableInterface from "@/interfaces/DataTableInterface";
import {
    UserInterface,
    UserSearchErrorInterface,
    UserSearchInterface,
} from "@/interfaces/UserInterface";
import { useRoleStore } from "@/stores/RoleState";
import useAxiosUtil from "@/utils/AxiosUtil";
import UrlUtil from "@/utils/UrlUtil";
import { Popover } from "primevue";
import { onMounted, reactive, ref, watch } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();
const loadService = useAxiosUtil<
    UserSearchInterface,
    DataTableInterface<UserInterface>
>();
const roleStore = useRoleStore();
const showUserForm = ref<boolean>(false);
const editElement = ref<null | InstanceType<typeof Popover>>();
const selectedUser = ref<UserInterface | null>(null);
const showChangeNameForm = ref<boolean>(false);
const showChangePasswordForm = ref<boolean>(false);
const showChangeProfileForm = ref<boolean>(false);
const showChangeRoleForm = ref<boolean>(false);

const statuses = [
    { label: "Active", value: 1 },
    { label: "Inactive", value: 0 },
];

const openEditElement = (event: Event, value: UserInterface) => {
    if (editElement.value) {
        editElement.value.toggle(event);
        selectedUser.value = value;
    }
};

const form: UserSearchInterface = reactive({
    search: null,
    limit: 5,
    page: 1,
    role_id: null,
    status: null,
});

const errors: UserSearchErrorInterface = reactive({
    search: [],
    role_id: [],
    status: [],
    limit: [],
    page: [],
});

const paginate: DataTableInterface<UserInterface> = reactive({
    data: [],
    current_page: 0,
    from: 0,
    to: 0,
    total: 0,
    last_page: 0,
});

const clearError = () => {
    errors.search = [];
    errors.role_id = [];
    errors.status = [];
    errors.limit = [];
    errors.page = [];
};

const validate = () => {
    clearError();
    if (!form.limit) {
        errors.limit.push("Limit is required");
    }
    if (!form.page) {
        errors.page.push("Page is required");
    }
    if (!form.role_id) {
        errors.role_id.push("Role is required");
    }

    const hasErrors = [errors.limit.length > 0, errors.page.length > 0];
    return hasErrors.includes(true) ? false : form;
};

const load = async (resetPage: boolean = false) => {
    if (resetPage) {
        form.page = 1;
    }
    if (!form.limit) {
        form.limit = 5;
    }

    const data = validate();

    if (data) {
        await loadService.get("admin/users", data).then(() => {
            if (
                loadService.request.status === 200 &&
                loadService.request.data
            ) {
                Object.keys(paginate).forEach((key) => {
                    paginate[key] = loadService.request.data[key];
                });
            } else {
                toast.error(loadService.request.message ?? "Please try again");
                if (loadService.request.errors) {
                    Object.keys(loadService.request.errors).forEach((key) => {
                        errors[key] = loadService.request.errors[key];
                    });
                }
            }
        });
    } else {
        toast.error("Please fill in the required fields.");
    }
};

const loadRoles = async () => {
    if (roleStore.roles.length === 0) {
        await roleStore.fetchRoles();
    }
};

const incrementPage = () => {
    if (paginate.current_page < paginate.last_page) {
        form.page = paginate.current_page + 1;
        load();
    }
};

const decrementPage = () => {
    if (paginate.current_page > 1) {
        form.page = paginate.current_page - 1;
        load();
    }
};

const initState = async () => {
    await loadRoles();
    form.role_id = roleStore.roles[0].role_id;
    await load();
};

const changeNameFormCb = () => {
    showChangeNameForm.value = false;
    load();
};

const changePasswordFormCb = () => {
    showChangePasswordForm.value = false;
    load();
};

const changeProfileFormCb = () => {
    showChangeProfileForm.value = false;
    load();
};

const changeRoleFormCb = () => {
    showChangeRoleForm.value = false;
    load();
};

const changeStatusFormCb = () => {
    editElement.value.hide();
    load();
};

watch(
    () => form.role_id,
    () => {
        form.page = 1;
        load(true);
    },
);

onMounted(() => {
    initState();
});
</script>
