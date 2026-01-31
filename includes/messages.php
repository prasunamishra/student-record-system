<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* SET MESSAGE */
function setMessage($type, $message) {
    $_SESSION['message'] = [
        'type' => $type,      // success | error
        'message' => $message
    ];
}

/* DISPLAY MESSAGE */
function showMessage() {
    if (!empty($_SESSION['message'])) {
        $type = $_SESSION['message']['type'];
        $message = $_SESSION['message']['message'];

        echo "<div class='message {$type}'>";
        echo htmlspecialchars($message);
        echo "</div>";

        // Remove after showing once
        unset($_SESSION['message']);
    }
}
