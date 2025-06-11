export default interface DataTableInterface<T> {
    data: T[];
    current_page: number;
    from: number;
    to: number;
    total: number;
    last_page: number;
}
