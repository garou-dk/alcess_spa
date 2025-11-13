import { IRate } from "./IRate";
import { ProductInterface } from "./ProductInterface";

interface IProductOrder {
    product_id: number;
    quantity: number;
    price: number;
    product: ProductInterface;
    rate: IRate | null;
    product_order_id: number;
}

export default IProductOrder