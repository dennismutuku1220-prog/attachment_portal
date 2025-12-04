<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('student');

$user_id = $_SESSION['user_id'];
$student = $conn->query("SELECT students.*, users.name, users.email 
    FROM students JOIN users ON students.user_id = users.id WHERE users.id = {$user_id}")->fetch_assoc();

$attachment = $conn->query("SELECT * FROM attachments WHERE student_id = {$student['id']} LIMIT 1")->fetch_assoc();

$assignment = $conn->query("SELECT sup_users.name AS supervisor_name
    FROM supervisor_assignments 
    JOIN supervisors ON supervisor_assignments.supervisor_id = supervisors.id
    JOIN users AS sup_users ON supervisors.user_id = sup_users.id
    WHERE supervisor_assignments.student_id = {$student['id']}")->fetch_assoc();

$reports = $conn->query("SELECT * FROM weekly_reports WHERE student_id = {$student['id']} ORDER BY week_number ASC");

$uploads = $conn->query("SELECT * FROM uploads WHERE user_id = {$user_id} ORDER BY upload_date DESC");

$upload_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_attachment'])) {
        $company = sanitize_input($_POST['company_name']);
        $start = sanitize_input($_POST['start_date']);
        $end = sanitize_input($_POST['end_date']);

        if ($attachment) {
            $stmt = $conn->prepare("UPDATE attachments SET company_name = ?, start_date = ?, end_date = ? WHERE id = ?");
            $stmt->bind_param('sssi', $company, $start, $end, $attachment['id']);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO attachments (student_id, company_name, start_date, end_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('isss', $student['id'], $company, $start, $end);
            $stmt->execute();
        }
        set_flash('Attachment details updated.');
        redirect('dashboard.php');
    }

    if (isset($_POST['upload_document'])) {
        $file_name = handle_file_upload($_FILES['document']);
        if ($file_name) {
            $type = $_FILES['document']['type'];
            $stmt = $conn->prepare("INSERT INTO uploads (user_id, file_name, file_type, upload_date) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param('iss', $user_id, $file_name, $type);
            $stmt->execute();
            set_flash('File uploaded successfully.');
            redirect('dashboard.php');
        } else {
            $upload_error = 'Upload failed. Check the file type (PDF/DOC/DOCX) and size (max 5MB).';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>Welcome, <?php echo htmlspecialchars($student['name']); ?></h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="submit_report.php">Submit Weekly Report</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>
<div class="container">
    <?php flash_message(); ?>
    <h2>Profile</h2>
    <p><strong>Email:</strong> <?php echo $student['email']; ?></p>
    <p><strong>Registration Number:</strong> <?php echo $student['registration_number']; ?></p>
    <p><strong>Course:</strong> <?php echo $student['course']; ?></p>
    <p><strong>Institution:</strong> <?php echo $student['institution']; ?></p>
    <p><strong>Supervisor:</strong> <?php echo $assignment['supervisor_name'] ?? 'Not Assigned'; ?></p>

    <h2>Attachment Details</h2>
    <form method="post">
        <input type="hidden" name="update_attachment" value="1">
        <label>Company Name</label>
        <input type="text" name="company_name" value="<?php echo $attachment['company_name'] ?? ''; ?>" required>

        <label>Start Date</label>
        <input type="date" name="start_date" value="<?php echo $attachment['start_date'] ?? ''; ?>" required>

        <label>End Date</label>
        <input type="date" name="end_date" value="<?php echo $attachment['end_date'] ?? ''; ?>" required>

        <button type="submit">Save Attachment Info</button>
    </form>
    <h2>Weekly Reports</h2>
    <table>
        <tr>
            <th>Week</th>
            <th>Tasks Done</th>
            <th>Challenges</th>
            <th>Supervisor Remarks</th>
            <th>Score</th>
            <th>Status</th>
        </tr>
        <?php while ($report = $reports->fetch_assoc()): ?>
            <tr>
                <td><?php echo $report['week_number']; ?></td>
                <td><?php echo nl2br(htmlspecialchars($report['tasks_done'])); ?></td>
                <td><?php echo nl2br(htmlspecialchars($report['challenges'])); ?></td>
                <td><?php echo htmlspecialchars($report['supervisor_remarks'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($report['score'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($report['approval_status'] ?? 'pending'); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>Document Uploads</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Upload Attachment Letter, Logbook, or Report (PDF / DOC / DOCX)</label>
        <input type="file" name="document" accept=".pdf,.doc,.docx" required>
        <button type="submit" name="upload_document">Upload File</button>
    </form>
    <?php if ($upload_error): ?>
        <p><?php echo $upload_error; ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>File</th>
            <th>Type</th>
            <th>Date</th>
            <th>Download</th>
        </tr>
        <?php while ($upload = $uploads->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($upload['file_name']); ?></td>
                <td><?php echo htmlspecialchars($upload['file_type']); ?></td>
                <td><?php echo htmlspecialchars($upload['upload_date']); ?></td>
                <td><a href="../uploads/<?php echo urlencode($upload['file_name']); ?>" target="_blank">View</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

