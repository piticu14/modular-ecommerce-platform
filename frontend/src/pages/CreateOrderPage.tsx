import {
  Container,
  TextField,
  Button,
  Box,
  Typography,
  IconButton,
} from "@mui/material";
import DeleteIcon from "@mui/icons-material/Delete";
import { Formik, FieldArray } from "formik";
import * as Yup from "yup";
import { useNavigate } from "react-router-dom";
import { Autocomplete } from "@mui/material";
import { useIntl } from "react-intl";

import { useCreateOrder } from "../hooks/mutations/useCreateOrder";
import { useProducts } from "../hooks/queries/useProducts";

export default function CreateOrderPage() {
  const createOrder = useCreateOrder();
  const { data: products } = useProducts();
  const navigate = useNavigate();
  const intl = useIntl();

  const productList = products ?? [];

  const validationSchema = Yup.object({
    items: Yup.array()
      .of(
        Yup.object({
          product_uuid: Yup.string().required(
            intl.formatMessage({ id: "validation.product_required" }),
          ),
          quantity: Yup.number()
            .required(
              intl.formatMessage({
                id: "validation.quantity_required",
              }),
            )
            .positive(
              intl.formatMessage({
                id: "validation.quantity_positive",
              }),
            ),
        }),
      )
      .min(1),
  });

  return (
    <Container maxWidth="sm">
      <Box mt={10}>
        <Typography variant="h4">
          {intl.formatMessage({ id: "order.create.title" })}
        </Typography>

        <Formik
          initialValues={{
            items: [
              {
                product_uuid: "",
                quantity: 1,
              },
            ],
          }}
          validationSchema={validationSchema}
          onSubmit={async (values) => {
            await createOrder.mutateAsync(values);
            navigate("/orders");
          }}
        >
          {({ values, setFieldValue, handleChange, handleSubmit }) => (
            <form onSubmit={handleSubmit}>
              <FieldArray name="items">
                {({ push, remove }) => (
                  <>
                    {values.items.map((item, index) => {
                      const selectedProduct =
                        productList.find((p) => p.id === item.product_uuid) ||
                        null;

                      return (
                        <Box key={index} display="flex" gap={2} mt={2}>
                          <Autocomplete
                            fullWidth
                            options={productList}
                            value={selectedProduct}
                            isOptionEqualToValue={(option, value) =>
                              option.id === value.id
                            }
                            getOptionLabel={(option) => option.name}
                            onChange={(_, value) =>
                              setFieldValue(
                                `items.${index}.product_uuid`,
                                value?.id ?? "",
                              )
                            }
                            renderInput={(params) => (
                              <TextField
                                {...params}
                                label={intl.formatMessage({
                                  id: "order.create.product",
                                })}
                              />
                            )}
                          />

                          <TextField
                            label={intl.formatMessage({
                              id: "order.create.quantity",
                            })}
                            type="number"
                            name={`items.${index}.quantity`}
                            value={item.quantity}
                            onChange={handleChange}
                          />

                          <IconButton
                            onClick={() => remove(index)}
                            disabled={values.items.length === 1}
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
                            quantity: 1,
                          })
                        }
                      >
                        {intl.formatMessage({
                          id: "order.create.add_item",
                        })}
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
                  {intl.formatMessage({
                    id: "order.create.submit",
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
