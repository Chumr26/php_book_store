@echo off
setlocal

REM Import database with correct UTF-8 charset on Windows.
REM NOTE: This will DROP and recreate the `bookstore` database (because the SQL contains DROP DATABASE).

REM If your MySQL root has no password (common in XAMPP), use:
D:\Downloads\tools\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe -u root --default-character-set=utf8mb4 -e "source db/bookstore.sql"

REM If your MySQL root has a password, use -p to be prompted:
REM mysql -u root -p --default-character-set=utf8mb4 -e "source db/bookstore.sql"

echo.
echo Done.
pause
endlocal
