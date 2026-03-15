import { useQuery } from "@tanstack/react-query";
import {getProduct} from "../../api/products.api.ts";

export const useProduct = (id: number) => {
    return useQuery({
        queryKey: ["products", id],
        queryFn: () => getProduct(id),
        enabled: !!id,
    });
};