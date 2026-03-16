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
import { useIntl } from "react-intl";

import { useProducts } from "../hooks/queries/useProducts";
import { useDeleteProduct } from "../hooks/mutations/useDeleteProduct";

export default function ProductsPage() {
    const { data: products, isLoading } = useProducts();
    const deleteProduct = useDeleteProduct();
    const navigate = useNavigate();
    const intl = useIntl();

    if (isLoading) {
        return <div>{intl.formatMessage({ id: "products.loading" })}</div>;
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
                    {intl.formatMessage({ id: "products.title" })}
                </Typography>

                <Button
                    variant="contained"
                    onClick={() => navigate("/products/create")}
                >
                    {intl.formatMessage({ id: "products.create" })}
                </Button>
            </Box>

            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell>
                            {intl.formatMessage({ id: "products.table.id" })}
                        </TableCell>

                        <TableCell>
                            {intl.formatMessage({ id: "products.table.name" })}
                        </TableCell>

                        <TableCell>
                            {intl.formatMessage({ id: "products.table.price" })}
                        </TableCell>

                        <TableCell>
                            {intl.formatMessage({ id: "products.table.currency" })}
                        </TableCell>

                        <TableCell>
                            {intl.formatMessage({ id: "products.table.stock" })}
                        </TableCell>

                        <TableCell>
                            {intl.formatMessage({ id: "products.table.reserved" })}
                        </TableCell>

                        <TableCell>
                            {intl.formatMessage({ id: "products.table.available" })}
                        </TableCell>

                        <TableCell>
                            {intl.formatMessage({ id: "products.table.actions" })}
                        </TableCell>
                    </TableRow>
                </TableHead>

                <TableBody>
                    {products?.map((product) => (
                        <TableRow key={product.uuid}>
                            <TableCell>{product.uuid}</TableCell>
                            <TableCell>{product.name}</TableCell>
                            <TableCell>{(product.price / 100).toFixed(2)}</TableCell>
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
                                        {intl.formatMessage({
                                            id: "products.action.detail"
                                        })}
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
                                        {intl.formatMessage({
                                            id: "products.action.delete"
                                        })}
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