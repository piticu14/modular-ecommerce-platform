import {
  Container,
  Typography,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
  Button,
  Box,
  Stack,
} from "@mui/material";

import { useNavigate } from "react-router-dom";
import { useIntl } from "react-intl";

import { useOrders } from "../hooks/queries/useOrders";
import { useDeleteOrder } from "../hooks/mutations/useDeleteOrder";

export default function OrdersPage() {
  const { data: orders, isLoading } = useOrders();
  const deleteOrder = useDeleteOrder();
  const navigate = useNavigate();
  const intl = useIntl();

  if (isLoading) {
    return <div>{intl.formatMessage({ id: "orders.loading" })}</div>;
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
          {intl.formatMessage({ id: "orders.title" })}
        </Typography>

        <Button variant="contained" onClick={() => navigate("/orders/create")}>
          {intl.formatMessage({ id: "orders.create" })}
        </Button>
      </Box>

      <Table>
        <TableHead>
          <TableRow>
            <TableCell>
              {intl.formatMessage({ id: "orders.table.id" })}
            </TableCell>

            <TableCell>
              {intl.formatMessage({ id: "orders.table.user_id" })}
            </TableCell>

            <TableCell>
              {intl.formatMessage({ id: "orders.table.status" })}
            </TableCell>

            <TableCell>
              {intl.formatMessage({ id: "orders.table.currency" })}
            </TableCell>

            <TableCell>
              {intl.formatMessage({ id: "orders.table.subtotal" })}
            </TableCell>

            <TableCell>
              {intl.formatMessage({ id: "orders.table.total" })}
            </TableCell>

            <TableCell>
              {intl.formatMessage({ id: "orders.table.actions" })}
            </TableCell>
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
                    {intl.formatMessage({
                      id: "orders.action.detail",
                    })}
                  </Button>

                  <Button
                    size="small"
                    variant="outlined"
                    color="error"
                    disabled={deleteOrder.isPending}
                    onClick={() => deleteOrder.mutate(order.id)}
                  >
                    {intl.formatMessage({
                      id: "orders.action.delete",
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
