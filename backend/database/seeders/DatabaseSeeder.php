<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ecommerce.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1234567890',
            'address' => '123 Admin Street, Admin City',
        ]);

        // Create test client
        User::create([
            'name' => 'John Client',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'phone' => '+1234567891',
            'address' => '456 Client Avenue, Client City',
        ]);

        // Create test shop owner
        User::create([
            'name' => 'Jane Shop Owner',
            'email' => 'shopowner@example.com',
            'password' => Hash::make('password'),
            'role' => 'shop_owner',
            'phone' => '+1234567892',
            'address' => '789 Shop Boulevard, Shop City',
        ]);

        // Create categories
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and gadgets'],
            ['name' => 'Clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Books', 'description' => 'Books and literature'],
            ['name' => 'Home & Garden', 'description' => 'Home improvement and gardening'],
            ['name' => 'Sports', 'description' => 'Sports equipment and accessories'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}