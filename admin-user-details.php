<?php
require_once 'admin_auth.php';
require_once 'db_connection.php';

$userId = $_GET['id'] ?? 0;

if (!$userId) {
    header('Location: admin-users.php');
    exit;
}

// Fetch user details
$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE user_id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: admin-users.php');
    exit;
}

// Format registration date
$regDate = new DateTime($user['registration_date']);
$formattedDate = $regDate->format('F j, Y, g:i a');

// Get last login if available
$lastLogin = $user['last_login'] ? (new DateTime($user['last_login']))->format('F j, Y, g:i a') : 'Never logged in';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details | TechMatts Admin</title>
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
    
    .admin-container {
        display: flex;
        min-height: 100vh;
    }
    
    .admin-sidebar {
        width: 280px;
        background: var(--sidebar-bg);
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
    
    /* User Details Styles */
    .user-details-container {
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .user-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .user-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 30px;
    }
    
    .user-info h2 {
        font-size: 24px;
        margin-bottom: 5px;
        color: #333;
    }
    
    .role-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 10px;
    }
    
    .role-admin {
        background: rgba(226, 19, 110, 0.1);
        color: var(--primary);
    }
    
    .role-supplier {
        background: rgba(33, 150, 243, 0.1);
        color: var(--info);
    }
    
    .role-customer {
        background: rgba(76, 175, 80, 0.1);
        color: var(--success);
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
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
    
    .user-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    
    .detail-section {
        margin-bottom: 20px;
    }
    
    .detail-section h3 {
        font-size: 18px;
        margin-bottom: 15px;
        color: #555;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .detail-row {
        display: flex;
        margin-bottom: 10px;
    }
    
    .detail-label {
        font-weight: 600;
        color: #555;
        min-width: 150px;
    }
    
    .detail-value {
        color: #333;
    }
    
    .action-buttons {
        margin-top: 30px;
        display: flex;
        gap: 15px;
    }
    
    .btn {
        padding: 12px 25px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    @media (max-width: 992px) {
        .admin-sidebar {
            width: 250px;
        }
        
        .admin-content {
            margin-left: 250px;
        }
        
        .user-details-grid {
            grid-template-columns: 1fr;
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
        
        .user-header {
            flex-direction: column;
            text-align: center;
        }
        
        .user-avatar {
            margin-right: 0;
            margin-bottom: 20px;
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
                <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin-products.php"><i class="fas fa-box-open"></i> Products</a></li>
                <li><a href="admin-orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="admin-users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
                <!-- <li><a href="admin-suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li> -->
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1>User Details</h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=e2136e&color=fff" alt="Admin">
                    <span>Admin</span>
                </div>
            </div>
            
            <div class="user-details-container">
                <div class="user-header">
                    <?php
                    $fullName = htmlspecialchars($user['first_name'] . ' ' . htmlspecialchars($user['last_name']));
                    $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($fullName) . "&background=e2136e&color=fff&size=100";
                    ?>
                    <img src="<?= $avatarUrl ?>" class="user-avatar" alt="<?= $fullName ?>">
                    <div class="user-info">
                        <h2><?= $fullName ?></h2>
                        <span class="role-badge role-<?= $user['role'] ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                        <span class="status-<?= $user['is_active'] ? 'active' : 'inactive' ?>">
                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>
                
                <div class="user-details-grid">
                    <div>
                        <div class="detail-section">
                            <h3>Basic Information</h3>
                            <div class="detail-row">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value"><?= htmlspecialchars($user['email']) ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value"><?= htmlspecialchars($user['phone']) ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Registered:</span>
                                <span class="detail-value"><?= $formattedDate ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Last Login:</span>
                                <span class="detail-value"><?= $lastLogin ?></span>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h3>Address Information</h3>
                            <div class="detail-row">
                                <span class="detail-label">Address:</span>
                                <span class="detail-value"><?= htmlspecialchars($user['address'] ?? 'Not provided') ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">District:</span>
                                <span class="detail-value"><?= htmlspecialchars($user['district'] ?? 'Not provided') ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Postcode:</span>
                                <span class="detail-value"><?= htmlspecialchars($user['postcode'] ?? 'Not provided') ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="detail-section">
                            <h3>Account Information</h3>
                            <div class="detail-row">
                                <span class="detail-label">User ID:</span>
                                <span class="detail-value"><?= $user['user_id'] ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Role:</span>
                                <span class="detail-value"><?= ucfirst($user['role']) ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value"><?= $user['is_active'] ? 'Active' : 'Inactive' ?></span>
                            </div>
                        </div>
                        
                        <?php if ($user['role'] === 'supplier'): ?>
                        <?php
                        // Fetch supplier details if available
                        $supplierStmt = $pdo->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
                        $supplierStmt->execute([$userId]);
                        $supplier = $supplierStmt->fetch();
                        ?>
                        <div class="detail-section">
                            <h3>Supplier Information</h3>
                            <?php if ($supplier): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Company:</span>
                                    <span class="detail-value"><?= htmlspecialchars($supplier['company_name'] ?? 'Not provided') ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Contact:</span>
                                    <span class="detail-value"><?= htmlspecialchars($supplier['contact_person'] ?? 'Not provided') ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Supplier Since:</span>
                                    <span class="detail-value"><?= $supplier['supplier_since'] ? (new DateTime($supplier['supplier_since']))->format('F j, Y') : 'Not provided' ?></span>
                                </div>
                            <?php else: ?>
                                <div class="detail-row">
                                    <span class="detail-value">No supplier details found</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="admin-edit-user.php?id=<?= $user['user_id'] ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit User
                    </a>
                    <a href="admin-users.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>