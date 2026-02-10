# work-tracker

A full-stack work/project tracker application built with Laravel 12 and Blade templates.

## Features

- **User Authentication**: Laravel Breeze with session-based authentication
- **Project Dashboard**: Kanban-style board with 5 columns (New, In Progress, On Hold, Completed, Stopped)
- **Drag & Drop**: Move projects between boards with automatic status updates using SortableJS
- **Project Management**: Full CRUD operations for projects in the admin panel
- **Responsive Design**: Mobile-friendly UI built with Tailwind CSS
- **Demo Data**: Pre-seeded with sample projects

## Tech Stack

### Backend & Frontend
- Laravel 12 (Blade templates)
- Laravel Breeze (Authentication scaffolding)
- Tailwind CSS (Styling)
- SortableJS (Drag & drop functionality)
- SQLite database (can be changed to MySQL/PostgreSQL)

## Setup Instructions

### Development Setup

1. Navigate to the backend directory:
   ```bash
   cd backend
   ```

2. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

3. Generate application key:
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

6. Install frontend dependencies and build assets:
   ```bash
   npm install
   npm run build
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

The application will be available at `http://localhost:8000`

For development with hot-reloading:
```bash
npm run dev  # In one terminal
php artisan serve  # In another terminal
```

## Default Credentials

The seeded database includes a test user:
- Email: `test@example.com`
- Password: `password`

## Usage

1. **Dashboard**: Visit the home page (`/`) to see the project boards. Login is required to view and manage projects.
2. **Login**: Click the "Log in" link in the navigation to authenticate.
3. **Admin Panel**: After logging in, click "Admin" in the navigation to access the project management interface where you can create, edit, and delete projects.
4. **Drag & Drop**: On the dashboard, drag projects between boards to change their status automatically.

## Project Structure

```
work-tracker/
├── backend/          # Laravel application
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   └── ProjectController.php  # Web controller for Blade views
│   │   └── Models/           # Eloquent models
│   ├── database/
│   │   ├── migrations/       # Database migrations
│   │   └── seeders/         # Database seeders
│   ├── resources/
│   │   ├── views/          # Blade templates
│   │   │   ├── dashboard.blade.php
│   │   │   ├── admin.blade.php
│   │   │   ├── projects/   # Project CRUD views
│   │   │   └── components/ # Reusable components
│   │   ├── css/            # Tailwind CSS
│   │   └── js/             # JavaScript assets
│   └── routes/
│       ├── web.php         # Web routes
│       └── api.php        # API routes (available for future API needs)
```

## Routes

### Web Routes
- `GET /` - Dashboard (Kanban board view)
- `GET /admin` - Admin panel (requires authentication)
- `GET /projects/create` - Create new project (requires authentication)
- `POST /projects` - Store new project (requires authentication)
- `GET /projects/{project}/edit` - Edit project (requires authentication)
- `PUT /projects/{project}` - Update project (requires authentication)
- `DELETE /projects/{project}` - Delete project (requires authentication)
- `POST /projects/update-order` - Update project order/status via drag & drop (requires authentication)

### Authentication Routes (Laravel Breeze)
- `GET /login` - Login page
- `POST /login` - Process login
- `GET /register` - Registration page
- `POST /register` - Process registration
- `POST /logout` - Logout

## Deployment

See the deployment guides:
- [Shared Hosting Deployment](deploy-shared-hosting.md)
- [VPS Deployment](deploy-vps.md)

## Benefits of Blade Templates

- ✅ **Simpler Deployment** - Single codebase, no separate frontend build needed
- ✅ **No CORS Issues** - Everything served from the same origin
- ✅ **Better SEO** - Server-rendered HTML
- ✅ **Works on Any Hosting** - Standard Laravel deployment
- ✅ **Easier Maintenance** - One deployment process

## License

This project is open-sourced software.
