import {
    Container,
    TextField,
    Button,
    Box,
    Typography,
    Alert,
    Link
} from "@mui/material";
import { Formik } from "formik";
import * as Yup from "yup";
import { useEffect } from "react";
import { useNavigate, Link as RouterLink } from "react-router-dom";
import { useIntl } from "react-intl";

import { useLogin } from "../hooks/mutations/useLogin";

export default function LoginPage() {
    const loginMutation = useLogin();
    const navigate = useNavigate();
    const intl = useIntl();

    const validationSchema = Yup.object({
        email: Yup.string()
            .email(intl.formatMessage({ id: "validation.email_invalid" }))
            .required(intl.formatMessage({ id: "validation.email_required" })),

        password: Yup.string()
            .required(intl.formatMessage({ id: "validation.password_required" }))
    });

    useEffect(() => {
        const token = localStorage.getItem("access_token");

        if (token) {
            navigate("/products");
        }
    }, [navigate]);

    return (
        <Container maxWidth="sm">
            <Box mt={10}>
                <Typography variant="h4" gutterBottom>
                    {intl.formatMessage({ id: "login.title" })}
                </Typography>

                <Formik
                    initialValues={{
                        email: "",
                        password: ""
                    }}
                    validationSchema={validationSchema}
                    onSubmit={async (values) => {
                        try {
                            await loginMutation.mutateAsync(values);
                            navigate("/products");
                        } catch (error) {
                            console.error(error);
                        }
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

                            {!!loginMutation.error && (
                                <Box mb={2}>
                                    <Alert severity="error">
                                        {intl.formatMessage({
                                            id: "login.invalid_credentials"
                                        })}
                                    </Alert>
                                </Box>
                            )}

                            <TextField
                                fullWidth
                                margin="normal"
                                label={intl.formatMessage({ id: "login.email" })}
                                name="email"
                                value={values.email}
                                onChange={handleChange}
                                error={touched.email && Boolean(errors.email)}
                                helperText={touched.email && errors.email}
                            />

                            <TextField
                                fullWidth
                                margin="normal"
                                label={intl.formatMessage({ id: "login.password" })}
                                type="password"
                                name="password"
                                value={values.password}
                                onChange={handleChange}
                                error={touched.password && Boolean(errors.password)}
                                helperText={touched.password && errors.password}
                            />

                            <Box mt={3}>
                                <Button
                                    fullWidth
                                    variant="contained"
                                    type="submit"
                                    disabled={loginMutation.isPending}
                                >
                                    {intl.formatMessage({ id: "login.submit" })}
                                </Button>
                            </Box>

                            <Box mt={2}>
                                <Link component={RouterLink} to="/register">
                                    {intl.formatMessage({
                                        id: "login.register_link"
                                    })}
                                </Link>
                            </Box>

                        </form>
                    )}
                </Formik>
            </Box>
        </Container>
    );
}