import { IBarangay } from "@/interfaces/IBarangay";

interface IAddress {
    address_id: number;
    other_details: string;
    barangay_id: number;
    contact_number: string;
    user_id: number;
    barangay: IBarangay;
    postal_code: string;
}

export type { IAddress };