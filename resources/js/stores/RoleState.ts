import { RoleInterface } from "@/interfaces/RoleInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useRoleStore = defineStore("role", () => {
    // Variables
    const loadRoleService = useAxiosUtil<null, RoleInterface[]>();
    const roles = ref<RoleInterface[]>([]);

    // Functions
    const fetchRoles = async () => {
        await loadRoleService.get("admin/roles").then(() => {
            if (
                loadRoleService.request.status === 200 &&
                loadRoleService.request.data
            ) {
                const uniqueRoles: RoleInterface[] = [];
                const seenNames = new Set<string>();

                loadRoleService.request.data.forEach((role) => {
                    const normalizedName = role.role_name
                        .trim()
                        .toLowerCase();
                    if (!seenNames.has(normalizedName)) {
                        seenNames.add(normalizedName);
                        uniqueRoles.push(role);
                    }
                });

                roles.value = uniqueRoles;
            }
        });
    };

    return {
        loadRoleService,
        roles,
        fetchRoles,
    };
});
