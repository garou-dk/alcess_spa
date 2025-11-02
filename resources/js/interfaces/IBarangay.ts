import { IMunicity } from "./IMunicity";

interface IBarangay {
    barangay_id: number;
    barangay_name: string;
    old_name: string | null;
    municity_id: number;
    municity?: IMunicity;
}

export type { IBarangay };