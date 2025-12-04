<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('student');

$user_id = $_SESSION['user_id'];
$student = $conn->query("SELECT * FROM students WHERE user_id = {$user_id}")->fetch_assoc();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $week_number = (int)($_POST['week_number'] ?? 0);
    $tasks_done = sanitize_input($_POST['tasks_done'] ?? '');
    $challenges = sanitize_input($_POST['challenges'] ?? '');

    if ($week_number <= 0) {
        $message = 'Week number must be greater than zero.';
    } else {
        $stmt = $conn->prepare("INSERT INTO weekly_reports (student_id, week_number, tasks_done, challenges, approval_status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param('iiss', $student['id'], $week_number, $tasks_done, $challenges);
        $stmt->execute();
        set_flash('Report submitted successfully.');
        redirect('dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Submit Weekly Report</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>Submit Weekly Report</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>
<div class="container">
    <?php if ($message): ?>
        <div class="flash"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Week Number</label>
        <input type="number" name="week_number" min="1" required>

        <label>Tasks Completed</label>
        <textarea name="tasks_done" rows="4" required></textarea>

        <label>Challenges Faced</label>
        <textarea name="challenges" rows="4" required></textarea>

        <button type="submit">Submit Report</button>
    </form>
</div>
</body>
</html>

