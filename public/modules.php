<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";

$editModule = null;

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editModule = $stmt->fetch();
}

if (isset($_POST['add_module'])) {
    if ($_POST['module_name'] === "") {
        setFlash('error', 'Module name required.');
    } else {
        $pdo->prepare(
            "INSERT INTO modules (module_name, course_id)
             VALUES (?, ?)"
        )->execute([$_POST['module_name'], $_POST['course_id']]);

        setFlash('success', 'Module added.');
    }
    header("Location: modules.php");
    exit;
}

if (isset($_POST['delete_module'])) {
    $pdo->prepare("DELETE FROM modules WHERE id=?")
        ->execute([$_POST['id']]);

    setFlash('success', 'Module deleted.');
    header("Location: modules.php");
    exit;
}

$courses = $pdo->query("SELECT * FROM courses")->fetchAll();
$modules = $pdo->query(
    "SELECT modules.*, courses.course_name
     FROM modules JOIN courses ON modules.course_id = courses.id"
)->fetchAll();
?>

<h2>Manage Modules</h2>

<form method="post">
    <input type="hidden" name="id"
           value="<?= $editModule['id'] ?? '' ?>">

    <input name="module_name"
           placeholder="Module Name"
           value="<?= $editModule['module_name'] ?? '' ?>"
           required>

    <button name="<?= $editModule ? 'update_module' : 'add_module' ?>">
        <?= $editModule ? 'Update Module' : 'Add Module' ?>
    </button>
</form>


<table>
<tr><th>Module</th><th>Course</th><th>Action</th></tr>
<?php foreach ($modules as $m): ?>
<tr>
<td><?= htmlspecialchars($m['module_name']) ?></td>
<td><?= htmlspecialchars($m['course_name']) ?></td>
<td>
<form method="post">
    <a href="modules.php?edit=<?= $m['id'] ?>">Edit</a>

<input type="hidden" name="id" value="<?= $m['id'] ?>">
<button name="delete_module">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>

<?php require_once "../includes/footer.php"; ?>
