<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";

/*ADD ATTENDANCE (NO DUPLICATES)*/
if (isset($_POST['add_attendance'])) {
    $student_id = $_POST['student_id'];
    $module_id  = $_POST['module_id'];
    $attendance = $_POST['attendance'];

    if ($attendance < 0 || $attendance > 100) {
        setMessage('error', 'Attendance must be between 0 and 100.');
        header("Location: attendance.php");
        exit;
    }

    // Checking duplicate attendance
    $check = $pdo->prepare(
        "SELECT id FROM attendance
         WHERE student_id = ? AND module_id = ?"
    );
    $check->execute([$student_id, $module_id]);

    if ($check->fetch()) {
        setMessage(
            'error',
            'Attendance for this student and module already exists.'
        );
        header("Location: attendance.php");
        exit;
    }

    // Insert attendance
    $stmt = $pdo->prepare(
        "INSERT INTO attendance (student_id, module_id, attendance_percentage)
         VALUES (?, ?, ?)"
    );
    $stmt->execute([$student_id, $module_id, $attendance]);

    setMessage('success', 'Attendance added successfully.');
    header("Location: attendance.php");
    exit;
}

/*DELETE ATTENDANCE*/
if (isset($_POST['delete_attendance'])) {
    $pdo->prepare("DELETE FROM attendance WHERE id = ?")
        ->execute([$_POST['id']]);

    setMessage('success', 'Attendance deleted.');
    header("Location: attendance.php");
    exit;
}

/* FETCH DATA */
$students = $pdo->query(
    "SELECT id, name FROM students"
)->fetchAll();

$modules = $pdo->query(
    "SELECT id, module_name FROM modules"
)->fetchAll();

$records = $pdo->query(
    "SELECT attendance.id,
            students.name AS student,
            modules.module_name,
            attendance.attendance_percentage
     FROM attendance
     JOIN students ON attendance.student_id = students.id
     JOIN modules ON attendance.module_id = modules.id"
)->fetchAll();
?>

<h2>Manage Attendance</h2>

<form method="post">
    <select name="student_id" required>
        <option value="">Select Student</option>
        <?php foreach ($students as $s): ?>
            <option value="<?= $s['id'] ?>">
                <?= htmlspecialchars($s['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="module_id" required>
        <option value="">Select Module</option>
        <?php foreach ($modules as $m): ?>
            <option value="<?= $m['id'] ?>">
                <?= htmlspecialchars($m['module_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="number"
           name="attendance"
           min="0" max="100"
           placeholder="Attendance %"
           required>

    <button name="add_attendance">Add Attendance</button>
</form>

<table>
<tr>
    <th>Student</th>
    <th>Module</th>
    <th>Attendance (%)</th>
    <th>Action</th>
</tr>

<?php if (!$records): ?>
<tr>
    <td colspan="4">No attendance records found.</td>
</tr>
<?php endif; ?>

<?php foreach ($records as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['student']) ?></td>
    <td><?= htmlspecialchars($r['module_name']) ?></td>
    <td><?= htmlspecialchars($r['attendance_percentage']) ?>%</td>
    <td class="actions">
        <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">
            <button name="delete_attendance"
                    onclick="return confirm('Delete this attendance?')">
                Delete
            </button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php require_once "../includes/footer.php"; ?>
