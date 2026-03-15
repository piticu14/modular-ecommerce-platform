import { api } from "./client";
import type {ApiResponse} from "../types/ApiResponse.ts";
import type {CreateProductPayload, Product} from "../types/Product.ts";


export const getProducts = async (): Promise<Product[]> => {
    const response = await api.get<ApiResponse<Product[]>>("/products");
    return response.data.data;
};

export const getProduct = async (uuid: string): Promise<Product> => {
    const response = await api.get<ApiResponse<Product>>(`/products/${uuid}`);
    return response.data.data;
};

export const createProduct = async (payload: CreateProductPayload): Promise<Product> => {
    const response = await api.post<ApiResponse<Product>>("/products", payload);
    return response.data.data;
};

export const deleteProduct = async (uuid: string) => {
    const { data } = await api.delete(`/products/${uuid}`);
    return data;
};