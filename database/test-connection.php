<?php
/**
 * Database Connection Test
 * Run this script to test if PHP can connect to MySQL
 */

echo "=== Sylva Database Connection Test ===\n";

// Test basic MySQL connection
try {
    $host = '127.0.0.1';
    $port = '3307';
    $username = 'root';
    $password = '';
    
    echo "Testing MySQL connection...\n";
    echo "Host: $host:$port\n";
    echo "Username: $username\n";
    echo "Password: " . (empty($password) ? '(empty)' : '***') . "\n\n";
    
    // Create connection
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ MySQL connection successful!\n\n";
    
    // Check if sylva database exists
    echo "Checking for 'sylva' database...\n";
    $stmt = $pdo->query("SHOW DATABASES LIKE 'sylva'");
    $database = $stmt->fetch();
    
    if ($database) {
        echo "✅ Database 'sylva' already exists!\n";
    } else {
        echo "❌ Database 'sylva' does not exist.\n";
        echo "Creating database 'sylva'...\n";
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `sylva` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✅ Database 'sylva' created successfully!\n";
    }
    
    // Test connection to sylva database
    echo "\nTesting connection to 'sylva' database...\n";
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=sylva", $username, $password);
    echo "✅ Connection to 'sylva' database successful!\n";
    
    echo "\n=== All tests passed! You can now run Laravel migrations. ===\n";
    
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting tips:\n";
    echo "1. Make sure XAMPP is running\n";
    echo "2. Check if MySQL service is started in XAMPP Control Panel\n";
    echo "3. Verify the connection details in .env file\n";
    echo "4. Make sure no firewall is blocking the connection\n";
}