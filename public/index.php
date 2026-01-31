<?php
require_once "../config/db.php";
require_once "../includes/header.php";

/* DASHBOARD STATS (ORIGINAL LOGIC) */

// Total students
$totalStudents = $pdo->query(
    "SELECT COUNT(*) FROM students"
)->fetchColumn();

// Total courses
$totalCourses = $pdo->query(
    "SELECT COUNT(*) FROM courses"
)->fetchColumn();

// Total modules
$totalModules = $pdo->query(
    "SELECT COUNT(*) FROM modules"
)->fetchColumn();

// Students with low attendance (< 75%)
$lowAttendance = $pdo->query(
    "SELECT students.name,
            modules.module_name,
            attendance.attendance_percentage
     FROM attendance
     JOIN students ON attendance.student_id = students.id
     JOIN modules ON attendance.module_id = modules.id
     WHERE attendance.attendance_percentage < 75"
)->fetchAll();
?>

<h2>Dashboard</h2>

<!-- (STRUCTURE ONLY) -->
<div class="cards">
    <div class="card">
        <h3>Total Students</h3>
        <p><?= $totalStudents ?></p>
    </div>

    <div class="card">
        <h3>Total Courses</h3>
        <p><?= $totalCourses ?></p>
    </div>

    <div class="card">
        <h3>Total Modules</h3>
        <p><?= $totalModules ?></p>
    </div>
</div>


<!-- LOW ATTENDANCE TABLE  -->

<div class="table-wrapper">
    <h3>Students with Low Attendance (&lt; 75%)</h3>
<table>
<tr>
    <th>Student</th>
    <th>Module</th>
    <th>Attendance</th>
</tr>

<?php if (!$lowAttendance): ?>
<tr>
    <td colspan="3">No low attendance cases </td>
</tr>
<?php endif; ?>

<?php foreach ($lowAttendance as $row): ?>
<tr>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['module_name']) ?></td>
    <td class="low-attendance">
    <?= htmlspecialchars($row['attendance_percentage']) ?>%
</td>

</tr>
<?php endforeach; ?>
</table>
</div>
</script>

<?php require_once "../includes/footer.php"; ?>
