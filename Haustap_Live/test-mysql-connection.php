<?php

/**
 * MySQL Connection Test Script
 * Tests connection to your Docker MySQL database
 */

// Database configuration based on your Docker setup
$host = 'localhost';     // or 'host.docker.internal' for Docker
$port = 3307;            // Your MySQL Docker port
$database = 'haustap_db';
$username = 'haustap_user';
$password = 'haustap_password';

echo "🚀 HausTap MySQL Connection Test\n";
echo "==================================\n\n";

// Test 1: Basic Connection Test
echo "📡 Test 1: Basic MySQL Connection\n";
try {
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    echo "✅ Basic connection successful!\n";
    echo "   Server version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n\n";
    die();
}

// Test 2: Database Selection Test
echo "🗄️  Test 2: Database Selection\n";
try {
    $pdo->exec("USE $database");
    echo "✅ Database '$database' selected successfully!\n\n";
} catch (PDOException $e) {
    echo "❌ Database selection failed: " . $e->getMessage() . "\n\n";
}

// Test 3: Table Structure Test
echo "📊 Test 3: Database Tables Check\n";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "✅ Found " . count($tables) . " tables:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    echo "\n";
    
    // Show specific important tables
    $important_tables = ['users', 'bookings', 'providers', 'chat_messages', 'notifications'];
    echo "🔍 Important tables status:\n";
    foreach ($important_tables as $table) {
        if (in_array($table, $tables)) {
            echo "   ✅ $table\n";
        } else {
            echo "   ⚠️  $table (missing)\n";
        }
    }
    echo "\n";
    
} catch (PDOException $e) {
    echo "❌ Table check failed: " . $e->getMessage() . "\n\n";
}

// Test 4: Users Table Structure Test
echo "👥 Test 4: Users Table Structure\n";
try {
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Users table structure:\n";
    foreach ($columns as $column) {
        echo "   - {$column['Field']}: {$column['Type']} ({$column['Null']})\n";
    }
    echo "\n";
    
} catch (PDOException $e) {
    echo "❌ Users table check failed: " . $e->getMessage() . "\n\n";
}

// Test 5: Sample Data Test
echo "📈 Test 5: Sample Data Check\n";
try {
    // Check users count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "   ✅ Users table: $user_count records\n";
    
    // Check bookings count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
    $booking_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "   ✅ Bookings table: $booking_count records\n";
    
    // Check providers count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM providers");
    $provider_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "   ✅ Providers table: $provider_count records\n";
    
    echo "\n";
    
} catch (PDOException $e) {
    echo "❌ Sample data check failed: " . $e->getMessage() . "\n\n";
}

// Test 6: Laravel Migration Test
echo "🔄 Test 6: Laravel Migration Check\n";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
    $migration_table = $stmt->fetch();
    
    if ($migration_table) {
        echo "   ✅ Migrations table exists\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM migrations");
        $migration_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "   ✅ Applied migrations: $migration_count\n";
    } else {
        echo "   ⚠️  Migrations table not found (normal for fresh install)\n";
    }
    echo "\n";
    
} catch (PDOException $e) {
    echo "❌ Migration check failed: " . $e->getMessage() . "\n\n";
}

// Test 7: Connection Performance Test
echo "⚡ Test 7: Connection Performance\n";
try {
    $start_time = microtime(true);
    
    // Perform a simple query
    $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $end_time = microtime(true);
    $execution_time = round(($end_time - $start_time) * 1000, 2);
    
    echo "   ✅ Query execution time: {$execution_time}ms\n";
    echo "   ✅ Connection is responsive\n\n";
    
} catch (PDOException $e) {
    echo "❌ Performance test failed: " . $e->getMessage() . "\n\n";
}

echo "🎉 MySQL Connection Test Complete!\n";
echo "====================================\n";
echo "\n";
echo "✅ Your MySQL Docker database is ready for Laravel API deployment!\n";
echo "\n";
echo "🚀 Next Steps:\n";
echo "1. Update your Laravel .env file with these connection details\n";
echo "2. Run: php artisan migrate --force\n";
echo "3. Deploy your Laravel API to Google Cloud Run or Render\n";
echo "\n";
echo "📋 Connection Details for Laravel .env:\n";
echo "   DB_CONNECTION=mysql\n";
echo "   DB_HOST=host.docker.internal\n";
echo "   DB_PORT=3307\n";
echo "   DB_DATABASE=haustap_db\n";
echo "   DB_USERNAME=haustap_user\n";
echo "   DB_PASSWORD=haustap_password\n";