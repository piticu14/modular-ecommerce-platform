# API Gateway

The API Gateway is the single entry point for all client requests. It handles routing, authentication (JWT), and communication with internal microservices.

## Features

- **Routing**: Proxies requests to internal services (Auth, Product, Order).
- **Authentication**: Validates JWT tokens for protected routes.
- **Service Discovery**: Routes requests based on environment-configured service URLs.
- **Error Handling**: Standardized error responses for the frontend.

## Tech Stack

- **Framework**: Laravel 11.x
- **Communication**: REST (to internal services), RabbitMQ (for events/jobs), Redis (for caching/throttling).

## API Endpoints

### Auth
- `POST /api/v1/auth/login` - Login and receive JWT
- `POST /api/v1/auth/register` - Register a new user
- `GET /api/v1/auth/me` - Get current user info (Protected)
- `POST /api/v1/auth/refresh` - Refresh JWT token (Protected)
- `POST /api/v1/auth/logout` - Invalidate JWT token (Protected)

### Products
- `GET /api/v1/products` - List products
- `GET /api/v1/products/{id}` - Get product details
- `POST /api/v1/products` - Create product (Internal/Admin)
- `DELETE /api/v1/products/{id}` - Delete product (Internal/Admin)
- `GET /api/v1/products/{id}/stock-reservations` - Check product stock reservations

### Orders
- `GET /api/v1/orders` - List user orders (Protected)
- `GET /api/v1/orders/{id}` - Get order details (Protected)
- `POST /api/v1/orders` - Place a new order (Protected)
- `DELETE /api/v1/orders/{id}` - Cancel/Delete order (Protected)

## Configuration

The gateway requires the following environment variables to be set (see `.env.example`):

- `AUTH_SERVICE_URL`
- `PRODUCT_SERVICE_URL`
- `ORDER_SERVICE_URL`
- `JWT_SECRET`
