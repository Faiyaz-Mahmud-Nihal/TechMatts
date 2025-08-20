<?php
require_once 'supplier_auth.php';
require_once 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Dashboard | TechMatts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #3a86ff; /* Different color from admin */
            --primary-dark: #2667cc;
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
        
        .supplier-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .supplier-sidebar {
            width: 280px;
            background: linear-gradient(135deg, #1a3a5a 0%, #2d4d6e 100%); /* Different gradient */
            color: white;
            padding: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            z-index: 100;
        }
        
        .supplier-logo {
            padding: 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
            text-align: center;
            background-color: rgba(0,0,0,0.2);
        }
        
        .supplier-logo h2 {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            background: linear-gradient(to right, #3a86ff, #4cc9f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .supplier-menu {
            list-style: none;
            padding: 0 15px;
        }
        
        .supplier-menu li {
            margin-bottom: 5px;
            position: relative;
        }
        
        .supplier-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #ddd;
            text-decoration: none;
            transition: all 0.3s;
            border-radius: 6px;
            font-size: 15px;
        }
        
        .supplier-menu a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .supplier-menu a.active {
            background: linear-gradient(90deg, rgba(58,134,255,0.2) 0%, rgba(58,134,255,0) 100%);
            color: white;
            border-left: 3px solid var(--primary);
        }
        
        .supplier-menu a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .supplier-menu a.active i {
            color: var(--primary);
        }
        
        /* Main Content Styles */
        .supplier-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
            background-color: #f5f5f5;
            min-height: 100vh;
        }
        
        .supplier-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .supplier-header h1 {
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
        
        /* Products Table - Similar to admin-products.php */
        .products-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .search-filter-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: white;
        }
        
        .search-box input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(58,134,255,0.1);
        }
        
        .filter-select {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            color: #555;
            min-width: 180px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(58,134,255,0.1);
        }
        
        .btn-add {
            background-color: var(--primary);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            margin-left: auto;
        }
        
        .btn-add:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(58,134,255,0.3);
        }
        
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .product-table th, .product-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .product-table th {
            background-color: #f9f9f9;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        
        .product-table tr:last-child td {
            border-bottom: none;
        }
        
        .product-table tr:hover {
            background-color: rgba(58,134,255,0.03);
        }
        
        .product-img-container {
            display: flex;
            align-items: center;
        }
        
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .product-img:hover {
            transform: scale(1.1);
        }
        
        .no-image {
            width: 50px;
            height: 50px;
            background: #f5f5f5;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 12px;
        }
        
        .product-name {
            font-weight: 500;
            color: #333;
        }
        
        .product-category {
            background: rgba(33, 150, 243, 0.1);
            color: var(--info);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        
        .product-price {
            font-weight: 600;
            color: #333;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-active {
            background: rgba(76, 175, 80, 0.1);
            color: var(--success);
        }
        
        .status-inactive {
            background: rgba(244, 67, 54, 0.1);
            color: var(--danger);
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: none;
            cursor: pointer;
        }
        
        .btn-edit {
            background-color: var(--info);
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #1a83d8;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(33, 150, 243, 0.3);
        }
        
        .btn-toggle {
            background-color: var(--warning);
            color: #333;
        }
        
        .btn-toggle:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(255, 193, 7, 0.3);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .supplier-sidebar {
                width: 250px;
            }
            
            .supplier-content {
                margin-left: 250px;
            }
        }
        
        @media (max-width: 768px) {
            .supplier-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .supplier-content {
                margin-left: 0;
            }
            
            .search-filter-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box, .filter-select {
                width: 100%;
            }
            
            .btn-add {
                width: 100%;
                justify-content: center;
            }
            
            .product-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="supplier-container">
        <!-- Sidebar -->
        <div class="supplier-sidebar">
            <div class="supplier-logo">
                <h2>TechMatts Supplier</h2>
            </div>
            <ul class="supplier-menu">
                <li><a href="supplier.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="supplier-content">
            <div class="supplier-header">
                <h1>My Products</h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Supplier&background=3a86ff&color=fff" alt="Supplier">
                    <span>Supplier</span>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Active Products</h3>
                    <div class="value" id="active-products">0</div>
                    <div class="change"><i class="fas fa-arrow-up"></i> <span id="product-change">0%</span> from last month</div>
                </div>
                
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <div class="value" id="total-products">0</div>
                    <div class="change"><i class="fas fa-arrow-up"></i> <span id="total-change">0%</span> from last month</div>
                </div>
            </div>
            
            <!-- Products Section -->
            <div class="search-filter-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search" placeholder="Search products...">
                </div>
                <select class="filter-select" id="status-filter">
                    <option value="">All Statuses</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <a href="supplier-add-product.php" class="btn-add">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>
            
            <div class="products-container">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-table-body">
                        <!-- Will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // Check if user is supplier
            try {
                const response = await fetch('auth.php?action=check');
                const data = await response.json();
                
                if (!data.loggedIn || data.role !== 'supplier') {
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
                await loadProducts();
            }
            
            async function loadStats() {
                try {
                    const response = await fetch('supplier_api.php?action=stats');
                    const data = await response.json();
                    
                    if (data.success) {
                        // Update counts
                        document.getElementById('active-products').textContent = data.stats.active_products;
                        document.getElementById('total-products').textContent = data.stats.total_products;
                        
                        // Update percentage changes if available
                        if (data.stats.product_change) {
                            document.getElementById('product-change').textContent = data.stats.product_change + '%';
                        }
                        if (data.stats.total_change) {
                            document.getElementById('total-change').textContent = data.stats.total_change + '%';
                        }
                    }
                } catch (error) {
                    console.error('Error loading stats:', error);
                }
            }
            
            async function loadProducts() {
                try {
                    const response = await fetch('supplier_api.php?action=get_products');
                    const data = await response.json();
                    
                    if (data.success) {
                        const productsTable = document.getElementById('products-table-body');
                        productsTable.innerHTML = data.products.map(product => `
                            <tr>
                                <td>
                                    ${product.main_image ? 
                                        `<img src="${product.main_image}" class="product-img" alt="${product.name}">` : 
                                        `<div class="no-image">No Image</div>`}
                                </td>
                                <td>${product.name}</td>
                                <td>${product.category.charAt(0).toUpperCase() + product.category.slice(1)}</td>
                                <td>${product.price}৳</td>
                                <td>
                                    <span class="status-${product.is_active ? 'active' : 'inactive'}">
                                        ${product.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td>
                                    <a href="supplier-edit-product.php?id=${product.product_id}" class="action-btn btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button class="action-btn btn-toggle" onclick="toggleProductStatus('${product.product_id}', ${product.is_active})">
                                        <i class="fas fa-power-off"></i> ${product.is_active ? 'Deactivate' : 'Activate'}
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    }
                } catch (error) {
                    console.error('Error loading products:', error);
                }
            }
            
            // Simple client-side filtering
            document.getElementById('search').addEventListener('input', filterProducts);
            document.getElementById('status-filter').addEventListener('change', filterProducts);
            
            function filterProducts() {
                const search = document.getElementById('search').value.toLowerCase();
                const status = document.getElementById('status-filter').value;
                
                const rows = document.querySelectorAll('#products-table-body tr');
                
                rows.forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const rowStatus = row.cells[4].querySelector('span').textContent.toLowerCase();
                    
                    const matchesSearch = name.includes(search);
                    const matchesStatus = status === '' || 
                        (status === '1' && rowStatus === 'active') || 
                        (status === '0' && rowStatus === 'inactive');
                    
                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
        
        async function toggleProductStatus(productId, currentStatus) {
            if (confirm(`Are you sure you want to ${currentStatus ? 'deactivate' : 'activate'} this product?`)) {
                try {
                    const response = await fetch('supplier_api.php?action=toggle_product_status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}&is_active=${currentStatus ? '0' : '1'}`
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Product status updated successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    alert('An error occurred. Please try again.');
                    console.error(error);
                }
            }
        }
    </script>
</body>
</html>