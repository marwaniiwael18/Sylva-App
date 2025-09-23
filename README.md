# 🌱 Sylva - Collaborative Urban Greening Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-red?style=for-the-badge&logo=laravel" alt="Laravel 11">
  <img src="https://img.shields.io/badge/React-18-blue?style=for-the-badge&logo=react" alt="React 18">
  <img src="https://img.shields.io/badge/Vite-7-purple?style=for-the-badge&logo=vite" alt="Vite 7">
  <img src="https://img.shields.io/badge/TailwindCSS-3-teal?style=for-the-badge&logo=tailwindcss" alt="TailwindCSS 3">
</p>

<p align="center">
  <strong>Connecting communities to create greener, more sustainable urban environments through collaborative action.</strong>
</p>

## 🌍 About Sylva

Sylva is a modern web application that empowers communities to participate in urban greening initiatives. Built with Laravel and React, it provides a comprehensive platform for organizing tree planting events, tracking environmental impact, and fostering community engagement in sustainability efforts.

### ✨ Key Features

- **🗺️ Interactive Map**: Explore greening projects and events in your area using Mapbox integration
- **📊 Impact Dashboard**: Track your environmental contributions and community achievements
- **🌳 Project Management**: Create and manage urban greening initiatives
- **📅 Event Organization**: Schedule and coordinate tree planting and sustainability events
- **👥 Community Engagement**: Connect with like-minded environmental enthusiasts
- **🏆 Gamification**: Earn badges and track your impact score
- **📱 Responsive Design**: Seamless experience across all devices
- **🔒 Secure Authentication**: Protected user accounts and data

## 🚀 Tech Stack

### Backend
- **Laravel 11** - Modern PHP framework with elegant syntax
- **PHP 8.2+** - Latest PHP features and performance improvements
- **SQLite** - Lightweight database for development
- **Laravel Sanctum** - API authentication system

### Frontend
- **React 18** - Component-based UI library with latest features
- **Vite** - Fast build tool and development server
- **TailwindCSS 3** - Utility-first CSS framework
- **Framer Motion** - Smooth animations and transitions
- **Mapbox GL JS** - Interactive maps and geolocation
- **Recharts** - Data visualization components
- **Lucide React** - Modern icon library

### Development Tools
- **Composer** - PHP dependency management
- **NPM** - Node.js package management
- **Laravel Artisan** - Command-line tools
- **Laravel Pint** - Code style fixer

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.2 or higher**
- **Composer** (PHP dependency manager)
- **Node.js 18 or higher**
- **NPM** or **Yarn**
- **Git**

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/marwaniiwael18/Sylva-App.git
cd Sylva-App
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Setup

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup

```bash
# Run database migrations
php artisan migrate

# Seed the database with sample data (optional)
php artisan db:seed
```

### 6. Build Frontend Assets

```bash
# For development
npm run dev

# For production
npm run build
```

## 🚀 Running the Application

### Development Mode

Start the Laravel development server:
```bash
php artisan serve
```

In a separate terminal, start the Vite development server:
```bash
npm run dev
```

The application will be available at `http://localhost:8000`

### Production Mode

Build the assets and start the server:
```bash
npm run build
php artisan serve
```

## 🔧 Available Scripts

### Laravel Commands
```bash
php artisan serve          # Start development server
php artisan migrate        # Run database migrations
php artisan migrate:fresh  # Fresh migration with data loss
php artisan db:seed        # Seed database with sample data
php artisan route:list     # List all registered routes
php artisan tinker         # Interactive PHP REPL
```

### NPM Scripts
```bash
npm run dev               # Start Vite development server
npm run build            # Build for production
npm run preview          # Preview production build
npm run lint             # Run ESLint
```

## 🗂️ Project Structure

```
sylva/
├── app/                          # Laravel application logic
│   ├── Http/Controllers/Api/     # API controllers
│   └── Models/                   # Eloquent models
├── bootstrap/                    # Application bootstrap
├── config/                       # Configuration files
├── database/                     # Migrations and seeders
├── public/                       # Web server document root
├── resources/                    # Frontend resources
│   ├── css/                      # CSS files
│   ├── js/react/                 # React application
│   │   ├── components/           # Reusable components
│   │   ├── contexts/             # React contexts
│   │   ├── data/                 # Static data
│   │   ├── hooks/                # Custom hooks
│   │   ├── pages/                # Page components
│   │   └── utils/                # Utility functions
│   └── views/                    # Blade templates
├── routes/                       # Application routes
├── storage/                      # Application storage
└── vendor/                       # Composer dependencies
```

## 🔐 Authentication

The application includes a demo authentication system:

**Demo Credentials:**
- Email: `demo@sylva.com`
- Password: `demo123`

### API Endpoints

```
POST /api/auth/login           # User login
POST /api/auth/register        # User registration
POST /api/auth/logout          # User logout
POST /api/auth/forgot-password # Password reset
GET  /api/projects             # Get all projects
GET  /api/events               # Get all events
GET  /api/dashboard/stats      # Get user dashboard stats
```

## 🌟 Features in Detail

### Interactive Dashboard
- Personal impact tracking (trees planted, events attended)
- Community statistics and leaderboards
- Environmental impact metrics (CO2 saved, biodiversity index)

### Project Management
- Create and manage greening projects
- Track project progress and milestones
- Coordinate with team members and volunteers

### Event System
- Schedule tree planting events
- RSVP and attendance tracking
- Event location mapping with Mapbox

### Community Features
- User profiles and achievements
- Community forums and discussions
- Collaborative project planning

## 🤝 Contributing

We welcome contributions to Sylva! Here's how you can help:

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/amazing-feature`
3. **Commit your changes**: `git commit -m 'Add amazing feature'`
4. **Push to the branch**: `git push origin feature/amazing-feature`
5. **Open a Pull Request**

### Development Guidelines

- Follow PSR-12 coding standards for PHP
- Use ESLint and Prettier for JavaScript/React code
- Write descriptive commit messages
- Add tests for new features
- Update documentation as needed

## 🐛 Known Issues & Troubleshooting

### Common Issues

**Build Failures:**
- Ensure Node.js version is 18 or higher
- Clear npm cache: `npm cache clean --force`
- Delete `node_modules` and run `npm install` again

**Database Issues:**
- Check database permissions
- Verify `.env` database configuration
- Run `php artisan migrate:fresh` if needed

**Authentication Issues:**
- Clear application cache: `php artisan cache:clear`
- Check CSRF token configuration
- Verify API routes are properly registered

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👥 Team

- **Marwani Wael** - Lead Developer
- **Aymen Jallouli** - Developer
- **Kenza Ben Slimen** - Developer
- **Nassim Khaldi** - Developer
- **Yasyne Manai** - Developer

**Project**: Advanced Web Applications  
**Institution**: TWIN 3

## 🙏 Acknowledgments

- **Laravel Team** - For the amazing PHP framework
- **React Team** - For the powerful UI library
- **TailwindCSS** - For the utility-first CSS framework
- **Mapbox** - For the interactive mapping platform
- **Open Source Community** - For the countless libraries and tools

---

<p align="center">
  <strong>🌱 Together, we can make our cities greener! 🌱</strong>
</p>
