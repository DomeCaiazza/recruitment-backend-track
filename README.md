# Project

## Docker setup
### Prerequisites:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Laravel 11 + php8.3](https://laravel.com/docs/11.x/installation#installing-php)

### Environment setup
To configure the docker environment, follow these instructions from the project folder:
- copy or rename '.env.example' in '.env' changing the information within it as you wish (we will generate LARAVEL_APP_KEY later)
- from terminal go to the 'php-app' directory `cd php-app`
- give the command `composer install`
- give the command `php artisan key:generate --show` 
- copy the output of the terminal (generated key) into the ‘.env’ file by updating the value of 'LARAVEL_APP_KEY' variable.
- exec command `docker-compose up`