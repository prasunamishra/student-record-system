<?php
require_once "../includes/header.php";
require_once "../config/db.php";

// ------------------------
// ADD ATTENDANCE
// ------------------------
if (isset($_POST['add_attendance'])) {
    $student_id = $_POST['student_id'];
    $module_id = $_POST['module_id'];
    $attendance = $_POST['attendance_percentage'];

    if ($student_id && $module_id && is_numeric($attendance)) {
        $stmt = $pdo->prepare(
            "INSERT INTO attendance (student_id, module_id, attendance_percentage)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$student_id, $module_id, $attendance]);
    }
}

// ------------------------
// DELETE ATTENDANCE
// ------------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE id = ?");
    $stmt->execute([$id]);
}

// ------------------------
// UPDATE ATTENDANCE
// ------------------------
if (isset($_POST['update_attendance'])) {
    $id = $_POST['id'];
    $student_id = $_POST['student_id'];
    $module_id = $_POST['module_id'];
    $attendance = $_POST['attendance_percentage'];

    $stmt = $pdo->prepare(
        "UPDATE attendance
         SET student_id = ?, module_id = ?, attendance_percentage = ?
         WHERE id = ?"
    );
    $stmt->execute([$student_id, $module_id, $attendance, $id]);
}

// ------------------------
// FETCH STUDENTS
// ------------------------
$students = $pdo->query("SELECT id, name FROM students")->fetchAll();

// ------------------------
// FETCH MODULES
// ------------------------
$modules = $pdo->query("SELECT id, module_name FROM modules")->fetchAll();

// ------------------------
// FETCH ATTENDANCE RECORDS
// ------------------------
$stmt = $pdo->query(
    "SELECT attendance.id, students.name AS student_name,
            modules.module_name, attendance.attendance_percentage
     FROM attendance
     JOIN students ON attendance.student_id = students.id
     JOIN modules ON attendance.module_id = modules.id"
);
$records = $stmt->fetchAll();
?>

<h2>Manage Attendance</h2>

<!-- ADD ATTENDANCE FORM -->
<form method="post">
    <select name="student_id" required>
        <option value="">Select Student</option>
        <?php foreach ($students as $student): ?>
            <option value="<?php echo $student['id']; ?>">
                <?php echo htmlspecialchars($student['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="module_id" required>
        <option value="">Select Module</option>
        <?php foreach ($modules as $module): ?>
            <option value="<?php echo $module['id']; ?>">
                <?php echo htmlspecialchars($module['module_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="number" name="attendance_percentage"
           placeholder="Attendance %" min="0" max="100" required>

    <button type="submit" name="add_attendance">Add Attendance</button>
</form>

<br>

<!-- ATTENDANCE LIST -->
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Module</th>
        <th>Attendance (%)</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($records as $r): ?>
    <tr>
        <td><?php echo htmlspecialchars($r['id']); ?></td>
        <td><?php echo htmlspecialchars($r['student_name']); ?></td>
        <td><?php echo htmlspecialchars($r['module_name']); ?></td>
        <td><?php echo htmlspecialchars($r['attendance_percentage']); ?>%</td>
        <td>
            <a href="attendance.php?edit=<?php echo $r['id']; ?>">Edit</a> |
            <a href="attendance.php?delete=<?php echo $r['id']; ?>"
               onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
// ------------------------
// EDIT ATTENDANCE FORM
// ------------------------
if (isset($_GET['edit'])):
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE id = ?");
    $stmt->execute([$id]);
    $attendanceData = $stmt->fetch();
?>

<hr>

<h3>Edit Attendance</h3>

<form method="post">
    <input type="hidden" name="id" value="<?php echo $attendanceData['id']; ?>">

    <select name="student_id" required>
        <?php foreach ($students as $student): ?>
            <option value="<?php echo $student['id']; ?>"
                <?php if ($student['id'] == $attendanceData['student_id']) echo "selected"; ?>>
                <?php echo htmlspecialchars($student['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="module_id" required>
        <?php foreach ($modules as $module): ?>
            <option value="<?php echo $module['id']; ?>"
                <?php if ($module['id'] == $attendanceData['module_id']) echo "selected"; ?>>
                <?php echo htmlspecialchars($module['module_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="number" name="attendance_percentage"
           value="<?php echo htmlspecialchars($attendanceData['attendance_percentage']); ?>"
           min="0" max="100" required>

    <button type="submit" name="update_attendance">Update Attendance</button>
</form>

<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>
