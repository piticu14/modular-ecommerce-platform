import { useQuery } from "@tanstack/react-query";
import {getOrders} from "../../api/orders.api.ts";

export const useOrders = () => {
    return useQuery({
        queryKey: ["orders"],
        queryFn: getOrders,
    });
};