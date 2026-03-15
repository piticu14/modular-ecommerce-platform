import { useQuery } from "@tanstack/react-query";
import {getOrder} from "../../api/orders.api.ts";

export const useOrder = (id: number) => {
    return useQuery({
        queryKey: ["orders", id],
        queryFn: () => getOrder(id),
        enabled: !!id,
    });
};