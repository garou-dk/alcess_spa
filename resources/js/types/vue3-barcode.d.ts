declare module "vue3-barcode" {
    import { DefineComponent } from "vue";
    const component: DefineComponent<{
        value: string;
        [key: string]: unknown;
    }>;
    export default component;
}
