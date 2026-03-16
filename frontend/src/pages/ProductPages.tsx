import {
    Container,
    Typography,
    Table,
    TableHead,
    TableRow,
    TableCell,
    TableBody,
    Button,
    Stack,
    Box
} from "@mui/material";

import { useNavigate } from "react-router-dom";

import { useProducts } from "../hooks/queries/useProducts";
import { useDeleteProduct } from "../hooks/mutations/useDeleteProduct";

export default function ProductsPage() {
    const { data: products, isLoading } = useProducts();
    const deleteProduct = useDeleteProduct();
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
                    Products
                </Typography>

                <Button
                    variant="contained"
                    onClick={() => navigate("/products/create")}
                >
                    Create product
                </Button>
            </Box>

            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell>ID</TableCell>
                        <TableCell>Name</TableCell>
                        <TableCell>Price</TableCell>
                        <TableCell>Currency</TableCell>
                        <TableCell>Stock</TableCell>
                        <TableCell>Reserved</TableCell>
                        <TableCell>Available</TableCell>
                        <TableCell>Actions</TableCell>
                    </TableRow>
                </TableHead>

                <TableBody>
                    {products?.map((product) => (
                        <TableRow key={product.uuid}>
                            <TableCell>{product.uuid}</TableCell>
                            <TableCell>{product.name}</TableCell>
                            <TableCell>{product.price}</TableCell>
                            <TableCell>{product.currency}</TableCell>
                            <TableCell>{product.stock_on_hand}</TableCell>
                            <TableCell>{product.stock_reserved}</TableCell>
                            <TableCell>{product.stock_available}</TableCell>

                            <TableCell>
                                <Stack direction="row" spacing={1}>
                                    <Button
                                        size="small"
                                        variant="outlined"
                                        onClick={() =>
                                            navigate(`/products/${product.uuid}`)
                                        }
                                    >
                                        Detail
                                    </Button>

                                    <Button
                                        size="small"
                                        variant="outlined"
                                        color="error"
                                        disabled={deleteProduct.isPending}
                                        onClick={() =>
                                            deleteProduct.mutate(product.uuid)
                                        }
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