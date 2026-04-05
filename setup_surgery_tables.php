<?php

// Database configuration
$host = '127.0.0.1';
$username = 'root';
$password = ''; // Change if you have a password
$database = 'u972011074_vzeTw';

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database: $database\n";
    
    // Read SQL file
    $sql = file_get_contents('add_surgery_tables.sql');
    
    // Execute SQL
    $pdo->exec($sql);
    
    echo "✅ Surgery Template System tables created successfully!\n";
    echo "✅ Sample data inserted!\n";
    echo "\nNow you can access:\n";
    echo "📋 Surgery Templates: http://127.0.0.1:8000/surgery-templates\n";
    echo "💊 Create Template: http://127.0.0.1:8000/surgery-templates/create\n";
    echo "📝 Prescription with Templates: http://127.0.0.1:8000/prescriptions/create-modern\n";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
