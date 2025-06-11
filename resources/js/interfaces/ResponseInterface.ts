export interface ResponseInterface<T> {
    message?: string;
    data?: T;
    errors?: Record<string, string[]>;
}
