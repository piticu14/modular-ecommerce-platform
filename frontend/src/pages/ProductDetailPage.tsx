import {
    Container,
    Typography,
    Grid,
    Card,
    CardContent,
    Table,
    TableHead,
    TableRow,
    TableCell,
    TableBody,
    Chip,
    Link
} from "@mui/material";

import {useParams, Link as RouterLink, Navigate} from "react-router-dom";

import { useProduct } from "../hooks/queries/useProduct";
import { useStockReservations } from "../hooks/queries/useStockReservations";

export default function ProductDetailPage() {
    const { uuid } = useParams();

    if (!uuid) {
        return <Navigate to="/products" />;
    }

    const { data: product, isLoading } = useProduct(uuid);
    const { data: reservations } = useStockReservations(uuid);

    if (isLoading) return <div>Loading...</div>;
    if (!product) return <div>Product not found</div>;

    const statusColor = (status: string) => {
        switch (status) {
            case "RESERVED":
                return "warning";
            case "CONFIRMED":
                return "success";
            case "CANCELLED":
                return "error";
            default:
                return "default";
        }
    };

    return (
        <Container>

            <Typography variant="h4" mb={3}>
                {product.name}
            </Typography>

            {/* PRODUCT STATS */}
            <Grid container spacing={3} mb={4}>

                <Grid size={{ xs: 12, md: 4 }}>
                    <Card>
                        <CardContent>
                            <Typography color="text.secondary">
                                Stock
                            </Typography>

                            <Typography variant="h4">
                                {product.stock_on_hand}
                            </Typography>
                        </CardContent>
                    </Card>
                </Grid>

                <Grid size={{ xs: 12, md: 4 }}>
                    <Card>
                        <CardContent>
                            <Typography color="text.secondary">
                                Reserved
                            </Typography>

                            <Typography variant="h4">
                                {product.stock_reserved}
                            </Typography>
                        </CardContent>
                    </Card>
                </Grid>

                <Grid size={{ xs: 12, md: 4 }}>
                    <Card>
                        <CardContent>
                            <Typography color="text.secondary">
                                Available
                            </Typography>

                            <Typography variant="h4">
                                {product.stock_available}
                            </Typography>
                        </CardContent>
                    </Card>
                </Grid>

            </Grid>

            <Typography variant="h6" mb={2}>
                Stock Reservations
            </Typography>

            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell>Order</TableCell>
                        <TableCell>Quantity</TableCell>
                        <TableCell>Status</TableCell>
                        <TableCell>Created</TableCell>
                        <TableCell>Updated</TableCell>
                    </TableRow>
                </TableHead>

                <TableBody>
                    {reservations?.map((reservation) => (
                        <TableRow key={reservation.id}>

                            <TableCell>
                                <Link
                                    component={RouterLink}
                                    to={`/orders/${reservation.order_uuid}`}
                                >
                                    #{reservation.order_uuid}
                                </Link>
                            </TableCell>

                            <TableCell>
                                {reservation.quantity}
                            </TableCell>

                            <TableCell>
                                <Chip
                                    label={reservation.status}
                                    color={statusColor(reservation.status)}
                                    size="small"
                                />
                            </TableCell>

                            <TableCell>
                                {reservation.created_at}
                            </TableCell>

                            <TableCell>
                                {reservation.updated_at}
                            </TableCell>

                        </TableRow>
                    ))}
                </TableBody>
            </Table>

        </Container>
    );
}