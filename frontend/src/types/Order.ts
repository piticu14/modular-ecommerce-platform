export type Order = {
    id: number;
    user_id: number;
    status: string;
    subtotal: number;
    currency: string;
    total: number;
    items: OrderItem[];
    created_at: string;
    updated_at: string;
};

export type OrderItem = {
    product_id: number;
    quantity: number;
};

export type CreateOrderPayload = {
    items: OrderItem[];
};