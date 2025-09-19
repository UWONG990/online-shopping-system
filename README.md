# E-Commerce Online Shopping System

A complete E-commerce monorepo with Laravel backend, React frontend, Docker, and PostgreSQL. The system supports multiple user roles including admin, clients, and shop owners with comprehensive features for online shopping and shop management.

## Features

### Admin Features
- Approve/reject shop registration requests
- Manage all shops and products
- View and manage all orders
- User management

### Client Features
- Browse products and shops
- Add products to cart and checkout
- Multiple payment methods (Card/Cash on Delivery)
- Order tracking
- Register to become a shop owner

### Shop Owner Features
- Register and manage shops (requires admin approval)
- Add and manage products
- View shop orders
- Inventory management

## Tech Stack

- **Backend**: Laravel 11 with PHP 8.2
- **Frontend**: React 18 with TypeScript
- **Database**: PostgreSQL 15
- **Authentication**: Laravel Sanctum
- **Containerization**: Docker & Docker Compose
- **Reverse Proxy**: Nginx

## Project Structure

```
├── backend/                 # Laravel API
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── Dockerfile
├── frontend/               # React application
│   ├── src/
│   ├── public/
│   └── Dockerfile
├── docker/                 # Docker configurations
│   └── nginx/
├── docker-compose.yml      # Docker services
└── README.md
```

## Quick Start

### Prerequisites
- Docker & Docker Compose
- Git

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd online-shopping-system
   ```

2. **Environment Setup**
   ```bash
   cp backend/.env.example backend/.env
   ```

3. **Start the application**
   ```bash
   docker-compose up -d
   ```

4. **Install dependencies and setup database**
   ```bash
   # Backend setup
   docker-compose exec backend composer install
   docker-compose exec backend php artisan key:generate
   docker-compose exec backend php artisan migrate
   docker-compose exec backend php artisan db:seed
   
   # Frontend setup (if needed)
   docker-compose exec frontend npm install
   ```

### Access the Application

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000/api
- **Nginx (Combined)**: http://localhost:80

### Default Credentials

After seeding, you can use these accounts:

- **Admin**: admin@ecommerce.com / password
- **Client**: client@example.com / password  
- **Shop Owner**: shopowner@example.com / password

## API Documentation

### Authentication Endpoints
- `POST /api/v1/register` - User registration
- `POST /api/v1/login` - User login
- `POST /api/v1/logout` - User logout (authenticated)
- `GET /api/v1/profile` - Get user profile (authenticated)

### Shop Endpoints
- `GET /api/v1/shops` - List shops
- `POST /api/v1/shops` - Create shop (authenticated)
- `GET /api/v1/shops/{id}` - Get shop details
- `PUT /api/v1/shops/{id}` - Update shop (owner/admin)
- `DELETE /api/v1/shops/{id}` - Delete shop (owner/admin)
- `POST /api/v1/shops/{id}/request-approval` - Request shop approval

### Product Endpoints
- `GET /api/v1/products` - List products
- `POST /api/v1/products` - Create product (shop owner/admin)
- `GET /api/v1/products/{id}` - Get product details
- `PUT /api/v1/products/{id}` - Update product (owner/admin)
- `DELETE /api/v1/products/{id}` - Delete product (owner/admin)

### Order Endpoints
- `GET /api/v1/orders` - List orders (user's orders or all for admin)
- `POST /api/v1/orders` - Create order (authenticated)
- `GET /api/v1/orders/{id}` - Get order details
- `PUT /api/v1/orders/{id}` - Update order status (admin only)
- `POST /api/v1/orders/{id}/pay` - Process payment

### Admin Endpoints
- `GET /api/v1/admin/shops/pending` - List pending shops (admin)
- `POST /api/v1/admin/shops/{id}/approve` - Approve shop (admin)
- `POST /api/v1/admin/shops/{id}/reject` - Reject shop (admin)

## Development

### Backend Development
```bash
# Run Laravel development server
docker-compose exec backend php artisan serve

# Run migrations
docker-compose exec backend php artisan migrate

# Create new migration
docker-compose exec backend php artisan make:migration create_table_name

# Create new controller
docker-compose exec backend php artisan make:controller ControllerName
```

### Frontend Development
```bash
# Start development server
docker-compose exec frontend npm start

# Install new package
docker-compose exec frontend npm install package-name

# Run tests
docker-compose exec frontend npm test
```

### Database Management
```bash
# Access PostgreSQL
docker-compose exec postgres psql -U postgres -d ecommerce

# Run fresh migrations with seeding
docker-compose exec backend php artisan migrate:fresh --seed
```

## Payment Methods

The system supports two payment methods:

1. **Prepaid (Card Payment)**: Credit/Debit card payments with simulated payment gateway
2. **Cash on Delivery (COD)**: Pay when the order is delivered

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).