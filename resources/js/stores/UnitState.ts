import { UnitInterface } from "@/interfaces/UnitInterface";
import useAxiosUtil from "@/utils/AxiosUtil";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useUnitStore = defineStore("unit", () => {
    // Variables
    const loadUnitService = useAxiosUtil<null, UnitInterface[]>();
    const units = ref<UnitInterface[]>([]);

    // Functions
    const fetchUnits = async () => {
        await loadUnitService.get("admin/units").then(() => {
            if (
                loadUnitService.request.status === 200 &&
                loadUnitService.request.data
            ) {
                units.value = loadUnitService.request.data;
            }
        });
    };

    return {
        loadUnitService,
        units,
        fetchUnits,
    };
});
