# Railway Deployment Guide

## Environment Variables yang Harus Ditambahkan di Railway

### 1. Core Application Settings
```bash
PORT=3000
NODE_ENV=production
```

### 2. Nuxt Public Configuration
```bash
NUXT_PUBLIC_API_BASE=https://your-laravel-backend.railway.app/api/v1
NUXT_PUBLIC_APP_NAME=Pants ERP
NUXT_PUBLIC_ENABLE_OFFLINE=true
NUXT_PUBLIC_API_TIMEOUT=30000
```

### 3. Database Configuration (jika deploy Laravel backend terpisah)
```bash
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=pants_erp
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

### 4. Laravel Application Settings (jika deploy Laravel backend)
```bash
APP_NAME="Pants ERP System"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-backend.railway.app
```

## Langkah Deployment Railway

1. **Connect Repository**
   - Login ke Railway.app
   - Create new project
   - Connect GitHub repository

2. **Add Environment Variables**
   - Masuk ke tab Variables di Railway dashboard
   - Tambahkan semua environment variables di atas
   - Sesuaikan nilai dengan konfigurasi Anda

3. **Deploy Configuration**
   - Railway akan otomatis detect Dockerfile
   - Build akan menggunakan Dockerfile yang sudah dikonfigurasi

4. **Domain Setup**
   - Railway akan generate domain otomatis
   - Atau custom domain di tab Domains

## Arsitektur Deployment

### Opsi 1: Nuxt Frontend Only (Current)
- Deploy Nuxt app sebagai static/SSR frontend
- Connect ke Laravel backend yang di-deploy terpisah
- Gunakan `NUXT_PUBLIC_API_BASE` untuk koneksi ke backend

### Opsi 2: Full Stack dalam Satu Container
- Memerlukan konfigurasi Docker yang lebih kompleks
- Nginx untuk routing antara Nuxt dan Laravel
- Satu service untuk keduanya

## Environment Variables Wajib untuk Railway

### Minimal Configuration (Frontend Only)
```bash
# Runtime
PORT=3000
NODE_ENV=production

# API Connection
NUXT_PUBLIC_API_BASE=https://your-backend.railway.app/api/v1
NUXT_PUBLIC_APP_NAME="Pants ERP"
```

### Complete Configuration (dengan Backend)
```bash
# Runtime
PORT=3000
NODE_ENV=production

# Nuxt Frontend
NUXT_PUBLIC_API_BASE=https://your-backend.railway.app/api/v1
NUXT_PUBLIC_APP_NAME="Pants ERP"
NUXT_PUBLIC_ENABLE_OFFLINE=true
NUXT_PUBLIC_API_TIMEOUT=30000

# Laravel Backend (jika dalam satu service)
APP_NAME="Pants ERP System"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-app.railway.app

# Database
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=pants_erp
DB_USERNAME=root
DB_PASSWORD=your-password

# Redis (optional)
REDIS_HOST=your-redis-host
REDIS_PORT=6379
REDIS_PASSWORD=your-redis-password
```

## Troubleshooting Common Issues

### 1. "Application failed to respond"
- Check PORT environment variable set
- Verify host binding to 0.0.0.0
- Check deploy logs for startup errors

### 2. API Connection Issues
- Verify NUXT_PUBLIC_API_BASE URL
- Check CORS settings on backend
- Ensure backend is deployed and accessible

### 3. Build Failures
- Check Node.js version compatibility
- Verify all dependencies in package.json
- Check build logs for specific errors

## Post-Deployment Checklist

- [ ] Application accessible on Railway domain
- [ ] Environment variables configured correctly
- [ ] API connections working (if applicable)
- [ ] Static assets loading properly
- [ ] PWA functionality working
- [ ] Responsive design working on mobile
- [ ] Language switching functional