interface UnitInterface {
    unit_id: number;
    unit_name: string;
    deleted_at: string | null;
}

interface UnitFormInterface {
    unit_name: string | null;
}

interface UnitFormErrorInterface {
    unit_name: string[];
}

export { UnitInterface, UnitFormInterface, UnitFormErrorInterface };
