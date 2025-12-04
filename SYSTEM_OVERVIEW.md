# Attachment Portal System Overview

Use this document as a quick reference for how each module works. Processes are described in point form for clarity.

## 1. Authentication & Sessions
- Users log in via `login.php` (students register at `register.php`).
- Credentials are validated in `includes/auth.php` using prepared statements.
- On success, `$_SESSION` stores `user_id`, `user_name`, and `role`.
- `includes/functions.php` provides `require_login()` and `require_role()` to protect pages.
- `logout.php` clears the session and redirects back to the login page.

## 2. User Roles & Dashboards
- **Students** land on `student/dashboard.php`.
- **Supervisors** land on `supervisor/dashboard.php`.
- **Admins** land on `admin/dashboard.php`.
- `route_after_login()` sends each user to the correct dashboard immediately after login.

## 3. Student Module
- Dashboard shows profile info, supervisor assignment, attachment details, weekly reports, and uploads.
- Students edit company/start/end dates directly from the dashboard (stored in `attachments` table).
- Weekly reports are created through `student/submit_report.php` and listed on the dashboard.
- File uploads (letters, logbooks, reports) are handled via `handle_file_upload()` with 5 MB limit and PDF/DOC/DOCX validation.

## 4. Supervisor Module
- `supervisor/dashboard.php` lists assigned students.
- `supervisor/review_reports.php` lists all weekly reports submitted by those students.
- Supervisors review each report, add remarks, score (0–100), and set approval status (pending/approved/rejected).
- Updates are written back to `weekly_reports` using prepared statements.

## 5. Admin Module
- `admin/dashboard.php` shows counts for students, supervisors, and reports plus an overview table with report counts and average scores.
- `admin/assign_supervisors.php` lets admins map each student to a supervisor via the junction table `supervisor_assignments`.
- `admin/summary.php` produces a printable attachment summary (company info + report stats) for management.

## 6. Database Structure (MySQL)
- `users`: stores every login (role field differentiates accounts).
- `students` and `supervisors`: profile details linked to `users` via `user_id`.
- `attachments`: company placement info per student.
- `weekly_reports`: student submissions plus supervisor feedback (remarks, score, status).
- `uploads`: document metadata for each user.
- `supervisor_assignments`: maps one supervisor to each student.
- Full schema + sample data is inside `attachment_portal.sql`.

## 7. File Upload Process
- Forms submit to the same page; PHP calls `handle_file_upload()`.
- Function checks error code, size, and MIME type.
- Files are stored inside `/uploads` with generated unique names to avoid collisions.
- Metadata is saved to the `uploads` table for listing and download.

## 8. Security Measures
- Every database interaction uses prepared statements to prevent SQL injection.
- Inputs go through `sanitize_input()` and outputs use `htmlspecialchars()` before rendering.
- Sessions guard all private pages; unauthorized access redirects to the home or login page.
- Upload validation enforces limited file types and size thresholds.

## 9. Reporting & Printing
- Admin summary page aggregates report counts and average scores.
- Browser print styles hide the print button for clean PDFs or paper copies.

## 10. Setup & Maintenance
- `SETUP.md` explains how to place the project in `htdocs`, import the SQL via phpMyAdmin, and update `includes/db.php` credentials.
- Default demo logins: admin@portal.test / Admin@123, student@portal.test / Student@123, supervisor@portal.test / Supervisor@123.
- Keep `uploads/` writable so Apache/PHP can save files.

