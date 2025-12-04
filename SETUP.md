# Attachment Portal Setup Guide

Follow these steps to run the project on XAMPP.

## 1. Prerequisites
- XAMPP installed (Apache + MySQL).
- PHP 8+ recommended.

## 2. Place Project Files
1. Copy the `attachment_portal` folder into `C:\xampp\htdocs\`.
2. Ensure the folder structure looks like `C:\xampp\htdocs\attachment_portal`.

## 3. Start XAMPP Services
1. Open the XAMPP control panel.
2. Start **Apache** and **MySQL** services.

## 4. Create Database
1. Visit `http://localhost/phpmyadmin`.
2. Click **Import** and choose the `attachment_portal.sql` file located inside the project folder.
3. Click **Go** to execute the SQL script. This creates the database, tables, and demo users.

## 5. Configure Database Credentials (if needed)
1. Open `includes/db.php`.
2. Update `$db_user` and `$db_pass` if your MySQL credentials are different.

## 6. Access the Site
1. In your browser, go to `http://localhost/attachment_portal/`.
2. Use the login menu to access the system.

## 7. Default Accounts
- **Admin:** `admin@portal.test` / `Admin@123`
- **Student:** `student@portal.test` / `Student@123`
- **Supervisor:** `supervisor@portal.test` / `Supervisor@123`

## 8. File Uploads
- Uploaded files are stored inside the `/uploads` folder.
- Make sure the folder has write permission for Apache (on Windows this is already allowed).

## 9. Troubleshooting
- If you get database errors, confirm the database name matches the one in `db.php`.
- Enable PHP error display by editing `php.ini` (`display_errors = On`) during development.
- Clear browser cache if the stylesheet seems outdated.

