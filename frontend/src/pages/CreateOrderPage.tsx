import {
    Container,
    TextField,
    Button,
    Box,
    Typography,
    IconButton
} from "@mui/material";
import DeleteIcon from "@mui/icons-material/Delete";
import { Formik, FieldArray } from "formik";
import * as Yup from "yup";
import { useNavigate } from "react-router-dom";
import { Autocomplete } from "@mui/material";

import { useCreateOrder } from "../hooks/mutations/useCreateOrder";
import { useProducts } from "../hooks/queries/useProducts";

const validationSchema = Yup.object({
    items: Yup.array()
        .of(
            Yup.object({
                product_uuid: Yup.string().required("Product is required"),
                quantity: Yup.number()
                    .required("Quantity is required")
                    .positive("Quantity must be positive")
            })
        )
        .min(1)
});

export default function CreateOrderPage() {
    const createOrder = useCreateOrder();
    const { data: products } = useProducts();
    const navigate = useNavigate();

    const productList = products ?? [];

    return (
        <Container maxWidth="sm">
            <Box mt={10}>
                <Typography variant="h4">
                    Create Order
                </Typography>

                <Formik
                    initialValues={{
                        items: [
                            {
                                product_uuid: "",
                                quantity: 1
                            }
                        ]
                    }}
                    validationSchema={validationSchema}
                    onSubmit={async (values) => {
                        await createOrder.mutateAsync(values);
                        navigate("/orders");
                    }}
                >
                    {({
                          values,
                          setFieldValue,
                          handleChange,
                          handleSubmit
                      }) => (
                        <form onSubmit={handleSubmit}>
                            {/*<pre>{JSON.stringify(errors, null, 2)}</pre>*/}
                            <FieldArray name="items">
                                {({ push, remove }) => (
                                    <>
                                        {values.items.map((item, index) => {
                                            const selectedProduct =
                                                productList.find(
                                                    (p) => p.uuid === item.product_uuid
                                                ) || null;
                                            return (
                                                <Box
                                                    key={index}
                                                    display="flex"
                                                    gap={2}
                                                    mt={2}
                                                >
                                                    <Autocomplete
                                                        fullWidth
                                                        options={productList}
                                                        value={selectedProduct}
                                                        isOptionEqualToValue={(option, value) =>
                                                            option.uuid === value.uuid
                                                        }
                                                        getOptionLabel={(option) => option.name}
                                                        onChange={(_, value) =>
                                                            setFieldValue(
                                                                `items.${index}.product_uuid`,
                                                                value?.uuid ?? ""
                                                            )
                                                        }
                                                        renderInput={(params) => (
                                                            <TextField {...params} label="Product" />
                                                        )}
                                                    />

                                                    <TextField
                                                        label="Quantity"
                                                        type="number"
                                                        name={`items.${index}.quantity`}
                                                        value={item.quantity}
                                                        onChange={handleChange}
                                                    />

                                                    <IconButton
                                                        onClick={() => remove(index)}
                                                        disabled={
                                                            values.items.length === 1
                                                        }
                                                    >
                                                        <DeleteIcon />
                                                    </IconButton>
                                                </Box>
                                            );
                                        })}

                                        <Box mt={2}>
                                            <Button
                                                variant="outlined"
                                                onClick={() =>
                                                    push({
                                                        product_uuid: "",
                                                        quantity: 1
                                                    })
                                                }
                                            >
                                                Add item
                                            </Button>
                                        </Box>
                                    </>
                                )}
                            </FieldArray>

                            <Box mt={4}>
                                <Button
                                    fullWidth
                                    variant="contained"
                                    type="submit"
                                    disabled={createOrder.isPending}
                                >
                                    Create order
                                </Button>
                            </Box>
                        </form>
                    )}
                </Formik>
            </Box>
        </Container>
    );
}