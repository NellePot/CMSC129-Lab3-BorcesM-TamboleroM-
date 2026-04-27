## 🚑 Emergency Inventory Management System

A web-based inventory management system built with **Laravel** and **PostgreSQL**, designed to track emergency supplies for critical operations. It supports full CRUD functionality, stock status monitoring, soft deletes, search and filtering, and expiration date tracking.

## 📸 Screenshots
> DASHBOARD
<img width="1920" height="1020" alt="Screenshot 2026-03-30 191914" src="https://github.com/user-attachments/assets/dad688a2-3bb2-4f7c-9fbc-676a0ca0d9b2" />

> ADD ITEM
<img width="1920" height="1020" alt="Screenshot 2026-03-30 191925" src="https://github.com/user-attachments/assets/4c7c1b40-ad33-47d8-b9c2-4f2562c9ee09" />

> EDIT ITEM
<img width="1920" height="1020" alt="Screenshot 2026-03-30 191937" src="https://github.com/user-attachments/assets/eb1dae0f-c932-433e-a10e-f01d9a588b51" />

> DELETE ITEM
<img width="1920" height="1020" alt="Screenshot 2026-03-30 191949" src="https://github.com/user-attachments/assets/de00e21c-424a-4b3e-ad60-2cbe067b48e5" />

> SEARCH & FILTERING
<img width="1920" height="1020" alt="Screenshot 2026-03-30 192016" src="https://github.com/user-attachments/assets/5e273667-2dd2-4e78-9896-60449984358a" />

> CRITICAL STATUS
<img width="1920" height="1020" alt="Screenshot 2026-03-30 192003" src="https://github.com/user-attachments/assets/d380d511-3764-49a5-81ba-84d4d558276b" />

## ✅ Features Implemented
- **Inventory CRUD**: Add items, read, edit item details, and delete items.
- **Soft Delete**: Deleted items are moved to the trash and can be restored or permanently deleted.
- **Expiration Date Tracking**: Gives a warning when items are near expiry or already expired.
- **Critical Items Modal**: Clickable stat card that shows a modal of all critical (low/out of stock) items
- **Search & Filter**: Search by name, and filter by status and category.

## ⚙️ Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install && npm run build
```

### 4. Copy Environment File

```bash
cp .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

---

## 🐘 Database Setup (PostgreSQL)

### 1. Create the Database

Open your PostgreSQL shell (psql) or use pgAdmin:

```sql
CREATE DATABASE your_pg_database;
```

### 2. Configure `.env`

Update your `.env` file with your PostgreSQL credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_pg_database
DB_USERNAME=your_pg_username
DB_PASSWORD=your_pg_password
```

### 3. Run Migrations

```bash
php artisan migrate
```

---

## ▶️ Running the Application

```bash
php artisan serve
```

Then visit: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## 🗂️ MVC Architecture
This follows Laravel's Model-View-Controller (MVC) architectural pattern, ensuring clean separation of concerns and maintainable code.

### 📋 MVC Overview

**MVC** separates an application into three interconnected components:

- **Model**: Handles data logic and database interactions
- **View**: Manages the presentation layer (UI/templates)
- **Controller**: Processes user requests and coordinates between Model and View

### 🗂️ Project Structure

```
lab2/
├── app/                          # Application core code
│   ├── Http/Controllers/         # Controllers (C in MVC)
│   │   ├── Controller.php        # Base controller class
│   │   └── InventoryController.php # Main inventory controller
│   ├── Models/                   # Models (M in MVC)
│   │   ├── Inventory.php         # Inventory data model
│   │   └── User.php              # User authentication model
│   └── Providers/                # Service providers
│       └── AppServiceProvider.php
├── resources/views/              # Views (V in MVC)
│   ├── inventory/                # Inventory-related views
│   │   ├── index.blade.php       # Main inventory listing
│   │   ├── create.blade.php      # Add new item form
│   │   ├── edit.blade.php        # Edit item form
│   │   ├── show.blade.php        # View single item details
│   │   └── trashed.blade.php     # Soft-deleted items view
│   ├── layouts/                  # Layout templates
│   │   └── app.blade.php         # Main application layout
│   └── welcome.blade.php         # Landing page
├── routes/                       # Route definitions
│   ├── web.php                   # Web routes (browser access)
│   └── console.php               # Console routes (CLI)
├── database/                     # Database-related files
│   ├── migrations/               # Database schema migrations
│   │   ├── 0001_01_01_000000_create_inventories_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   └── 0001_01_01_000002_create_jobs_table.php
│   ├── factories/                # Model factories for testing
│   │   └── UserFactory.php
│   └── seeders/                  # Database seeders
│       └── DatabaseSeeder.php
├── config/                       # Configuration files
├── public/                       # Public assets (CSS, JS, images)
├── storage/                      # File storage, logs, cache
├── tests/                        # Test files
└── vendor/                       # Composer dependencies
```
## AI Features and Description 
**AI Chatbot (Inquiry Mode)** - Answers questions about the inventory. Supports stock summaries, low stock alerts, expiring/expired item lookups, item quantity checks, and category breakdowns. 

**AI Assistant (CRUD Mode)** - Performs inventory operations. Supports adding, updating, and deleting inventory items via chat.  

## AI Model Used 

AI: Google Gemini API 
Model: gemini-2.5-flash 

## Setup Instructions 
**1. Get your Gemini API Key**
1. Go to Google AI Studio
2. Sign in with your Google account
3. Navigate to "Get API Key"
4. Click Create API Key
5. Copy the generated key

**2. Store API Key in .env**
Add your API key to your .env file: 
```
GEMINI_API_KEY= your_api_key_here
```
**3. Install Gemini PHP SDK**
If not installed yet: 
```
composer require google-gemini-php/laravel
```
**4. Clear Config Cache**
After setting .env: 
```
php artisan config:clear
php artisan cache:clear
```

## Environment Variables Needed 
```
APP_NAME=
APP_ENV=
APP_KEY=
APP_DEBUG=true
APP_URL=

DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

GEMINI_API_KEY=
```
## Example queries users can try 
**AI Chatbot (Inquiry Mode)** 
> "Stock Summary"

> "What item is about to expire?"

> "What item is out of stock?"

> "How many items are in Medicine?"

> "What is the quantity of Paracetamol?"

**AI Assistant (CRUD Mode)**
> "update cetirizine quantity to 10"

> "delete paracetamol"

> "add bandages with quantity 20"

> "update paracetamol expiration to May 10,2026"

> "update neozep category to medicine"

## Screenshots of Chatbot Interactions





