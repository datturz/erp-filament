# Railway Environment Variables Setup

## ✅ MySQL Database Variables (Auto-provided by Railway MySQL)
These are automatically available when you add MySQL service:
- `MYSQLHOST` → Private domain for DB connection
- `MYSQLPORT` → 3306
- `MYSQLDATABASE` → railway
- `MYSQLUSER` → root  
- `MYSQLPASSWORD` → Auto-generated password

## 🔧 Laravel Variables to Set in Railway

Copy these to Railway Laravel service environment variables:

```
APP_NAME=Pants ERP System
APP_ENV=production
APP_KEY=base64:KxZ3HgFWE8h4j7hG9vL8ZqPqXZu7FJ2YKyLJRvKNkzg=
APP_DEBUG=false
APP_URL=https://jubilant-prosperity-production.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
FILAMENT_PATH=admin
```

## 📝 Important Notes

1. Use `${{VARIABLE}}` syntax in Railway for referencing MySQL service variables
2. Don't use quotes around variable references
3. APP_URL should be your actual Railway domain
4. Database will auto-connect if variables are properly referenced

## 🧪 Test Database Connection

After deployment, visit:
`https://jubilant-prosperity-production.up.railway.app/debug.php`

Should show:
- `db_test`: "SUCCESS - Database connected and responsive!"
- All DB env vars as "SET"