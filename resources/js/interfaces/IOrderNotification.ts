interface IOrderNotification {
    order_notification_id: number;
    user_id: number;
    notification_type:
        | 'Pending Order'
        | 'Confirmed Order'
        | 'To Ship'
        | 'Paid'
        | 'Cancelled Order'
        | 'Rejected Order'
        | 'New Order'
        | 'Order Accepted'
        | 'Order Declined'
        | 'Payment Confirmed'
        | 'Payment Uploaded'
        | 'Order Shipped'
        | 'Order Completed'
        | 'Low Stock'
        | 'Out of Stock';
    notification_to: 'Store' | 'Customer';
    is_read: boolean;
    message: string;
    created_at: string;
    updated_at: string;
}

export type { IOrderNotification };