<?php
require_once "../includes/header.php";
require_once "../config/db.php";

// ------------------------
// ADD STUDENT
// ------------------------
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $roll = $_POST['roll'];
    $course_id = $_POST['course_id'];

    if ($name && $email && $roll) {
        $stmt = $pdo->prepare(
            "INSERT INTO students (name, email, roll_number, course_id)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$name, $email, $roll, $course_id]);
    }
}

// ------------------------
// DELETE STUDENT
// ------------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
}

// ------------------------
// UPDATE STUDENT
// ------------------------
if (isset($_POST['update_student'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $roll = $_POST['roll'];
    $course_id = $_POST['course_id'];

    $stmt = $pdo->prepare(
        "UPDATE students
         SET name = ?, email = ?, roll_number = ?, course_id = ?
         WHERE id = ?"
    );
    $stmt->execute([$name, $email, $roll, $course_id, $id]);
}

// ------------------------
// FETCH COURSES FOR DROPDOWN
// ------------------------
$courses = $pdo->query("SELECT * FROM courses")->fetchAll();

// ------------------------
// FETCH STUDENTS
// ------------------------
$stmt = $pdo->query(
    "SELECT students.*, courses.course_name
     FROM students
     LEFT JOIN courses ON students.course_id = courses.id"
);
$students = $stmt->fetchAll();
?>

<h2>Manage Students</h2>

<!-- ADD STUDENT FORM -->
<form method="post">
    <input type="text" name="name" placeholder="Student Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="roll" placeholder="Roll Number" required>

    <select name="course_id" required>
        <option value="">Select Course</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['id']; ?>">
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" name="add_student">Add Student</button>
</form>

<br>

<!-- STUDENT LIST -->
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Roll No</th>
        <th>Course</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($students as $student): ?>
    <tr>
        <td><?php echo htmlspecialchars($student['id']); ?></td>
        <td><?php echo htmlspecialchars($student['name']); ?></td>
        <td><?php echo htmlspecialchars($student['email']); ?></td>
        <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
        <td><?php echo htmlspecialchars($student['course_name']); ?></td>
        <td>
            <a href="students.php?edit=<?php echo $student['id']; ?>">Edit</a> |
            <a href="students.php?delete=<?php echo $student['id']; ?>"
               onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
// ------------------------
// EDIT STUDENT FORM
// ------------------------
if (isset($_GET['edit'])):
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
?>

<hr>

<h3>Edit Student</h3>

<form method="post">
    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

    <input type="text" name="name"
           value="<?php echo htmlspecialchars($student['name']); ?>" required>

    <input type="email" name="email"
           value="<?php echo htmlspecialchars($student['email']); ?>" required>

    <input type="text" name="roll"
           value="<?php echo htmlspecialchars($student['roll_number']); ?>" required>

    <select name="course_id" required>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['id']; ?>"
                <?php if ($course['id'] == $student['course_id']) echo "selected"; ?>>
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" name="update_student">Update Student</button>
</form>

<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>
