<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/flash.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Record System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="sidebar">
    <h2 class="logo">Student Record Management System</h2>

    <nav>
        <a href="index.php">Dashboard</a>
        <a href="students.php">Students</a>
        <a href="courses.php">Courses</a>
        <a href="modules.php">Modules</a>
        <a href="grades.php">Grades</a>
        <a href="attendance.php">Attendance</a>
        <a href="logout.php" class="logout">Logout</a>
    </nav>
</header>

<main class="main-content">
<?php showFlash(); ?>
