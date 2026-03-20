# Product Service

The Product Service manages the product catalog, including inventory and stock reservations.

## Features

- **Product Management**: CRUD operations for products.
- **Inventory Tracking**: Manage stock levels.
- **Stock Reservation**: Reserve stock when an order is placed to prevent overselling.
- **Transactional Outbox**: Reliable event publishing for inventory changes.

## Tech Stack

- **Framework**: Laravel 11.x
- **Database**: MySQL (products database)
- **Cache**: Redis
- **Messaging**: RabbitMQ

## API Endpoints

All endpoints are prefixed with `/api/v1/products`.

- `GET /` - List products
- `POST /` - Create product
- `GET /{id}` - Get product details
- `PUT/PATCH /{id}` - Update product
- `DELETE /{id}` - Delete product
- `GET /by-uuid` - Find products by multiple UUIDs
- `GET /{id}/stock-reservations` - List stock reservations for a product

## Events

- **Publishes**: `StockReserved`, `StockReleased`, `ProductCreated`, `ProductUpdated`, `ProductDeleted`.
- **Consumes**: `OrderCreated` (to trigger stock reservation), `OrderCancelled` (to release stock).
