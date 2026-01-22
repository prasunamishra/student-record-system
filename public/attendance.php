<?php
require_once "../config/db.php";

// ------------------------
// ADD ATTENDANCE (Prevent duplicate)
// ------------------------
if (isset($_POST['add_attendance'])) {
    $student_id = $_POST['student_id'];
    $module_id = $_POST['module_id'];
    $attendance = $_POST['attendance_percentage'];

    if ($student_id && $module_id && is_numeric($attendance)) {

        // Check if record already exists
        $check = $pdo->prepare(
            "SELECT id FROM attendance WHERE student_id = ? AND module_id = ?"
        );
        $check->execute([$student_id, $module_id]);

        if ($check->rowCount() == 0) {
            $stmt = $pdo->prepare(
                "INSERT INTO attendance (student_id, module_id, attendance_percentage)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$student_id, $module_id, $attendance]);
        }
    }
}

// ------------------------
// DELETE ATTENDANCE
// ------------------------
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
}

// ------------------------
// UPDATE ATTENDANCE
// ------------------------
if (isset($_POST['update_attendance'])) {
    $stmt = $pdo->prepare(
        "UPDATE attendance
         SET student_id = ?, module_id = ?, attendance_percentage = ?
         WHERE id = ?"
    );
    $stmt->execute([
        $_POST['student_id'],
        $_POST['module_id'],
        $_POST['attendance_percentage'],
        $_POST['id']
    ]);
}

// ------------------------
// FETCH STUDENTS & MODULES
// ------------------------
$students = $pdo->query("SELECT id, name FROM students")->fetchAll();
$modules  = $pdo->query("SELECT id, module_name FROM modules")->fetchAll();

// ------------------------
// FETCH ATTENDANCE RECORDS
// ------------------------
$records = $pdo->query(
    "SELECT attendance.id, students.name AS student_name,
            modules.module_name, attendance.attendance_percentage
     FROM attendance
     JOIN students ON attendance.student_id = students.id
     JOIN modules ON attendance.module_id = modules.id"
)->fetchAll();

require_once "../includes/header.php";
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

    <input type="number" name="attendance_percentage"
           min="0" max="100" placeholder="Attendance %" required>

    <button type="submit" name="add_attendance">Add Attendance</button>
</form>

<br>

<table>
    <tr>
        <th>Student</th>
        <th>Module</th>
        <th>Attendance (%)</th>
        <th>Action</th>
    </tr>

    <?php foreach ($records as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['student_name']) ?></td>
        <td><?= htmlspecialchars($r['module_name']) ?></td>
        <td><?= htmlspecialchars($r['attendance_percentage']) ?>%</td>
        <td>
            <a href="?edit=<?= $r['id'] ?>">Edit</a> |
            <a href="?delete=<?= $r['id'] ?>"
               onclick="return confirm('Delete attendance?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
// ------------------------
// EDIT FORM
// ------------------------
if (isset($_GET['edit'])):
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $data = $stmt->fetch();

    if ($data):
?>

<hr>

<h3>Edit Attendance</h3>

<form method="post">
    <input type="hidden" name="id" value="<?= $data['id'] ?>">

    <select name="student_id" required>
        <?php foreach ($students as $s): ?>
            <option value="<?= $s['id'] ?>"
                <?= $s['id'] == $data['student_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="module_id" required>
        <?php foreach ($modules as $m): ?>
            <option value="<?= $m['id'] ?>"
                <?= $m['id'] == $data['module_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['module_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="number" name="attendance_percentage"
           min="0" max="100"
           value="<?= htmlspecialchars($data['attendance_percentage']) ?>" required>

    <button type="submit" name="update_attendance">Update</button>
</form>

<?php endif; endif; ?>

<?php require_once "../includes/footer.php"; ?>
