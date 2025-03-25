Business Approval Workflow Application
Project Overview
A Laravel-based order management system with approval workflow, designed to handle business order processing with complex validation and tracking.
Technical Stack

Laravel 10.x
PHP 8.2+
MySQL
Laravel Sanctum (for API authentication)

Setup Instructions
Prerequisites

PHP 8.2+
Composer
MySQL
Git

Installation Steps

Clone the repository

git clone https://github.com/AbdoHany98/abdelrahman-abdelwahab-onPrem.git
cd business-approval-workflow

Install dependencies

composer install

Copy environment file

cp .env.example .env

Generate application key

php artisan key:generate

Configure database in .env file
Run migrations

php artisan migrate

Start development server

php artisan serve
Testing
Run test suite:
php artisan test
# Or for specific test
php artisan test --filter OrderServiceTest
Postman Collection
Import business-workflow-api.postman_collection.json for API endpoint documentation.