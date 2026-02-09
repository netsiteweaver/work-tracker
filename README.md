# work-tracker

A full-stack work/project tracker application built with Laravel 12 (API mode) and Vue 3 (Vite).

## Features

- **User Authentication**: Laravel Breeze API + Sanctum for secure email/password login
- **Project Dashboard**: Kanban-style board with 4 columns (New, In Progress, Completed, Stopped)
- **Drag & Drop**: Move projects between boards with automatic status updates
- **Project Management**: Full CRUD operations for projects in the back office
- **Responsive Design**: Mobile-friendly UI built with Tailwind CSS
- **Demo Data**: Pre-seeded with sample projects

## Tech Stack

### Backend
- Laravel 12 (API mode)
- Laravel Sanctum (API authentication)
- Laravel Breeze (API scaffolding)
- SQLite database

### Frontend
- Vue 3
- Vite
- Vue Router
- Tailwind CSS
- Axios
- Vue Draggable (SortableJS)

## Setup Instructions

### Backend Setup

1. Navigate to the backend directory:
   ```bash
   cd backend
   ```

2. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

3. Generate application key (if not already done):
   ```bash
   php artisan key:generate
   ```

4. Run migrations:
   ```bash
   php artisan migrate
   ```

5. Seed the database with demo data:
   ```bash
   php artisan db:seed
   ```

6. Start the development server:
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

### Frontend Setup

1. Navigate to the frontend directory:
   ```bash
   cd frontend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

4. Update `.env` if your backend URL is different:
   ```
   VITE_API_URL=http://localhost:8000/api
   ```

5. Start the development server:
   ```bash
   npm run dev
   ```

The frontend will be available at `http://localhost:5173`

## Default Credentials

The seeded database includes a test user:
- Email: `test@example.com`
- Password: `password`

## Usage

1. **Dashboard**: Visit the home page to see the project boards. Projects can be viewed without authentication.
2. **Login**: Click the "Login" button in the top bar to authenticate.
3. **Admin Panel**: After logging in, click "Admin" to access the project management interface where you can create, edit, and delete projects.
4. **Drag & Drop**: On the dashboard, drag projects between boards to change their status automatically.

## Project Structure

```
work-tracker/
├── backend/          # Laravel API backend
│   ├── app/
│   │   ├── Http/Controllers/Api/  # API controllers
│   │   └── Models/                 # Eloquent models
│   ├── database/
│   │   ├── migrations/             # Database migrations
│   │   └── seeders/                # Database seeders
│   └── routes/
│       ├── api.php                 # API routes
│       └── auth.php                # Auth routes
│
└── frontend/         # Vue 3 frontend
    ├── src/
    │   ├── components/             # Vue components
    │   ├── views/                  # Page views
    │   ├── composables/            # Vue composables
    │   ├── services/               # API services
    │   └── router/                 # Vue Router config
    └── public/
```

## API Endpoints

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/user` - Get authenticated user

### Projects (Public)
- `GET /api/projects` - List all projects
- `GET /api/projects/{id}` - Get single project

### Projects (Authenticated)
- `POST /api/projects` - Create project
- `PUT /api/projects/{id}` - Update project
- `DELETE /api/projects/{id}` - Delete project
- `POST /api/projects/update-order` - Update project order and status (for drag & drop)

## License

This project is open-sourced software.

