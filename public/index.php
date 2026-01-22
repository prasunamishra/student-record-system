<?php
require_once "../config/db.php";
require_once "../includes/header.php";

// Fetch students with course
$stmt = $pdo->query(
    "SELECT students.id, students.name, students.email, courses.course_name
     FROM students
     LEFT JOIN courses ON students.course_id = courses.id"
);
$students = $stmt->fetchAll();

// Total students count
$totalStudents = count($students);
?>

<h2 style="display:inline-block;">Student List</h2>

<span style="float:right; font-weight:bold;">
    Total Students: <?php echo $totalStudents; ?>
</span>

<p>Below is the list of students with their ID, email and enrolled course.</p>

<table>
    <tr>
        <th>Student ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Course</th>
    </tr>

    <?php if ($totalStudents == 0): ?>
        <tr>
            <td colspan="4">No students found.</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($students as $s): ?>
    <tr>
        <td><?php echo htmlspecialchars($s['id']); ?></td>
        <td><?php echo htmlspecialchars($s['name']); ?></td>
        <td><?php echo htmlspecialchars($s['email']); ?></td>
        <td><?php echo htmlspecialchars($s['course_name'] ?? 'Not Assigned'); ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require_once "../includes/footer.php"; ?>
