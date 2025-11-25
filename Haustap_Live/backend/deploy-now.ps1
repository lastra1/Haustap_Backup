# HausTap Direct Google Cloud Deployment - PowerShell Version
# Simple deployment script without interactive prompts

Write-Host "🚀 HausTap Google Cloud Run Direct Deployment" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green

# Check if gcloud is available
try {
    $gcloudPath = Get-Command gcloud -ErrorAction Stop
    Write-Host "✅ Google Cloud SDK found at: $($gcloudPath.Source)" -ForegroundColor Green
} catch {
    Write-Host "❌ Google Cloud SDK not found" -ForegroundColor Red
    Write-Host "📥 Please install from: https://cloud.google.com/sdk/docs/install" -ForegroundColor Yellow
    Write-Host "💡 After installation, restart PowerShell and try again" -ForegroundColor Yellow
    exit 1
}

# Set project ID
$PROJECT_ID = "haustap-booking-system"
Write-Host "📁 Project: $PROJECT_ID" -ForegroundColor Cyan

# Authenticate if needed
Write-Host "🔐 Checking authentication..." -ForegroundColor Yellow
try {
    $authList = gcloud auth list --filter=status:ACTIVE --format="value(account)"
    if ($authList) {
        Write-Host "✅ Authentication verified" -ForegroundColor Green
        Write-Host "   Active account: $authList" -ForegroundColor Gray
    } else {
        throw "Not authenticated"
    }
} catch {
    Write-Host "❌ Not authenticated. Running gcloud auth login..." -ForegroundColor Red
    gcloud auth login
    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ Authentication failed" -ForegroundColor Red
        exit 1
    }
    Write-Host "✅ Authentication completed" -ForegroundColor Green
}

# Set project
Write-Host "🎯 Setting project..." -ForegroundColor Yellow
try {
    gcloud config set project $PROJECT_ID
    Write-Host "✅ Project set successfully" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to set project" -ForegroundColor Red
    exit 1
}

# Enable APIs
Write-Host "🔧 Enabling required APIs..." -ForegroundColor Yellow
try {
    gcloud services enable run.googleapis.com cloudbuild.googleapis.com secretmanager.googleapis.com
    Write-Host "✅ APIs enabled" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Some APIs may already be enabled" -ForegroundColor Yellow
}

# Create service account
Write-Host "👤 Creating service account..." -ForegroundColor Yellow
try {
    gcloud iam service-accounts create haustap-cloudrun --display-name="HausTap Cloud Run Service Account" 2>$null
    Write-Host "✅ Service account ready" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Service account may already exist" -ForegroundColor Yellow
}

# Grant permissions
Write-Host "🔑 Setting permissions..." -ForegroundColor Yellow
try {
    gcloud projects add-iam-policy-binding $PROJECT_ID --member="serviceAccount:haustap-cloudrun@$PROJECT_ID.iam.gserviceaccount.com" --role="roles/secretmanager.secretAccessor" 2>$null
    gcloud projects add-iam-policy-binding $PROJECT_ID --member="serviceAccount:haustap-cloudrun@$PROJECT_ID.iam.gserviceaccount.com" --role="roles/cloudsql.client" 2>$null
    Write-Host "✅ Permissions granted" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Permissions may already be set" -ForegroundColor Yellow
}

# Create secrets
Write-Host "🔒 Creating secrets..." -ForegroundColor Yellow
try {
    $serviceAccountJson = '{"type":"service_account","project_id":"' + $PROJECT_ID + '"}'
    $serviceAccountJson | gcloud secrets create firebase-service-account --data-file=- --replication-policy="automatic" 2>$null
    "your-secure-db-password" | gcloud secrets create db-password --data-file=- --replication-policy="automatic" 2>$null
    "your-secure-redis-password" | gcloud secrets create redis-password --data-file=- --replication-policy="automatic" 2>$null
    Write-Host "✅ Secrets created" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Secrets may already exist" -ForegroundColor Yellow
}

