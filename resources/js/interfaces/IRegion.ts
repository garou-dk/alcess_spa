import { IIslandGroup } from "./IIslandGroup";

interface IRegion {
    region_id: number;
    region_name: string;
    region_name_long: string;
    island_group_id: number;
    island_group?: IIslandGroup;
}

export type { IRegion };