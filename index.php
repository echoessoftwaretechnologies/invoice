<?php
// Database configuration
$host = 'localhost';
$dbname = 'admin_db';
$username = 'root';
$password = '';

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    echo "Database connection successful!<br>";
    
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Create users table if not exists
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

try {
    $pdo->exec($sql);
    echo "Users table ready.<br>";
} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "<br>";
}

// Function to register new user
function registerUser($pdo, $username, $password, $email = null) {
    try {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':email' => $email
        ]);
        
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

// Function to verify user login
function verifyUser($pdo, $username, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    } catch(PDOException $e) {
        return false;
    }
}

// Example usage (comment out in production):
// registerUser($pdo, 'admin', 'admin123', 'admin@example.com');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP MySQL Connection</title>
</head>
<body>
    <h1>Database Connection Established</h1>
    <p>This page demonstrates MySQL database connection with PHP.</p>
    
    <h2>Available Functions:</h2>
    <ul>
        <li><strong>registerUser($pdo, $username, $password, $email)</strong> - Register new user</li>
        <li><strong>verifyUser($pdo, $username, $password)</strong> - Verify user credentials</li>
    </ul>
    
    <p><strong>Note:</strong> Update database credentials in the PHP code section before using.</p>
</body>
</html>
