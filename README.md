# 🏋️ GymGenius - Full Stack Fitness Tracking Platform

## 📌 Overview
**GymGenius** is a comprehensive fitness tracking application that helps users manage their workouts, track progress, and monitor nutrition goals. The platform consists of a modern React-based frontend built with Next.js and a robust Laravel backend with MySQL database.

---

## 🛠️ Tech Stack

### Frontend
- **Next.js 14+** (App Router)
- **TailwindCSS** (utility-first styling)
- **shadcn/ui** (accessible, modern UI components)
- **Axios** (API client)
- **Recharts** (data visualization)

### Backend
- **Laravel** (PHP framework)
- **MySQL** (relational database)
- **JWT Authentication** (secure token-based auth)
- **RESTful API** architecture

---

## 🔹 Core Features

### 1. Authentication & User Management
- User registration and login
- JWT-based authentication
- User profile management (weight, height, age, fitness goals)
- Role-based access control (admin/user)

### 2. Training & Workouts
- Pre-built training templates (Push/Pull/Legs, Upper/Lower, Full Body)
- Custom workout builder
- Exercise logging with sets, reps, and weight tracking
- Workout history and calendar view

### 3. Progress Tracking
- Weight progression graphs over time
- Personal record (PR) tracking for exercises
- Visual progress charts and statistics
- Workout completion calendar

### 4. Nutrition Tracking
- Daily calorie and macro goals
- Meal logging with nutritional breakdown
- Macro tracking (protein, carbs, fats)

### 5. Admin Panel
- User management dashboard
- System analytics and insights
- Content management tools


## 📂 Project Structure

### Frontend
```
gymgenius-frontend/
 ┣ 📂 app/                  # Next.js App Router pages
 ┃ ┣ 📂 api/               # API service functions
 ┃ ┣ 📂 dashboard/         # Dashboard pages
 ┃ ┣ 📂 admin/             # Admin panel
 ┃ ┗ 📂 settings/          # User settings
 ┣ 📂 components/          # React components
 ┃ ┣ 📂 ui/               # shadcn/ui components
 ┃ ┗ 📂 layout/           # Layout components (Sidebar, etc.)
 ┣ 📂 hooks/               # Custom React hooks
 ┣ 📂 lib/                 # Utilities and helpers
 ┣ 📂 styles/              # Global styles
 ┣ tailwind.config.js
 ┣ package.json
 ┗ tsconfig.json
```

### Backend
```
gymgenius-backend/
 ┣ 📂 app/
 ┃ ┣ 📂 Http/
 ┃ ┃ ┣ 📂 Controllers/     # API controllers
 ┃ ┃ ┗ 📂 Middleware/      # Auth & CORS middleware
 ┃ ┣ 📂 Models/            # Eloquent models
 ┃ ┗ 📂 Services/          # Business logic
 ┣ 📂 database/
 ┃ ┣ 📂 migrations/        # Database migrations
 ┃ ┗ 📂 seeders/           # Database seeders
 ┣ 📂 routes/
 ┃ ┣ api.php              # API routes
 ┃ ┗ web.php              # Web routes
 ┣ 📂 config/              # Configuration files
 ┗ .env                    # Environment variables
```

---

## 🚀 Getting Started

### Prerequisites
- Node.js 18+ and npm/yarn
- PHP 8.1+
- Composer
- MySQL 8.0+

### Frontend Setup
```bash
cd gymgenius-frontend
npm install
cp .env.example .env.local
npm run dev
```

### Backend Setup
```bash
cd gymgenius-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

---

## 🎨 UI/UX Guidelines
- **Component Library**: Use shadcn/ui for buttons, inputs, cards, modals, and forms
- **Styling**: TailwindCSS for layout and responsive design
- **Design Philosophy**: Minimal, modern, mobile-first approach
- **Color Scheme**: Dark theme with white accents for contrast
- **Accessibility**: WCAG 2.1 compliant components

---

## 🔄 Git Workflow

### Branching Model
- **master** → Production-ready stable code (protected)
- **dev** → Active development branch (protected)
- **feature/*** → Feature branches (branched from `dev`)


### Commit Message Convention
```
<type>(scope): short description

[optional body]
[optional footer]
```

**Types:**
- `feat` → New feature
- `fix` → Bug fix
- `docs` → Documentation only
- `style` → Code formatting (no logic change)
- `refactor` → Code restructuring
- `chore` → Maintenance, dependencies, CI/CD

**Examples:**
```
feat(workouts): add custom workout builder interface
fix(auth): resolve JWT token expiration handling
docs(readme): update installation instructions
refactor(sidebar): extract SidebarLink to separate component
```

---

## ✅ Next Steps

### MVP 1.0.0 Release (master deployment)
- [ ] Complete all core features (auth, workouts, progress, nutrition)
- [ ] Merge `dev` to `master`
- [ ] Deploy to production environment
- [ ] Monitor initial user feedback

### Post-MVP Enhancements
- [ ] Social features (friend system, workout sharing)
- [ ] Advanced analytics and AI-powered insights
- [ ] Integration with fitness wearables (Apple Watch, Fitbit)
- [ ] Video exercise demonstrations
- [ ] Personal trainer marketplace
- [ ] Workout plan recommendations using ML
- [ ] Real-time workout tracking with rest timers
- [ ] Community challenges and leaderboards
- [ ] Export workout data (PDF, CSV)

### Technical Improvements
- [ ] Optimize database queries and indexing
- [ ] Implement Redis caching for API responses
- [ ] Add API rate limiting and security hardening
- [ ] Improve SEO and performance metrics


## 👥 Team
- **All in One**: [Dominik Takáč]
---

## 📧 Contact
For questions or support, reach out at my LinkedIn! 
