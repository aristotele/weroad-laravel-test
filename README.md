## How to install

`composer install`

`php artisan migrate:fresh --seed`

`php artisan test`

The application is scaffolded with an ADMIN user:

    - email: test@mail.com
    - password: password

Laravel Sanctun has been used to issue tokens. For simplicity the first admin can create other users (wether ADMIN or EDITOR) and the response contains the Bearer token to use for subsequent authenticated requests.

## Additional resources
The code is delivered along with an exported Postman's collection API.
