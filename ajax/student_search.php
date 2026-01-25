<?php
require_once "../config/db.php";

$query = trim($_GET['q'] ?? '');

if ($query === '') {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT students.name, students.roll_number, courses.course_name
    FROM students
    LEFT JOIN courses ON students.course_id = courses.id
    WHERE students.name LIKE ?
       OR students.roll_number LIKE ?
       OR courses.course_name LIKE ?
    LIMIT 10
");

$like = "%$query%";
$stmt->execute([$like, $like, $like]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
