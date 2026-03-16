export type StockReservation = {
    id: number;
    order_uuid: string;
    order_item_uuid: string;
    product_id: number;
    quantity: number;
    status: string;
    created_at: string;
    updated_at: string;
};