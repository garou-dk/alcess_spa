import { ProductInterface } from "./ProductInterface";

interface ISaleItem {
    sale_item_id: number;
    sale_id: number;
    product_id: number;
    price: number;
    quantity: number;
    created_at: string;
    updated_at: string;
    product: ProductInterface
}

export type { ISaleItem };