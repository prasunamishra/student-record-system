<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";

/* =========================
   ADD GRADE (VALIDATION)
========================= */
if (isset($_POST['add_grade'])) {
    $student_id = $_POST['student_id'];
    $module_id  = $_POST['module_id'];
    $grade      = strtoupper(trim($_POST['grade']));

    $allowedGrades = ['A', 'B', 'C', 'D', 'E' , 'F'];

    if (!in_array($grade, $allowedGrades)) {
        setFlash(
            'error',
            'Invalid grade. Allowed grades: A, B, C, D, E, F.'
        );
        header("Location: grades.php");
        exit;
    }

    // 🔎 Prevent duplicate grade for same student & module
    $check = $pdo->prepare(
        "SELECT id FROM grades
         WHERE student_id = ? AND module_id = ?"
    );
    $check->execute([$student_id, $module_id]);

    if ($check->fetch()) {
        setFlash(
            'error',
            'Grade already exists for this student and module.'
        );
        header("Location: grades.php");
        exit;
    }

    // ✅ Insert grade
    $stmt = $pdo->prepare(
        "INSERT INTO grades (student_id, module_id, grade)
         VALUES (?, ?, ?)"
    );
    $stmt->execute([$student_id, $module_id, $grade]);

    setFlash('success', 'Grade added successfully.');
    header("Location: grades.php");
    exit;
}

/* =========================
   DELETE GRADE
========================= */
if (isset($_POST['delete_grade'])) {
    $pdo->prepare("DELETE FROM grades WHERE id = ?")
        ->execute([$_POST['id']]);

    setFlash('success', 'Grade deleted.');
    header("Location: grades.php");
    exit;
}

/* =========================
   FETCH DATA
========================= */
$students = $pdo->query(
    "SELECT id, name FROM students"
)->fetchAll();

$modules = $pdo->query(
    "SELECT id, module_name FROM modules"
)->fetchAll();

$records = $pdo->query(
    "SELECT grades.id,
            students.name AS student,
            modules.module_name,
            grades.grade
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

    <input name="grade"
           placeholder="Grade (A-F)"
           maxlength="1"
           required>

    <button name="add_grade">Add Grade</button>
</form>

<table>
<tr>
    <th>Student</th>
    <th>Module</th>
    <th>Grade</th>
    <th>Action</th>
</tr>

<?php if (!$records): ?>
<tr>
    <td colspan="4">No grades recorded.</td>
</tr>
<?php endif; ?>

<?php foreach ($records as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['student']) ?></td>
    <td><?= htmlspecialchars($r['module_name']) ?></td>
    <td><?= htmlspecialchars($r['grade']) ?></td>
    <td>
        <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">
            <button name="delete_grade"
                    onclick="return confirm('Delete this grade?')">
                Delete
            </button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php require_once "../includes/footer.php"; ?>
