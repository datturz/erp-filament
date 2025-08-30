# Vercel Deployment Instructions

## Environment Variables to Add in Vercel Dashboard

Go to: **Settings → Environment Variables** and add:

```
NUXT_PUBLIC_API_BASE=https://jubilant-prosperity-production.up.railway.app/api.php
NUXT_PUBLIC_APP_NAME=Pants ERP
NUXT_PUBLIC_ENABLE_OFFLINE=true
NUXT_PUBLIC_API_TIMEOUT=30000
```

## Steps to Fix Deployment:

1. **Login to Vercel Dashboard**
2. **Go to Project Settings → Environment Variables**
3. **Add the variables above**
4. **Go to Deployments tab**
5. **Click the 3 dots menu on latest deployment**
6. **Select "Redeploy"**

## Test After Deployment:

1. **Frontend URL**: https://erp-filament.vercel.app
2. **API Test**: https://jubilant-prosperity-production.up.railway.app/api.php/v1/test

## Expected Result:

You should see the Nuxt app with:
- Login page
- POS interface
- Inventory management
- Mobile-first responsive design

## If Still Downloading File:

This means Nuxt didn't build properly. Check:
1. Build logs in Vercel for errors
2. Ensure all dependencies are installed
3. Check that .output directory is created during build

## API Connection:

The app will connect to Railway backend at:
- Base URL: https://jubilant-prosperity-production.up.railway.app/api.php
- Test endpoint: /v1/test
- Health check: /v1/health