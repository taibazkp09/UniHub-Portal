<?php //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

$conn = new mysqli("localhost", "root", "", "clg", 3307);
$semester = $_GET['semester'] ?? '';

$response = [];
if (!empty($semester)) {
    $stmt = $conn->prepare("SELECT subject_name FROM subjects WHERE semester=?");
    $stmt->bind_param("s", $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc()){
        $response[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($response);
?>
