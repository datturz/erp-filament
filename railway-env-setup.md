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

## 🚨 CRITICAL: MySQL Service Setup Required

**Issue**: MySQL service variables (MYSQLHOST, MYSQLPORT, etc.) are NOT SET

**Solution Steps**:

### 1. Add MySQL Service
1. Go to Railway project dashboard
2. Click "**+ New**" button
3. Select "**Add MySQL**" 
4. Wait for MySQL service to deploy

### 2. Connect Services
1. Go to Laravel service settings
2. Click "**Connect**" tab
3. Select the MySQL service to connect
4. This will automatically provide `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD` variables

### 3. Set Database Environment Variables
After MySQL service is connected, add these to Laravel service variables:
```
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}
```

## 📝 Important Notes

1. **MySQL service MUST be added first** - Railway provides MySQL variables only when service exists
2. Use `${{VARIABLE}}` syntax in Railway for referencing MySQL service variables
3. Variables are automatically available to Laravel via `env()` function
4. Database will auto-connect if variables are properly set in Railway dashboard

## 🧪 Test Database Connection

Check these endpoints:
- `/env-check.php` - Shows all environment variables (MySQL vars should be "SET (hidden)")
- `/debug.php` - Laravel database connection test
`https://jubilant-prosperity-production.up.railway.app/debug.php`

Should show:
- `db_test`: "SUCCESS - Database connected and responsive!"
- All DB env vars as "SET"