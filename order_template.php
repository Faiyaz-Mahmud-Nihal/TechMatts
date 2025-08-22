<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= htmlspecialchars($order['order_number']) ?> | TechMatts</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #e63946;
            --primary-hover: #c1121f;
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #ffffff;
            --text-secondary: #aaaaaa;
            --border-color: #333;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }
        
        .order-details-page {
            padding: 120px 0 60px;
            min-height: 100vh;
        }
        
        .order-details-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .order-number {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .order-status {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        
        .status-pending { 
            background-color: rgba(255, 193, 7, 0.2); 
            color: #ffc107; 
        }
        .status-processing { 
            background-color: rgba(0, 123, 255, 0.2); 
            color: #007bff; 
        }
        .status-shipped { 
            background-color: rgba(40, 167, 69, 0.2); 
            color: #28a745; 
        }
        .status-delivered { 
            background-color: rgba(23, 162, 184, 0.2); 
            color: #17a2b8; 
        }
        .status-cancelled { 
            background-color: rgba(220, 53, 69, 0.2); 
            color: #dc3545; 
        }
        
        .order-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .shipping-info, .order-summary {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .section-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
            font-weight: 600;
        }
        
        .shipping-info p {
            margin-bottom: 10px;
            color: var(--text-color);
        }
        
        .shipping-info p strong {
            color: var(--text-color);
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: var(--text-color);
        }
        
        .grand-total {
            font-weight: bold;
            font-size: 1.2rem;
            margin: 20px 0;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
            color: var(--text-color);
        }
        
        .order-items {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items-table th {
            background-color: #252525;
            color: var(--text-color);
            text-align: left;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color);
            vertical-align: middle;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--border-color);
        }
        
        .continue-btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 30px;
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .continue-btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .order-grid {
                grid-template-columns: 1fr;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .items-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .shipping-info, .order-summary, .order-items {
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }
        
        .shipping-info { animation-delay: 0.1s; }
        .order-summary { animation-delay: 0.2s; }
        .order-items { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a href="index.html" class="logo">TechMatts</a>
            <div class="nav-links">
                <a href="index.html">Home</a>
                <a href="shop.html">Shop</a>
                <a href="contact.html">Contact</a>
                <a href="order.html">Order</a>
                <a href="login.html" id="auth-link">Login/Register</a>
                <a href="profile.html" id="profile-link">Profile</a>
                <a href="cart.html" class="icon-link cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
            </div>
            <button class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <main class="order-details-page">
        <div class="order-details-container">
            <div class="order-header">
                 <h1 class="order-number">Order #<?= htmlspecialchars($order['order_number']) ?></h1>
                <div class="order-status <?= $statusClass ?>">
                    <?= htmlspecialchars(ucfirst($order['status'])) ?>
                </div>
            </div>
            
            <div class="order-grid">
                <div class="shipping-info">
                    <h2 class="section-title">Shipping Address</h2>
                    <p><strong><?= htmlspecialchars($order['customer_name']) ?></strong></p>
                    <p><?= htmlspecialchars($order['shipping_address']) ?></p>
                    <p><?= htmlspecialchars($order['shipping_district']) ?></p>
                    <p><?= htmlspecialchars($order['shipping_postcode']) ?></p>
                    <p>Mobile: <?= htmlspecialchars($order['customer_phone'] ?? $order['shipping_phone']) ?></p>
                </div>
                
                <div class="order-summary">
                    <h2 class="section-title">Order Summary</h2>
                    
                    
                    
                    <?php if ($discount > 0): ?>
                    <div class="total-row">
                        <span>Discount:</span>
                        <span>-<?= number_format($discount, 2) ?>৳</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="total-row grand-total">
                        <span>Total:</span>
                        <span><?= number_format($total, 2) ?>৳</span>
                    </div>
                    
                    <div class="total-row">
                        <span>Paid:</span>
                        <span><?= number_format($paid, 2) ?>৳</span>
                    </div>
                    
                    <?php if ($due > 0): ?>
                    <div class="total-row">
                        <span>Due:</span>
                        <span><?= number_format($due, 2) ?>৳</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="total-row">
                        <span>Payment Method:</span>
                        <span><?= htmlspecialchars(ucfirst($order['payment_method'])) ?></span>
                    </div>
                    
                    <div class="total-row">
                        <span>Payment Status:</span>
                        <span><?= htmlspecialchars(ucfirst($order['payment_status'])) ?></span>
                    </div>
                    
                    <div class="total-row">
                        <span>Order Date:</span>
                        <span><?= $formattedDate ?></span>
                    </div>
                </div>
            </div>
            
            <div class="order-items">
                <h2 class="section-title">Products</h2>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($item['image_url'] ?? 'media/default-product.jpg') ?>" 
                                     alt="<?= htmlspecialchars($item['product_name']) ?>" class="item-image">
                            </td>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td><?= number_format($item['unit_price'], 2) ?>৳</td>
                            <td><?= number_format($item['item_total'], 2) ?>৳</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <a href="shop.html" class="continue-btn">Continue Shopping</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-section">
                <h3>TechMatts</h3>
                <p>Your one-stop shop for premium PC accessories that combine functionality with style.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="shop.html">Shop</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="login.html">Login</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: info@techmatts.com</p>
                <p>Phone: (123) 456-7890</p>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2023 TechMatts. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        
        if (hamburger && navLinks) {
            hamburger.addEventListener('click', () => {
                navLinks.classList.toggle('active');
                hamburger.classList.toggle('active');
            });
        }

        // Update auth link
        async function updateAuthLink() {
            try {
                const response = await fetch('auth.php?action=check', {
                    credentials: 'include'
                });
                const data = await response.json();
                
                const authLink = document.getElementById('auth-link');
                const profileLink = document.getElementById('profile-link');
                
                if (authLink && profileLink) {
                    if (data.loggedIn) {
                        const displayName = data.firstName || data.email || 'My Account';
                        authLink.textContent = displayName;
                        authLink.href = 'profile.html';
                        profileLink.style.display = 'inline-block';
                    } else {
                        authLink.textContent = 'Login/Register';
                        authLink.href = 'login.html';
                        profileLink.style.display = 'none';
                    }
                }
            } catch (error) {
                console.error('Error updating auth link:', error);
            }
        }

        // Update cart count
        async function updateCartCounter() {
            try {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
                document.querySelectorAll('.cart-count').forEach(el => el.textContent = totalItems);
            } catch (error) {
                console.error('Error updating cart counter:', error);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateAuthLink();
            updateCartCounter();
        });
    </script>
</body>
</html>