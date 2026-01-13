# Windows: Import database without breaking Vietnamese text

If you import `db/bookstore.sql` using PowerShell piping like:

```powershell
Get-Content .\db\bookstore.sql | mysql -u root -p
```

Windows PowerShell may change the encoding while piping, which can permanently corrupt Vietnamese characters into literal `?` in MySQL.

## Recommended (safe) methods

### Option A (simplest): use CMD redirection
From the project root:

```bat
mysql -u root -p --default-character-set=utf8mb4 < db\bookstore.sql
```

If your XAMPP MySQL root has **no password**:

```bat
mysql -u root --default-character-set=utf8mb4 < db\bookstore.sql
```

### Option B: run the provided helper
Double-click:

- `scripts/import_db_windows.cmd`

## Important

- The SQL file contains `DROP DATABASE IF EXISTS bookstore;`.
  Running it will **delete and recreate** the database.
- If the data is already stored as `?`, it **cannot be recovered** (the original characters are lost).
  You must re-import from the original SQL file using one of the safe methods above.
