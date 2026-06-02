# PHP Built-in Server Startup Guide

This guide covers:

1. Authentication & Cars Application
2. Securing Input Demo
3. API Endpoints Testing

---

# Part 1: Authentication & Cars Application

## Project Structure

```text
/app
├── database.php
├── auth/
│   ├── login.php
│   ├── logout.php
│   └── authorize.php
├── cars/
│   ├── index.php
│   ├── create.php
│   └── delete.php
└── api/
    ├── cars.php
    └── basic-cars.php
```

## Verify the Database

```bash
mysql -u php-user -pphp-pass car_db
```

```sql
SHOW TABLES;
SELECT name, email, role FROM users;
```

Expected tables:

```text
api_keys
cars
users
```

## Start the Server

```bash
cd /app
php -S 127.0.0.1:8080
```

## Test URLs

- http://127.0.0.1:8080/auth/login.php
- http://127.0.0.1:8080/dashboard.php
- http://127.0.0.1:8080/cars/index.php
- http://127.0.0.1:8080/cars/create.php
- http://127.0.0.1:8080/cars/delete.php

## Test Accounts

### Admin

```text
Email: alice@example.com
Password: password123
Role: admin
```

### Member

```text
Email: bob@example.com
Password: password123
Role: member
```

### Guest

```text
Email: carol@example.com
Password: password123
Role: guest
```

Logout:

```text
/auth/logout.php
```

---

# Part 2: Securing Input Demo

## Project Structure

```text
/app
├── src/
│   ├── components/
│   │   └── Template.php
│   └── templates/
│       ├── main.php
│       ├── sanitize-form.php
│       └── search.php
└── web/
    └── index.php
```

## Start the Server

```bash
cd /app
php -S 127.0.0.1:8081 -t web/
```

## Demo Pages

- http://127.0.0.1:8081/
- http://127.0.0.1:8081/sanitize
- http://127.0.0.1:8081/search

## Template.php Check

```php
return self::$viewsPath .
       DIRECTORY_SEPARATOR .
       $this->name .
       '.php';
```

---

# Part 3: API Endpoints Testing

## Start the Server

```bash
cd /app
php -S 127.0.0.1:8080
```

## Generate an API Key

```bash
php -r "
require 'database.php';
\$key = bin2hex(random_bytes(32));
\$pdo = Database::getInstance()->getPdo();
\$pdo->prepare(
    'INSERT INTO api_keys (user_id, api_key)
     VALUES (1, :k)'
)->execute([':k' => \$key]);
echo \$key . PHP_EOL;
"
```

## API Tests

### GET

```bash
curl -H "X-API-Key: YOUR_KEY_HERE" \
http://127.0.0.1:8080/api/cars.php
```

### POST

```bash
curl -X POST \
-H "X-API-Key: YOUR_KEY_HERE" \
-H "Content-Type: application/json" \
-d '{"make":"Porsche","model":"911","year":2023,"color":"Yellow","price":120000}' \
http://127.0.0.1:8080/api/cars.php
```

### DELETE

```bash
curl -X DELETE \
-H "X-API-Key: YOUR_KEY_HERE" \
"http://127.0.0.1:8080/api/cars.php?id=1"
```

### Missing Key Test

```bash
curl http://127.0.0.1:8080/api/cars.php
```

## HTTP Basic Authentication

### Valid Credentials

```bash
curl -u alice@example.com:password123 \
http://127.0.0.1:8080/api/basic-cars.php
```

### Invalid Credentials

```bash
curl -v \
-u wrong@email.com:wrongpass \
http://127.0.0.1:8080/api/basic-cars.php
```

---

## Notes

- Keep the terminal open while testing.
- Press `Ctrl+C` to stop the server.
- Use port **8080** for the Auth & Cars app.
- Use port **8081** for the Securing Input demo.
- Use HTTPS in production.
