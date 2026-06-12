# F&B Inventory Management System (stock-fnb)

A professional Food & Beverage (F&B) Inventory Management System built with **Laravel**, **Bootstrap (SB Admin 2)**, and **SQL Database**. Designed to streamline stock tracking, menu/recipe formulations, sales simulation auto-deductions, and monthly stock movement reports for cafes, restaurants, and bakeries.

---

## 🚀 Key Features

- **Role-Based Access Control (RBAC):**
    - **Admin:** Complete system control, user CRUD, ingredient and recipe edits, sales processing, and reports.
    - **Staff:** Real-time stock adjustments, sales processing, view recipes, and view reports.
    - **Owner:** View-only access to dashboard statistics, stock logs, recipes, and exportable reports.
- **Low Stock Alert System:** Automatic detection and highlighted alerts on the dashboard for ingredients dropping below predefined minimum stock thresholds.
- **Ingredient & Stock Management:** Complete tracking of ingredients with customizable measuring units (`ml`, `gram`, `pcs`, etc.) and log movements.
- **Dynamic Recipe Management:** Bind recipes to multiple ingredients with precise dosage per serving.
- **Automated Sales Simulation:** Process menu orders; the system validates ingredient availability, executes transactional database queries to decrement inventory, and logs stock movements instantly.
- **Comprehensive Reports & Export:** Search and filter historical stock movement logs by ingredient or date range, and export detailed monthly PDF reports in landscape A4 format.

---

## 🛠️ Tech Stack

- **Framework:** Laravel v8.x (PHP ^7.3 | ^8.0)
- **Database:** SQLite / MySQL / PostgreSQL (Laravel Eloquent ORM)
- **Frontend Template:** SB Admin 2 (Bootstrap 4 & jQuery)
- **Typography:** Google Fonts (Inter)
- **PDF Engine:** Barryvdh Laravel DomPDF (v2.2)

---

## 📊 Database Schema

The system uses 4 main database entities to control inventory:

1.  **`users`**: Manages auth and roles (`admin`, `staff`, `owner`).
2.  **`ingredients`**: Stores stock metadata, units, and `minimum_stock` limits.
3.  **`recipes`**: Holds menu items.
4.  **`recipe_ingredient`**: Connects recipes to ingredients with precise quantity ratios (many-to-many pivot).
5.  **`stock_movements`**: Auditable ledger tracking stock `in` / `out` changes and reasons.

---

## 💻 Installation & Setup

Follow these steps to set up the project locally:

### 1. Clone the Repository

```bash
git clone <repository-url>
cd fnb-inventory-caatis
```

### 2. Install Dependencies

Install PHP dependencies via Composer and frontend packages via npm:

```bash
composer install
npm install
```

### 3. Environment Configuration

Copy the sample environment file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
```

_Configure your database (SQLite, MySQL, etc.) inside the newly created `.env` file._

### 4. Database Migrations & Seeding

Run the database migrations and seed default ingredients, recipes, and users:

```bash
php artisan migrate --seed
```

### 5. Running the Application

Launch the Laravel development server:

```bash
php artisan serve
```

And compile assets:

```bash
npm run dev
```

---

## 🔑 Default Credentials (Seeded Accounts)

You can log in using the following test accounts (all passwords are `password`):

| Role      | Email           | Password   | Permissions                                                        |
| :-------- | :-------------- | :--------- | :----------------------------------------------------------------- |
| **Admin** | `admin@fnb.com` | `password` | Full CRUD, User Control Panel, Modify Ingredients/Recipes          |
| **Staff** | `staff@fnb.com` | `password` | Modify Stock, Process Sales, View Reports & Recipes                |
| **Owner** | `owner@fnb.com` | `password` | Read-only access to Dashboard stats, Logs, Recipes, and PDF Export |

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
