import {
    Container,
    Typography,
    Box,
    Table,
    TableHead,
    TableRow,
    TableCell,
    TableBody,
    Link
} from "@mui/material";

import { Navigate, useParams } from "react-router-dom";
import { useOrder } from "../hooks/queries/useOrder";
import type { OrderItem } from "../types/Order.ts";
import { Link as RouterLink } from "react-router";
import { useIntl } from "react-intl";

export default function OrderDetailPage() {
    const { uuid } = useParams();
    const intl = useIntl();

    if (!uuid) {
        return <Navigate to="/orders" />;
    }

    const { data: order, isLoading } = useOrder(uuid);

    if (isLoading) {
        return (
            <div>
                {intl.formatMessage({ id: "order.loading" })}
            </div>
        );
    }

    if (!order) {
        return (
            <div>
                {intl.formatMessage({ id: "order.not_found" })}
            </div>
        );
    }

    return (
        <Container>
            <Typography variant="h4" mb={3}>
                {intl.formatMessage(
                    { id: "order.detail.title" },
                    { id: order.id }
                )}
            </Typography>

            <Box mb={3}>
                <Typography>
                    {intl.formatMessage({ id: "order.detail.status" })}: {order.status}
                </Typography>

                <Typography>
                    {intl.formatMessage({ id: "order.detail.created" })}: {order.created_at}
                </Typography>
            </Box>

            <Typography variant="h6" mb={2}>
                {intl.formatMessage({ id: "order.detail.items" })}
            </Typography>

            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell>
                            {intl.formatMessage({ id: "order.detail.product_uuid" })}
                        </TableCell>
                        <TableCell>
                            {intl.formatMessage({ id: "order.detail.quantity" })}
                        </TableCell>
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