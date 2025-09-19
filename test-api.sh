#!/bin/bash

# Simple API Test Script for E-Commerce System

BASE_URL="http://localhost:8000/api/v1"
ADMIN_EMAIL="admin@ecommerce.com"
ADMIN_PASSWORD="password"

echo "🧪 Testing E-Commerce API..."

# Test 1: Health check
echo "1️⃣ Testing health check..."
curl -s "$BASE_URL/../up" || echo "❌ Health check failed"

# Test 2: Register a new user
echo "2️⃣ Testing user registration..."
REGISTER_RESPONSE=$(curl -s -X POST "$BASE_URL/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password",
    "role": "client"
  }')

if echo "$REGISTER_RESPONSE" | grep -q "token"; then
    echo "✅ User registration successful"
    USER_TOKEN=$(echo "$REGISTER_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
else
    echo "❌ User registration failed"
    echo "$REGISTER_RESPONSE"
fi

# Test 3: Admin login
echo "3️⃣ Testing admin login..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "'$ADMIN_EMAIL'",
    "password": "'$ADMIN_PASSWORD'"
  }')

if echo "$LOGIN_RESPONSE" | grep -q "token"; then
    echo "✅ Admin login successful"
    ADMIN_TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
else
    echo "❌ Admin login failed"
    echo "$LOGIN_RESPONSE"
fi

# Test 4: List products (should be empty initially)
echo "4️⃣ Testing product listing..."
PRODUCTS_RESPONSE=$(curl -s "$BASE_URL/products")
if echo "$PRODUCTS_RESPONSE" | grep -q "data"; then
    echo "✅ Product listing works"
else
    echo "❌ Product listing failed"
    echo "$PRODUCTS_RESPONSE"
fi

# Test 5: List shops (should be empty initially)
echo "5️⃣ Testing shop listing..."
SHOPS_RESPONSE=$(curl -s "$BASE_URL/shops")
if echo "$SHOPS_RESPONSE" | grep -q "data"; then
    echo "✅ Shop listing works"
else
    echo "❌ Shop listing failed"
    echo "$SHOPS_RESPONSE"
fi

echo ""
echo "🎯 API Tests Complete!"
echo "To run full system tests, use: ./start-dev.sh"