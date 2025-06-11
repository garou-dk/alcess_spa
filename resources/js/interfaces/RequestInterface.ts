export interface RequestInterface<T> {
    status?: number;
    errors?: Record<string, string[]>;
    message?: string;
    data?: T;
    loading: boolean;
    percentage?: number;
}
