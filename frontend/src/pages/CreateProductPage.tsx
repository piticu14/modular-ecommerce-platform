import { Container, TextField, Button, Box, Typography } from "@mui/material";
import { Formik } from "formik";
import * as Yup from "yup";
import { useNavigate } from "react-router-dom";

import { useCreateProduct } from "../hooks/mutations/useCreateProduct";

const validationSchema = Yup.object({
    name: Yup.string().required("Name is required"),

    price: Yup.number()
        .required("Price is required")
        .positive("Price must be positive"),

    currency: Yup.string()
        .required("Currency is required")
        .length(3, "Currency must be 3 characters"),

    stock_on_hand: Yup.number()
        .min(0, "Stock cannot be negative")
});

export default function CreateProductPage() {
    const createProduct = useCreateProduct();
    const navigate = useNavigate();

    return (
        <Container maxWidth="sm">
            <Box mt={10}>
                <Typography variant="h4" gutterBottom>
                    Create Product
                </Typography>

                <Formik
                    initialValues={{
                        name: "",
                        price: 0,
                        currency: "CZK",
                        stock_on_hand: 0,
                        stock_reserved: 0,
                    }}
                    validationSchema={validationSchema}
                    onSubmit={async (values) => {
                        await createProduct.mutateAsync({
                            ...values,
                            price: Math.round(values.price * 100)
                        });
                        navigate("/products");
                    }}
                >
                    {({
                          values,
                          errors,
                          touched,
                          handleChange,
                          handleSubmit
                      }) => (
                        <form onSubmit={handleSubmit}>
                            <TextField
                                fullWidth
                                margin="normal"
                                label="Name"
                                name="name"
                                value={values.name}
                                onChange={handleChange}
                                error={touched.name && Boolean(errors.name)}
                                helperText={touched.name && errors.name}
                            />

                            <TextField
                                fullWidth
                                margin="normal"
                                label="Price"
                                name="price"
                                type="number"
                                value={values.price}
                                onChange={handleChange}
                                error={touched.price && Boolean(errors.price)}
                                helperText={touched.price && errors.price}
                            />

                            <TextField
                                fullWidth
                                margin="normal"
                                label="Currency"
                                name="currency"
                                value={values.currency}
                                onChange={handleChange}
                                error={touched.currency && Boolean(errors.currency)}
                                helperText={touched.currency && errors.currency}
                            />

                            <TextField
                                fullWidth
                                margin="normal"
                                label="Stock on hand"
                                name="stock_on_hand"
                                type="number"
                                value={values.stock_on_hand}
                                onChange={handleChange}
                                error={touched.stock_on_hand && Boolean(errors.stock_on_hand)}
                                helperText={touched.stock_on_hand && errors.stock_on_hand}
                            />

                            <Box mt={3}>
                                <Button
                                    fullWidth
                                    variant="contained"
                                    type="submit"
                                    disabled={createProduct.isPending}
                                >
                                    Create product
                                </Button>
                            </Box>
                        </form>
                    )}
                </Formik>
            </Box>
        </Container>
    );
}