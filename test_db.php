<?php
require 'db_connection.php';

$stmt = $pdo->query("SELECT COUNT(*) AS total FROM products");
$row = $stmt->fetch();

echo "Total products in database: " . $row['total'];
