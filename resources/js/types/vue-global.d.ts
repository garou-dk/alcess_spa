declare module "*.vue" {
    import type { DefineComponent } from "vue";
    const component: DefineComponent<{}, {}, any>;
    export default component;
}

declare module "vue" {
    export interface GlobalComponents {
        InputForm: (typeof import("@/components/InputForm.vue"))["default"];
        PageLoader: (typeof import("@/components/PageLoader.vue"))["default"];
        BoxShadow: (typeof import("@/components/BoxShadow.vue"))["default"];
    }
}
export {};
