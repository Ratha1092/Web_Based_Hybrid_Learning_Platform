# Web-Based Hybrid Learning Platform

A modern hybrid learning system combining a Laravel backend with a React frontend. The platform supports course management, instructor verification, user roles, admin workflows, and secure file uploads.

## 🚀 Project Overview

This project is split into two main parts:

- **`backend/`** — Laravel 12 application handling:
  - authentication and authorization
  - instructor verification flow
  - course creation and management
  - admin panel and review workflows
  - file upload/storage and activity logging
  - API endpoints and backend business logic

- **`frontend_user/`** — React + Vite application handling:
  - user-facing pages and navigation
  - registration/login flows
  - course discovery and dashboards
  - instructor onboarding UI
  - SPA interactions with backend APIs

## 🧩 Architecture

The architecture is built around a clear separation of concerns:

1. **Presentation layer**
   - `frontend_user/` provides the user-facing Single Page Application (SPA)
   - `backend/` contains Laravel views and Filament admin UI for internal workflows

2. **Application/API layer**
   - Laravel controllers in `backend/app/Http/Controllers` handle request validation, authentication, file uploads, and business logic
   - `backend/routes/web.php` and API routes wire user actions to controllers

3. **Domain / data layer**
   - Eloquent models in `backend/app/Domains` represent users, instructor verifications, courses, payments, and other core entities
   - Migrations define the database schema in `backend/database/migrations`

4. **Admin & workflow layer**
   - Filament resources in `backend/app/Filament/Resources` expose admin review, approval, and status management interfaces
   - Laravel Sanctum secures frontend API access and session management
   - Spatie packages provide role/permission and activity logging support

## ⚙️ Project Flow

### User registration and instructor verification

1. A user registers via the frontend or Laravel auth flow
2. If the user selects `Instructor`, additional verification fields appear
3. The user submits supporting evidence and uploads documents
4. Laravel stores the data in `instructor_verifications` and updates `users.instructor_status`
5. Admins review the application from the Filament dashboard
6. Admin approves or rejects the application
7. The instructor sees updated status and permissions on their dashboard

### Admin review flow

1. Admin logs in to the Filament admin panel
2. Admin opens the `Instructor Verifications` resource
3. Pending applications are displayed with files and details
4. Admin approves or rejects the verification
5. User status updates automatically and user is notified

### API and frontend flow

1. Frontend user actions send requests to Laravel API endpoints
2. Laravel validates and processes requests
3. Data is saved to the database via Eloquent models
4. Responses are returned to the SPA for UI updates

## 📁 Folder Structure

- `backend/`
  - `app/` — core Laravel application code
  - `config/` — Laravel configuration
  - `database/` — migrations, seeders, factories
  - `routes/` — web and API routes
  - `resources/` — Blade views, frontend assets for Laravel views
  - `storage/` — file uploads and generated files
  - `tests/` — backend tests

- `frontend_user/`
  - `src/` — React application source
  - `routes/` — route definitions for the SPA
  - `public/` — static assets
  - `package.json` — frontend dependencies and scripts
  - `vite.config.ts` — build and dev server config

## 🛠️ Technology Stack

- **Backend**
  - Laravel 12
  - PHP 8.2+
  - Laravel Sanctum
  - Filament Admin
  - Laravel Horizon
  - Spatie packages: Permission, Activity Log, Media Library
  - Laravel Breeze
  - Laravel Socialite (OAuth support)

- **Frontend**
  - React 19
  - React Router Dom
  - Vite
  - Tailwind CSS
  - TypeScript (frontend)

- **Database and Storage**
  - PostgreSQL or SQLite
  - Eloquent ORM
  - Laravel file storage

## 📌 Key Features

- multi-role authentication (students, instructors, admins)
- instructor verification workflow with document uploads
- admin review and approval dashboard
- secure backend validation and middleware protection
- responsive frontend experience with SPA navigation
- activity logging and user audit trails
- optional OAuth login providers

## 🚀 Getting Started

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
```

### Frontend

```bash
cd frontend_user
npm install
npm run dev
```

## 📚 Additional Documentation

- `backend/README.md` — backend-specific setup and usage
- `backend/IMPLEMENTATION_SUMMARY.md` — instructor verification implementation overview
- `backend/QUICK_START.md` — quick startup instructions
- `backend/UI_REFERENCE.md` — UI and workflow diagrams
- `backend/INSTRUCTOR_VERIFICATION_GUIDE.md` — detailed verification guide

## ✅ Notes

This repository is designed as a hybrid platform: the Laravel backend controls business logic, data storage, and admin workflows, while the React frontend delivers a polished user experience for students and instructors.
