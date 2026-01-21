<?php
require_once "../includes/header.php";
require_once "../config/db.php";

// ------------------------
// ADD COURSE
// ------------------------
if (isset($_POST['add_course'])) {
    $course_name = $_POST['course_name'];

    if (!empty($course_name)) {
        $stmt = $pdo->prepare("INSERT INTO courses (course_name) VALUES (?)");
        $stmt->execute([$course_name]);
    }
}

// ------------------------
// DELETE COURSE
// ------------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$id]);
}

// ------------------------
// UPDATE COURSE
// ------------------------
if (isset($_POST['update_course'])) {
    $id = $_POST['id'];
    $course_name = $_POST['course_name'];

    $stmt = $pdo->prepare("UPDATE courses SET course_name = ? WHERE id = ?");
    $stmt->execute([$course_name, $id]);
}

// ------------------------
// FETCH ALL COURSES
// ------------------------
$stmt = $pdo->query("SELECT * FROM courses");
$courses = $stmt->fetchAll();
?>

<h2>Manage Courses</h2>

<!-- ADD COURSE FORM -->
<form method="post">
    <input type="text" name="course_name" placeholder="Course Name" required>
    <button type="submit" name="add_course">Add Course</button>
</form>

<br>

<!-- COURSE LIST -->
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Course Name</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($courses as $course): ?>
    <tr>
        <td><?php echo htmlspecialchars($course['id']); ?></td>
        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
        <td>
            <a href="courses.php?edit=<?php echo $course['id']; ?>">Edit</a> |
            <a href="courses.php?delete=<?php echo $course['id']; ?>"
               onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
// ------------------------
// EDIT COURSE FORM
// ------------------------
if (isset($_GET['edit'])):
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$id]);
    $course = $stmt->fetch();
?>

<hr>

<h3>Edit Course</h3>

<form method="post">
    <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
    <input type="text" name="course_name"
           value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
    <button type="submit" name="update_course">Update Course</button>
</form>

<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>
