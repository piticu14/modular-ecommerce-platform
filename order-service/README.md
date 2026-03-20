# Order Service

The Order Service manages the lifecycle of customer orders, from creation to completion. It interacts with the Product Service for stock validation and the Auth Service for user context.

## Features

- **Order Management**: Create, list, show, and delete orders.
- **Transactional Outbox**: Ensures reliable event delivery using the Outbox pattern.
- **Event-Driven**: Listens for product-related events and dispatches order-related events.
- **Background Processing**: Uses workers for asynchronous tasks and outbox processing.

## Tech Stack

- **Framework**: Laravel 11.x
- **Database**: MySQL (orders database)
- **Cache**: Redis
- **Messaging**: RabbitMQ
- **Patterns**: Transactional Outbox

## API Endpoints

All endpoints are prefixed with `/api/v1/orders`.

- `GET /` - List orders
- `POST /` - Create a new order
- `GET /{id}` - Get order details
- `DELETE /{id}` - Delete/Cancel an order

## Background Workers

- **Queue Worker**: Processes standard Laravel jobs (`php artisan queue:work rabbitmq`).
- **Outbox Worker**: Processes pending events in the outbox table (`php artisan outbox:work`).

## Events

- **Consumes**: Events from Product Service (e.g., StockReserved).
- **Publishes**: Order-related events (e.g., OrderCreated, OrderCancelled).
