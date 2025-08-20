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

// Fetch supplier details if available
$supplier = null;
if ($user['role'] === 'supplier') {
    $supplierStmt = $pdo->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
    $supplierStmt->execute([$userId]);
    $supplier = $supplierStmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | TechMatts Admin</title>
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
    
    /* Form Styles */
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        max-width: 900px;
        margin: 0 auto;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }
    
    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .form-row {
        display: flex;
        gap: 20px;
    }
    
    .form-row .form-group {
        flex: 1;
    }
    
    .btn {
        display: inline-block;
        padding: 12px 25px;
        background-color: var(--primary);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-size: 16px;
        transition: all 0.3s;
    }
    
    .btn:hover {
        background-color: var(--primary-dark);
    }
    
    .btn-secondary {
        background-color: #6c757d;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268;
    }
    
    .password-field {
        position: relative;
    }
    
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: var(--gray);
    }
    
    .toggle-password:hover {
        color: var(--primary);
    }
    
    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 0;
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
                <h1>Edit User</h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=e2136e&color=fff" alt="Admin">
                    <span>Admin</span>
                </div>
            </div>
            
            <div class="form-container">
                <form id="edit-user-form">
                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first-name">First Name</label>
                            <input type="text" id="first-name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last-name">Last Name</label>
                            <input type="text" id="last-name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group password-field">
                            <label for="password">Password (leave blank to keep current)</label>
                            <input type="password" id="password" name="password">
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i>
                        </div>
                        
                        <div class="form-group password-field">
                            <label for="confirm-password">Confirm Password</label>
                            <input type="password" id="confirm-password" name="confirm_password">
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm-password')"></i>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                <option value="supplier" <?= $user['role'] === 'supplier' ? 'selected' : '' ?>>Supplier</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="is_active" required>
                                <option value="1" <?= $user['is_active'] ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= !$user['is_active'] ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="supplier-fields" style="display: <?= $user['role'] === 'supplier' ? 'block' : 'none' ?>;">
                        <div class="form-group">
                            <label for="company-name">Company Name</label>
                            <input type="text" id="company-name" name="company_name" value="<?= htmlspecialchars($supplier['company_name'] ?? '') ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact-person">Contact Person</label>
                                <input type="text" id="contact-person" name="contact_person" value="<?= htmlspecialchars($supplier['contact_person'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="supplier-since">Supplier Since</label>
                                <input type="date" id="supplier-since" name="supplier_since" value="<?= htmlspecialchars($supplier['supplier_since'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bank-account">Bank Account</label>
                                <input type="text" id="bank-account" name="bank_account" value="<?= htmlspecialchars($supplier['bank_account'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="tax-id">Tax ID</label>
                                <input type="text" id="tax-id" name="tax_id" value="<?= htmlspecialchars($supplier['tax_id'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="district">District</label>
                            <input type="text" id="district" name="district" value="<?= htmlspecialchars($user['district'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="postcode">Postcode</label>
                            <input type="text" id="postcode" name="postcode" value="<?= htmlspecialchars($user['postcode'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">Update User</button>
                        <a href="admin-users.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show/hide supplier fields based on role
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            document.getElementById('supplier-fields').style.display = role === 'supplier' ? 'block' : 'none';
        });
        
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Form submission
        document.getElementById('edit-user-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            if (password && password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            try {
                const response = await fetch('admin_api.php?action=update_user', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('User updated successfully!');
                    window.location.href = 'admin-users.php';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                console.error(error);
            }
        });
    </script>
</body>
</html>