<?php
session_start();
// Hancurkan semua data memori
session_destroy();

// Kembalikan ke halaman login
header("Location: ../login.php");
exit;
?>