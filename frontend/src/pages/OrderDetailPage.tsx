import {
    Container,
    Typography,
    Box,
    Table,
    TableHead,
    TableRow,
    TableCell,
    TableBody
} from "@mui/material";

import {Navigate, useParams} from "react-router-dom";
import { useOrder } from "../hooks/queries/useOrder";

export default function OrderDetailPage() {
    const { uuid } = useParams();
    const { data: order, isLoading } = useOrder(uuid);

    if (!uuid) {
        return <Navigate to="/orders" />;
    }

    if (isLoading) {
        return <div>Loading...</div>;
    }

    if (!order) {
        return <div>Order not found</div>;
    }

    return (
        <Container>
            <Typography variant="h4" mb={3}>
                Order #{order.id}
            </Typography>

            <Box mb={3}>
                <Typography>Status: {order.status}</Typography>
                <Typography>Created: {order.created_at}</Typography>
            </Box>

            <Typography variant="h6" mb={2}>
                Items
            </Typography>

            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell>Product ID</TableCell>
                        <TableCell>Quantity</TableCell>
                    </TableRow>
                </TableHead>

                <TableBody>
                    {order.items.map((item: any, index: number) => (
                        <TableRow key={index}>
                            <TableCell>{item.product_id}</TableCell>
                            <TableCell>{item.quantity}</TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </Container>
    );
}