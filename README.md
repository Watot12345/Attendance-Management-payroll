# Siglatrolap - Smart Attendance Management & Automated Payroll System

[![Laravel](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)
[![SQLite](https://img.shields.io/badge/Database-SQLite-blue.svg)](https://www.sqlite.org)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v4-38bdf8.svg)](https://tailwindcss.com)
[![Tests](https://img.shields.io/badge/Tests-16%20Passed-emerald.svg)]()

> A full rewrite and architectural modernization of **[Attendance-Management-payroll](https://github.com/Watot12345/Attendance-Management-payroll)**. Built with Laravel, normalized SQLite schema, contactless camera QR kiosk scanning, automated payroll engine with statutory deduction formulas, and role-based self-service portals.

---

## ✨ Key Features & Enhancements

### 1. 🗄️ Normalized Database Architecture
- **Legacy Issue**: The legacy system squeezed user profiles, time logs, and leave requests into a single chaotic `users` table.
- **Modern Solution**: Clean separation into dedicated Eloquent models and migrations:
  - `users`: Profile, roles (`admin`, `employee`), unique QR tokens, department, basic salary & rate derivations.
  - `attendances`: Daily time logs, clock-in, clock-out, late arrivals (minutes), overtime (minutes), total hours rendered, scan methods (`qr`, `web`, `manual`).
  - `leaves`: Vacation, sick, and emergency leave applications, date spans, HR review workflows, admin feedback remarks.
  - `payrolls`: Period compensation summaries, regular attendance pay, overtime blocks, late penalty deductions, statutory SSS, PhilHealth, withholding taxes, net salary.
  - `company_settings`: Configurable operating hours, overtime block rates, late penalty formulas, and company contact details.

### 2. 📷 Biometric QR Kiosk Terminal (`/scanner`)
- **Direct in-browser camera scanning** powered by `Html5Qrcode`.
- **Intelligent Auto-Detection**:
  - Unclocked staff scanning automatically records **Time In** (checks start time e.g. 08:00 AM, calculates tardy minutes).
  - Clocked-in staff scanning automatically records **Time Out** (calculates total hours rendered and overtime beyond 5:00 PM).
- **Web Audio API Feedback**: Instant synthesized audio chime for successful scans or alerts.
- **Live Ticker**: Real-time display of recent scans.
- **Fallback**: Supports USB barcode / 2D scanner input and manual token submission.

### 3. 💵 Automated Payroll Engine
- Compute company-wide or individual payroll for any pay period with a single click.
- **Formulas Implemented**:
  - Daily Rate: $\text{Basic Salary} / 22\text{ days}$
  - Hourly Rate: $\text{Daily Rate} / 8\text{ hours}$
  - Overtime Pay: $\lceil \text{Overtime Minutes} / 30 \rceil \times \text{Overtime Block Rate}$
  - Late Penalty: $\lfloor \text{Late Minutes} / 30 \rfloor \times \text{Late Penalty Rate}$
  - Statutory Deductions: SSS, PhilHealth/Medical, and progressive withholding tax.
- **Printable Payslip**: Formal, paper-ready payslip with employee voucher reference, compensation breakdown, and HR/employee signature blocks.

### 4. 👥 Employee Self-Service & Digital ID Pass
- Interactive dashboard with live digital clock and one-click Time-In / Time-Out buttons.
- In-browser personal QR Code Pass (`<canvas>` rendering) viewable on mobile or downloadable.
- Real-time leave request submission and tracking.
- Personal payslip archive.

### 5. 🛡️ Executive Admin Dashboard
- Live KPIs: Present staff today, on-time percentage, pending leave requests, total monthly disbursed payroll.
- One-click approve/reject leave applications with remarks.
- Manual attendance adjustments and audit logs.
- Employee directory with salary configurations and instant QR pass regeneration.

---

## 🚀 Quick Start Guide

### Prerequisites
- PHP 8.2+ with `pdo_sqlite`
- Composer
- Node.js 18+ & NPM

### Setup & Run

1. **Clone & Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

2. **Configure Environment & SQLite Database**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Migrate & Seed Demo Data**:
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Build Frontend Assets**:
   ```bash
   npm run build
   # Or for active development:
   # npm run dev
   ```

5. **Start Application Server**:
   ```bash
   php artisan serve
   ```
   Access the application at `http://127.0.0.1:8000`

---

## 🔑 Demo Accounts

| Role | Email / Login | Password | Notes |
|---|---|---|---|
| **Admin** | `admin@siglatrolap.com` | `password` | Full system executive control |
| **Legacy Admin** | `dmins@gmail.com` | `admin123` | Preserved from original database |
| **Employee** | `joshua@gmail.com` | `password` | Joshua Sierra (Senior Developer) |
| **Employee** | `joshuasierra725@gmail.com` | `password` | Joshua Arncel (Lead Designer) |
| **Employee** | `maria.santos@siglatrolap.com` | `password` | Maria Santos (Payroll Specialist) |

---

## 🧪 Testing Suite

Run the full automated test suite (16 feature tests covering Auth, Attendance, QR verification, Payroll calculation, and Leaves):

```bash
php artisan test
```

---

## 📁 Repository Structure

```
├── app/
│   ├── Http/Controllers/     # Auth, Attendance, Leave, Payroll, Employee, Settings
│   ├── Http/Middleware/      # EnsureAdmin role protection
│   └── Models/               # User, Attendance, Leave, Payroll, CompanySetting
├── database/
│   ├── database.sqlite       # Portable zero-config SQLite database
│   ├── migrations/           # Normalized database tables
│   └── seeders/              # Comprehensive test data seeder
├── legacy_v1/                # Original repository archive safely preserved
├── resources/
│   ├── css/                  # Tailwind CSS v4 styling
│   ├── js/                   # HTML5-QRCode & QRCode camera modules
│   └── views/                # Modern Blade templates (responsive & print-ready)
└── routes/web.php            # Structured application routes
```
