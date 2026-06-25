# F&B Inventory Management System (stock-fnb)

A professional Food & Beverage (F&B) Inventory Management System built with **Laravel**, **Bootstrap (SB Admin 2)**, and **SQL Database**. Designed to streamline stock tracking, multi-outlet storage, purchase order operations, supplier networks, menu/recipe formulations, sales simulation auto-deductions, and advanced analytical business intelligence reports for cafes, restaurants, and bakeries.

---

## 🚀 Key Features

- **Role-Based Access Control (RBAC):**
    - **Admin:** Complete system control, user CRUD, outlet/unit rules, supplier & PO management, ingredient/recipe edits, stock opname, and reports.
    - **Staff:** Real-time stock adjustments, log wastes, create and receive POs, stock opname, process sales, and view reports.
    - **Owner:** View-only access to dashboard statistics, stock logs, recipes, advanced analytics, and exportable reports.
- **Low Stock Alert System:** Automatic detection and highlighted alerts on the dashboard for ingredients dropping below predefined minimum stock thresholds.
- **Multi-Outlet & Warehouse Management:** Distribute and track inventory levels across multiple geographical outlets or storage warehouses independently, with global cached sums.
- **Supplier & Purchase Order (PO) Engine:** Create detailed Purchase Orders mapped to supplier profiles, manage PO lifecycle status (Pending vs Received), and automatically update outlet inventories upon PO receipt.
- **Unit Conversions:** Configure conversion factor mappings (e.g., `kg` to `gram`, `l` to `ml`) to enable purchasing ingredients in bulk packages while tracking inventory stock and recipe calculations in base units.
- **Waste / Pembuangan Tracker:** Record damaged, spoiled, or expired ingredients with specific reasons (Expired, Spilled, Rotten) to decrease inventory levels and audit discrepancies.
- **Advanced Business Analytics:**
    - **Food Cost & Margin Calculator:** Analyze menu profitability, net profit margins, and food cost percentage ratios based on ingredient cost values (`cost_price`) and menu retail price (`selling_price`).
    - **Variance Report (Stock Opname):** Log actual physical stock counts compared with system theoretical figures, compute variance anomalies, and automatically reconcile database metrics.
    - **Dead Stock Detector:** Detect slow-moving ingredients that haven't registered usage/output logs within the past 30 days, showing estimated financial capital loss.
    - **Seasonal Consumption Pattern:** Aggregate monthly consumption records to identify seasonal purchasing and preparation trends.
- **Comprehensive Reports & Export:** Filter historical stock movement logs by ingredient, outlet, or date range, and export detailed monthly PDF reports in landscape A4 format.

---

## 🛠️ Tech Stack & Design

- **Framework:** Laravel v8.x (PHP ^7.3 | ^8.0)
- **Database:** SQLite / MySQL / PostgreSQL (Laravel Eloquent ORM)
- **Frontend Template:** SB Admin 2 (Bootstrap 4 & jQuery)
- **Design Tokens & Theme:** Custom Premium Theme Palette:
  - **Primary Navy:** `#293681` (Used for sidebar and navigation structure)
  - **Accent Blue:** `#4274D9` (Used for primary buttons, focus highlights, and primary charts)
  - **Soft Cyan:** `#95CCDD` (Used for visual details and table underlines)
  - **Mint Ice:** `#D0E7E6` (Used for backgrounds, hover rows, and card borders)
  - **Background:** `#ffffff`
- **Charts:** Chart.js
- **Typography:** Google Fonts (Inter)
- **PDF Engine:** Barryvdh Laravel DomPDF (v2.2)

---

## 📊 Database Schema

The system uses the following main database entities:

1. **`users`**: Manages auth and roles (`admin`, `staff`, `owner`).
2. **`ingredients`**: Stores stock metadata, units, global cached stock sums, minimum threshold limits, and `cost_price` values.
3. **`recipes`**: Holds menu items and `selling_price` values.
4. **`recipe_ingredient`**: Connects recipes to ingredients with precise quantity ratios (many-to-many pivot).
5. **`stock_movements`**: Auditable ledger tracking stock `in` / `out` changes and reasons mapped to outlets.
6. **`wastes`**: Logs damaged/discarded ingredient details.
7. **`suppliers`**: Stores supplier profile contact parameters.
8. **`purchase_orders`**: Handles purchase transactions and receiving parameters.
9. **`purchase_order_details`**: Lists items, prices, and conversion units ordered.
10. **`outlets`**: Distinguishes independent stores / storage warehouses.
11. **`outlet_ingredient`**: Tracks stock quantities per ingredient per outlet (many-to-many pivot).
12. **`units` & `unit_conversions`**: Mapped unit scaling values for automatic bulk conversions.
13. **`stock_takes`**: Records stock opname actuals, theoretical values, and variances.

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

Run the database migrations and seed default ingredients, recipes, outlets, unit conversions, and users:

```bash
php artisan migrate:fresh --seed
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
| **Admin** | `admin@fnb.com` | `password` | Full CRUD, User Control Panel, Manage Settings, Price Configuration |
| **Staff** | `staff@fnb.com` | `password` | Modify Stock, Log Wastes, Manage POs, Process Sales, View Reports  |
| **Owner** | `owner@fnb.com` | `password` | Read-only access to Dashboard, Analytics, Logs, and PDF Export    |

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
