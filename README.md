# ISAMS — Integrated Student Affairs Management System
### Laravel 11 | PostgreSQL (Supabase) | Three-Portal System

## Quick Setup

```bash
# 1. Install dependencies
composer install

# 2. Setup environment
copy .env.example .env

# 3. Fill Supabase credentials in .env
# DB_HOST, DB_USERNAME, DB_PASSWORD

# 4. Generate app key
php artisan key:generate

# 5. Migrate & seed
php artisan migrate:fresh --seed

# 6. Visit http://isams.test
```

## Demo Login Accounts

| Role        | Email                       | Password |
|-------------|----------------------------|----------|
| Super Admin | superadmin@isams.edu.ph    | password |
| Admin       | admin@isams.edu.ph         | password |
| Student     | student@isams.edu.ph       | password |

## Portals

- **Super Admin** → `/superadmin/dashboard` — User management, role assignment, login logs, monitoring
- **Admin** → `/admin/dashboard` — Scholarships, AI filter, applications, counseling
- **Student** → `/student/dashboard` — Apply, AI eligibility check, counseling request

## Supabase Setup

Edit `.env`:
```
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.YOURPROJECTREF
DB_PASSWORD=YOURPASSWORD
DB_SSLMODE=require
SESSION_DRIVER=file
```
