import { api } from "./client";
import type {ApiResponse} from "../types/ApiResponse.ts";
import type {CreateProductPayload, Product} from "../types/Product.ts";


export const getProducts = async (): Promise<Product[]> => {
    const response = await api.get<ApiResponse<Product[]>>("/products");
    return response.data.data;
};

export const getProduct = async (id: number): Promise<Product> => {
    const response = await api.get<ApiResponse<Product>>(`/products/${id}`);
    return response.data.data;
};

export const createProduct = async (payload: CreateProductPayload): Promise<Product> => {
    const response = await api.post<ApiResponse<Product>>("/products", payload);
    return response.data.data;
};

export const deleteProduct = async (id: number) => {
    const { data } = await api.delete(`/products/${id}`);
    return data;
};