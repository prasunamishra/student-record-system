<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";

/* =========================
   FETCH STUDENT FOR EDIT
========================= */
$editStudent = null;

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editStudent = $stmt->fetch();
}

/* =========================
   ADD STUDENT
========================= */
if (isset($_POST['add_student'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $roll  = trim($_POST['roll']);
    $course_id = $_POST['course_id'];

    if ($name === "" || $email === "" || $roll === "") {
        setFlash('error', 'All fields are required.');
        header("Location: students.php");
        exit;
    }

    // Check duplicate email or roll
    $check = $pdo->prepare(
        "SELECT id FROM students WHERE email = ? OR roll_number = ?"
    );
    $check->execute([$email, $roll]);

    if ($check->fetch()) {
        setFlash('error', 'Email or Roll Number already exists.');
        header("Location: students.php");
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO students (name, email, roll_number, course_id)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$name, $email, $roll, $course_id]);

    setFlash('success', 'Student added successfully.');
    header("Location: students.php");
    exit;
}

/* =========================
   UPDATE STUDENT
========================= */
if (isset($_POST['update_student'])) {
    $id    = $_POST['id'];
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $roll  = trim($_POST['roll']);
    $course_id = $_POST['course_id'];

    if ($name === "" || $email === "" || $roll === "") {
        setFlash('error', 'All fields are required.');
        header("Location: students.php");
        exit;
    }

    // Check duplicates (exclude current)
    $check = $pdo->prepare(
        "SELECT id FROM students
         WHERE (email = ? OR roll_number = ?)
         AND id != ?"
    );
    $check->execute([$email, $roll, $id]);

    if ($check->fetch()) {
        setFlash('error', 'Email or Roll Number already used.');
        header("Location: students.php");
        exit;
    }

    $stmt = $pdo->prepare(
        "UPDATE students
         SET name = ?, email = ?, roll_number = ?, course_id = ?
         WHERE id = ?"
    );
    $stmt->execute([$name, $email, $roll, $course_id, $id]);

    setFlash('success', 'Student updated successfully.');
    header("Location: students.php");
    exit;
}

/* =========================
   DELETE STUDENT
========================= */
if (isset($_POST['delete_student'])) {
    $pdo->prepare("DELETE FROM students WHERE id = ?")
        ->execute([$_POST['id']]);

    setFlash('success', 'Student deleted.');
    header("Location: students.php");
    exit;
}

/* =========================
   FETCH DATA
========================= */
$courses = $pdo->query("SELECT * FROM courses")->fetchAll();

$students = $pdo->query(
    "SELECT students.*, courses.course_name
     FROM students
     LEFT JOIN courses ON students.course_id = courses.id"
)->fetchAll();
?>

<h2>Manage Students</h2>

<!-- STUDENT FORM -->
<form method="post">
    <input type="hidden" name="id"
           value="<?= $editStudent['id'] ?? '' ?>">

    <input type="text" name="name"
           placeholder="Student Name"
           value="<?= $editStudent['name'] ?? '' ?>"
           required>

    <input type="email" name="email"
           placeholder="Email"
           value="<?= $editStudent['email'] ?? '' ?>"
           required>

    <input type="text" name="roll"
           placeholder="Roll Number"
           value="<?= $editStudent['roll_number'] ?? '' ?>"
           required>

    <select name="course_id" required>
        <option value="">Select Course</option>
        <?php foreach ($courses as $c): ?>
            <option value="<?= $c['id'] ?>"
                <?= ($editStudent && $editStudent['course_id'] == $c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['course_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit"
            name="<?= $editStudent ? 'update_student' : 'add_student' ?>">
        <?= $editStudent ? 'Update Student' : 'Add Student' ?>
    </button>
</form>

<hr>

<!-- LIVE SEARCH -->
<input type="text"
       id="studentSearch"
       placeholder="Search by name, roll or course"
       style="margin-bottom:15px; width:300px;">

<!-- STUDENT TABLE -->
<table id="studentTable">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Roll</th>
    <th>Course</th>
    <th>Action</th>
</tr>

<?php foreach ($students as $s): ?>
<tr>
    <td><?= htmlspecialchars($s['name']) ?></td>
    <td><?= htmlspecialchars($s['email']) ?></td>
    <td><?= htmlspecialchars($s['roll_number']) ?></td>
    <td><?= htmlspecialchars($s['course_name'] ?? '—') ?></td>
    <td>
        <a href="students.php?edit=<?= $s['id'] ?>">Edit</a>

        <form method="post" style="display:inline;">
            <input type="hidden" name="id"
                   value="<?= $s['id'] ?>">
            <button name="delete_student"
                    onclick="return confirm('Delete this student?')">
                Delete
            </button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<!-- LIVE SEARCH SCRIPT -->
<script>
document.getElementById("studentSearch").addEventListener("keyup", function () {
    const search = this.value.toLowerCase();
    const rows = document.querySelectorAll("#studentTable tr");

    rows.forEach((row, index) => {
        if (index === 0) return; // skip header
        row.style.display =
            row.innerText.toLowerCase().includes(search)
            ? "" : "none";
    });
});
</script>

<?php require_once "../includes/footer.php"; ?>
