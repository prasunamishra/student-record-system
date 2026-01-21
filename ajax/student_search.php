<?php
require_once "../config/db.php";

$query = $_GET['query'] ?? '';

$stmt = $pdo->prepare(
    "SELECT name FROM students WHERE name LIKE ? LIMIT 5"
);
$stmt->execute(["%$query%"]);

echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
