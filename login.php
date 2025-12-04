<?php
require_once __DIR__ . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login_user($email, $password)) {
        redirect(route_after_login($_SESSION['role']));
    } else {
        $error = 'Invalid email or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login | Attachment Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Attachment Portal Login</h1>
</header>
<div class="container">
    <?php if ($error): ?>
        <div class="flash"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <p>Need a student account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>

