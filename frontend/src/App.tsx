import React from 'react';
import './App.css';

function App() {
  return (
    <div className="App">
      <header className="App-header">
        <h1>E-Commerce Online Shopping System</h1>
        <nav>
          <ul style={{ listStyle: 'none', display: 'flex', gap: '20px', margin: 0, padding: 0 }}>
            <li><a href="#products" style={{ color: 'white', textDecoration: 'none' }}>Products</a></li>
            <li><a href="#shops" style={{ color: 'white', textDecoration: 'none' }}>Shops</a></li>
            <li><a href="#login" style={{ color: 'white', textDecoration: 'none' }}>Login</a></li>
            <li><a href="#register" style={{ color: 'white', textDecoration: 'none' }}>Register</a></li>
          </ul>
        </nav>
      </header>
      
      <main style={{ padding: '20px', maxWidth: '1200px', margin: '0 auto' }}>
        <section id="hero" style={{ textAlign: 'center', margin: '40px 0' }}>
          <h2>Welcome to Your Complete E-Commerce Solution</h2>
          <p>Buy products, open shops, and manage your online business all in one place.</p>
        </section>

        <section id="features" style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '20px', margin: '40px 0' }}>
          <div style={{ padding: '20px', border: '1px solid #ddd', borderRadius: '8px' }}>
            <h3>🛍️ Shop & Buy</h3>
            <p>Browse products from approved shops, add to cart, and choose your preferred payment method - card or cash on delivery.</p>
          </div>
          
          <div style={{ padding: '20px', border: '1px solid #ddd', borderRadius: '8px' }}>
            <h3>🏪 Open Your Shop</h3>
            <p>Register as a shop owner, get admin approval, and start selling your products to customers worldwide.</p>
          </div>
          
          <div style={{ padding: '20px', border: '1px solid #ddd', borderRadius: '8px' }}>
            <h3>⚙️ Admin Dashboard</h3>
            <p>Admins can approve shop registrations, manage users, and oversee the entire marketplace ecosystem.</p>
          </div>
        </section>

        <section id="api-info" style={{ backgroundColor: '#f5f5f5', padding: '20px', borderRadius: '8px', margin: '40px 0' }}>
          <h3>API Documentation</h3>
          <p>The backend API is available at <code>http://localhost:8000/api</code></p>
          <p>Key endpoints:</p>
          <ul style={{ textAlign: 'left', maxWidth: '600px', margin: '0 auto' }}>
            <li><code>POST /api/v1/register</code> - User registration</li>
            <li><code>POST /api/v1/login</code> - User login</li>
            <li><code>GET /api/v1/products</code> - List products</li>
            <li><code>GET /api/v1/shops</code> - List shops</li>
            <li><code>POST /api/v1/orders</code> - Create order</li>
          </ul>
        </section>

        <section id="demo-accounts" style={{ backgroundColor: '#e8f5e8', padding: '20px', borderRadius: '8px', margin: '40px 0' }}>
          <h3>Demo Accounts</h3>
          <p>Use these accounts to test the system:</p>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '15px', margin: '20px 0' }}>
            <div>
              <strong>Admin:</strong><br />
              admin@ecommerce.com<br />
              password: password
            </div>
            <div>
              <strong>Client:</strong><br />
              client@example.com<br />
              password: password
            </div>
            <div>
              <strong>Shop Owner:</strong><br />
              shopowner@example.com<br />
              password: password
            </div>
          </div>
        </section>
      </main>

      <footer style={{ textAlign: 'center', padding: '20px', backgroundColor: '#282c34', color: 'white', marginTop: '40px' }}>
        <p>&copy; 2024 E-Commerce System. Built with Laravel, React, Docker & PostgreSQL.</p>
      </footer>
    </div>
  );
}

export default App;
