<?php
require_once 'admin_auth.php'; // Keep this for security
require_once 'db_connection.php'; // Add this for database access
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | TechMatts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #e2136e;
            --primary-dark: #c10e5d;
            --dark: #1e1e1e;
            --light: #f5f5f5;
            --gray: #aaa;
            --success: #4CAF50;
            --warning: #FFC107;
            --danger: #F44336;
            --info: #2196F3;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            padding: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            z-index: 100;
        }
        
        .admin-logo {
            padding: 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
            text-align: center;
            background-color: rgba(0,0,0,0.2);
        }
        
        .admin-logo h2 {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            background: linear-gradient(to right, #e2136e, #ff8a00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .admin-menu {
            list-style: none;
            padding: 0 15px;
        }
        
        .admin-menu li {
            margin-bottom: 5px;
            position: relative;
        }
        
        .admin-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #ddd;
            text-decoration: none;
            transition: all 0.3s;
            border-radius: 6px;
            font-size: 15px;
        }
        
        .admin-menu a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .admin-menu a.active {
            background: linear-gradient(90deg, rgba(226,19,110,0.2) 0%, rgba(226,19,110,0) 100%);
            color: white;
            border-left: 3px solid var(--primary);
        }
        
        .admin-menu a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .admin-menu a.active i {
            color: var(--primary);
        }
        
        /* Main Content Styles */
        .admin-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
            background-color: #f5f5f5;
            min-height: 100vh;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .admin-header h1 {
            font-size: 28px;
            color: #333;
            font-weight: 600;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
        }
        
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        
        .user-profile span {
            font-weight: 500;
            color: #555;
        }
        
        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid var(--primary);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            margin-top: 0;
            color: #666;
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        
        .stat-card .change {
            color: var(--success);
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .stat-card .change i {
            margin-right: 5px;
        }
        
        .stat-card .change.down {
            color: var(--danger);
        }
        
        /* Recent Orders */
        .recent-orders {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .recent-orders h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
            color: #333;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f9f9f9;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        
        tr:hover {
            background-color: #fafafa;
        }
        
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-pending {
            background: #FFF3CD;
            color: #856404;
        }
        
        .status-processing {
            background: #CCE5FF;
            color: #004085;
        }
        
        .status-delivered {
            background: #D4EDDA;
            color: #155724;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(226,19,110,0.3);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .admin-sidebar {
                width: 250px;
            }
            
            .admin-content {
                margin-left: 250px;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .admin-content {
                margin-left: 0;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-logo">
                <h2>TechMatts Admin</h2>
            </div>
            <ul class="admin-menu">
                <li><a href="admin.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin-products.php"><i class="fas fa-box-open"></i> Products</a></li>
                <li><a href="admin-orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="admin-users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="admin-suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1>Dashboard Overview</h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=e2136e&color=fff" alt="Admin">
                    <span>Admin</span>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <div class="value" id="total-products">0</div>
                    <div class="change"><i class="fas fa-arrow-up"></i> <span id="product-change">0%</span> from last month</div>
                </div>
                
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <div class="value" id="total-orders">0</div>
                    <div class="change"><i class="fas fa-arrow-up"></i> <span id="order-change">0%</span> from last month</div>
                </div>
                
                <div class="stat-card">
                    <h3>Pending Orders</h3>
                    <div class="value" id="pending-orders">0</div>
                    <div class="change down"><i class="fas fa-arrow-down"></i> <span id="pending-change">0%</span> from last month</div>
                </div>
                
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="value" id="total-users">0</div>
                    <div class="change"><i class="fas fa-arrow-up"></i> <span id="user-change">0%</span> from last month</div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="recent-orders">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="recent-orders">
                        <!-- Will be filled by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // Check if user is admin
            try {
                const response = await fetch('auth.php?action=check');
                const data = await response.json();
                
                if (!data.loggedIn || data.role !== 'admin') {
                    window.location.href = 'login.html';
                    return;
                }
                
                // Load all dashboard data
                loadDashboardData();
                
                // Set interval to refresh data every 5 minutes
                setInterval(loadDashboardData, 300000);
                
            } catch (error) {
                console.error('Error:', error);
                window.location.href = 'login.html';
            }
            
            async function loadDashboardData() {
                await loadStats();
                await loadRecentOrders();
            }
            
            async function loadStats() {
                try {
                    const response = await fetch('admin_api.php?action=stats');
                    const data = await response.json();
                    
                    if (data.success) {
                        // Update counts
                        document.getElementById('total-products').textContent = data.stats.total_products;
                        document.getElementById('total-orders').textContent = data.stats.total_orders;
                        document.getElementById('pending-orders').textContent = data.stats.pending_orders;
                        document.getElementById('total-users').textContent = data.stats.total_users;
                        
                        // Update percentage changes if available
                        if (data.stats.product_change) {
                            document.getElementById('product-change').textContent = data.stats.product_change + '%';
                        }
                        if (data.stats.order_change) {
                            document.getElementById('order-change').textContent = data.stats.order_change + '%';
                        }
                        if (data.stats.pending_change) {
                            document.getElementById('pending-change').textContent = data.stats.pending_change + '%';
                        }
                        if (data.stats.user_change) {
                            document.getElementById('user-change').textContent = data.stats.user_change + '%';
                        }
                    }
                } catch (error) {
                    console.error('Error loading stats:', error);
                }
            }
            
            async function loadRecentOrders() {
                try {
                    const response = await fetch('admin_api.php?action=recent_orders');
                    const data = await response.json();
                    
                    if (data.success) {
                        const ordersTable = document.getElementById('recent-orders');
                        ordersTable.innerHTML = data.orders.map(order => `
                            <tr>
                                <td>${order.order_number}</td>
                                <td>${order.customer_name}</td>
                                <td>${new Date(order.order_date).toLocaleDateString()}</td>
                                <td>${order.total_amount}৳</td>
                                <td><span class="status status-${order.status.toLowerCase()}">${order.status}</span></td>
                                <td><a href="admin-order-details.php?id=${order.order_id}" class="btn btn-outline">View</a></td>
                            </tr>
                        `).join('');
                    }
                } catch (error) {
                    console.error('Error loading recent orders:', error);
                }
            }
        });
    </script>
</body>
</html>