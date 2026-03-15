import { api } from "./client";

export type Order = {
    id: number;
    user_id: number;
    status: string;
    subotal: number;
    total: number;
    items: {
        product_id: number;
        quantity: number;
    }[];
    created_at: string;
    updated_at: string;
};

export type CreateOrderPayload = {
    items: {
        product_id: number;
        quantity: number;
    }[];
};

export const getOrders = async (): Promise<Order[]> => {
    const { data } = await api.get("/orders");
    return data;
};

export const getOrder = async (id: number): Promise<Order> => {
    const { data } = await api.get(`/orders/${id}`);
    return data;
};

export const createOrder = async (payload: CreateOrderPayload): Promise<Order> => {
    const { data } = await api.post("/orders", payload);
    return data;
};

export const deleteOrder = async (id: number) => {
    const { data } = await api.delete(`/orders/${id}`);
    return data;
};