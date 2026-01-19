# Deployment Guide: Render + TiDB

This guide outlines the steps to deploy the BookStore application using **Render** (for web hosting) and **TiDB Cloud** (for the database).

## Prerequisites
- A [GitHub](https://github.com/) account (Application code must be in a repository).
- A [Render](https://render.com/) account.
- A [TiDB Cloud](https://tidbcloud.com/) account.

---

## Step 1: Prepare Database (TiDB Cloud)

1.  **Create a Cluster**:
    - Log in to TiDB Cloud.
    - Create a new "Serverless" cluster (Free Tier is sufficient for testing).
    - Give it a name (e.g., `bookstore-db`).

2.  **Get Connection Info**:
    - Once the cluster is created, click **"Connect"**.
    - Select **"Connect with general SQL client"**.
    - Note down the values:
        - **Host**: (e.g., `gateway01.us-west-2.prod.aws.tidbcloud.com`)
        - **Port**: `4000`
        - **User**: (e.g., `2.root`)
        - **Password**: (The password you set/generated)

3.  **Import Database Schema**:
    - You need to run the content of `db/bookstore.sql` on your TiDB cluster.
    - **Option A (TiDB SQL Editor)**:
        - Open the "Chat2Query" or SQL Editor in TiDB Cloud.
        - Copy and paste the contents of `db/bookstore.sql`.
        - Run the script *twice* (or block by block) if there are foreign key dependency issues, though the script usually handles order.
    - **Option B (Local MySQL Client)**:
        - Connect from your local terminal:
          ```bash
          mysql -u [User] -h [Host] -P 4000 -p --ssl-mode=VERIFY_IDENTITY --ssl-ca=/etc/ssl/cert.pem
          ```
          *(Note: TiDB requires SSL usually. The exact command depends on your OS/Setup.)*
        - Source the file: `source db/bookstore.sql`
        
        **For Windows (PowerShell with XAMPP/MariaDB):**
        ```powershell
        cmd /c "mysql -u [User] -h [Host] -P 4000 -p --ssl < db\bookstore.sql"
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
    - Scroll down to "Environment Variables" and add the following keys using your TiDB credentials:

    | Key | Value (Example) |
    |-----|-----------------|
    | `DB_HOST` | `gateway01...tidbcloud.com` |
    | `DB_PORT` | `4000` |
    | `DB_USER` | `2.root` |
    | `DB_PASS` | `YOUR_TIDB_PASSWORD` |
    | `DB_NAME` | `bookstore` |
    | `DB_SSL` | `true` (Optional, as TiDB enforces SSL typically) |

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
    -   Ensure TiDB "Traffic Filters" (IP Access List) allow `0.0.0.0/0` (Allow All) or include Render's IP ranges (Allow All is easiest for Serverless).

-   **404 Errors**:
    -   This usually means `.htaccess` is not working. The provided `Dockerfile` enables `mod_rewrite`, so this should work automatically. 

-   **Mixed Content / Styles Missing**:
    -   Ensure the `BASE_URL` in `index.php` (Line 24) matches your Render URL.
    -   *Project Update Note*: Currently `BASE_URL` is hardcoded to `http://localhost/book_store/`. You may need to update this to `getenv('BASE_URL') ?: '...'` for full compatibility, or rely on relative paths.

    > **Recommendation**: Add `BASE_URL` to your Render Environment Variables (e.g., `https://your-app.onrender.com/`) and update `index.php` to use it.
