import { ISaleItem } from "./ISaleItem";
import { UserInterface } from "./UserInterface";

interface ISale {
    sale_id: number;
    sale_code: string;
    user_id: number;
    total_amount: number;
    customer_name: string;
    customer_address: string;
    prepared_by: string;
    created_at: string;
    sale_items: ISaleItem[];
    user: UserInterface;
    payment_method: string;
}

export type { ISale };