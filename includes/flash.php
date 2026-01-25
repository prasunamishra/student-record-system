<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   SET FLASH MESSAGE
========================= */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,      // success | error
        'message' => $message
    ];
}

/* =========================
   DISPLAY FLASH MESSAGE
========================= */
function showFlash() {
    if (!empty($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];

        echo "<div class='flash {$type}'>";
        echo htmlspecialchars($message);
        echo "</div>";

        // Remove after showing once
        unset($_SESSION['flash']);
    }
}
