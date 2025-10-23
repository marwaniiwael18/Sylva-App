<div align="center">

# 🌳 Sylva - Urban Greening Platform

### *Collaborative Environmental Reporting & Tree Management System*

[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

[Features](#-features) • [Quick Start](#-quick-start) • [Tech Stack](#-tech-stack) • [Documentation](#-documentation) • [Contributing](#-contributing)

</div>

---

## 📖 About Sylva

**Sylva** is a comprehensive urban greening platform that empowers communities to collaborate on environmental initiatives. From reporting environmental issues to managing tree plantations, organizing events, and fostering community engagement through forums and blogs - Sylva brings citizens, organizations, and administrators together to create greener, healthier urban spaces.

### 🎯 Mission
Transform urban environments through collaborative action, AI-powered insights, and transparent community engagement.

---

## ✨ Features

### 🚨 Environmental Reporting System
- **📍 Interactive Map Integration** - Report issues with precise location using Leaflet.js
- **📸 Multi-Image Upload** - Upload up to 5 images per report (2MB each)
- **🤖 AI-Powered Descriptions** - Google Gemini analyzes images and generates detailed descriptions
- **🏷️ Smart Categorization** - Tree planting, maintenance, pollution, or green space suggestions
- **⚡ Urgency Levels** - Low, Medium, High priority classification
- **💬 Community Engagement** - Comments, votes, and reactions on reports
- **📊 Activity Feed** - Real-time tracking of report updates and community interactions

### 🌲 Tree Management
- **🌳 Tree Inventory** - Comprehensive database of urban trees
- **📈 Health Monitoring** - Track tree health status and maintenance needs
- **🗓️ Care Scheduling** - Plan and log tree care activities
- **📍 GPS Location Tracking** - Precise geolocation for every tree
- **📷 Photo Documentation** - Visual history of tree growth and condition
- **🔍 Advanced Filtering** - Search by species, location, health status, and more

### 🎉 Events & Community
- **📅 Event Management** - Create and manage environmental events
- **👥 User Registration** - Track event participants and RSVPs
- **🗺️ Event Locations** - Interactive maps for event venues
- **📢 Notifications** - Keep community informed about upcoming activities

### 💰 Donations & Support
- **💳 Stripe Integration** - Secure payment processing
- **🎯 Campaign-Based Donations** - Support specific environmental projects
- **🔄 Refund Management** - Transparent refund processing
- **📊 Donation Analytics** - Track funding and impact metrics

### 💬 Community Engagement
- **📝 Blog System** - Share environmental stories and updates
- **🗨️ Forum Platform** - Discuss topics, ask questions, get answers
- **💡 Threaded Discussions** - Organized posts and replies
- **👍 Engagement Metrics** - Likes, reactions, and community feedback

### 🔐 Admin Dashboard
- **📊 Comprehensive Analytics** - Real-time statistics and insights
- **✅ Report Moderation** - Validate, reject, or delete reports
- **👥 User Management** - Manage community members and permissions
- **🌳 Tree Database Management** - Full CRUD operations on tree inventory
- **🎫 Event Administration** - Oversee and manage community events
- **💰 Donation Oversight** - Monitor and manage fundraising campaigns

### 🤖 AI-Powered Features
- **🌿 Plant Identification** - Identify plant species from uploaded images
- **📝 Smart Report Generation** - AI-generated descriptions from environmental photos
- **🎯 Type Detection** - Automatic categorization of environmental issues
- **💡 Suggestion Engine** - AI-powered recommendations for environmental actions

### 🛠️ Technical Features
- **🔄 Real-time Updates** - Live data synchronization
- **📱 Responsive Design** - Mobile-first approach with Tailwind CSS
- **🗺️ Interactive Maps** - Leaflet.js integration for location services
- **🔒 Secure Authentication** - Laravel Sanctum token-based auth
- **📈 Code Quality** - SonarCloud analysis and monitoring
- **🐳 Docker Support** - Containerized deployment ready
- **📊 Monitoring Stack** - Prometheus & Grafana integration
- **🚀 CI/CD Pipeline** - Automated testing and deployment

---

## 🚀 Quick Start

### Prerequisites

Before you begin, ensure you have:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **NPM** >= 9.x
- **MySQL** >= 8.0 or **MariaDB** >= 10.3
- **Git**

### Installation

#### 1️⃣ Clone the Repository

```bash
git clone https://github.com/marwaniiwael18/Sylva-App.git
cd Sylva-App
```

#### 2️⃣ Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

#### 3️⃣ Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 4️⃣ Configure Your `.env` File

```env
# Application
APP_NAME="Sylva"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sylva_app
DB_USERNAME=root
DB_PASSWORD=your_password

# Stripe Payment (Optional)
STRIPE_KEY=pk_test_your_key
STRIPE_SECRET=sk_test_your_secret

# AI Services (Optional but Recommended)
GEMINI_API_KEY=your_gemini_api_key
PLANT_ID_API_KEY=your_plant_id_key
```

#### 5️⃣ Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE sylva_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed
```

#### 6️⃣ Build Assets

```bash
# Development build
npm run dev

# Or production build
npm run build
```

#### 7️⃣ Start Development Server

```bash
# Start Laravel server
php artisan serve

# The application will be available at http://localhost:8000
```

### 🎉 You're Ready!

- **Frontend**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **API Docs**: http://localhost:8000/api/documentation

---

## 🐳 Docker Deployment

### Quick Docker Setup

```bash
# Start all services
docker-compose up -d

# Check logs
docker-compose logs -f

# Stop services
docker-compose down
```

### Access Services

- **Application**: http://localhost:8000
- **Grafana Dashboard**: http://localhost:3000 (admin/admin)
- **Prometheus**: http://localhost:9090

### Docker Services Included

- 🐘 **PHP-FPM** (Laravel Application)
- 🗄️ **MySQL** (Database)
- 🔧 **Nginx** (Web Server)
- 📊 **Prometheus** (Metrics Collection)
- 📈 **Grafana** (Visualization & Dashboards)

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12.x
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0 / MariaDB 10.3+
- **Authentication**: Laravel Sanctum
- **Payment Processing**: Stripe API
- **AI Integration**: Google Gemini API, Plant.id API

### Frontend
- **CSS Framework**: Tailwind CSS 3.4
- **JavaScript**: Alpine.js 3.x, Vanilla JS
- **Build Tool**: Vite 7.x
- **Maps**: Leaflet.js 1.9.4
- **Icons**: Lucide Icons

### DevOps & Monitoring
- **Containerization**: Docker & Docker Compose
- **CI/CD**: GitHub Actions
- **Code Quality**: SonarCloud
- **Monitoring**: Prometheus & Grafana
- **Version Control**: Git & GitHub

### Development Tools
- **Package Manager**: Composer (PHP), NPM (JS)
- **Testing**: PHPUnit, Laravel Dusk
- **Code Style**: Laravel Pint (PSR-12)
- **API Testing**: Postman

---

## 📁 Project Structure

```
sylva-app/
├── 📂 app/                          # Application core
│   ├── Http/
│   │   ├── Controllers/             # HTTP controllers
│   │   │   ├── AdminController.php  # Admin panel logic
│   │   │   ├── ReportController.php # Report management
│   │   │   └── ...
│   │   ├── Middleware/              # HTTP middleware
│   │   └── Requests/                # Form validation
│   ├── Models/                      # Eloquent models
│   │   ├── User.php
│   │   ├── Report.php
│   │   ├── Tree.php
│   │   ├── Event.php
│   │   ├── Donation.php
│   │   └── ...
│   └── Services/                    # Business logic
│       ├── GeminiService.php        # AI description generation
│       ├── PlantIdentificationService.php
│       └── ...
├── 📂 database/
│   ├── migrations/                  # Database migrations
│   ├── seeders/                     # Database seeders
│   └── factories/                   # Model factories
├── 📂 resources/
│   ├── css/                         # Styles
│   ├── js/                          # JavaScript
│   └── views/                       # Blade templates
│       ├── admin/                   # Admin panel views
│       ├── pages/                   # Public pages
│       ├── components/              # Reusable components
│       └── layouts/                 # Layout templates
├── 📂 routes/
│   ├── web.php                      # Web routes
│   ├── api.php                      # API routes
│   └── console.php                  # Artisan commands
├── 📂 public/                       # Public assets
├── 📂 storage/                      # File storage
├── 📂 tests/                        # Automated tests
├── 📂 docker/                       # Docker configs
├── 📂 monitoring/                   # Prometheus/Grafana
├── docker-compose.yml               # Docker services
├── .github/workflows/               # CI/CD pipelines
└── README.md                        # This file
```

---

## 🔌 API Documentation

### Authentication

```http
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
POST /api/auth/forgot-password
```

### Reports

```http
GET    /api/reports-public          # List all reports
POST   /api/reports-public          # Create report
GET    /api/reports-public/{id}     # Get single report
PUT    /api/reports-public/{id}     # Update report
DELETE /api/reports-public/{id}     # Delete report
```

### Trees

```http
GET    /api/trees                   # List trees
POST   /api/trees                   # Add tree
GET    /api/trees/{id}              # Get tree details
PUT    /api/trees/{id}              # Update tree
DELETE /api/trees/{id}              # Remove tree
```

### Events

```http
GET    /api/events                  # List events
POST   /api/events                  # Create event
GET    /api/events/{id}             # Get event
PUT    /api/events/{id}             # Update event
DELETE /api/events/{id}             # Delete event
```

### Donations

```http
GET    /api/donations               # List donations
POST   /api/donations               # Process donation
GET    /api/donations/{id}          # Get donation details
```

### AI Services

```http
POST   /api/ai/analyze-image        # Analyze environmental image
POST   /api/ai/identify-plant       # Identify plant species
POST   /api/ai/generate-description # Generate report description
```

### Response Format

All API responses follow this structure:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data here
  }
}
```

Error responses:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

---

## 🧪 Testing

### Run All Tests

```bash
# Run PHPUnit tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Test Database

Tests use a separate SQLite database automatically. No setup required!

---

## 📊 CI/CD Pipeline

### GitHub Actions Workflow

Our automated pipeline includes:

1. **🧪 Testing** - Run PHPUnit tests & build assets
2. **🔍 Code Quality** - SonarCloud analysis
3. **🏗️ Build** - Create Docker images
4. **🚀 Deploy** - Deploy to production (on main branch)
5. **📊 Monitor** - Health checks & monitoring setup

### Pipeline Triggers

- Push to `main` branch
- Pull requests
- Manual workflow dispatch

### Required Secrets

Configure these in your GitHub repository settings:

| Secret | Description |
|--------|-------------|
| `SONAR_TOKEN` | SonarCloud authentication token |
| `SONAR_HOST_URL` | SonarCloud server URL |
| `DOCKER_USERNAME` | Docker Hub username (optional) |
| `DOCKER_PASSWORD` | Docker Hub password (optional) |

---

## 🎨 Key Features Showcase

### 🗺️ Interactive Reporting with Maps

Users can report environmental issues by clicking directly on an interactive map. The system captures precise GPS coordinates and uses reverse geocoding to provide human-readable addresses.

```javascript
// Example: Click-to-report functionality
map.on('click', function(e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;
    // Create marker and capture location
    addMarker(lat, lng);
    // Reverse geocode to get address
    getAddressFromCoordinates(lat, lng);
});
```

### 🤖 AI-Powered Report Generation

Upload an image, and our Gemini AI analyzes it to:
- Generate detailed environmental descriptions
- Suggest appropriate report types
- Determine urgency levels
- Extract relevant environmental data

### 💬 Real-Time Community Engagement

- **Voting System**: Upvote/downvote reports
- **Reactions**: Like, Love, Support, Concern
- **Comments & Replies**: Threaded discussions
- **Activity Feed**: Live updates on report status

### 📊 Admin Dashboard Analytics

Comprehensive metrics including:
- Total reports (pending, validated, rejected)
- Tree inventory statistics
- Event participation rates
- Donation analytics
- User engagement metrics

---

## 🔧 Configuration

### Environment Variables

#### Core Settings
```env
APP_NAME="Sylva"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://your-domain.com
```

#### Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sylva_app
DB_USERNAME=root
DB_PASSWORD=secure_password
```

#### Mail (Optional)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sylva.app
MAIL_FROM_NAME="${APP_NAME}"
```

#### Payment Processing
```env
STRIPE_KEY=pk_live_your_key
STRIPE_SECRET=sk_live_your_secret
```

#### AI Services
```env
# Google Gemini API
GEMINI_API_KEY=your_gemini_api_key

# Plant Identification
PLANT_ID_API_KEY=your_plant_id_key

# Google Maps (optional)
GOOGLE_API_KEY=your_google_maps_key
```

---

## 👥 Contributing

We welcome contributions! Here's how you can help:

### 🐛 Reporting Bugs

1. Check if the issue already exists
2. Create a detailed bug report with:
   - Steps to reproduce
   - Expected vs actual behavior
   - Screenshots (if applicable)
   - Environment details

### 💡 Suggesting Features

1. Open an issue with the `enhancement` label
2. Describe the feature and its benefits
3. Discuss implementation approach

### 🔧 Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Make your changes
4. Write/update tests
5. Ensure all tests pass (`php artisan test`)
6. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
7. Push to the branch (`git push origin feature/AmazingFeature`)
8. Open a Pull Request

### 📝 Code Standards

- Follow PSR-12 for PHP code
- Use ESLint rules for JavaScript
- Write meaningful commit messages
- Add comments for complex logic
- Update documentation as needed

---

## 🏆 Team

### Core Contributors

- **Wael Marwani** - Full Stack Developer
- **Yassine** - Tree Management Module
- **Contributors** - [View all contributors](https://github.com/marwaniiwael18/Sylva-App/graphs/contributors)

---

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- **Laravel Community** for the amazing framework
- **Tailwind CSS** for the utility-first CSS framework
- **Leaflet.js** for interactive maps
- **Google Gemini** for AI-powered features
- **Alpine.js** for reactive components
- **All Contributors** who help improve Sylva

---

## 📞 Support & Contact

- **Documentation**: [ARCHITECTURE.md](ARCHITECTURE.md)
- **Issues**: [GitHub Issues](https://github.com/marwaniiwael18/Sylva-App/issues)
- **Email**: wm.express4@gmail.com
- **Project Link**: [https://github.com/marwaniiwael18/Sylva-App](https://github.com/marwaniiwael18/Sylva-App)

---

## 🗺️ Roadmap

### ✅ Completed
- [x] Environmental reporting system
- [x] Tree inventory management
- [x] Admin dashboard
- [x] AI-powered image analysis
- [x] Interactive maps integration
- [x] Community engagement features
- [x] Event management
- [x] Donation system with Stripe
- [x] Docker deployment
- [x] CI/CD pipeline

### 🚧 In Progress
- [ ] Mobile application (React Native)
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Notification system (Email/SMS)
- [ ] Export reports (PDF/Excel)

### 🔮 Future Plans
- [ ] Machine learning for issue prioritization
- [ ] Augmented reality tree identification
- [ ] Blockchain-based donation tracking
- [ ] Integration with smart city sensors
- [ ] Gamification and rewards system

---

<div align="center">

### 🌟 Star us on GitHub!

If you find Sylva useful, please consider giving us a star ⭐

**Made with 💚 for a greener planet**

[⬆ Back to Top](#-sylva---urban-greening-platform)

</div>
