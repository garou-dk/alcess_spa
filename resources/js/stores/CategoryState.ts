import { CategoryInterface } from "@/interfaces/CategoryInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useCategoryStore = defineStore("category", () => {
    // Variables
    const loadCategoryService = useAxiosUtil<null, CategoryInterface[]>();
    const categories = ref<CategoryInterface[]>([]);

    // Functions
    const fetchCategories = async () => {
        await loadCategoryService.get("admin/categories").then(() => {
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
