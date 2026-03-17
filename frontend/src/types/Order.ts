export type Order = {
  id: string;
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
  product_uuid: string;
  quantity: number;
};

export type CreateOrderPayload = {
  items: OrderItem[];
};
