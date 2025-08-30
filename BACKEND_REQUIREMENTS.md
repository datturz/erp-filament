# Backend API Requirements for Pants Manufacturing ERP

## Railway Backend URL
Base URL: `https://jubilant-prosperity-production.up.railway.app/api.php/v1`

## Required Endpoints

### 1. Dashboard
**GET** `/dashboard`
```json
{
  "totalSales": 125000,
  "inventoryCount": 2345,
  "productionCount": 145,
  "staffCount": 24,
  "recentSales": [],
  "lowStockAlerts": []
}
```

### 2. Products
**GET** `/products` - List all products
**POST** `/products` - Create new product
**PUT** `/products/{id}` - Update product
**DELETE** `/products/{id}` - Delete product

Product structure:
```json
{
  "id": 1,
  "name": "Classic Jeans",
  "sku": "SKU-001",
  "category": "Denim",
  "price": 45.00,
  "stock": 120,
  "minStock": 20,
  "maxStock": 200
}
```

### 3. Categories
**GET** `/categories` - List all categories
**POST** `/categories` - Create new category
**PUT** `/categories/{id}` - Update category
**DELETE** `/categories/{id}` - Delete category

### 4. Inventory/Stock Management
**GET** `/inventory` - Get current stock levels
```json
{
  "totalStock": 5000,
  "lowStockCount": 12,
  "outOfStockCount": 3,
  "stockValue": 125000,
  "items": [
    {
      "id": 1,
      "name": "Classic Jeans",
      "sku": "SKU-001",
      "stockLevel": 120,
      "minStock": 20,
      "maxStock": 200,
      "location": "Main Store",
      "price": 45.00
    }
  ]
}
```

**POST** `/inventory/adjust` - Adjust stock levels
**POST** `/inventory/transfer` - Transfer stock between locations

### 5. Sales/POS
**GET** `/sales` - Get sales history
**POST** `/sales` - Create new sale
**GET** `/sales/{id}` - Get specific sale details

Sale structure:
```json
{
  "id": 1,
  "transactionId": "TRX-001",
  "date": "2024-01-15",
  "customer": "John Doe",
  "items": [
    {
      "productId": 1,
      "name": "Classic Jeans",
      "quantity": 2,
      "price": 45.00,
      "total": 90.00
    }
  ],
  "subtotal": 90.00,
  "tax": 9.00,
  "total": 99.00,
  "paymentMethod": "cash",
  "status": "completed"
}
```

### 6. Reports
**GET** `/reports/sales` - Sales report with filters
```json
{
  "totalRevenue": 125000,
  "totalOrders": 342,
  "averageOrderValue": 365,
  "topProducts": [],
  "salesByDate": []
}
```

**GET** `/reports/inventory` - Stock report
```json
{
  "totalSKUs": 156,
  "totalValue": 250000,
  "lowStockItems": [],
  "outOfStockItems": [],
  "categoryBreakdown": []
}
```

### 7. Warehouse Operations
**GET** `/incoming-goods` - List incoming goods
**POST** `/incoming-goods` - Record new incoming goods
**GET** `/outgoing-goods` - List outgoing goods  
**POST** `/outgoing-goods` - Record outgoing goods

### 8. Finance
**GET** `/income` - Get income records
**POST** `/income` - Record new income
**GET** `/expenses` - Get expense records
**POST** `/expenses` - Record new expense

### 9. Authentication
**POST** `/auth/login`
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**POST** `/auth/logout`
**GET** `/auth/user` - Get current user info

## Database Schema Requirements

### Tables Needed:
1. **products** - Product catalog
2. **categories** - Product categories
3. **inventory** - Stock levels and locations
4. **sales** - Sales transactions
5. **sale_items** - Items in each sale
6. **customers** - Customer information
7. **suppliers** - Supplier information
8. **users** - System users
9. **stores** - Store/location information
10. **stock_movements** - Stock adjustment history
11. **income** - Income records
12. **expenses** - Expense records

## Implementation Priority
1. ✅ Test endpoint (already working)
2. Dashboard endpoint
3. Products CRUD
4. Inventory management
5. Sales/POS endpoints
6. Reports
7. Authentication
8. Other modules

## Notes
- All endpoints should support CORS for Vercel frontend
- Use JSON responses
- Implement proper error handling
- Add pagination for list endpoints
- Include timestamps in all records