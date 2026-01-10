import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";
import { definePreset } from "@primeuix/themes";

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
                class: "!bg-blue-600 !bg-gradient-to-r !from-blue-600 !to-blue-600 !text-white !rounded-t-lg !border-blue-600 !border-t !border-l !border-r",
                style: "background-color: #2563eb !important; background-image: none !important;",
            },
            closeButton: {
                class: "!text-white !border-white !bg-transparent hover:!bg-blue-700 hover:!text-white",
                style: "color: white !important; border-color: white !important;",
            },
            closeButtonIcon: {
                class: "!text-white",
                style: "color: white !important;",
            },
            root: {
                class: "border-1! border-[rgba(0,0,0,0.4)]! pt-0!",
            },
            content: {
                class: "pt-2!",
            },
        },
    },
};

export default {
    primevue: PrimeVue,
    config: config,
};
