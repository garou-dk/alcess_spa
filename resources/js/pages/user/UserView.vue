<template>
    <div :class="responsive.getResponsiveClasses({
        mobile: 'min-h-screen bg-gray-50 py-3 px-3',
        tablet: 'min-h-screen bg-gray-50 py-4 px-4',
        desktop: 'min-h-screen bg-gray-50 py-4 sm:py-6 px-3 sm:px-6'
    })">
        <!-- Date and Time Display -->
        <DateTimeDisplay />

        <!-- Filters Card -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'mb-3 rounded-lg bg-white border border-gray-200 shadow-sm',
            tablet: 'mb-4 rounded-lg bg-white border border-gray-200 shadow-sm',
            desktop: 'mb-4 rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm'
        })">
            <div :class="responsive.getResponsiveClasses({
                mobile: 'px-3 py-3',
                tablet: 'px-4 py-4',
                desktop: 'px-5 py-4'
            })">
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'space-y-4',
                    tablet: 'space-y-4 md:space-y-0 md:flex md:flex-wrap md:gap-4 md:items-end',
                    desktop: 'flex flex-wrap gap-4 items-end'
                })">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full',
                        tablet: 'flex-1 min-w-[200px]',
                        desktop: 'flex-1 min-w-[250px]'
                    })">
                        <label :class="responsive.getResponsiveClasses({
                            mobile: 'block text-xs font-medium text-gray-700 mb-2',
                            tablet: 'block text-sm font-medium text-gray-700 mb-2',
                            desktop: 'block text-sm font-medium text-gray-700 mb-2'
                        })">Search User</label>
                        <InputText
                            type="text"
                            v-model="form.search"
                            :invalid="errors.search.length > 0"
                            placeholder="Search by name"
                            :class="[
                                'w-full',
                                responsive.getResponsiveClasses({
                                    mobile: 'text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                            id="search"
                        />
                    </div>
                    
                    <!-- Role and Status in 2x2 grid for mobile/tablet -->
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'grid grid-cols-2 gap-3',
                        tablet: 'grid grid-cols-2 gap-3 md:contents',
                        desktop: 'contents'
                    })">
                        <div :class="responsive.getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'w-full sm:w-auto sm:min-w-[160px]',
                            desktop: 'w-full sm:w-auto sm:min-w-[180px]'
                        })">
                            <label :class="responsive.getResponsiveClasses({
                                mobile: 'block text-xs font-medium text-gray-700 mb-2',
                                tablet: 'block text-sm font-medium text-gray-700 mb-2',
                                desktop: 'block text-sm font-medium text-gray-700 mb-2'
                            })">Role</label>
                            <Select
                                v-model="form.role_id"
                                :options="rolesWithAll"
                                :invalid="errors.role_id.length > 0"
                                option-label="role_name"
                                option-value="role_id"
                                :loading="loadingRoles"
                                placeholder="All Roles"
                                show-clear
                                :class="[
                                    'w-full',
                                    responsive.getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })
                                ]"
                            />
                        </div>
                        <div :class="responsive.getResponsiveClasses({
                            mobile: 'w-full',
                            tablet: 'w-full sm:w-auto sm:min-w-[120px]',
                            desktop: 'w-full sm:w-auto sm:min-w-[150px]'
                        })">
                            <label :class="responsive.getResponsiveClasses({
                                mobile: 'block text-xs font-medium text-gray-700 mb-2',
                                tablet: 'block text-sm font-medium text-gray-700 mb-2',
                                desktop: 'block text-sm font-medium text-gray-700 mb-2'
                            })">Status</label>
                            <Select
                                v-model="form.status"
                                :options="statuses"
                                :invalid="errors.status.length > 0"
                                option-label="label"
                                option-value="value"
                                placeholder="All Status"
                                show-clear
                                :class="[
                                    'w-full',
                                    responsive.getResponsiveClasses({
                                        mobile: 'text-sm',
                                        tablet: 'text-base',
                                        desktop: 'text-base'
                                    })
                                ]"
                            />
                        </div>
                    </div>
                    
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'w-full flex justify-center',
                        tablet: 'flex gap-2 ml-auto',
                        desktop: 'flex gap-2 ml-auto'
                    })">
                        <Button
                            type="button"
                            @click="showUserForm = true"
                            icon="pi pi-plus"
                            :label="responsive.getResponsiveClasses({
                                mobile: 'Create User',
                                tablet: 'Create User',
                                desktop: 'Create User'
                            })"
                            :class="[
                                '!bg-green-600 hover:!bg-green-700',
                                responsive.getResponsiveClasses({
                                    mobile: 'w-full text-sm',
                                    tablet: 'text-base',
                                    desktop: 'text-base'
                                })
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Users Table/Cards Container -->
        <div :class="responsive.getResponsiveClasses({
            mobile: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            tablet: 'rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden',
            desktop: 'rounded-lg sm:rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden'
        })">
            <div
                v-if="errorUsers"
                :class="responsive.getResponsiveClasses({
                    mobile: 'p-6 text-center',
                    tablet: 'p-8 text-center',
                    desktop: 'p-8 text-center'
                })"
            >
                <div class="mb-4">
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center',
                        tablet: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center',
                        desktop: 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'pi pi-exclamation-triangle text-2xl text-red-600',
                            tablet: 'pi pi-exclamation-triangle text-3xl text-red-600',
                            desktop: 'pi pi-exclamation-triangle text-3xl text-red-600'
                        })"></i>
                    </div>
                </div>
                <h3 :class="responsive.getResponsiveClasses({
                    mobile: 'mb-2 text-base font-semibold text-red-800',
                    tablet: 'mb-2 text-lg font-semibold text-red-800',
                    desktop: 'mb-2 text-lg font-semibold text-red-800'
                })">Failed to Load Users</h3>
                <p :class="responsive.getResponsiveClasses({
                    mobile: 'mb-4 text-sm text-red-600',
                    tablet: 'mb-4 text-sm text-red-600',
                    desktop: 'mb-4 text-sm text-red-600'
                })">{{ errorUsers }}</p>
                <Button
                    label="Retry"
                    icon="pi pi-refresh"
                    @click="retryUsers"
                    class="!bg-red-600 hover:!bg-red-700"
                />
            </div>
            <div v-else class="w-full">
                <div :class="responsive.getResponsiveClasses({
                    mobile: 'flex items-center justify-between px-3 py-3 border-b border-gray-200 bg-gray-50',
                    tablet: 'flex items-center justify-between px-4 py-4 border-b border-gray-200 bg-gray-50',
                    desktop: 'flex items-center justify-between px-5 py-4 border-b border-gray-200'
                })">
                    <div>
                        <h1 :class="responsive.getResponsiveClasses({
                            mobile: 'text-base font-bold text-gray-800',
                            tablet: 'text-lg font-bold text-gray-800',
                            desktop: 'text-lg font-semibold text-gray-800'
                        })">All Users</h1>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-500 mt-0.5',
                            tablet: 'text-xs text-gray-500 mt-0.5',
                            desktop: 'text-xs text-gray-500 mt-0.5'
                        })">Manage user accounts and permissions</p>
                    </div>
                    <div :class="responsive.getResponsiveClasses({
                        mobile: 'flex flex-col items-end',
                        tablet: 'flex flex-col items-end',
                        desktop: 'flex items-center gap-2 text-sm text-gray-600'
                    })">
                        <i :class="responsive.getResponsiveClasses({
                            mobile: 'hidden',
                            tablet: 'hidden',
                            desktop: 'pi pi-users text-blue-600'
                        })"></i>
                        <span :class="responsive.getResponsiveClasses({
                            mobile: 'text-xs text-gray-600 font-medium',
                            tablet: 'text-xs text-gray-600 font-medium',
                            desktop: 'font-medium'
                        })">{{ paginate.total }} Users</span>
                    </div>
                </div>
                
                <!-- Desktop DataTable -->
                <DataTable
                    v-if="responsive.isDesktop.value"
                    class="w-full"
                    :value="paginate.data"
                    columnResizeMode="expand"
                    stripedRows
                    :loading="loadingUsers || loadService.request.loading"
                >
                    <Column field="full_name" header="User" :sortable="true">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3 py-1">
                                <Avatar
                                    v-if="data.image"
                                    shape="circle"
                                    :image="UrlUtil.getBaseAppUrl(`storage/images/profile/${data.image}`)"
                                    class="w-10 h-10"
                                    size="large"
                                />
                                <Avatar
                                    v-else
                                    shape="circle"
                                    :label="data.full_name[0]"
                                    class="bg-blue-100 text-blue-600"
                                    size="large"
                                />
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">
                                        {{ data.full_name }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="role.role_name" header="Role" :sortable="true">
                        <template #body="{ data }">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700">
                                <i class="pi pi-shield mr-1"></i>
                                {{ data.role.role_name }}
                            </span>
                        </template>
                    </Column>
                    <Column field="status" header="Status" :sortable="true">
                        <template #body="{ data }">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                :class="data.is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                            >
                                <i :class="data.is_active ? 'pi pi-check-circle' : 'pi pi-times-circle'" class="mr-1"></i>
                                {{ data.is_active ? "Active" : "Inactive" }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="{ data }">
                            <Button
                                type="button"
                                @click="openEditElement($event, data)"
                                icon="pi pi-ellipsis-v"
                                text
                                class="!text-blue-600 hover:!bg-blue-50 !text-lg !font-bold"
                            />
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center py-8">
                            <i class="pi pi-users text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">No users found</p>
                            <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                        </div>
                    </template>

                    <template #footer>
                        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200">
                            <div class="text-sm text-gray-700">
                                <span v-if="paginate.from && paginate.to">
                                    Showing {{ paginate.from }} - {{ paginate.to }} of {{ paginate.total }}
                                </span>
                                <span v-else>No page results</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    icon="pi pi-chevron-left"
                                    text
                                    size="small"
                                    @click="decrementPage()"
                                    :disabled="paginate.current_page === 1 || loadService.request.loading"
                                    v-tooltip.top="'Previous'"
                                />
                                <span class="text-sm font-medium text-gray-700 px-3">
                                    {{ paginate.current_page }}
                                </span>
                                <Button
                                    icon="pi pi-chevron-right"
                                    text
                                    size="small"
                                    @click="incrementPage()"
                                    :disabled="paginate.last_page === paginate.current_page || loadService.request.loading"
                                    v-tooltip.top="'Next'"
                                />
                            </div>
                        </div>
                    </template>
                </DataTable>

                <!-- Mobile/Tablet Card Layout -->
                <div v-else>
                    <div v-if="paginatedData.length === 0 && !loadingUsers && !loadService.request.loading" :class="responsive.getResponsiveClasses({
                        mobile: 'text-center py-8',
                        tablet: 'text-center py-12',
                        desktop: 'text-center py-12'
                    })">
                        <div class="mb-4">
                            <div :class="responsive.getResponsiveClasses({
                                mobile: 'mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center',
                                tablet: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center',
                                desktop: 'mx-auto w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center'
                            })">
                                <i :class="responsive.getResponsiveClasses({
                                    mobile: 'pi pi-users text-3xl text-gray-400',
                                    tablet: 'pi pi-users text-4xl text-gray-400',
                                    desktop: 'pi pi-users text-4xl text-gray-400'
                                })"></i>
                            </div>
                        </div>
                        <h3 :class="responsive.getResponsiveClasses({
                            mobile: 'text-base font-semibold text-gray-700 mb-2',
                            tablet: 'text-lg font-semibold text-gray-700 mb-2',
                            desktop: 'text-lg font-semibold text-gray-700 mb-2'
                        })">No users found</h3>
                        <p :class="responsive.getResponsiveClasses({
                            mobile: 'text-sm text-gray-500',
                            tablet: 'text-sm text-gray-500',
                            desktop: 'text-sm text-gray-500'
                        })">Try adjusting your filters</p>
                    </div>
                    <div v-else>
                        <!-- Cards Container -->
                        <div :class="responsive.getResponsiveClasses({
                            mobile: 'space-y-3 p-3',
                            tablet: 'space-y-4 p-4',
                            desktop: 'space-y-4 p-4'
                        })">
                            <div 
                                v-for="(user, index) in paginatedData" 
                                :key="user.user_id"
                                :class="responsive.getResponsiveClasses({
                                    mobile: 'bg-white border border-gray-200 rounded-lg p-3 shadow-sm',
                                    tablet: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm',
                                    desktop: 'bg-white border border-gray-200 rounded-lg p-4 shadow-sm'
                                })"
                            >
                                <!-- User Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <Avatar
                                            v-if="user.image"
                                            shape="circle"
                                            :image="UrlUtil.getBaseAppUrl(`storage/images/profile/${user.image}`)"
                                            :class="responsive.getResponsiveClasses({
                                                mobile: 'w-10 h-10',
                                                tablet: 'w-12 h-12',
                                                desktop: 'w-12 h-12'
                                            })"
                                        />
                                        <Avatar
                                            v-else
                                            shape="circle"
                                            :label="user.full_name[0]"
                                            :class="responsive.getResponsiveClasses({
                                                mobile: 'w-10 h-10 bg-blue-100 text-blue-600',
                                                tablet: 'w-12 h-12 bg-blue-100 text-blue-600',
                                                desktop: 'w-12 h-12 bg-blue-100 text-blue-600'
                                            })"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <h3 :class="responsive.getResponsiveClasses({
                                                mobile: 'font-semibold text-gray-900 truncate text-sm',
                                                tablet: 'font-semibold text-gray-900 truncate text-base',
                                                desktop: 'font-semibold text-gray-900 truncate text-base'
                                            })">
                                                {{ user.full_name }}
                                            </h3>
                                        </div>
                                    </div>
                                    <Button
                                        type="button"
                                        @click="openEditElement($event, user)"
                                        icon="pi pi-ellipsis-v"
                                        text
                                        :class="[
                                            '!text-blue-600 hover:!bg-blue-50 !text-lg !font-bold',
                                            responsive.getResponsiveClasses({
                                                mobile: '!p-1',
                                                tablet: '!p-2',
                                                desktop: '!p-2'
                                            })
                                        ]"
                                    />
                                </div>

                                <!-- User Details -->
                                <div :class="responsive.getResponsiveClasses({
                                    mobile: 'grid grid-cols-1 gap-3 mb-3',
                                    tablet: 'grid grid-cols-2 gap-4 mb-4',
                                    desktop: 'grid grid-cols-2 gap-4 mb-4'
                                })">
                                    <div>
                                        <p :class="responsive.getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600 mb-1',
                                            tablet: 'text-sm text-gray-600 mb-1',
                                            desktop: 'text-sm text-gray-600 mb-1'
                                        })">Role:</p>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700">
                                            <i class="pi pi-shield mr-1"></i>
                                            {{ user.role.role_name }}
                                        </span>
                                    </div>
                                    <div>
                                        <p :class="responsive.getResponsiveClasses({
                                            mobile: 'text-xs text-gray-600 mb-1',
                                            tablet: 'text-sm text-gray-600 mb-1',
                                            desktop: 'text-sm text-gray-600 mb-1'
                                        })">Status:</p>
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                            :class="user.is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                                        >
                                            <i :class="user.is_active ? 'pi pi-check-circle' : 'pi pi-times-circle'" class="mr-1"></i>
                                            {{ user.is_active ? "Active" : "Inactive" }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile/Tablet Pagination Controls -->
                        <div v-if="totalPages > 1" :class="responsive.getResponsiveClasses({
                            mobile: 'flex items-center justify-between px-3 py-3 border-t border-gray-200 bg-gray-50',
                            tablet: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50',
                            desktop: 'flex items-center justify-between px-4 py-4 border-t border-gray-200 bg-gray-50'
                        })">
                            <div :class="responsive.getResponsiveClasses({
                                mobile: 'text-xs text-gray-600',
                                tablet: 'text-sm text-gray-600',
                                desktop: 'text-sm text-gray-600'
                            })">
                                Showing {{ ((currentPage - 1) * rowsPerPage) + 1 }} to {{ Math.min(currentPage * rowsPerPage, filteredData.length) }} of {{ filteredData.length }} users
                            </div>
                            <div class="flex items-center gap-2">
                                <Button
                                    icon="pi pi-chevron-left"
                                    :disabled="currentPage === 1 || loadService.request.loading"
                                    @click="currentPage--"
                                    :class="[
                                        'p-button-text p-button-sm',
                                        responsive.getResponsiveClasses({
                                            mobile: '!text-xs !p-1',
                                            tablet: '!text-sm !p-2',
                                            desktop: '!text-sm !p-2'
                                        })
                                    ]"
                                />
                                <span :class="responsive.getResponsiveClasses({
                                    mobile: 'text-xs font-medium text-gray-700 px-2',
                                    tablet: 'text-sm font-medium text-gray-700 px-3',
                                    desktop: 'text-sm font-medium text-gray-700 px-3'
                                })">
                                    {{ currentPage }} of {{ totalPages }}
                                </span>
                                <Button
                                    icon="pi pi-chevron-right"
                                    :disabled="currentPage === totalPages || loadService.request.loading"
                                    @click="currentPage++"
                                    :class="[
                                        'p-button-text p-button-sm',
                                        responsive.getResponsiveClasses({
                                            mobile: '!text-xs !p-1',
                                            tablet: '!text-sm !p-2',
                                            desktop: '!text-sm !p-2'
                                        })
                                    ]"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Dialog
            v-model:visible="showUserForm"
            modal
            header="Create New User"
            :dismissableMask="true"
            :style="{ width: '28rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <UserForm @cb="load" />
        </Dialog>
        <Dialog
            v-model:visible="showChangeNameForm"
            modal
            header="Change Name"
            :dismissableMask="true"
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
            :dismissableMask="true"
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
            :dismissableMask="true"
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
            :dismissableMask="true"
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
import DateTimeDisplay from "@/components/DateTimeDisplay.vue";
import TableSkeleton from "@/components/skeletons/TableSkeleton.vue";
import DataTableInterface from "@/interfaces/DataTableInterface";
import {
    UserInterface,
    UserSearchErrorInterface,
    UserSearchInterface,
} from "@/interfaces/UserInterface";
import { useRoleStore } from "@/stores/RoleState";
import { useResponsive } from "@/composables/useResponsive";
import useAxiosUtil from "@/utils/AxiosUtil";
import UrlUtil from "@/utils/UrlUtil";
import { Popover } from "primevue";
import { onMounted, reactive, ref, watch, computed } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();
const responsive = useResponsive();
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

