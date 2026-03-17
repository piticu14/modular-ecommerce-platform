import { useMutation, useQueryClient } from "@tanstack/react-query";
import { deleteOrder } from "../../api/orders.api";

export const useDeleteOrder = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: deleteOrder,

    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: ["orders"],
      });
    },
  });
};
