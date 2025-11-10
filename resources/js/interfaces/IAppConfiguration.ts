interface IAppConfiguration {
    app_configuration_id: number;
    key: string;
    value: string | null;
    created_at: string;
    updated_at: string;
}

export type { IAppConfiguration };