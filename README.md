# README

This project is a Symfony application built using PHP 8.2 and utilizes Docker for environment isolation. Below are directions to set up, run, and maintain the project effectively.

---

## Setup and Installation

1. **Install Dependencies**  
   Run the following command to install PHP dependencies using Composer in a Dockerized environment:

   ```bash
   docker compose pull
   docker compose up --detach
   docker compose exec phpfpm composer install
   ```

2. **Environment Configuration**  
   Create a `.env.local` file from the base `.env` file:

   ```bash
   cp .env .env.local
   ```

   Update the configuration in `.env.local` as needed.

---

## Running Coding Standards

The project uses [PHP CS Fixer](https://cs.symfony.com/) for code formatting and [PHPStan](https://phpstan.org/) for static analysis to maintain clean and robust code. Below are the commands to ensure coding standards are followed.

### Apply Coding Standards

Fix code formatting issues:

```bash
docker run --interactive --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer run-script coding-standards-apply
```

### Check Coding Standards

Check formatting without making any changes:

```bash
docker run --interactive --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer run-script coding-standards-check
```

### Run Static Analysis

Run PHPStan to check for code issues:

```bash
docker run --interactive --rm --volume ${PWD}:/app itkdev/php8.3-fpm:latest composer run-script phpstan
```

---

## Folder Structure

The core structure of this Symfony project follows standard conventions:

- **`src/`**: Contains the application code.
- **`public/`**: Publicly accessible files, such as the entry point (`index.php`).
- **`translations/`**: Holds translation files for multilingual support.
- **`templates/`**: Contains Twig templates for front-end rendering.
- **`migrations/`**: Database migration files.
- **`tests/`**: Unit and functional tests.

---

## Extra

- Always clear the cache after making any environment or configuration changes:

  ```bash
  itkdev-docker-compose bin/console cache:clear
  ```

---
