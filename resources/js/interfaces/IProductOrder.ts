import { ProductInterface } from "./ProductInterface";

interface IProductOrder {
    product_id: number;
    quantity: number;
    price: number;
    product: ProductInterface;
}

export default IProductOrder