import { useQuery } from "@tanstack/react-query";
import { getOrder } from "../../api/orders.api.ts";

export const useOrder = (uuid?: string) => {
  return useQuery({
    queryKey: ["orders", uuid],
    queryFn: () => getOrder(uuid!),
    enabled: !!uuid,
  });
};
