### Deploy

Install dependencies:

```bash
composer install
```

Copy the .env file and set Azure API key

```bash
cp .env.example .env
```

Also setup the database in the .env, sqlite for easy setup

Make a filament user

```bash
php artisan make:filament-user
```
Launch the web server 

```bashbash
php artisan serve
```

Access the panel at http://localhost:8000/admin.
