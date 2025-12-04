<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$student_count = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'] ?? 0;
$supervisor_count = $conn->query("SELECT COUNT(*) AS total FROM supervisors")->fetch_assoc()['total'] ?? 0;
$report_count = $conn->query("SELECT COUNT(*) AS total FROM weekly_reports")->fetch_assoc()['total'] ?? 0;

$progress_query = $conn->query("SELECT students.id, users.name AS student_name, supervisors.id AS supervisor_id, sup_users.name AS supervisor_name,
    COALESCE(AVG(weekly_reports.score), 0) AS avg_score,
    COUNT(weekly_reports.id) AS reports_submitted
    FROM students
    JOIN users ON students.user_id = users.id
    LEFT JOIN attachments ON attachments.student_id = students.id
    LEFT JOIN supervisor_assignments ON supervisor_assignments.student_id = students.id
    LEFT JOIN supervisors ON supervisor_assignments.supervisor_id = supervisors.id
    LEFT JOIN users AS sup_users ON supervisors.user_id = sup_users.id
    LEFT JOIN weekly_reports ON weekly_reports.student_id = students.id
    GROUP BY students.id, student_name, supervisor_id, supervisor_name");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>Admin Dashboard</h1>
    <nav>
        <a href="dashboard.php">Overview</a>
        <a href="assign_supervisors.php">Assign Supervisors</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>
<div class="container">
    <h2>Quick Stats</h2>
    <p>Total Students: <?php echo $student_count; ?></p>
    <p>Total Supervisors: <?php echo $supervisor_count; ?></p>
    <p>Total Reports Submitted: <?php echo $report_count; ?></p>

    <h2>Student Progress Summary</h2>
    <table>
        <tr>
            <th>Student</th>
            <th>Supervisor</th>
            <th>Reports Submitted</th>
            <th>Average Score</th>
        </tr>
        <?php while ($row = $progress_query->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                <td><?php echo htmlspecialchars($row['supervisor_name'] ?? 'Not Assigned'); ?></td>
                <td><?php echo $row['reports_submitted']; ?></td>
                <td><?php echo round($row['avg_score'], 1); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="summary.php" target="_blank">Printable Attachment Summary</a></p>
</div>
</body>
</html>

