export type Product = {
  uuid: string;
  name: string;
  price: number;
  currency: string;
  stock_on_hand: number;
  stock_reserved: number;
  stock_available: number;
  created_at: string;
  updated_at: string;
};

export type CreateProductPayload = {
  name: string;
  price: number;
  currency: string;
  stock_on_hand: number;
  stock_reserved: number;
};
