<?php
require_once "auth.php";
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Student Record System</title>
</head>
<body>

<h3>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h3>

<nav>
    <a href="index.php">Dashboard</a> |
    <a href="courses.php">Courses</a> |
    <a href="students.php">Students</a> |
    <a href="modules.php">Modules</a> |
    <a href="grades.php">Grades</a> |
    <a href="attendance.php">Attendance</a> |
    <a href="search.php">Search</a> |
    <a href="logout.php">Logout</a>
</nav>

<hr>
