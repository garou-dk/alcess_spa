import { IProvince } from "./IProvince";

interface IMunicity {
    municity_id: number;
    municity_name: string;
    province_id: number;
    province?: IProvince;
}

export type { IMunicity };