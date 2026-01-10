import { UserInterface } from "./UserInterface";

interface IRateLike {
    rate_like_id: number;
    rate_id: number;
    user_id: number;
}

interface IRate {
    rate_id: number;
    product_id: number;
    user_id: number;
    product_order_id: number;
    rate: number;
    comment: string | null;
    reply: string | null;
    user: UserInterface;
    likes?: IRateLike[];
    likes_count?: number;
    created_at: string;
    images?: string[];
}

export type { IRate, IRateLike };