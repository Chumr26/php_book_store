# Deployment Guide: Render + Aiven

This guide outlines the steps to deploy the BookStore application using **Render** (for web hosting) and **Aiven for MySQL** (for the database).

## Prerequisites
- A [GitHub](https://github.com/) account (Application code must be in a repository).
- A [Render](https://render.com/) account.
- A [Aiven](https://aiven.io/) account.

---

## Step 1: Prepare Database (Aiven for MySQL)

1.  **Create a Cluster**:
    - Log in to Aiven.
    - Create a new **MySQL** service (Free tier or trial if available).
    - Give it a name (e.g., `bookstore-db`).

2.  **Get Connection Info**:
    - Once the service is created, click **"Quick Connect"** or **"Service Overview"**.
    - Note down the values:
        - **Host**: (e.g., `mysql-xxxx.aivencloud.com`)
        - **Port**: `3306`
        - **User**: (e.g., `avnadmin`)
        - **Password**: (The password you set/generated)

3.  **Import Database Schema**:
        - You need to run the content of `db/bookstore.sql` on your Aiven service.
        - **Option A (MySQL Client)**:
                - Connect from your local terminal:
                    ```bash
                    mysql -u [User] -h [Host] -P 3306 -p --ssl-ca=/path/to/ca.pem
                    ```
                    *(Note: Aiven requires SSL. Use the CA certificate provided by Aiven.)*
                - Source the file: `source db/bookstore.sql`
        
                **For Windows (PowerShell with XAMPP/MariaDB):**
                ```powershell
                cmd /c "mysql -u [User] -h [Host] -P 3306 -p --ssl-ca=PATH\to\ca.pem < db\bookstore.sql"
                ```

---

## Step 2: Deploy to Render

1.  **Create New Web Service**:
    - Log in to Render Dashboard.
    - Click **New +** -> **Web Service**.
    - Connect your GitHub repository.

2.  **Configure Service**:
    - **Name**: `bookstore-app` (or similar)
    - **Region**: Choose one close to you (e.g., Singapore/Japan).
    - **Branch**: `main` (or your working branch).
    - **Runtime**: **Docker** (Render should auto-detect the `Dockerfile`).
    - **Instance Type**: Free (if available) or Starter.

3.  **Environment Variables (Advanced)**:
    - Scroll down to "Environment Variables" and add the following keys using your Aiven credentials:

    | Key | Value (Example) |
    |-----|-----------------|
    | `DB_HOST` | `mysql-xxxx.aivencloud.com` |
    | `DB_PORT` | `3306` |
    | `DB_USER` | `avnadmin` |
    | `DB_PASS` | `YOUR_AIVEN_PASSWORD` |
    | `DB_NAME` | `bookstore` |
    | `DB_SSL` | `true` |
    | `DB_SSL_CA_PATH` | `/path/to/ca.pem` (optional) |
    | `DB_SSL_CA` | `(CA cert content)` (optional) |

4.  **Deploy**:
    - Click **Create Web Service**.
    - Render will start building the Docker image. This may take a few minutes.

---

## Step 3: Verify Deployment

1.  **Check Logs**:
    - Watch the deployment logs in Render.
    - Ensure `composer install` runs successfully.
    - Look for "Server running" or Apache start messages.

2.  **Access the URL**:
    - Once deployed, click the URL provided by Render (e.g., `https://bookstore-app.onrender.com`).
    - Verify the homepage loads.
    - Try logging in or viewing a book to confirm database connection.

---

## Troubleshooting

-   **Database Connection Error**:
    -   Double-check your Environment Variables in Render.
    -   Ensure Aiven allows connections from Render (add Render IPs or allowlist as required).

-   **404 Errors**:
    -   This usually means `.htaccess` is not working. The provided `Dockerfile` enables `mod_rewrite`, so this should work automatically. 

-   **Mixed Content / Styles Missing**:
    -   Ensure the `BASE_URL` in `index.php` (Line 24) matches your Render URL.
    -   *Project Update Note*: Currently `BASE_URL` is hardcoded to `http://localhost/book_store/`. You may need to update this to `getenv('BASE_URL') ?: '...'` for full compatibility, or rely on relative paths.

    > **Recommendation**: 
    > 1. Add `BASE_URL` to your Render Environment Variables. Value should be your Render URL with a trailing slash (e.g., `https://your-app.onrender.com/`). Do **NOT** include `book_store/` unless you specifically set up a subdirectory.
    > 2. Ensure `DB_SSL` is NOT required if using the auto-detection logic, but verifying connection details is always good practice.

-   **500 Errors / Insecure Transport**:
    -   If you see "Connections using insecure transport are prohibited", ensure `DB_SSL` is `true` and a valid CA is available.
    -   The application now auto-detects Aiven/TiDB hosts for SSL.

