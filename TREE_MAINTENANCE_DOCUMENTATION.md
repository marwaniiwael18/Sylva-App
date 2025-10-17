# 🌳 Tree Maintenance Feature - Complete Documentation

## Overview
The Tree Maintenance feature allows users to track and record maintenance activities performed on trees in the Sylva application. This includes watering, pruning, fertilizing, disease treatment, inspections, and other care activities.

---

## 📋 Database Structure

### Tree Maintenance Table
```sql
CREATE TABLE tree_maintenance (
    id BIGINT PRIMARY KEY,
    tree_id BIGINT (FK to trees),
    user_id BIGINT (FK to users),
    event_id BIGINT NULLABLE (FK to events),
    activity_type ENUM('watering', 'pruning', 'fertilizing', 'disease_treatment', 'inspection', 'other'),
    notes TEXT NULLABLE,
    images JSON NULLABLE,
    performed_at DATE,
    condition_after ENUM('excellent', 'good', 'fair', 'poor') NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 🔗 Relationships

### TreeMaintenance Model
- **belongsTo** Tree (`tree`)
- **belongsTo** User (`maintainer` / `user`)
- **belongsTo** Event (`event`)

### Tree Model (Updated)
- **hasMany** TreeMaintenance (`maintenanceRecords`)
- **hasOne** TreeMaintenance (`latestMaintenance`)

### User Model (Add these relationships)
```php
public function maintenances()
{
    return $this->hasMany(TreeMaintenance::class, 'user_id');
}
```

### Event Model (Add these relationships)
```php
public function maintenances()
{
    return $this->hasMany(TreeMaintenance::class, 'event_id');
}
```

---

## 🛠️ API Endpoints

### Base URL: `/api/tree-maintenance`

All endpoints require authentication (`auth:sanctum` middleware)

#### 1. List Maintenance Records
```http
GET /api/tree-maintenance
```

**Query Parameters:**
- `tree_id` - Filter by tree ID
- `user_id` - Filter by user ID
- `event_id` - Filter by event ID
- `activity_type` - Filter by activity type
- `condition` - Filter by condition
- `days` - Get records from last N days
- `per_page` - Items per page (default: 15)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "tree_id": 5,
        "user_id": 2,
        "event_id": null,
        "activity_type": "watering",
        "activity_type_name": "Watering",
        "activity_icon": "💧",
        "notes": "Regular watering session",
        "images": ["tree-maintenance/image1.jpg"],
        "image_urls": ["http://domain.com/storage/tree-maintenance/image1.jpg"],
        "performed_at": "2025-10-17",
        "performed_at_formatted": "Oct 17, 2025",
        "condition_after": "good",
        "condition_color": "blue",
        "tree": {
          "id": 5,
          "species": "Oak",
          "type": "Forest",
          "status": "Planted"
        },
        "maintainer": {
          "id": 2,
          "name": "John Doe",
          "email": "john@example.com"
        }
      }
    ],
    "total": 50,
    "per_page": 15
  },
  "message": "Maintenance records retrieved successfully"
}
```

#### 2. Create Maintenance Record
```http
POST /api/tree-maintenance
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body (FormData):**
```
tree_id: 5
activity_type: watering
performed_at: 2025-10-17
condition_after: good
notes: Regular watering session
event_id: 10 (optional)
images[]: file1.jpg (optional, max 5 images)
images[]: file2.jpg (optional)
```

**Response:**
```json
{
  "success": true,
  "data": { /* maintenance object */ },
  "message": "Maintenance record created successfully"
}
```

#### 3. Get Single Maintenance Record
```http
GET /api/tree-maintenance/{id}
```

**Response:**
```json
{
  "success": true,
  "data": { /* maintenance object with tree, maintainer, event */ },
  "message": "Maintenance record retrieved successfully"
}
```

#### 4. Update Maintenance Record
```http
PUT /api/tree-maintenance/{id}
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Body (FormData):**
```
activity_type: pruning (optional)
performed_at: 2025-10-18 (optional)
condition_after: excellent (optional)
notes: Updated notes (optional)
images[]: newfile.jpg (optional)
```

**Authorization:** Only the user who created the record or admins can update.

**Response:**
```json
{
  "success": true,
  "data": { /* updated maintenance object */ },
  "message": "Maintenance record updated successfully"
}
```

