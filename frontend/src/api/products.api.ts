import { api } from "./client";

export type Product = {
    id: number;
    name: string;
    price: number;
    currency: string;
    stock_on_hand: number;
    stock_reserved: number;
    created_at: string;
    updated_at: string;
};

export type CreateProductPayload = {
    id: number;
    name: string;
    price: number;
    currency: string;
    stock_on_hand: number;
    stock_reserved: number;
};

export const getProducts = async (): Promise<Product[]> => {
    const { data } = await api.get("/products");
    return data;
};

export const getProduct = async (id: number): Promise<Product> => {
    const { data } = await api.get(`/products/${id}`);
    return data;
};

export const createProduct = async (payload: CreateProductPayload): Promise<Product> => {
    const { data } = await api.post("/orders", payload);
    return data;
};

export const deleteProduct = async (id: number) => {
    const { data } = await api.delete(`/products/${id}`);
    return data;
};