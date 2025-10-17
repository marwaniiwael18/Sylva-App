# 🌳 How to Access Tree Maintenance - Quick Guide

## 📍 Where to Access Tree Maintenance

### **Base URL**: `http://localhost:8000/api`

All Tree Maintenance endpoints are accessible through the API. Here's how to use them:

---

## 🔑 Step 1: Get Authentication Token

First, you need to login to get an access token:

```bash
# Login Request
POST http://localhost:8000/api/auth/login
Content-Type: application/json

{
  "email": "demo@sylva.com",
  "password": "demo123"
}

# Response will include:
{
  "success": true,
  "token": "your-token-here",
  "user": { ... }
}
```

**Save the token!** You'll need it for all maintenance requests.

---

## 📋 Step 2: Access Tree Maintenance Endpoints

### 1️⃣ **View All Maintenance Records**

```bash
GET http://localhost:8000/api/tree-maintenance
Authorization: Bearer your-token-here
```

**Try in browser:**
- Won't work directly (needs auth token)
- Use Postman, Insomnia, or curl

**Try with curl:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     http://localhost:8000/api/tree-maintenance
```

---

### 2️⃣ **View Maintenance for a Specific Tree**

```bash
GET http://localhost:8000/api/tree-maintenance?tree_id=1
Authorization: Bearer your-token-here
```

**Try with curl:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     "http://localhost:8000/api/tree-maintenance?tree_id=1"
```

---

### 3️⃣ **Create a New Maintenance Record**

```bash
POST http://localhost:8000/api/tree-maintenance
Authorization: Bearer your-token-here
Content-Type: multipart/form-data

tree_id=1
activity_type=watering
performed_at=2025-10-17
condition_after=good
notes=Regular watering session
```

**Try with curl:**
```bash
curl -X POST http://localhost:8000/api/tree-maintenance \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -F "tree_id=1" \
     -F "activity_type=watering" \
     -F "performed_at=2025-10-17" \
     -F "condition_after=good" \
     -F "notes=Regular watering session"
```

---

### 4️⃣ **View Tree's Complete Maintenance History**

```bash
GET http://localhost:8000/api/trees/1/maintenance-history
Authorization: Bearer your-token-here
```

This shows:
- All maintenance records for tree #1
- Tree details
- Health score
- Last maintenance date

**Try with curl:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     http://localhost:8000/api/trees/1/maintenance-history
```

---

### 5️⃣ **View Your Maintenance Activities**

```bash
GET http://localhost:8000/api/user/maintenance-activities
Authorization: Bearer your-token-here
```

Shows all maintenance activities you've performed.

**Try with curl:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     http://localhost:8000/api/user/maintenance-activities
```

---

### 6️⃣ **View Maintenance Statistics**

```bash
GET http://localhost:8000/api/tree-maintenance-stats
Authorization: Bearer your-token-here
```

**Try with curl:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json" \
     http://localhost:8000/api/tree-maintenance-stats
```

---

## 🛠️ Recommended Tools to Test

### Option 1: **Postman** (Easiest - GUI)
1. Download Postman: https://www.postman.com/downloads/
2. Import: `postman_collection_tree_maintenance.json`
3. Set `base_url` variable to `http://localhost:8000`
4. Login to get token
5. Set `auth_token` variable
6. Test all endpoints with clicks!

### Option 2: **cURL** (Command Line)
```bash
# 1. Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@sylva.com","password":"demo123"}' \
  | jq -r '.token')

# 2. View all maintenance
curl -H "Authorization: Bearer $TOKEN" \
     http://localhost:8000/api/tree-maintenance

# 3. Create maintenance
curl -X POST http://localhost:8000/api/tree-maintenance \
     -H "Authorization: Bearer $TOKEN" \
     -F "tree_id=1" \
     -F "activity_type=watering" \
     -F "performed_at=2025-10-17" \
     -F "condition_after=good"
```

### Option 3: **Insomnia** (GUI Alternative)
Similar to Postman, can also import collections.

### Option 4: **Laravel Tinker** (For Testing Relationships)
```bash
php artisan tinker

# Get a tree with maintenance
$tree = Tree::with('maintenanceRecords')->first();
$tree->maintenanceRecords;

# Get tree health score
$tree->health_score;

# Check if needs maintenance
$tree->needsMaintenance(7);

# Get latest maintenance
$tree->latestMaintenance;
```

---

## 🖥️ Frontend Integration

If you want to build a web interface, you would create pages like:

### **Maintenance List Page** (`/trees/{id}/maintenance`)
Shows all maintenance records for a tree

### **Create Maintenance Page** (`/maintenance/create`)
Form to create new maintenance records

### **User Dashboard** (`/my-maintenance`)
Shows user's maintenance activities

### **Tree Profile Page** (`/trees/{id}`)
Include a "Maintenance History" section

---

## 📊 Complete Endpoint List

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tree-maintenance` | List all maintenances (with filters) |
| POST | `/api/tree-maintenance` | Create maintenance record |
| GET | `/api/tree-maintenance/{id}` | Get specific maintenance |
| PUT | `/api/tree-maintenance/{id}` | Update maintenance |
| DELETE | `/api/tree-maintenance/{id}` | Delete maintenance |
| GET | `/api/tree-maintenance-stats` | Get statistics |
| GET | `/api/trees/{tree}/maintenance-history` | Get tree's history |
| GET | `/api/user/maintenance-activities` | Get user's activities |

---

## 🎯 Quick Test Command

Run this to test if everything works:

```bash
# Start server (if not running)
php artisan serve

# In another terminal, test the endpoint
curl http://localhost:8000/api/tree-maintenance-stats \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

---

## 💡 Next Steps

### For Development:
1. **Use Postman**: Import the collection and test all endpoints
2. **Check Database**: Run `php artisan tinker` to verify data
3. **Build Frontend**: Create React/Vue components to display maintenance

### For Production:
1. Create admin panel to view all maintenance
2. Add maintenance calendar view
3. Build mobile app interface
4. Add maintenance notifications

---

## 🆘 Troubleshooting

### "Unauthenticated" Error
- Make sure you're sending the `Authorization: Bearer TOKEN` header
- Token might be expired - login again

### "Tree not found" Error
- Make sure tree ID exists in database
- Check: `php artisan tinker` → `Tree::all()`

### Can't see any maintenance records
- Create some first using POST endpoint
- Or check if any exist: `php artisan tinker` → `TreeMaintenance::all()`

---

## 📞 For More Help

See full documentation: **TREE_MAINTENANCE_DOCUMENTATION.md**

---

**Ready to use! 🚀** Start with Postman for the easiest testing experience!