#### 5. Delete Maintenance Record
```http
DELETE /api/tree-maintenance/{id}
```

**Authorization:** Only the user who created the record or admins can delete.

**Response:**
```json
{
  "success": true,
  "message": "Maintenance record deleted successfully"
}
```

---

## 📊 Additional Endpoints

### 6. Get Maintenance Statistics
```http
GET /api/tree-maintenance-stats?tree_id=5&user_id=2
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_maintenances": 45,
    "this_month": 8,
    "this_year": 45,
    "by_activity_type": {
      "watering": 20,
      "pruning": 10,
      "fertilizing": 8,
      "inspection": 7
    },
    "by_condition": {
      "excellent": 15,
      "good": 20,
      "fair": 8,
      "poor": 2
    },
    "recent_activities": [ /* last 5 activities */ ]
  },
  "message": "Statistics retrieved successfully"
}
```

### 7. Get Tree Maintenance History
```http
GET /api/trees/{treeId}/maintenance-history
```

**Response:**
```json
{
  "success": true,
  "data": {
    "tree": { /* tree object */ },
    "maintenance_history": [ /* all maintenance records */ ],
    "total_maintenances": 15,
    "last_maintenance": { /* most recent maintenance */ },
    "health_score": 75
  },
  "message": "Tree maintenance history retrieved successfully"
}
```

### 8. Get User's Maintenance Activities
```http
GET /api/user/maintenance-activities?user_id=2
```

**Response:**
```json
{
  "success": true,
  "data": {
    "maintenances": { /* paginated list */ },
    "stats": {
      "total_activities": 45,
      "trees_maintained": 12,
      "this_month": 8,
      "favorite_activity": "watering"
    }
  },
  "message": "User maintenance activities retrieved successfully"
}
```

---

## 🎨 Activity Types

| Type | Display Name | Icon |
|------|--------------|------|
| `watering` | Watering | 💧 |
| `pruning` | Pruning | ✂️ |
| `fertilizing` | Fertilizing | 🌱 |
| `disease_treatment` | Disease Treatment | 💊 |
| `inspection` | Inspection | 🔍 |
| `other` | Other | 🛠️ |

---

## 🎯 Condition After Maintenance

| Value | Display Name | Color | Health Score |
|-------|--------------|-------|--------------|
| `excellent` | Excellent | green | 100 |
| `good` | Good | blue | 75 |
| `fair` | Fair | yellow | 50 |
| `poor` | Poor | red | 25 |

---

## 🔍 Model Scopes

### TreeMaintenance Scopes

```php
// Filter by tree
TreeMaintenance::byTree($treeId)->get();

// Filter by user
TreeMaintenance::byUser($userId)->get();

// Filter by event
TreeMaintenance::byEvent($eventId)->get();

// Filter by activity type
TreeMaintenance::byActivityType('watering')->get();

// Filter by condition
TreeMaintenance::byCondition('excellent')->get();

// Get recent records (default: 30 days)
TreeMaintenance::recent(30)->get();

// Get records from this month
TreeMaintenance::thisMonth()->get();

// Get records from this year
TreeMaintenance::thisYear()->get();
```

---

## 🌲 Tree Model Helper Methods

### New Methods Added to Tree Model

```php
// Get all maintenance records
$tree->maintenanceRecords;

// Get latest maintenance
$tree->latestMaintenance;

// Get recent maintenances (default: 30 days)
$tree->recentMaintenances(30);

// Get last maintenance date
$tree->last_maintenance_date;

// Get total maintenance count
$tree->maintenance_count;

// Get last recorded condition
$tree->last_condition;

// Check if tree needs maintenance
$tree->needsMaintenance(7); // Returns true if no maintenance in 7 days

// Get health score (0-100 based on last condition)
$tree->health_score;
```

---

## 📝 Validation Rules

### Creating Maintenance Record

- `tree_id`: required, must exist in trees table
- `activity_type`: required, must be one of the valid types
- `performed_at`: required, must be a valid date, cannot be in future
- `condition_after`: optional, must be: excellent, good, fair, or poor
- `notes`: optional, max 1000 characters
- `event_id`: optional, must exist in events table
- `images`: optional, max 5 images
- `images.*`: must be image file (jpeg, png, jpg, gif, webp), max 2MB each

