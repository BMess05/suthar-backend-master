## About Project

Web application to handle admin level activities. Follow following steps for project setup:

- git clone https://github.com/softradix/suthar-backend.git
- cd suthar-backend
- cp .env.example .env
- composer update
- php artisan key:generate
- php artisan vendor:publish --tag=laravel-pagination
- Fill the DB info in .env file
- Fill in the Mail configurations in .env file
- php artisan migrate
- php artisan db:seed --class=AdminSeeder
- After that you the dashboard can be logged in by adding credentiald added in AdminSeeder

This software is accessible, powerful, and provides tools required for large, robust applications.
