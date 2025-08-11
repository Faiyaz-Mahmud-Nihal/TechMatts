<?php
require_once 'admin_auth.php'; // Keep this for security
require_once 'db_connection.php'; // Add this for database access
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | TechMatts Admin</title>
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
        --card-bg: #ffffff;
        --sidebar-bg: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
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
    
    /* Admin Container and Sidebar - Match admin.php exactly */
    .admin-container {
        display: flex;
        min-height: 100vh;
    }
    
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
    
    /* Main Content - Add margin to account for fixed sidebar */
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
    
    /* Products Container */
    .products-container {
        background: var(--card-bg);
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    /* Search and Filter Section */
    .search-filter-container {
        background: var(--card-bg);
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
        box-shadow: 0 0 0 3px rgba(226,19,110,0.1);
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
        box-shadow: 0 0 0 3px rgba(226,19,110,0.1);
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
        box-shadow: 0 5px 15px rgba(226,19,110,0.3);
    }
    
    /* Products Table */
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
        background-color: rgba(226,19,110,0.03);
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
    
    .btn-delete {
        background-color: var(--danger);
        color: white;
    }
    
    .btn-delete:hover {
        background-color: #d32f2f;
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(244, 67, 54, 0.3);
    }
    
    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .admin-sidebar {
            width: 250px;
        }
        
        .admin-content {
            margin-left: 250px;
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
    <div class="admin-container">
        <!-- Sidebar - Must match admin.php exactly -->
        <div class="admin-sidebar">
            <div class="admin-logo">
                <h2>TechMatts Admin</h2>
            </div>
            <ul class="admin-menu">
                <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin-products.php" class="active"><i class="fas fa-box-open"></i> Products</a></li>
                <li><a href="admin-orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="admin-users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="admin-suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1>Manage Products</h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=e2136e&color=fff" alt="Admin">
                    <span>Admin</span>
                </div>
            </div>
            
            <div class="search-filter-container">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="search" placeholder="Search products...">
    </div>
    <select class="filter-select" id="category-filter">
        <option value="">All Categories</option>
        <option value="mousepad">Mousepads</option>
        <option value="pcbuild">PC Builds</option>
    </select>
    <select class="filter-select" id="status-filter">
        <option value="">All Statuses</option>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
    </select>
    <a href="admin-add-product.php" class="btn-add">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>
            
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
                    <?php
                    require_once 'db_connection.php';
                    
                    // Fetch all products
                    $stmt = $pdo->query("
                        SELECT p.product_id, p.name, p.category, p.price, p.is_active, 
                               pi.image_url as main_image
                        FROM products p
                        LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_thumbnail = 1
                        ORDER BY p.created_at DESC
                    ");
                    $products = $stmt->fetchAll();
                    
                    foreach ($products as $product):
                    ?>
                    <tr>
                        <td>
                            <?php if ($product['main_image']): ?>
                                <img src="<?= htmlspecialchars($product['main_image']) ?>" class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= ucfirst($product['category']) ?></td>
                        <td><?= number_format($product['price'], 2) ?>৳</td>
                        <td>
                            <span class="status-<?= $product['is_active'] ? 'active' : 'inactive' ?>">
                                <?= $product['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <a href="admin-edit-product.php?id=<?= $product['product_id'] ?>" class="action-btn btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="#" class="action-btn btn-delete" onclick="confirmDelete('<?= $product['product_id'] ?>')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete(productId) {
    if (confirm('Are you sure you want to permanently delete this product? This cannot be undone!')) {
        fetch('admin_api.php?action=delete_product&id=' + productId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product deleted successfully');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the product');
            });
    }
}
        
        // Simple client-side filtering
        document.getElementById('search').addEventListener('input', filterProducts);
        document.getElementById('category-filter').addEventListener('change', filterProducts);
        document.getElementById('status-filter').addEventListener('change', filterProducts);
        
        function filterProducts() {
            const search = document.getElementById('search').value.toLowerCase();
            const category = document.getElementById('category-filter').value;
            const status = document.getElementById('status-filter').value;
            
            const rows = document.querySelectorAll('#products-table-body tr');
            
            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const rowCategory = row.cells[2].textContent.toLowerCase();
                const rowStatus = row.cells[4].querySelector('span').textContent.toLowerCase();
                
                const matchesSearch = name.includes(search);
                const matchesCategory = category === '' || rowCategory.includes(category);
                const matchesStatus = status === '' || 
                    (status === '1' && rowStatus === 'active') || 
                    (status === '0' && rowStatus === 'inactive');
                
                if (matchesSearch && matchesCategory && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>