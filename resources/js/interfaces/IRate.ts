import { UserInterface } from "./UserInterface";

interface IRate {
    rate_id: number;
    product_id: number;
    user_id: number;
    product_order_id: number;
    rate: number;
    comment: string | null;
    reply: string | null;
    user: UserInterface;
}

export type { IRate };