<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";

$editModule = null;

/* ===============================
   FETCH MODULE FOR EDIT (POST)
================================ */
if (isset($_POST['edit_module']) && is_numeric($_POST['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
    $stmt->execute([$_POST['edit_id']]);
    $editModule = $stmt->fetch();

    if (!$editModule) {
        setFlash('error', 'Module not found.');
        header("Location: modules.php");
        exit;
    }
}

/* ===============================
   ADD MODULE
================================ */
if (isset($_POST['add_module'])) {

    if (empty($_POST['module_name']) || empty($_POST['course_id'])) {
        setFlash('error', 'Module name and course are required.');
        header("Location: modules.php");
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO modules (module_name, course_id)
         VALUES (?, ?)"
    );
    $stmt->execute([
        $_POST['module_name'],
        $_POST['course_id']
    ]);

    setFlash('success', 'Module added successfully.');
    header("Location: modules.php");
    exit;
}

/* ===============================
   UPDATE MODULE
================================ */
if (isset($_POST['update_module'])) {

    if (empty($_POST['module_name']) || empty($_POST['course_id'])) {
        setFlash('error', 'Module name and course are required.');
        header("Location: modules.php");
        exit;
    }

    $stmt = $pdo->prepare(
        "UPDATE modules
         SET module_name = ?, course_id = ?
         WHERE id = ?"
    );
    $stmt->execute([
        $_POST['module_name'],
        $_POST['course_id'],
        $_POST['id']
    ]);

    setFlash('success', 'Module updated successfully.');
    header("Location: modules.php");
    exit;
}

/* ===============================
   DELETE MODULE
================================ */
if (isset($_POST['delete_module']) && is_numeric($_POST['id'])) {
    $pdo->prepare("DELETE FROM modules WHERE id = ?")
        ->execute([$_POST['id']]);

    setFlash('success', 'Module deleted.');
    header("Location: modules.php");
    exit;
}

/* ===============================
   FETCH DATA
================================ */
$courses = $pdo->query("SELECT * FROM courses")->fetchAll();

$modules = $pdo->query(
    "SELECT modules.*, courses.course_name
     FROM modules
     JOIN courses ON modules.course_id = courses.id
     ORDER BY courses.course_name"
)->fetchAll();
?>

<h2>Manage Modules</h2>

<!-- ADD / EDIT MODULE FORM -->
<form method="post">
    <input type="hidden" name="id"
           value="<?= $editModule['id'] ?? '' ?>">

    <input type="text"
           name="module_name"
           placeholder="Module Name"
           value="<?= htmlspecialchars($editModule['module_name'] ?? '') ?>"
           required>

    <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php foreach ($courses as $c): ?>
            <option value="<?= $c['id'] ?>"
                <?= ($editModule && $editModule['course_id'] == $c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['course_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button name="<?= $editModule ? 'update_module' : 'add_module' ?>">
        <?= $editModule ? 'Update Module' : 'Add Module' ?>
    </button>
</form>

<hr>

<table>
    <tr>
        <th>Module</th>
        <th>Course</th>
        <th>Action</th>
    </tr>

    <?php foreach ($modules as $m): ?>
        <tr>
            <td><?= htmlspecialchars($m['module_name']) ?></td>
            <td><?= htmlspecialchars($m['course_name']) ?></td>
            <td>
                <!-- EDIT (POST) -->
                <form method="post" style="display:inline;">
                    <input type="hidden" name="edit_id"
                           value="<?= $m['id'] ?>">
                    <button name="edit_module">Edit</button>
                </form>

                <!-- DELETE -->
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id"
                           value="<?= $m['id'] ?>">
                    <button name="delete_module"
                            onclick="return confirm('Delete this module?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php require_once "../includes/footer.php"; ?>
