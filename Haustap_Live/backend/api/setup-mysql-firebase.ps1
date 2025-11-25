# MySQL-Firebase Integration Setup Script for HausTap Service Booking Platform
# PowerShell version for Windows

param(
    [string]$Direction = "both",
    [string]$Type = "all",
    [switch]$Force,
    [switch]$SkipDocker,
    [switch]$Help
)

# Display help if requested
if ($Help) {
    Write-Host @"
HausTap MySQL-Firebase Integration Setup Script

Usage: .\setup-mysql-firebase.ps1 [options]

Options:
    -Direction <string>    Sync direction: to-firebase, from-firebase, both (default: both)
    -Type <string>         Sync type: users, bookings, all (default: all)
    -Force                 Force sync even if data exists
    -SkipDocker           Skip Docker setup and use existing MySQL
    -Help                 Show this help message

Examples:
    .\setup-mysql-firebase.ps1
    .\setup-mysql-firebase.ps1 -Direction to-firebase -Type users
    .\setup-mysql-firebase.ps1 -SkipDocker
"@
    exit 0
}

# Colors for output
$Colors = @{
    Info = "Cyan"
    Success = "Green"
    Warning = "Yellow"
    Error = "Red"
}

function Write-Status {
    param($Message, $Type = "Info")
    Write-Host "[$Type] $Message" -ForegroundColor $Colors[$Type]
}

function Test-Prerequisites {
    Write-Status "Checking prerequisites..."
    
    # Check PHP
    if (!(Get-Command php -ErrorAction SilentlyContinue)) {
        Write-Status "PHP is not installed or not in PATH" "Error"
        exit 1
    }
    
    # Check Composer
    if (!(Get-Command composer -ErrorAction SilentlyContinue)) {
        Write-Status "Composer is not installed or not in PATH" "Error"
        exit 1
    }
    
    Write-Status "Prerequisites check completed" "Success"
}

function Install-Dependencies {
    Write-Status "Installing PHP dependencies..."
    
    try {
        composer install --no-dev --optimize-autoloader
        Write-Status "PHP dependencies installed successfully" "Success"
    } catch {
        Write-Status "Failed to install PHP dependencies: $($_.Exception.Message)" "Error"
        exit 1
    }
}

function Setup-Environment {
    Write-Status "Setting up environment configuration..."
    
    if (!(Test-Path ".env")) {
        Copy-Item ".env.example" ".env"
        Write-Status "Created .env file from example"
    }
    
    # Update database configuration
    (Get-Content ".env") -replace 'DB_CONNECTION=.*', 'DB_CONNECTION=mysql' |
    -replace 'DB_HOST=.*', 'DB_HOST=127.0.0.1' |
    -replace 'DB_PORT=.*', 'DB_PORT=3306' |
    -replace 'DB_DATABASE=.*', 'DB_DATABASE=haustap_db' |
    -replace 'DB_USERNAME=.*', 'DB_USERNAME=haustap_user' |
    -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=haustap_password' |
    -replace 'FIREBASE_PROJECT_ID=.*', 'FIREBASE_PROJECT_ID=haustap-booking-system' |
    Set-Content ".env"
    
    Write-Status "Environment configuration updated" "Success"
}

function Generate-AppKey {
    Write-Status "Generating application key..."
    php artisan key:generate
    Write-Status "Application key generated" "Success"
}

function Test-MySQLConnection {
    Write-Status "Testing MySQL connection..."
    
    try {
        $result = php artisan tinker --execute="
        try {
            DB::connection()->getPdo();
            echo 'MySQL connection successful' . PHP_EOL;
            echo 'Database: ' . DB::connection()->getDatabaseName() . PHP_EOL;
        } catch (Exception `$e) {
            echo 'MySQL connection failed: ' . `$e->getMessage() . PHP_EOL;
            exit(1);
        }
        "
        
        if ($result -match "successful") {
            Write-Status "MySQL connection test passed" "Success"
            return $true
        } else {
            Write-Status "MySQL connection test failed" "Error"
            return $false
        }
    } catch {
        Write-Status "MySQL connection test failed: $($_.Exception.Message)" "Error"
        return $false
    }
}

