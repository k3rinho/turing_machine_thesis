# Turing portal

## Requirements
- PHP 7.4 +
- MySQL
- Composer
- Node (npm)


## Stack
- Laravel 8
- Vue.js /w Tailwind.css mixed with Vuetify


## Local dev installation
- On Windows make sure you have XAMPP installed, with PHP 7.4+
- pull this repository
- `composer install`
- `npm install`
- copy *.env.example* to *.env* (so you'll have a *.env* file in your root)
- open the env file, and change the database credentials based on your MySQL config (e.g. DB_DATABASE, DB_USERNAME...)
- create an EMPTY database (e.g. with name 'laravel')
- run `php artisan migrate` (this will create all the tables we'll need in the DB)
- run `php artisan key:generate`
- run `php artisan jwt:secret`


## Running the project locally after installation is completed
### starting backend: 
- run `php artisan serve`

### building frontend
- **Option 1** - run `npm run dev`
- **Option 2 - RECOMMENDED**: run `npm run watch`  (this will listen for changes during development, no need to rebuild the frontend on each change)

## Now your app is available on: http://localhost:8000




