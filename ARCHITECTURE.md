# 🏗️ Sylva - Developer Architecture Guide

## 📋 Table of Contents
- [Project Overview](#project-overview)
- [Architecture Patterns](#architecture-patterns)
- [Folder Structure](#folder-structure)
- [Development Guidelines](#development-guidelines)
- [Code Standards](#code-standards)
- [API Documentation](#api-documentation)
- [Component Guidelines](#component-guidelines)

## 🎯 Project Overview

Sylva follows a modern full-stack architecture with:
- **Backend**: Laravel 11 with API-first approach
- **Frontend**: React 18 with component-based architecture  
- **State Management**: Context API with custom hooks
- **Styling**: TailwindCSS with utility-first approach
- **Build Tool**: Vite for fast development and optimized builds

## 🏛️ Architecture Patterns

### Backend Architecture (Laravel)
```
┌─ Controllers (HTTP Layer)
├─ Requests (Validation Layer) 
├─ Services (Business Logic)
├─ Resources (Data Transformation)
├─ Repositories (Data Access)
└─ Models (Data Models)
```

### Frontend Architecture (React)
```
┌─ Pages (Route Components)
├─ Components (UI Components)
├─ Services (API Calls)
├─ Hooks (Reusable Logic)
├─ Contexts (Global State)
├─ Utils (Helper Functions)
└─ Constants (Static Data)
```

## 📁 Folder Structure

### Laravel Backend Structure
```
app/
├── Http/
│   ├── Controllers/Api/     # API endpoint handlers
│   ├── Requests/           # Form validation classes
│   └── Resources/          # API resource transformers
├── Models/                 # Eloquent models
├── Services/              # Business logic services
├── Repositories/          # Data access layer
└── Providers/             # Service providers
```

### React Frontend Structure
```
resources/js/react/
├── components/
│   ├── ui/               # Reusable UI components
│   ├── forms/            # Form-specific components
│   ├── layout/           # Layout components
│   ├── maps/             # Map-related components
│   └── charts/           # Chart components
├── pages/                # Route-level components
├── contexts/             # React Context providers
├── hooks/                # Custom React hooks
├── services/             # API service layers
├── utils/                # Utility functions
├── constants/            # Application constants
└── data/                 # Static data files
```

## 🛠️ Development Guidelines

### Backend Development

#### 1. Controller Responsibilities
- Handle HTTP requests/responses only
- Delegate business logic to Services
- Use Form Requests for validation
- Return consistent JSON responses

```php
// ✅ Good - Thin controller
public function login(LoginRequest $request): JsonResponse
{
    $result = $this->authService->login($request->validated());
    return response()->json($result);
}

// ❌ Bad - Fat controller with business logic
public function login(Request $request): JsonResponse
{
    // Validation logic...
    // Database queries...
    // Business logic...
}
```

#### 2. Service Layer Pattern
- Contain business logic
- Be framework-agnostic
- Return structured data
- Handle exceptions gracefully

```php
// ✅ Service class structure
class AuthService
{
    public function login(array $credentials): array
    {
        // Business logic here
        return [
            'success' => true,
            'user' => $userData,
            'token' => $token
        ];
    }
}
```

#### 3. Request Validation
- Use Form Request classes
- Provide custom error messages
- Keep validation rules centralized

```php
// ✅ Form Request class
class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ];
    }
}
```

### Frontend Development

#### 1. Component Organization
- One component per file
- Use functional components with hooks
- Separate UI logic from business logic
- Keep components focused and reusable

```jsx
// ✅ Good component structure
const Button = ({ variant, size, children, ...props }) => {
  const classes = getButtonClasses(variant, size)
  return (
    <button className={classes} {...props}>
      {children}
    </button>
  )
}
```

#### 2. Custom Hooks
- Extract reusable logic into hooks
- Follow the `use` naming convention
- Return objects for multiple values
- Handle loading and error states

```jsx
// ✅ Custom hook example
export const useApi = (apiFunction) => {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  const execute = async () => {
    try {
      setLoading(true)
      const result = await apiFunction()
      setData(result)
    } catch (err) {
      setError(err.message)
    } finally {
      setLoading(false)
    }
  }

  return { data, loading, error, execute }
}
```

#### 3. Service Layer
- Centralize API calls
- Handle authentication automatically
- Provide consistent error handling
- Use proper HTTP methods

```jsx
// ✅ Service example
export const authService = {
  async login(credentials) {
    try {
      const response = await api.post('/auth/login', credentials)
      return response.data
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Login failed')
    }
  }
}
```

## 📝 Code Standards

### PHP (Laravel)
- Follow PSR-12 coding standards
- Use type hints for parameters and return types
- Write descriptive method and variable names
- Add PHPDoc comments for complex methods

### JavaScript/React
- Use ES6+ features (arrow functions, destructuring, etc.)
- Follow functional programming patterns
- Use meaningful variable and function names
- Add JSDoc comments for complex functions

### CSS/TailwindCSS
- Use utility classes over custom CSS when possible
- Create component classes for repeated patterns
- Use consistent spacing and naming conventions
- Follow mobile-first responsive design

## 🔌 API Documentation

### Authentication Endpoints
```
POST /api/auth/login
POST /api/auth/register  
POST /api/auth/logout
POST /api/auth/forgot-password
```

### Data Endpoints
```
GET  /api/reports
POST /api/reports
GET  /api/reports/{id}
PUT  /api/reports/{id}
DELETE /api/reports/{id}

GET  /api/dashboard/stats
```

### Response Format
All API responses follow this structure:
```json
{
  "success": boolean,
  "message": "string",
  "data": object|array,
  "errors": object (only on validation errors)
}
```

## 🧩 Component Guidelines

### UI Component Structure
```jsx
// Component definition
const ComponentName = ({ 
  prop1, 
  prop2 = defaultValue,
  className = '',
  ...props 
}) => {
  // Hooks and state
  const [state, setState] = useState(initialValue)
  
  // Event handlers
  const handleEvent = () => {
    // Handle logic
  }
  
  // Render
  return (
    <div className={`base-classes ${className}`} {...props}>
      {children}
    </div>
  )
}

// PropTypes or TypeScript types (optional)
ComponentName.propTypes = {
  prop1: PropTypes.string.isRequired,
  prop2: PropTypes.string
}

export default ComponentName
```

### Page Component Structure
```jsx
const PageName = () => {
  // Data fetching
  const { data, loading, error } = useApi(serviceFunction)
  
  // Local state
  const [localState, setLocalState] = useState(initialValue)
  
  // Effects
  useEffect(() => {
    // Side effects
  }, [dependencies])
  
  // Event handlers
  const handleAction = () => {
    // Handle user actions
  }
  
  // Loading state
  if (loading) return <LoadingSpinner />
  
  // Error state
  if (error) return <ErrorMessage error={error} />
  
  // Main render
  return (
    <div>
      <Header />
      <Main>
        {/* Page content */}
      </Main>
    </div>
  )
}
```

## 🚀 Getting Started for New Developers

1. **Clone and Setup**
   ```bash
   git clone <repository>
   cd sylva
   ./setup.sh
   ```

2. **Development Workflow**
   ```bash
   # Start Laravel server
   php artisan serve
   
   # Start Vite dev server (separate terminal)
   npm run dev
   ```

3. **Key Files to Understand**
   - `resources/js/react/App.jsx` - Main React app
   - `routes/api.php` - API routes definition
   - `app/Http/Controllers/Api/` - API controllers
   - `resources/js/react/services/` - API services

4. **Common Tasks**
   - Adding new API endpoint: Controller → Route → Service → Frontend Service
   - Adding new page: Create page component → Add route → Update navigation
   - Adding new UI component: Create in `components/ui/` → Export → Use

## 📚 Best Practices Summary

### ✅ Do
- Use meaningful names for variables, functions, and classes
- Keep components small and focused
- Handle loading and error states
- Use TypeScript or PropTypes for type checking
- Write tests for critical functionality
- Follow established patterns and conventions
- Document complex logic with comments

### ❌ Don't
- Put business logic in controllers or components
- Create massive components or functions  
- Ignore error handling
- Use inline styles over TailwindCSS classes
- Commit commented-out code
- Skip validation on API endpoints
- Forget to handle edge cases

---

This architecture ensures maintainable, scalable, and developer-friendly code. Always prioritize readability and consistency over cleverness.