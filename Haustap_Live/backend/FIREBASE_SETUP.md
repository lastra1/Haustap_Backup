# Firebase Service Account Setup for HausTap

## Step 1: Get Your Firebase Service Account JSON

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select your project: `haustap-booking-system`
3. Click the **gear icon** (⚙️) → **Project Settings**
4. Go to **Service Accounts** tab
5. Click **Generate New Private Key**
6. A JSON file will download - **save this file safely**

## Step 2: Add to Render Environment Variables

1. Go to your Render dashboard
2. Find your `haustap-nodejs-api` service
3. Click **Environment** tab
4. Click **Add Environment Variable**
5. **Name**: `FIREBASE_SERVICE_ACCOUNT_JSON`
6. **Value**: Open the downloaded JSON file and copy the ENTIRE contents
7. Click **Save**

## Step 3: Verify Your Firebase Configuration

Your Firebase project details:
- **Project ID**: `haustap-booking-system`
- **Database URL**: `https://haustap-booking-system-default-rtdb.asia-southeast1.firebasedatabase.app`
- **Auth Domain**: `haustap-booking-system.firebaseapp.com`

## Step 4: Test After Deployment

Once deployed, test these endpoints:
```bash
# Health check (should show Firebase as healthy)
curl https://your-service.onrender.com/api/health

# Test Firebase connection
curl https://your-service.onrender.com/api/firebase/status
```

## ⚠️ Important Security Notes

1. **Never commit** the service account JSON to GitHub
2. **Never share** the private key or service account details
3. **Use environment variables** for all sensitive data
4. **Restrict Firebase permissions** in the Firebase Console if needed

## 🔧 Troubleshooting

If Firebase shows as "unhealthy" in health check:
1. Verify the JSON was copied completely (no missing characters)
2. Check that the service account has proper permissions
3. Ensure your Firebase project is active and billing is enabled
4. Check Render logs for specific Firebase errors