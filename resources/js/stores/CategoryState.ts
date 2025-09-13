import { CategoryInterface } from "@/interfaces/CategoryInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { defineStore } from "pinia";
import { ref } from "vue";
import Page from "./Page";
import { RoleEnum } from "@/enums/RoleEnum";

export const useCategoryStore = defineStore("category", () => {
    // Variables
    const loadCategoryService = useAxiosUtil<null, CategoryInterface[]>();
    const categories = ref<CategoryInterface[]>([]);

    // Functions
    const fetchCategories = async () => {
        const useAdminLink =
            Page.user && Page.user.role.role_name === RoleEnum.ADMIN;
        await loadCategoryService
            .get(useAdminLink ? "admin/categories" : "categories")
            .then(() => {
                if (
                    loadCategoryService.request.status === 200 &&
                    loadCategoryService.request.data
                ) {
                    categories.value = loadCategoryService.request.data;
                }
            });
    };

    return {
        loadCategoryService,
        categories,
        fetchCategories,
    };
});
