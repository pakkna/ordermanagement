<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Product & Order Management

A Laravel-based API project for managing products, orders, and user roles.

## Getting Started

Follow these instructions to set up the project on your local machine.

### Prerequisites

-   PHP 8.0 or higher
-   Composer
-   A database (MySQL)

### Installation

1. **Clone the Repository**

    ```bash
    git clone <repository-url>
    cd <project-directory>
    ```

2. **Install Dependencies**

    Use Composer to install all the necessary packages:

    ```bash
    composer install
    ```

3. **Clear Cache and Routes**

    To ensure no cached configurations affect the project, clear the cache and routes:

    ```bash
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    ```

4. **Environment Setup**

    Create a `.env` file by copying `.env.example`:

    ```bash
    cp .env.example .env
    ```

    Update the `.env` file with your database credentials.

5. **Generate Application Key**

    ```bash
    php artisan key:generate
    ```

6. **Run Migrations**

    Run the migrations to create the required database tables:

    ```bash
    php artisan migrate
    ```

7. **Seed the Database**

    Populate the database with initial data:

    ```bash
    php artisan db:seed
    ```

### API Documentation

A Postman collection has been provided to test the API endpoints. Download and import it into Postman to get started.

### Additional Information

Ensure that all dependencies are compatible with PHP 8.0 or higher.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# ordermanagement
