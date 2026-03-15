import { api } from "./client";
import type {ApiResponse} from "../types/ApiResponse.ts";
import type {CreateOrderPayload, Order} from "../types/Order.ts";

export const getOrders = async (): Promise<Order[]> => {
    const response = await api.get<ApiResponse<Order[]>>("/orders");
    return response.data.data;
};

export const getOrder = async (id: number): Promise<Order> => {
    const response = await api.get<ApiResponse<Order>> (`/orders/${id}`);
    return response.data.data;
};

export const createOrder = async (payload: CreateOrderPayload): Promise<Order> => {
    const response = await api.post<ApiResponse<Order>>("/orders", payload);
    return response.data.data;
};

export const deleteOrder = async (id: number) => {
    const { data } = await api.delete(`/orders/${id}`);
    return data;
};