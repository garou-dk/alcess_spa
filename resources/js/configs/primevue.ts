import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";
import { definePreset } from "@primeuix/themes";
import { content } from "@primeuix/themes/aura/accordion";

const config = {
    theme: {
        preset: definePreset(Aura, {
            semantic: {
                primary: {
                    50: "{sky.50}",
                    100: "{sky.100}",
                    200: "{sky.200}",
                    300: "{sky.300}",
                    400: "{sky.400}",
                    500: "{sky.500}",
                    600: "{sky.600}",
                    700: "{sky.700}",
                    800: "{sky.800}",
                    900: "{sky.900}",
                    950: "{sky.950}",
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
            content: {
                class: "pt-2!"
            }
        },
    },
};

export default {
    primevue: PrimeVue,
    config: config,
};
