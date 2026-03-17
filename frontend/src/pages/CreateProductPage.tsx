import { Container, TextField, Button, Box, Typography } from "@mui/material";
import { Formik } from "formik";
import * as Yup from "yup";
import { useNavigate } from "react-router-dom";
import { useIntl } from "react-intl";

import { useCreateProduct } from "../hooks/mutations/useCreateProduct";

export default function CreateProductPage() {
  const createProduct = useCreateProduct();
  const navigate = useNavigate();
  const intl = useIntl();

  const validationSchema = Yup.object({
    name: Yup.string().required(
      intl.formatMessage({ id: "validation.name_required" }),
    ),

    price: Yup.number()
      .required(intl.formatMessage({ id: "validation.price_required" }))
      .positive(intl.formatMessage({ id: "validation.price_positive" })),

    currency: Yup.string()
      .required(intl.formatMessage({ id: "validation.currency_required" }))
      .length(3, intl.formatMessage({ id: "validation.currency_length" })),

    stock_on_hand: Yup.number().min(
      0,
      intl.formatMessage({ id: "validation.stock_non_negative" }),
    ),
  });

  return (
    <Container maxWidth="sm">
      <Box mt={10}>
        <Typography variant="h4" gutterBottom>
          {intl.formatMessage({ id: "product.create.title" })}
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
              price: Math.round(values.price * 100),
            });

            navigate("/products");
          }}
        >
          {({ values, errors, touched, handleChange, handleSubmit }) => (
            <form onSubmit={handleSubmit}>
              <TextField
                fullWidth
                margin="normal"
                label={intl.formatMessage({
                  id: "product.create.name",
                })}
                name="name"
                value={values.name}
                onChange={handleChange}
                error={touched.name && Boolean(errors.name)}
                helperText={touched.name && errors.name}
              />

              <TextField
                fullWidth
                margin="normal"
                label={intl.formatMessage({
                  id: "product.create.price",
                })}
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
                label={intl.formatMessage({
                  id: "product.create.currency",
                })}
                name="currency"
                value={values.currency}
                onChange={handleChange}
                error={touched.currency && Boolean(errors.currency)}
                helperText={touched.currency && errors.currency}
              />

              <TextField
                fullWidth
                margin="normal"
                label={intl.formatMessage({
                  id: "product.create.stock_on_hand",
                })}
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
                  {intl.formatMessage({
                    id: "product.create.submit",
                  })}
                </Button>
              </Box>
            </form>
          )}
        </Formik>
      </Box>
    </Container>
  );
}
