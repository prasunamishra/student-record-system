<?php
require_once "../includes/header.php";
require_once "../config/db.php";

// ADD GRADE
if (isset($_POST['add_grade'])) {
    $student_id = $_POST['student_id'];
    $module_id = $_POST['module_id'];
    $grade = $_POST['grade'];

    if ($student_id && $module_id && $grade) {
        $stmt = $pdo->prepare(
            "INSERT INTO grades (student_id, module_id, grade)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$student_id, $module_id, $grade]);
    }
}

// DELETE GRADE
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
}

// UPDATE GRADE
if (isset($_POST['update_grade'])) {
    $stmt = $pdo->prepare(
        "UPDATE grades
         SET student_id = ?, module_id = ?, grade = ?
         WHERE id = ?"
    );
    $stmt->execute([
        $_POST['student_id'],
        $_POST['module_id'],
        $_POST['grade'],
        $_POST['id']
    ]);
}

// FETCH DATA
$students = $pdo->query("SELECT id, name FROM students")->fetchAll();
$modules = $pdo->query("SELECT id, module_name FROM modules")->fetchAll();

$grades = $pdo->query(
    "SELECT grades.id, students.name AS student,
            modules.module_name, grades.grade
     FROM grades
     JOIN students ON grades.student_id = students.id
     JOIN modules ON grades.module_id = modules.id"
)->fetchAll();
?>

<h2>Manage Grades</h2>

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

    <input type="text" name="grade" placeholder="Grade (A/B/C)" required>

    <button type="submit" name="add_grade">Add Grade</button>
</form>

<br>

<table>
    <tr>
        <th>Student</th>
        <th>Module</th>
        <th>Grade</th>
        <th>Action</th>
    </tr>

    <?php foreach ($grades as $g): ?>
    <tr>
        <td><?= htmlspecialchars($g['student']) ?></td>
        <td><?= htmlspecialchars($g['module_name']) ?></td>
        <td><?= htmlspecialchars($g['grade']) ?></td>
        <td>
            <a href="grades.php?delete=<?= $g['id'] ?>"
               onclick="return confirm('Delete this grade?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require_once "../includes/footer.php"; ?>
