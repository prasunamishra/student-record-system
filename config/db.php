<?php
/* DATABASE */

// Detect environment
$isLocal = ($_SERVER['HTTP_HOST'] === 'localhost');

/* LOCAL */
if ($isLocal) {
    $host     = "localhost";
    $dbname   = "student_record_system";
    $username = "root";
    $password = "";
}
/* SERVER */
else {
    $host     = "localhost";
    $dbname   = "np03cs4s250105";
    $username = "np03cs4s250105";
    $password = "IBZ0FeE4gF";
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed.");
}
