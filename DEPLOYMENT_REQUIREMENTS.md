# 📋 Vercel Deployment Requirements

## ⚡ Quick Summary
**Frontend**: Deploy ke Vercel (Nuxt 3 PWA)  
**Backend**: Deploy terpisah (Laravel API)  
**Database**: MySQL eksternal  

## 🔧 Yang Perlu Anda Siapkan

### 1. Backend API (Pilih Salah Satu)
| Platform | Biaya/Bulan | Setup Time | Rekomendasi |
|----------|-------------|------------|-------------|
| **Railway** | $5-20 | 10 min | ⭐ Termudah |
| **Render** | $7-25 | 15 min | Good uptime |
| **DigitalOcean** | $12-40 | 30 min | Full control |
| **Heroku** | $7-50 | 20 min | Mature |

### 2. Database MySQL (Pilih Salah Satu)
| Service | Free Tier | Paid | Features |
|---------|-----------|------|----------|
| **PlanetScale** | 5GB | $29+ | Serverless, scaling |
| **Railway MySQL** | 500MB | $5+ | Integrated |
| **Aiven** | Trial | $19+ | Managed, backups |
| **DigitalOcean** | No | $15+ | Full MySQL |

### 3. Environment Variables untuk Vercel
```env
# WAJIB DIISI
NUXT_PUBLIC_API_BASE=https://[backend-url]/api/v1

# OPTIONAL
NUXT_PUBLIC_APP_NAME=Pants ERP
NUXT_PUBLIC_DEFAULT_LOCALE=id
NUXT_PUBLIC_ENABLE_OFFLINE=true
```

## 📦 File yang Sudah Disiapkan
✅ `vercel.json` - Konfigurasi Vercel  
✅ `.env.vercel` - Template environment variables  
✅ `nuxt.config.ts` - Updated untuk Vercel  
✅ `deployment-vercel.md` - Panduan lengkap  

## 🚀 Langkah Deployment

### Step 1: Deploy Backend Laravel
```bash
# Clone backend code
git clone [repo-url]

# Setup di Railway (Recommended)
railway login
railway init
railway add mysql
railway up

# Catat URL: https://xxxxx.railway.app
```

### Step 2: Deploy Frontend ke Vercel
```bash
# Via CLI
vercel --prod

# Via GitHub
1. Push code ke GitHub
2. Import di vercel.com
3. Add environment variables
4. Deploy
```

### Step 3: Konfigurasi CORS di Laravel
```php
// config/cors.php
'allowed_origins' => [
    'https://your-app.vercel.app',
    'http://localhost:3000'
]
```

## 💰 Estimasi Biaya Bulanan
- **Minimum**: Rp 180.000 - 400.000
- **Recommended**: Rp 600.000 - 900.000  
- **Enterprise**: Rp 1.500.000+

## ✅ Checklist Deployment
- [ ] Deploy Laravel API ke Railway/Render
- [ ] Setup database MySQL
- [ ] Jalankan migrations & seeders
- [ ] Test API endpoint
- [ ] Deploy frontend ke Vercel
- [ ] Set environment variables
- [ ] Test koneksi frontend-backend
- [ ] Setup custom domain (optional)

## 🆘 Butuh Bantuan?
1. **Backend tidak bisa diakses**: Check CORS settings
2. **Login error**: Verify Sanctum configuration  
3. **Slow API**: Enable Redis caching
4. **Offline not working**: Check PWA manifest

## 📞 Support Resources
- Vercel Docs: vercel.com/docs
- Railway Docs: docs.railway.app
- Laravel Deploy: laravel.com/docs/deployment