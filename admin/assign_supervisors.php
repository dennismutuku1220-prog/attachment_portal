<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$students = $conn->query("SELECT students.id, users.name 
    FROM students JOIN users ON students.user_id = users.id ORDER BY users.name ASC");

$supervisors = get_supervisors();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $supervisor_id = (int)($_POST['supervisor_id'] ?? 0);

    if ($student_id && $supervisor_id) {
        $stmt = $conn->prepare("REPLACE INTO supervisor_assignments (student_id, supervisor_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $student_id, $supervisor_id);
        $stmt->execute();
        $message = 'Assignment updated successfully.';
    } else {
        $message = 'Please select both student and supervisor.';
    }
}

$assignments = $conn->query("SELECT students.id, users.name AS student_name, sup_users.name AS supervisor_name
    FROM students
    JOIN users ON students.user_id = users.id
    LEFT JOIN supervisor_assignments ON supervisor_assignments.student_id = students.id
    LEFT JOIN supervisors ON supervisor_assignments.supervisor_id = supervisors.id
    LEFT JOIN users AS sup_users ON supervisors.user_id = sup_users.id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Assign Supervisors</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <h1>Assign Supervisors</h1>
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
        <label>Select Student</label>
        <select name="student_id" required>
            <option value="">-- choose student --</option>
            <?php while ($student = $students->fetch_assoc()): ?>
                <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['name']); ?></option>
            <?php endwhile; ?>
        </select>

        <label>Select Supervisor</label>
        <select name="supervisor_id" required>
            <option value="">-- choose supervisor --</option>
            <?php foreach ($supervisors as $supervisor): ?>
                <option value="<?php echo $supervisor['id']; ?>"><?php echo htmlspecialchars($supervisor['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Assign Supervisor</button>
    </form>

    <h2>Current Assignments</h2>
    <table>
        <tr>
            <th>Student</th>
            <th>Supervisor</th>
        </tr>
        <?php while ($row = $assignments->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                <td><?php echo htmlspecialchars($row['supervisor_name'] ?? 'Not Assigned'); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

