<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('supervisor');

$user_id = $_SESSION['user_id'];
$supervisor = $conn->query("SELECT * FROM supervisors WHERE user_id = {$user_id}")->fetch_assoc();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = (int)($_POST['report_id'] ?? 0);
    $remarks = sanitize_input($_POST['supervisor_remarks'] ?? '');
    $score = (int)($_POST['score'] ?? 0);
    $status = sanitize_input($_POST['approval_status'] ?? 'pending');

    if ($report_id) {
        $stmt = $conn->prepare("UPDATE weekly_reports SET supervisor_remarks = ?, score = ?, approval_status = ? WHERE id = ?");
        $stmt->bind_param('sisi', $remarks, $score, $status, $report_id);
        $stmt->execute();
        $message = 'Report updated.';
    }
}

$reports = $conn->query("SELECT weekly_reports.*, users.name AS student_name
    FROM weekly_reports
    JOIN students ON weekly_reports.student_id = students.id
    JOIN users ON students.user_id = users.id
    JOIN supervisor_assignments ON supervisor_assignments.student_id = students.id
    WHERE supervisor_assignments.supervisor_id = {$supervisor['id']}
    ORDER BY week_number ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Review Weekly Reports</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>Review Reports</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>
<div class="container">
    <?php if ($message): ?>
        <div class="flash"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php while ($report = $reports->fetch_assoc()): ?>
        <div class="report-card">
            <h3>Week <?php echo $report['week_number']; ?> - <?php echo $report['student_name']; ?></h3>
            <p><strong>Tasks:</strong> <?php echo nl2br(htmlspecialchars($report['tasks_done'])); ?></p>
            <p><strong>Challenges:</strong> <?php echo nl2br(htmlspecialchars($report['challenges'])); ?></p>

            <form method="post">
                <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">

                <label>Remarks</label>
                <textarea name="supervisor_remarks" rows="3"><?php echo htmlspecialchars($report['supervisor_remarks']); ?></textarea>

                <label>Score (0-100)</label>
                <input type="number" name="score" min="0" max="100" value="<?php echo htmlspecialchars($report['score']); ?>">

                <label>Status</label>
                <select name="approval_status">
                    <option value="pending" <?php if ($report['approval_status'] === 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="approved" <?php if ($report['approval_status'] === 'approved') echo 'selected'; ?>>Approved</option>
                    <option value="rejected" <?php if ($report['approval_status'] === 'rejected') echo 'selected'; ?>>Rejected</option>
                </select>

                <button type="submit">Save Feedback</button>
            </form>
        </div>
        <hr>
    <?php endwhile; ?>
</div>
</body>
</html>

