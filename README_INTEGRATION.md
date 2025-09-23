# Sylva - Laravel + React Integration

This project integrates the React frontend template with Laravel backend to create a full-stack web application for collaborative urban greening.

## 🏗️ Architecture

- **Backend**: Laravel 11.x with API routes
- **Frontend**: React 18.2.0 with Vite bundling
- **Database**: SQLite (configured in .env)
- **Authentication**: Laravel Sanctum (future implementation)
- **Styling**: TailwindCSS with custom green theme

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Node.js 18+
- Composer

### Installation

1. **Install Backend Dependencies**
   ```bash
   composer install
   ```

2. **Install Frontend Dependencies**
   ```bash
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   php artisan migrate
   ```

### Development

Run both servers simultaneously:

```bash
# Terminal 1 - Laravel API Server
php artisan serve --port=8000

# Terminal 2 - Vite Dev Server
npm run dev
```

Then visit: **http://127.0.0.1:8000**

## 📁 Project Structure

```
sylva/
├── app/Http/Controllers/Api/     # API Controllers
├── routes/api.php                # API Routes
├── routes/web.php               # Web Routes (React SPA)
├── resources/
│   ├── js/react/               # React Application
│   │   ├── components/         # React Components
│   │   ├── pages/             # Page Components
│   │   ├── contexts/          # React Context (Auth, etc.)
│   │   └── data/              # Mock Data
│   ├── views/app.blade.php    # Main SPA Template
│   └── css/app.css            # TailwindCSS Styles
├── vite.config.js             # Vite Configuration
└── tailwind.config.js         # Tailwind Configuration
```

## 🔧 API Endpoints

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration  
- `POST /api/auth/logout` - User logout
- `POST /api/auth/forgot-password` - Password reset

### Mock Endpoints (Development)
- `GET /api/projects` - Get projects list
- `GET /api/events` - Get events list
- `GET /api/dashboard/stats` - Get user stats

## 🎨 Frontend Features

- ✅ **Authentication System** - Login, signup, password reset
- ✅ **Dashboard** - User stats and activity feed
- ✅ **Interactive Map** - Mapbox integration with markers
- ✅ **Projects Module** - Browse, join, and manage projects
- ✅ **Events Calendar** - Event registration and management
- ✅ **Feedback System** - Rate and review projects/events
- ✅ **Impact Dashboard** - Progress tracking with charts
- ✅ **Responsive Design** - Mobile-first approach

## 🔐 Demo Credentials

- **Email**: `demo@sylva.com`
- **Password**: `demo123`

## 🛠️ Configuration

### Mapbox Setup
1. Get API key from [Mapbox](https://www.mapbox.com/)
2. Add to your `.env`:
   ```
   VITE_MAPBOX_ACCESS_TOKEN=your_mapbox_token_here
   ```

### Production Build
```bash
npm run build
php artisan optimize
```

## 🧪 Testing

```bash
# PHP Tests
php artisan test

# Frontend Tests (future)
npm run test
```

## 🚀 Deployment

1. **Build Assets**
   ```bash
   npm run build
   ```

2. **Optimize Laravel**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Set Production Environment**
   - Update `.env` with production settings
   - Set `APP_ENV=production`
   - Configure database credentials

## 📚 Next Steps

- [ ] Implement real database models
- [ ] Add Laravel Sanctum authentication
- [ ] Create API resource controllers
- [ ] Add form request validation
- [ ] Implement file upload for images
- [ ] Add email notifications
- [ ] Set up testing environment

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

---

Built with ❤️ for a greener future 🌱