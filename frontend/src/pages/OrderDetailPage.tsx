import {
    Container,
    Typography,
    Box,
    Table,
    TableHead,
    TableRow,
    TableCell,
    TableBody, Link
} from "@mui/material";

import {Navigate, useParams} from "react-router-dom";
import { useOrder } from "../hooks/queries/useOrder";
import type {OrderItem} from "../types/Order.ts";
import {Link as RouterLink} from "react-router";

export default function OrderDetailPage() {
    const { uuid } = useParams();

    if (!uuid) {
        return <Navigate to="/orders" />;
    }

    const { data: order, isLoading } = useOrder(uuid);

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
                        <TableCell>Product UUID</TableCell>
                        <TableCell>Quantity</TableCell>
                    </TableRow>
                </TableHead>

                <TableBody>
                    {order.items.map((item: OrderItem, index: number) => (
                        <TableRow key={index}>
                            <TableCell>
                                <Link
                                    component={RouterLink}
                                    to={`/products/${item.product_uuid}`}
                                >
                                    #{item.product_uuid}
                                </Link>
                            </TableCell>
                            <TableCell>{item.quantity}</TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </Container>
    );
}