# Database Connection Troubleshooting

## 🔍 Current Issue Analysis

Based on debug output, we have:
- ✅ **Connection**: MySQL host/port works (`mysql.railway.internal:3306`)
- ✅ **Credentials**: Username/password work (`SET (hidden)`) 
- ❌ **Database Name**: Still shows literal `{{MySQL.MYSQL_DATABASE}}` instead of actual value

## 🚨 Problem: Mixed Variable Syntax

Your current variables show inconsistent syntax:
```
DB_HOST="${{MYSQLHOST}}"           # ✅ Works (legacy format)
DB_DATABASE="{{MySQL.MYSQL_DATABASE}}" # ❌ Broken (missing $)
```

## 🔧 Solution Options

### Option 1: Use All Legacy Variables
Since `MYSQLHOST` works, try using all legacy format:
```
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQL_DATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}
```

### Option 2: Use All Service-Scoped Variables
Make all variables consistently use service name:
```
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
```

## 📋 Action Steps

1. **Check Railway Variables Tab**: Look at actual variable names provided by MySQL service
2. **Use Consistent Format**: Pick ONE format and use it for ALL DB variables
3. **Test After Change**: Check `/debug.php` to confirm database name shows actual value, not literal string

## 🧪 Expected Result After Fix

Debug output should show:
```json
{
  "database": {
    "database": "railway"  // ✅ Actual database name, not {{MySQL.MYSQL_DATABASE}}
  },
  "db_test": "SUCCESS - Database connected and responsive!"
}
```

## 🎯 Most Likely Fix

Based on your working `MYSQLHOST`, try changing just:
```
DB_DATABASE=${{MYSQL_DATABASE}}
```
(Remove the service prefix since legacy format is working)