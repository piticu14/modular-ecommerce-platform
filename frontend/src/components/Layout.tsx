import { AppBar, Toolbar, Typography, Button, Container } from "@mui/material";
import { NavLink, useNavigate, Outlet } from "react-router-dom";

export default function Layout() {
    const navigate = useNavigate();

    const logout = () => {
        localStorage.removeItem("access_token");
        navigate("/login");
    };

    return (
        <>
            <AppBar position="fixed">
                <Toolbar>
                    <Typography variant="h6" sx={{ flexGrow: 1 }}>
                        Microservices Demo
                    </Typography>

                    <Button
                        color="inherit"
                        component={NavLink}
                        to="/products"
                        sx={{
                            "&.active": {
                                fontWeight: "bold",
                                textDecoration: "underline"
                            }
                        }}
                    >
                        Products
                    </Button>

                    <Button
                        color="inherit"
                        component={NavLink}
                        to="/orders"
                        sx={{
                            "&.active": {
                                fontWeight: "bold",
                                textDecoration: "underline"
                            }
                        }}
                    >
                        Orders
                    </Button>

                    <Button
                        color="inherit"
                        onClick={logout}
                    >
                        Logout
                    </Button>
                </Toolbar>
            </AppBar>

            {/* offset for fixed AppBar */}
            <Toolbar />

            <Container sx={{ mt: 4 }}>
                <Outlet />
            </Container>
        </>
    );
}