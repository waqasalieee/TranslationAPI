# Translation Management Service - Laravel `11.x`

API-driven service with the following features: 
Store translations, Tag translations, endpoints to create, update, view, and search translations by tags, keys, or content. 
A JSON export endpoint to supply translations for frontend applications like Vue.js. 
A factory to populate the database with 100K records.

## Requirements:
- Laravel `11.x`
- PHPUnit test package `^11.x`

## Project Setup
Git clone -
```console
git clone https://github.com/waqasalieee/TranslationAPI.git
```

Go to project folder -
```console
cd TranslationAPI
```

Install Laravel Dependencies -
```console
composer install
```


Create database called - `translations`   <--- If using MySql
<br /> OR use DB_CONNECTION=sqlite (It will auto create the database)


Create `.env` file by copying `.env.example` file in project root and do following changes
```
#If you want to use MySql for database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root

#Database name: 
DB_DATABASE=translations
```


Generate Artisan Key (If needed) -
```console
php artisan key:generate
```

Migrate Database with seeder -
```console
php artisan migrate:fresh --seed
```

Make sure 
Run Project -
```php
php artisan serve
```

So, You've got the project of Laravel Role & Permission Management on your http://localhost:8000

## How it works
Use Thunder Client extension in VS code or install PostMan to run API's.<br/>
First use the login API to login and get the TOKEN.<br />
Copy token from Login Api response and send it in Headers [Authorization] in all API calls.<br/>
Like this: 
```console
Authorization : Bearer TOKEN
```
E.g: 
```
Authorization : Bearer 1|gt5Ledi600LEZbXVkXEjDGuGTtEzCYqjgvd410fpa8a0ae01
```

## It has following endpoints
1. Login
```php
POST - http://localhost:8000/api/login
Parameters
{
    "email": "waqas@domain.com",
    "password": "password"
}
```
2. Get Translations
```php
GET http://localhost:8000/api/translations
Optional Parameters
{
    "key": "STRING",
    "value": "STRING",
    "tags": "STRING,STRING",  //Comma separated tags
}
```
3. Create Translation
```php
POST http://localhost:8000/api/translations
Optional Parameters
{
    "locale_id": 1,
    "key": "greeting 2",
    "value": "Hello 2",
    "tags": ["mobile", "web"]
}
```
4. Update Translation
```php
PUT http://localhost:8000/api/translations/{Translation id}
Optional Parameters
{
    "key": "welcome",
    "value": "Welcome to our platform",
    "tags": ["web"]
}
```
5. Export Translations
```php
GET http://localhost:8000/api/translations/export/{Language code}
```
6. Logout
```php
POST - http://localhost:8000/api/logout
```


## Wanna talk with me
Please mail me at - waqasalieee@gmail.com
<br/>Call/Whatsapp at [ +92-321-4105651 ]

## Support
If you like my work you may consider buying me a ☕ / 🍕

## Contribution
Contribution is open. Create Pull-request and I'll add it to the project if it's good enough.

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
