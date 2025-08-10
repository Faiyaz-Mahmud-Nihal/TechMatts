<?php
require_once 'db_connection.php';

session_start();

header('Content-Type: application/json');

// Helper function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Helper function to validate phone
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

// User registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    try {
        $required = ['name', 'email', 'phone', 'password', 'confirm'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields are required.");
            }
        }

        if (!isValidEmail($_POST['email'])) {
            throw new Exception("Invalid email format.");
        }

        if (!isValidPhone($_POST['phone'])) {
            throw new Exception("Invalid phone number.");
        }

        if ($_POST['password'] !== $_POST['confirm']) {
            throw new Exception("Passwords don't match.");
        }

        if (strlen($_POST['password']) < 8) {
            throw new Exception("Password must be at least 8 characters.");
        }

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        if ($stmt->fetch()) {
            throw new Exception("Email already registered.");
        }

        // Check if phone already exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE phone = ?");
        $stmt->execute([$_POST['phone']]);
        if ($stmt->fetch()) {
            throw new Exception("Phone number already registered.");
        }

        // Hash password
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users 
            (first_name, last_name, email, phone, password_hash, role, address, district, postcode) 
            VALUES (?, ?, ?, ?, ?, 'customer', '', '', '')");

        // Split full name into first and last
        $nameParts = explode(' ', trim($_POST['name']));
        $firstName = array_shift($nameParts);
        $lastName = implode(' ', $nameParts);

        $stmt->execute([
            $firstName,
            $lastName,
            $_POST['email'],
            $_POST['phone'],
            $passwordHash
        ]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['role'] = 'customer';

        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// User login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    try {
        if (empty($_POST['email']) || empty($_POST['password'])) {
            throw new Exception("Email and password are required.");
        }

        $stmt = $pdo->prepare("SELECT user_id, email, password_hash, role FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$_POST['email']]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($_POST['password'], $user['password_hash'])) {
            throw new Exception("Invalid email or password.");
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Update last login
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);

        // Return different response for admin vs regular user
        $redirect = ($user['role'] === 'admin') ? 'admin.php' : 'index.html';
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful!',
            'redirect' => $redirect  // Fixed variable name and admin.php
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Check if user is logged in
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check') {
    echo json_encode([
        'loggedIn' => isset($_SESSION['user_id']),
        'email' => $_SESSION['email'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ]);
    exit;
}

// User logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
    exit;
}

// Additional check with first name
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check') {
    $response = [
        'loggedIn' => isset($_SESSION['user_id']),
        'email' => $_SESSION['email'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
    
    if (isset($_SESSION['user_id'])) {
        // Get user's first name for display
        $stmt = $pdo->prepare("SELECT first_name FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        $response['firstName'] = $user['first_name'] ?? null;
    }
    
    echo json_encode($response);
    exit;
}