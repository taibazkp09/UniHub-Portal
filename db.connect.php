<?php
// Detect environment
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    // Localhost settings
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'clg';
    $port = 3307; // your XAMPP port
} else {
    // InfinityFree settings
    $host = 'sql104.infinityfree.com';
    $user = 'if0_39491677';
    $pass = 'gK3j0t1BC5nZC0';
    $db   = 'if0_39491677_epiz_0105_clg';
    $port = 3306;
}

// Create connection
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
