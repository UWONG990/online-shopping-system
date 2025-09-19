#!/bin/bash

# E-Commerce Development Setup Script

echo "🚀 Starting E-Commerce System Setup..."

# Start PostgreSQL first
echo "📊 Starting PostgreSQL database..."
docker compose up -d postgres

# Wait for PostgreSQL to be ready
echo "⏳ Waiting for PostgreSQL to be ready..."
sleep 10

# Check if PostgreSQL is ready
docker compose exec postgres pg_isready -U postgres -d ecommerce

if [ $? -eq 0 ]; then
    echo "✅ PostgreSQL is ready!"
else
    echo "❌ PostgreSQL failed to start. Please check the logs."
    exit 1
fi

# Start backend
echo "🔧 Starting Laravel backend..."
docker compose up -d backend

# Wait for backend to be ready
echo "⏳ Waiting for backend to initialize..."
sleep 15

# Run migrations and seed data
echo "📋 Running database migrations and seeding..."
docker compose exec backend php artisan migrate --force
docker compose exec backend php artisan db:seed --force

# Start frontend
echo "⚛️ Starting React frontend..."
docker compose up -d frontend

# Start nginx
echo "🌐 Starting Nginx reverse proxy..."
docker compose up -d nginx

echo ""
echo "🎉 E-Commerce System is now running!"
echo ""
echo "📍 Access Points:"
echo "   Frontend: http://localhost:3000"
echo "   Backend API: http://localhost:8000"
echo "   Full App (via Nginx): http://localhost"
echo ""
echo "👤 Demo Accounts:"
echo "   Admin: admin@ecommerce.com / password"
echo "   Client: client@example.com / password"
echo "   Shop Owner: shopowner@example.com / password"
echo ""
echo "📚 API Documentation: See README.md"
echo ""
echo "🔧 Useful Commands:"
echo "   View logs: docker compose logs [service]"
echo "   Stop all: docker compose down"
echo "   Restart: docker compose restart [service]"