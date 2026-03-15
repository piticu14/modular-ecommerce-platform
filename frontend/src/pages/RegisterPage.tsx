import { Container, TextField, Button, Box, Typography } from "@mui/material";
import { Formik } from "formik";
import * as Yup from "yup";
import { useNavigate, Link as RouterLink } from "react-router-dom";
import { Alert, Link } from "@mui/material";

import { useRegister } from "../hooks/mutations/useRegister";

const validationSchema = Yup.object({
    name: Yup.string()
        .min(2, "Name must have at least 2 characters")
        .required("Name is required"),

    email: Yup.string()
        .email("Invalid email")
        .required("Email is required"),

    password: Yup.string()
        .min(6, "Password must have at least 6 characters")
        .required("Password is required"),

    password_confirmation: Yup.string()
        .oneOf([Yup.ref("password")], "Passwords do not match")
        .required("Password confirmation is required"),
});

export default function RegisterPage() {
    const registerMutation = useRegister();
    const navigate = useNavigate();

    return (
        <Container maxWidth="sm">
            <Box mt={10}>
                <Typography variant="h4" gutterBottom>
                    Register
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
                    {({
                          values,
                          errors,
                          touched,
                          handleChange,
                          handleSubmit,
                      }) => (
                        <form onSubmit={handleSubmit}>
                            {!!registerMutation.error && (
                                <Box mb={2}>
                                    <Alert severity="error">
                                        Registration failed.
                                    </Alert>
                                </Box>
                            )}

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

                            <TextField
                                fullWidth
                                margin="normal"
                                label="Confirm password"
                                type="password"
                                name="password_confirmation"
                                value={values.password_confirmation}
                                onChange={handleChange}
                                error={
                                    touched.password_confirmation &&
                                    Boolean(errors.password_confirmation)
                                }
                                helperText={
                                    touched.password_confirmation &&
                                    errors.password_confirmation
                                }
                            />

                            <Box mt={3}>
                                <Button
                                    fullWidth
                                    variant="contained"
                                    type="submit"
                                    disabled={registerMutation.isPending}
                                >
                                    Register
                                </Button>
                            </Box>

                            <Box mt={2}>
                                <Link component={RouterLink} to="/login">
                                    Already have an account? Login
                                </Link>
                            </Box>
                        </form>
                    )}
                </Formik>
            </Box>
        </Container>
    );
}