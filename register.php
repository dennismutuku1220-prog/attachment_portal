<?php
require_once __DIR__ . '/includes/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $registration_number = sanitize_input($_POST['registration_number'] ?? '');
    $course = sanitize_input($_POST['course'] ?? '');
    $institution = sanitize_input($_POST['institution'] ?? '');

    if (!$name || !$email || !$password) {
        $message = 'Please fill in all required fields.';
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $role = 'student';
        $stmt->bind_param('ssss', $name, $email, $hashed, $role);

        try {
            $stmt->execute();
            $user_id = $stmt->insert_id;

            $student_stmt = $conn->prepare('INSERT INTO students (user_id, registration_number, course, institution) VALUES (?, ?, ?, ?)');
            $student_stmt->bind_param('isss', $user_id, $registration_number, $course, $institution);
            $student_stmt->execute();

            set_flash('Account created successfully. You can now log in.');
            redirect('login.php');
        } catch (mysqli_sql_exception $e) {
            $message = 'Could not register. The email might already be taken.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Student Registration</h1>
</header>
<div class="container">
    <?php if ($message): ?>
        <div class="flash"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Registration Number</label>
        <input type="text" name="registration_number" required>

        <label>Course</label>
        <input type="text" name="course" required>

        <label>Institution</label>
        <input type="text" name="institution" required>

        <button type="submit">Create Account</button>
    </form>
</div>
</body>
</html>