// Pagination for mobile/tablet card layout
const currentPage = ref(1);
const rowsPerPage = ref(10);

// Computed property for filtered data (for mobile/tablet pagination)
const filteredData = computed(() => {
    return paginate.data || [];
});

const totalPages = computed(() => {
    return Math.ceil(filteredData.value.length / rowsPerPage.value);
});

const paginatedData = computed(() => {
    if (responsive.isDesktop.value) {
        return filteredData.value; // Desktop uses server-side pagination
    }
    const start = (currentPage.value - 1) * rowsPerPage.value;
    const end = start + rowsPerPage.value;
    return filteredData.value.slice(start, end);
});

const rolesWithAll = computed(() => {
    // Only show "All Roles" option if a specific role is currently selected
    if (form.role_id !== null) {
        return [
            { role_id: null, role_name: "All Roles" },
            ...roleStore.roles
        ];
    }
    // If no role is selected (showing all), just return the regular roles
    return roleStore.roles;
});

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
        loadingRoles.value = true;
        await roleStore.fetchRoles();
        loadingRoles.value = false;
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

const loadingUsers = ref(false);
const loadingRoles = ref(false);
const errorUsers = ref<string | null>(null);

const retryUsers = async () => {
    errorUsers.value = null;
    await initState();
};

const initState = async () => {
    loadingUsers.value = true;
    errorUsers.value = null;
    try {
        await loadRoles();
        form.role_id = null;
        await load();
    } catch (error) {
        errorUsers.value = 'Failed to load users. Please try again.';
    } finally {
        loadingUsers.value = false;
    }
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
        currentPage.value = 1; // Reset mobile pagination
        load(true);
    },
);

watch(
    () => form.status,
    () => {
        form.page = 1;
        currentPage.value = 1; // Reset mobile pagination
        load(true);
    },
);

// Reset mobile pagination when data changes
watch(
    () => paginate.data,
    () => {
        currentPage.value = 1;
    },
);

// Debounced search - auto-filter as user types
let searchTimeout: number | null = null;
watch(
    () => form.search,
    () => {
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        searchTimeout = window.setTimeout(() => {
            form.page = 1;
            currentPage.value = 1; // Reset mobile pagination
            load(true);
        }, 500); // Wait 500ms after user stops typing
    },
);

onMounted(() => {
    initState();
});
</script>