### Updating Maintenance Record
Same rules as creating, but all fields are optional (use `sometimes` instead of `required`)

---

## 🔒 Authorization Rules

1. **Create**: Any authenticated user can create maintenance records
2. **View**: Anyone can view maintenance records
3. **Update**: Only the user who created the record OR admins
4. **Delete**: Only the user who created the record OR admins

---

## 💡 Usage Examples

### Frontend Integration (JavaScript/React)

```javascript
// Get maintenance records for a tree
const getTreeMaintenances = async (treeId) => {
  const response = await fetch(`/api/tree-maintenance?tree_id=${treeId}`, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  return response.json();
};

// Create a maintenance record
const createMaintenance = async (data) => {
  const formData = new FormData();
  formData.append('tree_id', data.tree_id);
  formData.append('activity_type', data.activity_type);
  formData.append('performed_at', data.performed_at);
  formData.append('condition_after', data.condition_after);
  formData.append('notes', data.notes);
  
  // Add images
  if (data.images) {
    data.images.forEach((image, index) => {
      formData.append(`images[${index}]`, image);
    });
  }
  
  const response = await fetch('/api/tree-maintenance', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    },
    body: formData
  });
  return response.json();
};

// Get tree health overview
const getTreeHealth = async (treeId) => {
  const response = await fetch(`/api/trees/${treeId}/maintenance-history`, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  return response.json();
};
```

---

## 🎨 UI Component Suggestions

### 1. Maintenance Form Component
```jsx
<MaintenanceForm 
  treeId={tree.id}
  onSuccess={(maintenance) => console.log('Created:', maintenance)}
/>
```

**Fields:**
- Tree selector (autocomplete)
- Activity type (dropdown with icons)
- Date picker (performed_at)
- Condition selector (radio buttons with colors)
- Notes textarea
- Image uploader (multi-file)
- Event selector (optional)

### 2. Maintenance History List
```jsx
<MaintenanceHistory 
  treeId={tree.id}
  limit={10}
/>
```

**Display:**
- Timeline view of maintenance activities
- Activity icons and names
- User who performed the maintenance
- Date and time
- Condition indicators
- Notes preview
- Image gallery

### 3. Tree Health Card
```jsx
<TreeHealthCard 
  tree={tree}
/>
```

**Shows:**
- Health score gauge (0-100)
- Last maintenance date
- Days since last maintenance
- Warning if maintenance is overdue
- Quick action buttons

### 4. Maintenance Statistics Dashboard
```jsx
<MaintenanceStats 
  userId={user.id}
/>
```

**Displays:**
- Total maintenances count
- Trees maintained count
- Activity breakdown (pie chart)
- Monthly activity graph
- Favorite activity type

---

## 🚀 Next Steps

### Recommended Enhancements

1. **Notifications**: Send reminders when trees need maintenance
2. **Maintenance Schedule**: Allow users to schedule future maintenance
3. **Bulk Maintenance**: Record maintenance for multiple trees at once
4. **Maintenance Tasks**: Create and assign maintenance tasks
5. **Recurring Maintenance**: Set up recurring maintenance schedules
6. **Maintenance Reports**: Generate PDF reports of maintenance activities
7. **QR Codes**: Generate QR codes for trees to quickly record maintenance

---

## 🧪 Testing

### API Testing with Postman/Insomnia

1. **Create Token**: Login to get authentication token
2. **Test Each Endpoint**: Use the examples above
3. **Test Filters**: Try different query parameters
4. **Test Authorization**: Try updating/deleting other users' records
5. **Test Validation**: Submit invalid data to test error responses

### Example Postman Collection Structure
```
📁 Tree Maintenance
  ├─ GET List Maintenances
  ├─ POST Create Maintenance
  ├─ GET Single Maintenance
  ├─ PUT Update Maintenance
  ├─ DELETE Maintenance
  ├─ GET Stats
  ├─ GET Tree History
  └─ GET User Activities
```

---

## 📧 Support

For questions or issues with the Tree Maintenance feature:
- Check this documentation first
- Review the code in `app/Models/TreeMaintenance.php`
- Inspect the controller at `app/Http/Controllers/Api/TreeMaintenanceController.php`
- Test API endpoints using the examples provided

---

**Created**: October 17, 2025  
**Version**: 1.0.0  
**Author**: Sylva Development Team
