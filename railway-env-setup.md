# Railway Environment Variables Setup

## ✅ MySQL Database Variables (Auto-provided by Railway MySQL)
These are automatically available when you add MySQL service:
- `MYSQLHOST` → Private domain for DB connection
- `MYSQLPORT` → 3306
- `MYSQLDATABASE` → railway
- `MYSQLUSER` → root  
- `MYSQLPASSWORD` → Auto-generated password

## 🔧 Laravel Variables to Set in Railway Dashboard

**IMPORTANT**: Set these directly in Railway dashboard environment variables:

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

1. **NO .env file needed** - Railway provides environment variables directly
2. Use `${{VARIABLE}}` syntax in Railway for referencing MySQL service variables
3. Variables are automatically available to Laravel via `env()` function
4. Database will auto-connect if variables are properly set in Railway dashboard

## 🧪 Test Database Connection

After deployment, visit:
`https://jubilant-prosperity-production.up.railway.app/debug.php`

Should show:
- `db_test`: "SUCCESS - Database connected and responsive!"
- All DB env vars as "SET"