# Auth Service

The Auth Service manages user registration, authentication, and JWT token issuance. It is the source of truth for user identities across the platform.

## Features

- **User Management**: Registration and user profile management.
- **Authentication**: JWT-based login and token refresh.
- **Security**: Password hashing and secure token management.

## Tech Stack

- **Framework**: Laravel 11.x
- **Database**: MySQL (auth database)
- **Cache/Session**: Redis

## API Endpoints

All endpoints are prefixed with `/api/v1/auth`.

- `POST /register` - Register a new user
- `POST /login` - Authenticate user and return JWT
- `POST /logout` - Invalidate current JWT (Protected)
- `POST /refresh` - Refresh current JWT (Protected)
- `GET /me` - Get authenticated user details (Protected)

## API Documentation

The API documentation is available at `docs/api`.

## Demo User

For testing purposes, you can use the following credentials:

- **Username**: `demo@example.com`
- **Password**: `password`

```php
User::updateOrCreate(
    ['email' => 'demo@example.com'],
    [
        'name' => 'Demo User',
        'password' => 'password',
    ]
);
```

## Events

- **Publishes**: None.
- **Consumes**: None.
