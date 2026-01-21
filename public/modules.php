<?php
require_once "../includes/header.php";
require_once "../config/db.php";

// ------------------------
// ADD MODULE
// ------------------------
if (isset($_POST['add_module'])) {
    $module_name = $_POST['module_name'];
    $course_id = $_POST['course_id'];

    if ($module_name && $course_id) {
        $stmt = $pdo->prepare(
            "INSERT INTO modules (module_name, course_id)
             VALUES (?, ?)"
        );
        $stmt->execute([$module_name, $course_id]);
    }
}

// ------------------------
// DELETE MODULE
// ------------------------
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM modules WHERE id = ?");
    $stmt->execute([$id]);
}

// ------------------------
// UPDATE MODULE
// ------------------------
if (isset($_POST['update_module'])) {
    $id = $_POST['id'];
    $module_name = $_POST['module_name'];
    $course_id = $_POST['course_id'];

    $stmt = $pdo->prepare(
        "UPDATE modules
         SET module_name = ?, course_id = ?
         WHERE id = ?"
    );
    $stmt->execute([$module_name, $course_id, $id]);
}

// ------------------------
// FETCH COURSES
// ------------------------
$courses = $pdo->query("SELECT * FROM courses")->fetchAll();

// ------------------------
// FETCH MODULES
// ------------------------
$stmt = $pdo->query(
    "SELECT modules.*, courses.course_name
     FROM modules
     JOIN courses ON modules.course_id = courses.id"
);
$modules = $stmt->fetchAll();
?>

<h2>Manage Modules</h2>

<!-- ADD MODULE FORM -->
<form method="post">
    <input type="text" name="module_name" placeholder="Module Name" required>

    <select name="course_id" required>
        <option value="">Select Course</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['id']; ?>">
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" name="add_module">Add Module</button>
</form>

<br>

<!-- MODULE LIST -->
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Module Name</th>
        <th>Course</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($modules as $module): ?>
    <tr>
        <td><?php echo htmlspecialchars($module['id']); ?></td>
        <td><?php echo htmlspecialchars($module['module_name']); ?></td>
        <td><?php echo htmlspecialchars($module['course_name']); ?></td>
        <td>
            <a href="modules.php?edit=<?php echo $module['id']; ?>">Edit</a> |
            <a href="modules.php?delete=<?php echo $module['id']; ?>"
               onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
// ------------------------
// EDIT MODULE FORM
// ------------------------
if (isset($_GET['edit'])):
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
    $stmt->execute([$id]);
    $module = $stmt->fetch();
?>

<hr>

<h3>Edit Module</h3>

<form method="post">
    <input type="hidden" name="id" value="<?php echo $module['id']; ?>">

    <input type="text" name="module_name"
           value="<?php echo htmlspecialchars($module['module_name']); ?>" required>

    <select name="course_id" required>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['id']; ?>"
                <?php if ($course['id'] == $module['course_id']) echo "selected"; ?>>
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" name="update_module">Update Module</button>
</form>

<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>
