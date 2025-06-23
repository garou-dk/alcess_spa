import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";
import { definePreset } from "@primeuix/themes";

const config = {
    theme: {
        preset: definePreset(Aura, {
            semantic: {
                primary: {
                    50: "{blue.50}",
                    100: "{blue.100}",
                    200: "{blue.200}",
                    300: "{blue.300}",
                    400: "{blue.400}",
                    500: "{blue.500}",
                    600: "{blue.600}",
                    700: "{blue.700}",
                    800: "{blue.800}",
                    900: "{blue.900}",
                    950: "{blue.950}",
                },
            },
        }),
        options: {
            darkModeSelector: false,
        },
    },
    pt: {
        Dialog: {
            header: {
                class: "bg-sky-800! text-white! rounded-t-[9px]! border-x-1! border-sky-800! shadow-[0_0_0_1px_#075985]!",
            },
            root: {
                class: "border-1! border-[rgba(0,0,0,0.4)]! pt-0!",
            },
        },
    },
};

export default {
    primevue: PrimeVue,
    config: config,
};
