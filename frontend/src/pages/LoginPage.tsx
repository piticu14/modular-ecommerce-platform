import { Container, TextField, Button, Box, Typography, Alert, Link } from "@mui/material";
import { Formik } from "formik";
import * as Yup from "yup";
import { useEffect } from "react";
import { useNavigate, Link as RouterLink } from "react-router-dom";

import { useLogin } from "../hooks/mutations/useLogin";

const validationSchema = Yup.object({
    email: Yup.string()
        .email("Invalid email")
        .required("Email is required"),

    password: Yup.string()
        .required("Password is required")
});

export default function LoginPage() {
    const loginMutation = useLogin();
    const navigate = useNavigate();

    useEffect(() => {
        const token = localStorage.getItem("access_token");

        if (token) {
            navigate("/products");
        }
    }, []);

    return (
        <Container maxWidth="sm">
            <Box mt={10}>
                <Typography variant="h4" gutterBottom>
                    Login
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
                                        Invalid email or password
                                    </Alert>
                                </Box>
                            )}

                            <TextField
                                fullWidth
                                margin="normal"
                                label="Email"
                                name="email"
                                value={values.email}
                                onChange={handleChange}
                                error={touched.email && Boolean(errors.email)}
                                helperText={touched.email && errors.email}
                            />

                            <TextField
                                fullWidth
                                margin="normal"
                                label="Password"
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
                                    Login
                                </Button>
                            </Box>

                            <Box mt={2}>
                                <Link component={RouterLink} to="/register">
                                    Don't have an account? Register
                                </Link>
                            </Box>

                        </form>
                    )}
                </Formik>
            </Box>
        </Container>
    );
}