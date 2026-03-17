import { useQuery } from "@tanstack/react-query";
import { getProduct } from "../../api/products.api.ts";

export const useProduct = (uuid?: string) => {
  return useQuery({
    queryKey: ["products", uuid],
    queryFn: () => getProduct(uuid!),
    enabled: !!uuid,
  });
};
