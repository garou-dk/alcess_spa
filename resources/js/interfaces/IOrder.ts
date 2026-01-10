import { IBarangay } from "./IBarangay";
import IProductOrder from "./IProductOrder";
import { UserInterface } from "./UserInterface";

interface IOrder {
    order_id: number;
    order_public_id: string;
    user_id: number;
    order_type: string;
    barangay_id: number;
    other_details: string;
    contact_number: string;
    status: string;
    payment_method: string;
    bank_name: string;
    transaction_number: string;
    date_paid_confirmed: string;
    estimated_delivery_date_start: string;
    estimated_delivery_date_end: string;
    customer_received_date: string;
    shipping_fee: number;
    total_amount: number;
    delivery_courier: string;
    tracking_number: string;
    delivery_proof_images: string | null;
    delivery_proof_video: string | null;
    created_at: string;
    updated_at: string;
    product_orders: IProductOrder[];
    barangay: IBarangay;
    user: UserInterface;
    postal_code: string;
    date_payment_processed: string | null;
    sale_id: number | null;
    invoice_generated_at: string | null;
    remarks: string | null;
}

export type { IOrder };