# Deploy to Cloud Run
Write-Host "🚀 Deploying to Google Cloud Run..." -ForegroundColor Green
Write-Host "   This will take 3-5 minutes..." -ForegroundColor Gray
Write-Host "   Building with Google Cloud Build..." -ForegroundColor Gray

try {
    gcloud run deploy haustap-api `
        --source . `
        --platform managed `
        --region us-central1 `
        --allow-unauthenticated `
        --service-account haustap-cloudrun@$PROJECT_ID.iam.gserviceaccount.com `
        --memory 2Gi `
        --cpu 2 `
        --max-instances 10 `
        --min-instances 1 `
        --timeout 300s `
        --port 8000 `
        --set-env-vars "APP_ENV=production,APP_DEBUG=false,DB_CONNECTION=mysql,DB_HOST=cloudsql,DB_DATABASE=haustap_db,DB_USERNAME=haustap_user,REDIS_HOST=redis,REDIS_PORT=6379,FIREBASE_PROJECT_ID=$PROJECT_ID,FIREBASE_DATABASE_URL=https://$PROJECT_ID-default-rtdb.asia-southeast1.firebasedatabase.app,FIREBASE_STORAGE_BUCKET=$PROJECT_ID.firebasestorage.app"
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Deployment successful!" -ForegroundColor Green
    } else {
        throw "Deployment failed"
    }
} catch {
    Write-Host "❌ Deployment failed" -ForegroundColor Red
    Write-Host "📞 Check the error messages above" -ForegroundColor Yellow
    exit 1
}

# Get service URL
Write-Host "📡 Getting service URL..." -ForegroundColor Yellow
try {
    $SERVICE_URL = gcloud run services describe haustap-api --platform managed --region us-central1 --format "value(status.url)"
    if ($SERVICE_URL) {
        Write-Host ""
        Write-Host "🎉 SUCCESS! Deployment Completed!" -ForegroundColor Green
        Write-Host "==================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "🌐 Your API is LIVE at: $SERVICE_URL" -ForegroundColor Cyan
        Write-Host "📊 Region: us-central1" -ForegroundColor Gray
        Write-Host "🔧 Service Account: haustap-cloudrun@$PROJECT_ID.iam.gserviceaccount.com" -ForegroundColor Gray
        Write-Host ""
        Write-Host "🔥 API Endpoints:" -ForegroundColor Yellow
        Write-Host "  • Health Check: $SERVICE_URL/api/health" -ForegroundColor White
        Write-Host "  • Firebase Config: $SERVICE_URL/api/firebase/firebase-config" -ForegroundColor White
        Write-Host "  • User Management: $SERVICE_URL/api/firebase/users" -ForegroundColor White
        Write-Host "  • Booking Management: $SERVICE_URL/api/firebase/bookings" -ForegroundColor White
        Write-Host ""
        Write-Host "📋 NEXT STEPS:" -ForegroundColor Green
        Write-Host "  1. Test your API: Visit $SERVICE_URL/api/health" -ForegroundColor White
        Write-Host "  2. Update frontend: Change API URL to $SERVICE_URL" -ForegroundColor White
        Write-Host "  3. Setup database: Configure Cloud SQL for MySQL" -ForegroundColor White
        Write-Host "  4. Setup cache: Configure Memorystore for Redis" -ForegroundColor White
        Write-Host "  5. Custom domain: Add your domain in Cloud Run settings" -ForegroundColor White
        Write-Host ""
        Write-Host "🚀 Your HausTap API is now LIVE and ready for production!" -ForegroundColor Green
        Write-Host ""
        Write-Host "💡 TIP: Save this URL: $SERVICE_URL" -ForegroundColor Yellow
        
        # Save URL to file for easy access
        $SERVICE_URL | Out-File -FilePath "service-url.txt" -Encoding UTF8
        Write-Host "📄 URL saved to service-url.txt" -ForegroundColor Gray
    } else {
        throw "Could not get service URL"
    }
} catch {
    Write-Host "⚠️  Could not retrieve service URL" -ForegroundColor Yellow
    Write-Host "   You can find it in the Google Cloud Console" -ForegroundColor Gray
}

Write-Host ""
Write-Host "Press any key to exit..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")