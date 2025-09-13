import { ProductInterface } from "@/interfaces/ProductInterface";

interface CartInterface {
    cart_id: number;
    product_id: number;
    quantity: number;
    product: ProductInterface;
}

interface CartFormInterface {
    product_id: number | null;
    quantity: number | null;
}

interface CartFormErrorInterface {
    product_id: string[];
    quantity: string[];
}

export { CartInterface, CartFormInterface };
