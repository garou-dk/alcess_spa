import { IRegion } from "./IRegion";

interface IProvince {
    province_id: number;
    province_name: string;
    region_id: number;
    region?: IRegion;
}

export type { IProvince };