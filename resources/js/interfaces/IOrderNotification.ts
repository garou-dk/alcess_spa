interface IOrderNotification {
    order_notification_id: number;
    user_id: number;
    notification_type: 'Pending Order' | 'Confirmed Order' | 'To Ship' | 'Paid' | 'Cancelled Order' | 'Rejected Order';
    notification_to: 'Store' | 'Customer';
    is_read: boolean;
    message: string;
    created_at: string;
    updated_at: string;
}

export type { IOrderNotification };