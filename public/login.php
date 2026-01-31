<?php
session_start();

require_once "../config/db.php";
require_once "../includes/messages.php"; // <-- IMPORTANT

// Already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare(
        "SELECT id, username, password 
         FROM admins 
         WHERE username = ? 
         LIMIT 1"
    );
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {

        session_regenerate_id(true);

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];

        header("Location: index.php");
        exit;

    } else {
        setMessage('error', 'Invalid username or password');
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="login">

<h2>ADMIN LOGIN</h2>

<?php showMessage(); ?>

<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" required>

    <label>Password:</label><br>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
</form>

</body>
</html>
