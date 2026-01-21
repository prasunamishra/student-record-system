<?php
require_once "../includes/header.php";
require_once "../config/db.php";

// Fetch courses
$courses = $pdo->query("SELECT * FROM courses")->fetchAll();

$results = [];

if (isset($_GET['search'])) {
    $name = $_GET['name'];
    $course_id = $_GET['course_id'];
    $attendance_limit = $_GET['attendance'];

    $sql = "
        SELECT students.name, students.roll_number,
               courses.course_name,
               modules.module_name,
               attendance.attendance_percentage
        FROM students
        JOIN courses ON students.course_id = courses.id
        JOIN attendance ON students.id = attendance.student_id
        JOIN modules ON attendance.module_id = modules.id
        WHERE 1=1
    ";

    $params = [];

    if (!empty($name)) {
        $sql .= " AND students.name LIKE ?";
        $params[] = "%$name%";
    }

    if (!empty($course_id)) {
        $sql .= " AND courses.id = ?";
        $params[] = $course_id;
    }

    if (!empty($attendance_limit)) {
        $sql .= " AND attendance.attendance_percentage < ?";
        $params[] = $attendance_limit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
}
?>

<h2>Advanced Student Search</h2>
<h3>Live Student Search</h3>
<input type="text" id="live-search" placeholder="Type student name">
<div id="result" style="border:1px solid #000;"></div>

<script src="../assets/js/search.js"></script>


<form method="get">
    <input type="text" name="name" placeholder="Student Name">

    <select name="course_id">
        <option value="">All Courses</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['id']; ?>">
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="number" name="attendance"
           placeholder="Attendance below (%)">

    <button type="submit" name="search">Search</button>
</form>

<br>

<?php if ($results): ?>
<table border="1" cellpadding="5">
    <tr>
        <th>Student</th>
        <th>Roll No</th>
        <th>Course</th>
        <th>Module</th>
        <th>Attendance (%)</th>
    </tr>

    <?php foreach ($results as $row): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
        <td><?php echo htmlspecialchars($row['module_name']); ?></td>
        <td><?php echo htmlspecialchars($row['attendance_percentage']); ?>%</td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>
