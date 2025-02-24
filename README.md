# README

Platform to create and update QR codes for URLs. All QR codes/URLs will point back to the system, which then
handles redirect to the relevant end URL. This enables updates to QR codes even after they are deployed in
the field.

This project is a Symfony application built using PHP 8.4 and utilizes Docker for environment isolation.
Below are directions to set up, run, and maintain the project effectively.

---

## Setup and Installation

1. **Install Dependencies**  
   Run the following command to install PHP dependencies using Composer in a Dockerized environment:

   ```bash
   task start
   ```

2. **Environment Configuration**  
   Create a `.env.local` file from the base `.env` file:

   ```bash
   cp .env .env.local
   ```

   Update the configuration in `.env.local` as needed.

---

## Building assets

## Building assets

The assets [app.css](/assets/styles/app.css) and [app.js](/assets/app.js) are served through webpack.

Build assets:

  ```bash
   task run-dev
   ```

Build and watch assets:

  ```bash
   task run-watch
   ```

## Running Coding Standards

The project uses [PHP CS Fixer](https://cs.symfony.com/) for code formatting and [PHPStan](https://phpstan.org/) for static
analysis to maintain clean and robust code. Below are the commands to ensure coding standards are followed.

### Apply Coding Standards

Fix code formatting issues:

```bash
task coding-standards-apply
```

### Check Coding Standards

Check formatting without making any changes:

```bash
task composer -- coding-standards-check
```

### Run Static Analysis

Run PHPStan to check for code issues:

```bash
task composer -- code-analysis
```

### Run code through all available tools

Markdownlint, code analysis, Code sniffer, Composer normalizer

```bash
task check-code
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
  task console -- cache:clear
  ```

---
