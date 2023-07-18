Steps To Run this Application:

1- Intall PHP, Apatech Server, MYSQL and Composer on your machine.
2- git pull this repo (https://github.com/nafilahmed/swtest.git)
3- Create Database and configure it on .env file.
4- Run "composer install".
5- Run "php artisan migrate".
6- Run "php artisan passport:install".
7- Run "php artisan db:seed".
8- Run "php artisan serve".
9- Import Postman Collection for API's
10- Following command for Testing:
	
	- For Authentication API's "./vendor/bin/phpunit tests/Feature/AuthTest.php"
	- For Movie API's "./vendor/bin/phpunit tests/Feature/MovieTest.php"


