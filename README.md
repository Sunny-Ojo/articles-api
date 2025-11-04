# 🚀 Laravel Articles API

A modern **Laravel 12** project demonstrating integration of **Elasticsearch**, **Laravel Horizon**, and **Laravel Scout**, enhanced with **Pipelines** and containerized using **Laravel Sail** for a clean, reproducible dev environment.

---

## ⚙️ Quick Start (One-Click Setup)

### 🧩 1. Setup & Run Everything

```bash
# Clone the repository
git clone git@github.com:Sunny-Ojo/articles-api.git
cd laravel-articles-api

# Copy environment variables
cp .env.example .env


# Start Laravel Sail (Docker)
./vendor/bin/sail up -d

# Run database migrations & seeders
./vendor/bin/sail artisan migrate --seed

# Start Laravel Horizon
./vendor/bin/sail artisan horizon

# Run all tests
./vendor/bin/sail artisan test
```
