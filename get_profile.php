<?php
session_start(); //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

if (!isset($_SESSION['usn'])) {
    echo "Session expired. Please login again.";
    exit;
}
$usn = $_SESSION['usn'];

// DB Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student info
$stmt = $conn->prepare("SELECT full_name, email, phone, usn, semester FROM s_info WHERE usn = ?");
$stmt->bind_param("s", $usn);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    echo "<div style='text-align:left;font-size:16px;line-height:1.8;'>
            <p><strong>Name:</strong> " . htmlspecialchars($row['full_name']) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>
            <p><strong>Phone:</strong> " . htmlspecialchars($row['phone']) . "</p>
            <p><strong>USN:</strong> " . htmlspecialchars($row['usn']) . "</p>
            <p><strong>Semester:</strong> " . htmlspecialchars($row['semester']) . "</p>
          </div>";
} else {
    echo "No profile data found.";
}

$stmt->close();
$conn->close();
?>
