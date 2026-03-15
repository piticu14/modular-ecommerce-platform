import { api } from "./client";
import type {ApiResponse} from "../types/ApiResponse.ts";
import type {StockReservation} from "../types/StockReservation.ts";

export const getStockReservations = async (
    productId: number
): Promise<StockReservation[]> => {

    const response = await api.get<ApiResponse<StockReservation[]>>(
        `/products/${productId}/stock-reservations`
    );

    return response.data.data;
};