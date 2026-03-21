# CineHall - Cinema Management System

CineHall is a robust, full-featured Cinema Management System built with **Laravel 12**. It provides a seamless experience for both administrators and movie-goers, handling everything from film scheduling and room management to secure seat reservations and automated ticket generation.

## 🚀 Key Features

- **🔐 Advanced Authentication**: Secure JWT-based authentication for API security, along with Laravel Socialite for social logins (Google, etc.).
- **🎬 Film & Genre Management**: Comprehensive management of movie listings, trailers, posters, and categorized genres.
- **📅 Session Scheduling**: Efficient room management and dynamic movie session scheduling.
- **🎟️ Reservation System**: Interactive seat selection and reservation management with real-time status tracking.
- **💳 Integrated Payments**: Secure payment processing through **Stripe** (via Laravel Cashier) and **PayPal**.
- **📄 Ticket Generation**: Automated PDF ticket generation with unique **QR Codes** for quick entry validation.
- **📊 Admin Dashboard**: High-level statistical insights into reservations, users, and revenue.
- **📖 API Documentation**: Built-in interactive API explorer powered by **Swagger** (L5-Swagger).

## 🛠️ Tech Stack

- **Framework**: [Laravel 12.x](https://laravel.com/)
- **Language**: PHP 8.2+
- **Authentication**: JWT Auth (`tymon/jwt-auth`), Laravel Sanctum, Laravel Socialite.
- **Payments**: Laravel Cashier (Stripe), `srmklive/paypal`.
- **Infrastructure**: MySQL, Vite (for asset bundling).
- **Extra Tools**: 
  - `barryvdh/laravel-dompdf` (PDF Generation)
  - `simplesoftwareio/simple-qrcode` (QR Code Generation)
  - `darkaonline/l5-swagger` (API Docs)

## ⚙️ Installation

To set up the project locally, follow these steps:

1. **Clone the repository**:
   ```bash
   git clone https://github.com/bahaztariq/CineHall.git
   cd CineHall
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: Configure your database, Stripe, and PayPal credentials in the `.env` file.*

4. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   ```

5. **Generate JWT Secret**:
   ```bash
   php artisan jwt:secret
   ```

6. **Serve the Application**:
   ```bash
   php artisan serve
   ```

7. **Build Assets**:
   ```bash
   npm run dev
   ```

## 📖 API Documentation

The API documentation is available via Swagger. Once the server is running, you can access it at:
`http://localhost:8000/api/documentation`

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
