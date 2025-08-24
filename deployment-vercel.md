# 🚀 Vercel Deployment Guide

## Prerequisites

### 1️⃣ Backend API Deployment (Choose One)
- **Railway**: Laravel + MySQL bundle
- **Render**: Web service + managed DB  
- **DigitalOcean App Platform**: Full stack
- **Supabase**: PostgreSQL + Auth + Realtime

### 2️⃣ Required Accounts
- Vercel account
- GitHub/GitLab repository
- Backend hosting service
- Domain name (optional)

## Setup Steps

### Step 1: Backend Deployment
```bash
# Railway Quick Deploy
railway login
railway init
railway add mysql
railway up

# Get API URL: https://your-app.railway.app
```

### Step 2: Vercel Configuration
```bash
# Install Vercel CLI
npm i -g vercel

# Login & Deploy
vercel login
vercel --prod
```

### Step 3: Environment Variables (Vercel Dashboard)
```
NUXT_PUBLIC_API_BASE=https://your-backend.railway.app/api/v1
NUXT_PUBLIC_APP_NAME=Pants ERP
NUXT_PUBLIC_DEFAULT_LOCALE=en
NUXT_PUBLIC_ENABLE_OFFLINE=true
```

### Step 4: Domain Setup
```
Custom Domain: erp.yourcompany.com
SSL: Automatic via Vercel
```

## Required Backend Changes

### CORS Configuration (Laravel)
```php
// config/cors.php
'allowed_origins' => [
    'https://your-app.vercel.app',
    'https://erp.yourcompany.com'
],
```

### API Rate Limiting
```php
// app/Http/Kernel.php
'api' => [
    'throttle:api',
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
],
```

## Monitoring & Analytics

### Vercel Analytics
- Web Vitals tracking
- Real User Monitoring
- Error tracking

### Backend Monitoring
- Laravel Telescope
- Sentry integration
- CloudWatch/Datadog

## Performance Optimization

### Frontend (Vercel)
- Edge Functions for API caching
- Image optimization via Vercel
- Static regeneration for catalog

### Backend
- Redis caching
- Query optimization
- CDN for assets

## Cost Estimation

### Vercel (Frontend)
- **Hobby**: Free (personal use)
- **Pro**: $20/month (commercial)
- **Enterprise**: Custom pricing

### Backend Hosting
- **Railway**: $5-20/month
- **Render**: $7-25/month
- **DigitalOcean**: $12-40/month

### Total Monthly Cost
- **Minimum**: $12-27/month
- **Recommended**: $40-60/month
- **Enterprise**: $100+/month

## Deployment Checklist

- [ ] Backend API deployed & accessible
- [ ] Database migrated & seeded
- [ ] Environment variables configured
- [ ] CORS settings updated
- [ ] SSL certificates active
- [ ] Monitoring setup
- [ ] Backup strategy implemented
- [ ] CI/CD pipeline configured