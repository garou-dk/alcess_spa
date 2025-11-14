import { ProductInterface } from "./ProductInterface";

interface IBatch {
    batch_id: number;
    batch_products: ProductInterface[];
}

export type { IBatch };