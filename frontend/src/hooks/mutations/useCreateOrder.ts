import { useMutation, useQueryClient } from "@tanstack/react-query";
import { createOrder } from "../../api/orders.api";

export const useCreateOrder = () => {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: createOrder,
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ["orders"] });
        },
    });
};