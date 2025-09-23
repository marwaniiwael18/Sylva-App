# Sylva - Collaborative Urban Greening Platform

A modern React web application designed to connect communities with urban greening initiatives. Sylva enables users to discover, join, and create environmental projects while tracking their collective impact on urban ecosystems.

## ✨ Features

### 🔐 Authentication System
- Secure login and registration
- Password recovery functionality
- Protected routes with user session management

### 🗺️ Interactive Map
- Real-time visualization of green spaces and projects
- Report environmental issues or opportunities
- Discover nearby greening initiatives
- Integrated with Mapbox for smooth map interactions

### 🌱 Project Management
- Browse and join community projects
- Detailed project pages with progress tracking
- Comment and collaboration system
- Advanced filtering and search capabilities

### 📅 Events Calendar
- Community event listings with calendar view
- Event registration and attendance tracking
- Detailed event pages with testimonials
- Grid and calendar view options

### 📝 Feedback System
- Rate and review projects and events
- Community feedback aggregation
- Rating distribution analytics
- Search and filter reviews

### 📊 Impact Dashboard
- Personal environmental impact tracking
- Achievement badges and milestones
- Community impact visualization
- Progress towards sustainability goals
- Interactive charts and analytics

### 🎨 Modern Design
- Beautiful, responsive interface inspired by Spotify, Notion, and Airbnb
- Smooth animations and transitions using Framer Motion
- Consistent design system with TailwindCSS
- Mobile-first responsive design

## 🚀 Technology Stack

- **Frontend Framework**: React 18.2.0
- **Build Tool**: Vite 5.0.8
- **Styling**: TailwindCSS 3.3.6 with custom design system
- **Routing**: React Router 6.20.1
- **Animations**: Framer Motion 10.16.16
- **Icons**: Lucide React 0.294.0
- **Maps**: Mapbox GL JS 3.0.1
- **Charts**: Recharts 2.8.0
- **Forms**: React Hook Form 7.48.2
- **Date Handling**: date-fns 3.0.6

## 📁 Project Structure

```
frontend/
├── public/
│   ├── index.html
│   └── vite.svg
├── src/
│   ├── components/
│   │   └── layout/
│   │       ├── DashboardLayout.jsx
│   │       ├── Header.jsx
│   │       └── Sidebar.jsx
│   ├── contexts/
│   │   └── AuthContext.jsx
│   ├── data/
│   │   └── mockData.js
│   ├── pages/
│   │   ├── auth/
│   │   │   ├── LoginPage.jsx
│   │   │   ├── SignupPage.jsx
│   │   │   └── ForgotPasswordPage.jsx
│   │   ├── DashboardPage.jsx
│   │   ├── MapPage.jsx
│   │   ├── ProjectsPage.jsx
│   │   ├── ProjectDetailPage.jsx
│   │   ├── EventsPage.jsx
│   │   ├── EventDetailPage.jsx
│   │   ├── FeedbackPage.jsx
│   │   └── ImpactPage.jsx
│   ├── App.jsx
│   ├── main.jsx
│   └── index.css
├── package.json
├── vite.config.js
├── tailwind.config.js
├── postcss.config.js
└── README.md
```

## 🛠️ Installation & Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd sylva/frontend
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Environment Setup**
   Create a `.env` file in the frontend directory:
   ```env
   VITE_MAPBOX_ACCESS_TOKEN=your_mapbox_token_here
   ```

4. **Start development server**
   ```bash
   npm run dev
   ```

5. **Build for production**
   ```bash
   npm run build
   ```

## 🎯 Getting Started

### Demo Credentials
Use these credentials to explore the application:
- **Email**: demo@sylva.com
- **Password**: demo123

### Key Features to Explore

1. **Dashboard**: View your personal stats, recent activities, and progress
2. **Map**: Explore green spaces, submit reports, and discover opportunities
3. **Projects**: Join community initiatives and track collective progress
4. **Events**: Attend workshops, cleanup events, and educational sessions
5. **Feedback**: Share experiences and read community reviews
6. **Impact**: Track your environmental contributions and earn badges

## 🎨 Design System

### Color Palette
- **Primary**: Green tones (#059669, #10B981, #16A34A)
- **Secondary**: Blue accents (#3B82F6, #1D4ED8)
- **Neutral**: Gray scales for text and backgrounds
- **Accent**: Yellow/orange for achievements (#F59E0B, #EAB308)

### Typography
- **Font Family**: Inter (system fallbacks included)
- **Sizes**: Responsive typography using clamp() functions
- **Weights**: 400 (regular), 500 (medium), 600 (semibold), 700 (bold)

### Components
- Consistent button styles with hover effects
- Card components with subtle shadows and animations
- Form inputs with focus states
- Badge system for status indicators
- Progress bars and charts for data visualization

## 📱 Responsive Design

The application is built with a mobile-first approach:
- **Mobile**: Optimized for phones (320px+)
- **Tablet**: Enhanced layouts for tablets (768px+)
- **Desktop**: Full-featured experience (1024px+)
- **Large Screens**: Optimized for large displays (1440px+)

## 🔧 Customization

### Modifying Colors
Edit `tailwind.config.js` to customize the color scheme:

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0fdf4',
          // ... add your custom colors
        }
      }
    }
  }
}
```

### Adding New Pages
1. Create component in `src/pages/`
2. Add route in `src/App.jsx`
3. Update navigation in `src/components/layout/Sidebar.jsx`

### Mock Data
All demo data is centralized in `src/data/mockData.js` for easy modification.

## 🚀 Deployment

### Build Optimization
```bash
npm run build
npm run preview
```

### Deployment Platforms
- **Vercel**: `vercel --prod`
- **Netlify**: Drag and drop `dist/` folder
- **GitHub Pages**: Use `gh-pages` package

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- **Design Inspiration**: Spotify, Notion, Airbnb
- **Icons**: Lucide React icon library
- **Images**: Unsplash for demo imagery
- **Maps**: Mapbox for geospatial functionality
- **Charts**: Recharts for data visualization

## 📞 Support

For questions or support, please open an issue in the repository or contact the development team.

---

**Built with 💚 for a greener future** 🌍