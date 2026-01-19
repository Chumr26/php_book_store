# Deployment Plan: Render + TiDB

This document outlines the steps to deploy the Bookstore PHP application to **Render** (Web Service) using **TiDB Cloud** (Database).

## Phase 1: Database Migration (TiDB Cloud)
**Objective:** Move your local `bookstore` database to the cloud.

1.  **Create TiDB Cluster:**
    *   Sign up/Login to [TiDB Cloud](https://tidbcloud.com/).
    *   Create a "Serverless" cluster (Free tier is sufficient for testing).
2.  **Export Local Database:**
    *   Use phpMyAdmin or `mysqldump` to export your local database to a `.sql` file.
    *   Target file: `db/bookstore.sql` (already exists, but ensure it has the latest data).
3.  **Import to TiDB:**
    *   Use the TiDB Cloud "Import" feature or a MySQL client (like MySQL Workbench, DBeaver, or CLI) to connect to TiDB and import the `.sql` file.
    *   **Important:** Get the Connection Details from TiDB Console:
        *   **Host** (e.g., `gateway01.us-west-2.prod.aws.tidbcloud.com`)
        *   **Port** (e.g., `4000`)
        *   **User** (e.g., `2.root`)
        *   **Password**
        *   **Database Name** (e.g., `bookstore`)
    *   *Note:* TiDB requires a secure connection (SSL/TLS). Our PHP connection code usually handles this automatically if the environment supports it, but we might need to enforce it.

## Phase 2: Application Containerization (Docker)
**Objective:** Package the application to run consistently on Render.
*Why Docker?* This project uses `.htaccess` for routing. Render's native PHP environment uses Nginx, which does not support `.htaccess`. Using a Docker container with **Apache** ensures your routing works exactly like it does on XAMPP.

1.  **Create `Dockerfile`:**
    *   Base image: `php:8.2-apache`
    *   Install extensions: `mysqli`, `pdo_mysql`, `gd`, `zip`.
    *   Enable Apache modules: `mod_rewrite` (crucial for `.htaccess`).
    *   Copy application code to `/var/www/html`.
    *   Set permissions.
2.  **Create `.dockerignore`:**
    *   Exclude `.git`, `tmp`, `vendor` (install fresh), `scripts`, `docs`.

## Phase 3: Project Configuration
**Objective:** Prepare the codebase for the cloud.

1.  **Environment Variables:**
    *   We have already updated `Model/connect.php` and `Admin/Model/connect.php` to use `getenv('DB_HOST')`, etc.
2.  **Dependency Management:**
    *   Ensure `composer.json` is valid.
    *   The Docker build process will run `composer install`.

## Phase 4: Deployment on Render
**Objective:** Launch the service.

1.  **Push to GitHub:** Ensure all changes (including Dockerfile) are committed and pushed.
2.  **Create Web Service on Render:**
    *   Source: GitHub Repository.
    *   Runtime: **Docker**.
3.  **Configure Environment Variables (on Render Dashboard):**
    *   `DB_HOST`: (Your TiDB Host)
    *   `DB_PORT`: `4000`
    *   `DB_USER`: (Your TiDB User)
    *   `DB_PASSWORD`: (Your TiDB Password)
    *   `DB_NAME`: `bookstore`
    *   `PAYOS_CLIENT_ID`, `PAYOS_API_KEY`, etc. (if needed).

## Phase 5: Verification
1.  **Check Logs:** Ensure Apache starts and connects to TiDB.
2.  **Test Functionality:**
    *   Visit the URL.
    *   Test Login/Register (Database writes).
    *   Test Admin Panel.
