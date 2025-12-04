<?php
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Industrial Attachment Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Industrial Attachment Portal</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a href="register.php">Student Registration</a>
        </nav>
    </header>

    <div class="container">
        <?php flash_message(); ?>
        <p>This portal helps students, supervisors, and administrators manage industrial attachment activities. Use the links above to sign in or create a new student account.</p>
        <h3>Key features</h3>
        <ul>
            <li>Weekly report submission with supervisor feedback.</li>
            <li>Document uploads (letters, logbooks, reports).</li>
            <li>Admin dashboards with progress tracking.</li>
        </ul>
    </div>
</body>
</html>

