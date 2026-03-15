import { useQuery } from "@tanstack/react-query";
import { getStockReservations } from "../../api/stockReservations.api";

export const useStockReservations = (uuid: string) => {
    return useQuery({
        queryKey: ["stock-reservations", uuid],
        queryFn: () => getStockReservations(uuid),
        enabled: !!uuid
    });
};