function Test-FirebaseConnection {
    Write-Status "Testing Firebase connection..."
    
    try {
        $result = php artisan tinker --execute="
        try {
            `$fs = app(\App\Services\Firebase\FirestoreClient::class);
            `$result = `$fs->list('users', 1);
            echo 'Firebase connection successful' . PHP_EOL;
        } catch (Exception `$e) {
            echo 'Firebase connection failed: ' . `$e->getMessage() . PHP_EOL;
            exit(1);
        }
        "
        
        if ($result -match "successful") {
            Write-Status "Firebase connection test passed" "Success"
            return $true
        } else {
            Write-Status "Firebase connection test failed" "Warning"
            return $false
        }
    } catch {
        Write-Status "Firebase connection test failed: $($_.Exception.Message)" "Warning"
        return $false
    }
}

function Setup-FirebaseCredentials {
    Write-Status "Setting up Firebase credentials..."
    
    # Create config directory if it doesn't exist
    if (!(Test-Path "config")) {
        New-Item -ItemType Directory -Path "config" -Force
    }
    
    if (!(Test-Path "config/firebase-service-account.json")) {
        Write-Status "Please place your Firebase service account JSON file at: config/firebase-service-account.json" "Warning"
        Write-Status "You can download this from Firebase Console > Project Settings > Service Accounts" "Info"
    } else {
        Write-Status "Firebase service account file found" "Success"
    }
}

function Run-Migrations {
    Write-Status "Running database migrations..."
    
    try {
        php artisan migrate --force
        Write-Status "Database migrations completed" "Success"
    } catch {
        Write-Status "Database migrations failed: $($_.Exception.Message)" "Error"
        exit 1
    }
}

function Show-SyncStatus {
    Write-Status "Current sync status:"
    
    try {
        $result = php artisan tinker --execute="
        `$status = app(\App\Services\MySQLFirebaseBridgeService::class)->getSyncStatus();
        echo 'MySQL Users: ' . `$status['mysql_users'] . PHP_EOL;
        echo 'MySQL Bookings: ' . `$status['mysql_bookings'] . PHP_EOL;
        echo 'Users with Firebase ID: ' . `$status['users_with_firebase_id'] . PHP_EOL;
        echo 'Unsynced Bookings: ' . `$status['unsynced_bookings'] . PHP_EOL;
        echo 'Last User Sync: ' . (`$status['last_sync']['users'] ?? 'Never') . PHP_EOL;
        echo 'Last Booking Sync: ' . (`$status['last_sync']['bookings'] ?? 'Never') . PHP_EOL;
        "
        
        Write-Output $result
    } catch {
        Write-Status "Failed to get sync status: $($_.Exception.Message)" "Warning"
    }
}

function Invoke-Sync {
    param($Direction, $Type)
    
    Write-Status "Starting MySQL-Firebase synchronization..."
    Write-Status "Direction: $Direction, Type: $Type"
    
    try {
        $startTime = Get-Date
        
        # Run the sync command
        php artisan sync:firebase --direction=$Direction --type=$Type
        
        $endTime = Get-Date
        $duration = ($endTime - $startTime).TotalSeconds
        
        Write-Status "Synchronization completed in $([math]::Round($duration, 2)) seconds" "Success"
        
        # Show updated status
        Show-SyncStatus
        
    } catch {
        Write-Status "Synchronization failed: $($_.Exception.Message)" "Error"
        exit 1
    }
}

function Setup-DockerMySQL {
    Write-Status "Setting up MySQL via Docker..."
    
    if (Get-Command docker -ErrorAction SilentlyContinue) {
        try {
            docker-compose -f docker-compose-mysql-only.yml up -d
            
            Write-Status "Waiting for MySQL to be ready..."
            Start-Sleep -Seconds 30
            
            # Test connection
            $testResult = docker exec haustap_mysql mysql -u haustap_user -phaustap_password -e "SELECT 1;"
            if ($testResult) {
                Write-Status "MySQL Docker container is ready" "Success"
                return $true
            } else {
                Write-Status "MySQL Docker container failed to start properly" "Error"
                return $false
            }
        } catch {
            Write-Status "Docker MySQL setup failed: $($_.Exception.Message)" "Error"
            return $false
        }
    } else {
        Write-Status "Docker is not installed" "Error"
        return $false
    }
}

function Show-FinalInstructions {
    Write-Host ""
    Write-Status "MySQL-Firebase Integration Setup Complete!" "Success"
    Write-Host "=============================================="
    Write-Host ""
    Write-Host "📋 Next Steps:"
    Write-Host "1. Ensure your Firebase service account file is placed at: config/firebase-service-account.json"
    Write-Host "2. Test the API endpoints:"
    Write-Host "   - GET  http://localhost:8000/api/sync/status"
    Write-Host "   - POST http://localhost:8000/api/sync/users/to-firebase"
    Write-Host "   - POST http://localhost:8000/api/sync/bookings"
    Write-Host "   - POST http://localhost:8000/api/sync/full"
    Write-Host ""
    Write-Host "🔄 Manual Sync Commands:"
    Write-Host "   php artisan sync:firebase --direction=both --type=all"
    Write-Host "   php artisan sync:firebase --direction=to-firebase --type=users"
    Write-Host "   php artisan sync:firebase --direction=from-firebase --type=bookings"
    Write-Host ""
    Write-Host "📊 Monitoring:"
    Write-Host "   Get-Content storage/logs/laravel.log -Tail 50"
    Write-Host ""
}

# Main execution
function Main {
    Write-Status "Starting MySQL-Firebase integration setup..."
    
    # Check prerequisites
    Test-Prerequisites
    
    # Setup process
    Install-Dependencies
    Setup-Environment
    Generate-AppKey
    
    # Database setup
    if (!$SkipDocker) {
        if (Setup-DockerMySQL) {
            Write-Status "MySQL Docker setup completed" "Success"
        } else {
            Write-Status "Docker MySQL setup failed, checking existing MySQL..." "Warning"
        }
    }
    
    # Test connections
    if (Test-MySQLConnection) {
        Write-Status "MySQL connection established" "Success"
    } else {
        Write-Status "MySQL connection failed. Please set up MySQL first." "Error"
        exit 1
    }
    
    Run-Migrations
    Setup-FirebaseCredentials
    
    # Test Firebase (optional)
    Test-FirebaseConnection
    
    # Show current status
    Show-SyncStatus
    
    # Run initial sync if requested
    $runSync = Read-Host "Would you like to run initial data synchronization? (y/N)"
    if ($runSync -match "^[Yy]$") {
        Invoke-Sync -Direction $Direction -Type $Type
    }
    
    Show-FinalInstructions
}

# Execute main function
Main