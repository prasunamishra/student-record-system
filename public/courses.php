<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";

/* =========================
   ADD COURSE
========================= */
if (isset($_POST['add_course'])) {
    $course_name = trim($_POST['course_name']);

    if ($course_name === "") {
        setFlash('error', 'Course name is required.');
        header("Location: courses.php");
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO courses (course_name) VALUES (?)"
    );

    if ($stmt->execute([$course_name])) {
        setFlash('success', 'Course added successfully.');
    } else {
        setFlash('error', 'Failed to add course.');
    }

    header("Location: courses.php");
    exit;
}

/* =========================
   DELETE COURSE
========================= */
if (isset($_POST['delete_course'])) {
    $id = $_POST['id'];

    $stmt = $pdo->prepare(
        "DELETE FROM courses WHERE id = ?"
    );

    if ($stmt->execute([$id])) {
        setFlash('success', 'Course deleted.');
    } else {
        setFlash('error', 'Delete failed.');
    }

    header("Location: courses.php");
    exit;
}

/* =========================
   UPDATE COURSE
========================= */
if (isset($_POST['update_course'])) {
    $id = $_POST['id'];
    $course_name = trim($_POST['course_name']);

    if ($course_name === "") {
        setFlash('error', 'Course name is required.');
        header("Location: courses.php");
        exit;
    }

    $stmt = $pdo->prepare(
        "UPDATE courses SET course_name = ? WHERE id = ?"
    );

    if ($stmt->execute([$course_name, $id])) {
        setFlash('success', 'Course updated.');
    } else {
        setFlash('error', 'Update failed.');
    }

    header("Location: courses.php");
    exit;
}

/* =========================
   FETCH COURSES
========================= */
$courses = $pdo->query(
    "SELECT * FROM courses"
)->fetchAll();
?>

<h2>Manage Courses</h2>

<!-- ADD COURSE -->
<form method="post">
    <input type="text" name="course_name"
           placeholder="Course Name" required>
    <button type="submit" name="add_course">
        Add Course
    </button>
</form>

<br>

<table>
    <tr>
        <th>ID</th>
        <th>Course Name</th>
        <th>Action</th>
    </tr>

    <?php if (!$courses): ?>
        <tr>
            <td colspan="3">No courses found.</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($courses as $c): ?>
    <tr>
        <td><?= htmlspecialchars($c['id']); ?></td>
        <td><?= htmlspecialchars($c['course_name']); ?></td>
        <td>
            <a href="courses.php?edit=<?= $c['id']; ?>">Edit</a>

            <form method="post" style="display:inline;"
                  onsubmit="return confirm('Delete this course?');">
                <input type="hidden" name="id"
                       value="<?= $c['id']; ?>">
                <button type="submit"
                        name="delete_course"
                        style="background:none;border:none;
                               color:#e74c3c;cursor:pointer;">
                    Delete
                </button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
/* =========================
   EDIT COURSE FORM
========================= */
if (isset($_GET['edit'])):
    $stmt = $pdo->prepare(
        "SELECT * FROM courses WHERE id = ?"
    );
    $stmt->execute([$_GET['edit']]);
    $course = $stmt->fetch();

    if ($course):
?>

<hr>

<h3>Edit Course</h3>

<form method="post">
    <input type="hidden" name="id"
           value="<?= $course['id']; ?>">

    <input type="text" name="course_name"
           value="<?= htmlspecialchars($course['course_name']); ?>"
           required>

    <button type="submit" name="update_course">
        Update Course
    </button>
</form>

<?php endif; endif; ?>

<?php require_once "../includes/footer.php"; ?>
