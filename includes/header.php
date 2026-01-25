<?php
require_once __DIR__ . "/flash.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Record Management System</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header>
    <h1>Student Record Management System</h1>
    <nav>
        <a href="students.php">Students</a>
        <a href="courses.php">Courses</a>
        <a href="modules.php">Modules</a>
        <a href="grades.php">Grades</a>
        <a href="attendance.php">Attendance</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>

<!-- FLASH MESSAGE -->
<?php showFlash(); ?>
