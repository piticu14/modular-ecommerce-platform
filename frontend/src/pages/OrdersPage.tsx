import {
    Container,
    Typography,
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow,
    Button,
    Box, Stack
} from "@mui/material";

import { useNavigate } from "react-router-dom";
import { useOrders } from "../hooks/queries/useOrders";
import { useDeleteOrder } from "../hooks/mutations/useDeleteOrder";

export default function OrdersPage() {
    const { data: orders, isLoading } = useOrders();
    const deleteOrder = useDeleteOrder();
    const navigate = useNavigate();

    if (isLoading) {
        return <div>Loading...</div>;
    }

    return (
        <Container>
            <Box
                display="flex"
                justifyContent="space-between"
                alignItems="center"
                mt={4}
                mb={2}
            >
                <Typography variant="h4">
                    Orders
                </Typography>

                <Button
                    variant="contained"
                    onClick={() => navigate("/orders/create")}
                >
                    Create order
                </Button>
            </Box>

            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell>ID</TableCell>
                        <TableCell>User ID</TableCell>
                        <TableCell>Status</TableCell>
                        <TableCell>Currency</TableCell>
                        <TableCell>Subtotal</TableCell>
                        <TableCell>Total</TableCell>
                        <TableCell>Actions</TableCell>
                    </TableRow>
                </TableHead>

                <TableBody>
                    {orders?.map((order) => (
                        <TableRow key={order.id}>
                            <TableCell>{order.id}</TableCell>
                            <TableCell>{order.user_id}</TableCell>
                            <TableCell>{order.status}</TableCell>
                            <TableCell>{order.currency}</TableCell>
                            <TableCell>{order.subtotal}</TableCell>
                            <TableCell>{order.total}</TableCell>

                            <TableCell>
                                <Stack direction="row" spacing={1}>
                                    <Button
                                        size="small"
                                        variant="outlined"
                                        onClick={() => navigate(`/orders/${order.id}`)}
                                    >
                                        Detail
                                    </Button>

                                    <Button
                                        size="small"
                                        variant="outlined"
                                        color="error"
                                        disabled={deleteOrder.isPending}
                                        onClick={() => deleteOrder.mutate(order.id)}
                                    >
                                        Delete
                                    </Button>
                                </Stack>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </Container>
    );
}