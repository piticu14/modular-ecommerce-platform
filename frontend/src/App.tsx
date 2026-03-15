import { BrowserRouter, Routes, Route } from "react-router-dom";

import Layout from "./components/Layout";

import CreateProductPage from "./pages/CreateProductPage";
import OrdersPage from "./pages/OrdersPage";
import CreateOrderPage from "./pages/CreateOrderPage";
import LoginPage from "./pages/LoginPage.tsx";
import {PrivateRoute} from "./components/PrivateRoutes.tsx";
import ProductsPage from "./pages/ProductPages.tsx";
import RegisterPage from "./pages/RegisterPage.tsx";
import ProductDetailPage from "./pages/ProductDetailPage.tsx";
import OrderDetailPage from "./pages/OrderDetailPage.tsx";

function App() {
  return (
      <BrowserRouter>
        <Routes>
          {/* public */}
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />

          {/* protected layout */}
          <Route
              element={
                <PrivateRoute>
                  <Layout />
                </PrivateRoute>
              }
          >
            <Route path="/" element={<ProductsPage />} />
            <Route path="/products" element={<ProductsPage />} />
            <Route path="/products/create" element={<CreateProductPage />} />
              <Route path="/products/:uuid" element={<ProductDetailPage />} />

            <Route path="/orders" element={<OrdersPage />} />
            <Route path="/orders/create" element={<CreateOrderPage />} />
              <Route path="/orders/:uuid" element={<OrderDetailPage />} />
          </Route>
        </Routes>
      </BrowserRouter>
  );
}

export default App;