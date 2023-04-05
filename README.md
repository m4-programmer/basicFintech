
## About Project

This is a basic test project using laravel to handle the following:
- authentication of users,
- account topup,
- mailing,
- referral system,
- mail subscription, 
- sending of funds
- recieving of funds
- among others 


## How to Use

1. Clone the repository
2. Run Composer install, to install the require dependencies by the application
3. Create the .env file by using the console command "cp .env.example. .env"
4. create a database of your choice, preferable name it: "purscliq_test"
5. Fillup the .env file database requirements with your local  settings.
6. Generate Key using "php artisan key:generate"
7. Migrate the database with  "php artisan migrate"
8. Serve the project by running "Php artisan serve"


## User Instruction

Please when testing the application, do well to use real emails, to advoid getting the website email account from being suspended.

### Plan for future

- **To add beautiful Ui's , probably 😆😆**




## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
