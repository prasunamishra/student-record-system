<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";


/*FETCH COURSE FOR EDIT (POST)*/
$editCourse = null;

if (isset($_POST['edit_course']) && is_numeric($_POST['edit_id'])) {
    // Fetch course data by ID
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$_POST['edit_id']]);
    $editCourse = $stmt->fetch();

    if (!$editCourse) {
        setMessage('error', 'Course not found.');
        header("Location: courses.php");
        exit;
    }
}

/* ADD COURSE */
if (isset($_POST['add_course'])) {
    $course_name = trim($_POST['course_name']);

    if ($course_name === "") {
        setMessage('error', 'Course name is required.');
        header("Location: courses.php");
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO courses (course_name) VALUES (?)"
    );

    $stmt->execute([$course_name]);

    setMessage('success', 'Course added successfully.');
    header("Location: courses.php");
    exit;
}

/* UPDATE COURSE */
if (isset($_POST['update_course'])) {
    $id = $_POST['id'];
    $course_name = trim($_POST['course_name']);

    if ($course_name === "") {
        setMessage('error', 'Course name is required.');
        header("Location: courses.php");
        exit;
    }

    $stmt = $pdo->prepare(
        "UPDATE courses SET course_name = ? WHERE id = ?"
    );

    $stmt->execute([$course_name, $id]);

    setMessage('success', 'Course updated.');
    header("Location: courses.php");
    exit;
}

/* DELETE COURSE */
if (isset($_POST['delete_course']) && is_numeric($_POST['id'])) {
    $stmt = $pdo->prepare(
        "DELETE FROM courses WHERE id = ?"
    );

    $stmt->execute([$_POST['id']]);

    setMessage('success', 'Course deleted.');
    header("Location: courses.php");
    exit;
}

/* FETCH COURSES  */
$courses = $pdo->query(
    "SELECT * FROM courses"
)->fetchAll();
?>

<h2>Manage Courses</h2>

<!-- ADD / EDIT COURSE FORM -->
<form method="post">
    <input type="hidden" name="id"
           value="<?= $editCourse['id'] ?? '' ?>">

    <input type="text" name="course_name"
           placeholder="Course Name"
           value="<?= $editCourse['course_name'] ?? '' ?>"
           required>

    <button type="submit"
            name="<?= $editCourse ? 'update_course' : 'add_course' ?>">
        <?= $editCourse ? 'Update Course' : 'Add Course' ?>
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
        <td class="actions">
            <!-- EDIT (POST) -->
            <form method="post" style="display:inline;">
                <input type="hidden" name="edit_id"
                       value="<?= $c['id']; ?>">
                <button name="edit_course">
                    Edit
                </button>
            </form>

            <!-- DELETE -->
            <form method="post"
                  style="display:inline;"
                  onsubmit="return confirm('Delete this course?');">
                <input type="hidden" name="id"
                       value="<?= $c['id']; ?>">
                <button name="delete_course">
                    Delete
                </button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require_once "../includes/footer.php"; ?>
