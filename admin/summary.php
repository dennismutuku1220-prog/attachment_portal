<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$summary = $conn->query("SELECT users.name AS student_name,
    students.registration_number,
    attachments.company_name,
    attachments.start_date,
    attachments.end_date,
    COALESCE(AVG(weekly_reports.score), 0) AS average_score,
    COUNT(weekly_reports.id) AS report_count
    FROM students
    JOIN users ON students.user_id = users.id
    LEFT JOIN attachments ON attachments.student_id = students.id
    LEFT JOIN weekly_reports ON weekly_reports.student_id = students.id
    GROUP BY students.id, users.name, students.registration_number, attachments.company_name, attachments.start_date, attachments.end_date");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Printable Attachment Summary</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Attachment Summary</h1>
    <button onclick="window.print()">Print Summary</button>
    <table>
        <tr>
            <th>Student</th>
            <th>Reg No</th>
            <th>Company</th>
            <th>Duration</th>
            <th>Reports</th>
            <th>Average Score</th>
        </tr>
        <?php while ($row = $summary->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                <td><?php echo htmlspecialchars($row['registration_number']); ?></td>
                <td><?php echo htmlspecialchars($row['company_name'] ?? '-'); ?></td>
                <td>
                    <?php
                    if ($row['start_date'] && $row['end_date']) {
                        echo htmlspecialchars($row['start_date']) . ' to ' . htmlspecialchars($row['end_date']);
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td><?php echo $row['report_count']; ?></td>
                <td><?php echo round($row['average_score'], 1); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

