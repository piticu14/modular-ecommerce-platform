import {
    AppBar,
    Toolbar,
    Typography,
    Button,
    Container,
    Select,
    MenuItem,
    Box
} from "@mui/material";

import { NavLink, useNavigate, Outlet } from "react-router-dom";
import { useIntl } from "react-intl";
import { useContext } from "react";

import { LocaleContext } from "../providers/IntlProvider";

export default function Layout() {
    const navigate = useNavigate();
    const intl = useIntl();
    const { locale, setLocale } = useContext(LocaleContext);

    const logout = () => {
        localStorage.removeItem("access_token");
        navigate("/login");
    };

    return (
        <>
            <AppBar position="fixed">
                <Toolbar>

                    <Typography variant="h6" sx={{ flexGrow: 1 }}>
                        {intl.formatMessage({ id: "layout.title" })}
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
                        {intl.formatMessage({ id: "layout.products" })}
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
                        {intl.formatMessage({ id: "layout.orders" })}
                    </Button>

                    <Box
                        sx={{
                            display: "flex",
                            alignItems: "center",
                            mx: 2
                        }}
                    >

                        <Select
                            value={locale}
                            onChange={(e) =>
                                setLocale(e.target.value as "cs" | "en")
                            }
                            size="small"
                            variant="outlined"
                            sx={{
                                color: "white",
                                ".MuiOutlinedInput-notchedOutline": {
                                    borderColor: "rgba(255,255,255,0.3)"
                                },
                                "&:hover .MuiOutlinedInput-notchedOutline": {
                                    borderColor: "white"
                                },
                                ".MuiSvgIcon-root": {
                                    color: "white"
                                }
                            }}
                        >
                            <MenuItem value="cs">🇨🇿 CZ</MenuItem>
                            <MenuItem value="en">🇬🇧 EN</MenuItem>
                        </Select>
                    </Box>

                    <Button color="inherit" onClick={logout}>
                        {intl.formatMessage({ id: "layout.logout" })}
                    </Button>

                </Toolbar>
            </AppBar>

            <Toolbar />

            <Container sx={{ mt: 4 }}>
                <Outlet />
            </Container>
        </>
    );
}