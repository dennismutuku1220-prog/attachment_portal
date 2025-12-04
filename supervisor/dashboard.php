<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('supervisor');

$user_id = $_SESSION['user_id'];
$supervisor = $conn->query("SELECT * FROM supervisors WHERE user_id = {$user_id}")->fetch_assoc();
$students = $conn->query("SELECT students.id, users.name, students.registration_number, students.course
    FROM supervisor_assignments
    JOIN students ON supervisor_assignments.student_id = students.id
    JOIN users ON students.user_id = users.id
    WHERE supervisor_assignments.supervisor_id = {$supervisor['id']}
    ORDER BY users.name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Supervisor Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>Supervisor Dashboard</h1>
    <nav>
        <a href="dashboard.php">Students</a>
        <a href="review_reports.php">Review Reports</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>
<div class="container">
    <h2>Assigned Students</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Registration</th>
            <th>Course</th>
        </tr>
        <?php while ($student = $students->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
                <td><?php echo htmlspecialchars($student['registration_number']); ?></td>
                <td><?php echo htmlspecialchars($student['course']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

