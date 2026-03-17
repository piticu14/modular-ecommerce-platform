import { Container, TextField, Button, Box, Typography } from "@mui/material";
import { Formik } from "formik";
import * as Yup from "yup";
import { useNavigate, Link as RouterLink } from "react-router-dom";
import { Alert, Link } from "@mui/material";

import { useRegister } from "../hooks/mutations/useRegister";
import { useIntl } from "react-intl";

export default function RegisterPage() {
  const registerMutation = useRegister();
  const navigate = useNavigate();

  const intl = useIntl();

  const validationSchema = Yup.object({
    name: Yup.string()
      .min(2, intl.formatMessage({ id: "validation.name_min" }))
      .required(intl.formatMessage({ id: "validation.name_required" })),

    email: Yup.string()
      .email(intl.formatMessage({ id: "validation.email_invalid" }))
      .required(intl.formatMessage({ id: "validation.email_required" })),

    password: Yup.string()
      .min(6, intl.formatMessage({ id: "validation.password_min" }))
      .required(intl.formatMessage({ id: "validation.password_required" })),

    password_confirmation: Yup.string()
      .oneOf(
        [Yup.ref("password")],
        intl.formatMessage({ id: "validation.password_match" }),
      )
      .required(
        intl.formatMessage({
          id: "validation.password_confirmation_required",
        }),
      ),
  });

  return (
    <Container maxWidth="sm">
      <Box mt={10}>
        <Typography variant="h4">
          {intl.formatMessage({ id: "register.title" })}
        </Typography>

        <Formik
          initialValues={{
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
          }}
          validationSchema={validationSchema}
          onSubmit={async (values) => {
            try {
              await registerMutation.mutateAsync(values);
              navigate("/login");
            } catch (error) {
              console.error(error);
            }
          }}
        >
          {({ values, errors, touched, handleChange, handleSubmit }) => (
            <form onSubmit={handleSubmit}>
              {!!registerMutation.error && (
                <Box mb={2}>
                  <Alert severity="error">Registration failed.</Alert>
                </Box>
              )}

              <TextField
                fullWidth
                margin="normal"
                label={intl.formatMessage({ id: "register.name" })}
                name="name"
                value={values.name}
                onChange={handleChange}
                error={touched.name && Boolean(errors.name)}
                helperText={touched.name && errors.name}
              />

              <TextField
                fullWidth
                margin="normal"
                label={intl.formatMessage({ id: "register.email" })}
                name="email"
                value={values.email}
                onChange={handleChange}
                error={touched.email && Boolean(errors.email)}
                helperText={touched.email && errors.email}
              />

              <TextField
                fullWidth
                margin="normal"
                label={intl.formatMessage({ id: "register.password" })}
                type="password"
                name="password"
                value={values.password}
                onChange={handleChange}
                error={touched.password && Boolean(errors.password)}
                helperText={touched.password && errors.password}
              />

              <TextField
                fullWidth
                margin="normal"
                label={intl.formatMessage({
                  id: "register.password_confirmation",
                })}
                type="password"
                name="password_confirmation"
                value={values.password_confirmation}
                onChange={handleChange}
                error={
                  touched.password_confirmation &&
                  Boolean(errors.password_confirmation)
                }
                helperText={
                  touched.password_confirmation && errors.password_confirmation
                }
              />

              <Box mt={3}>
                <Button
                  fullWidth
                  variant="contained"
                  type="submit"
                  disabled={registerMutation.isPending}
                >
                  {intl.formatMessage({ id: "register.submit" })}
                </Button>
              </Box>

              <Box mt={2}>
                <Link component={RouterLink} to="/login">
                  {intl.formatMessage({ id: "register.login_link" })}
                </Link>
              </Box>
            </form>
          )}
        </Formik>
      </Box>
    </Container>
  );
}
