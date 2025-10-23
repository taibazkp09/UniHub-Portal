<?php //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

session_start();
if (!isset($_SESSION['usn'])) {
    header("Location: student_login.php");
    exit();
}

$semester = $_SESSION['semester'];
$type = $_GET['type'] ?? 'notes';

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

$subQuery = $conn->prepare("SELECT subject_code, subject_name FROM subjects WHERE semester=?");
$subQuery->bind_param("s", $semester);
$subQuery->execute();
$subjects = $subQuery->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo ucfirst($type); ?> Resources</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css">
<style>
body {margin:0;font-family:'Segoe UI',sans-serif;background:#f4f6fc;color:#333;}
nav {background:#06389d;color:white;display:flex;justify-content:space-between;align-items:center;padding:12px 20px;}
nav .logo {display:flex;align-items:center;gap:10px;}
nav .logo img {height:35px;border-radius:50%;}
nav .logo h2 {font-size:18px;}
nav .btn-back {background:#ffcc00;color:#06389d;text-decoration:none;padding:8px 14px;border-radius:6px;font-weight:bold;}
.container {padding:20px;max-width:1100px;margin:auto;}
.container h1 {text-align:center;color:#06389d;margin-bottom:15px;font-size:26px;}
.table-container {background:white;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);overflow:hidden;margin-top:20px;}
table {width:100%;border-collapse:collapse;}
th, td {padding:14px;border-bottom:1px solid #ddd;text-align:left;font-size:15px;vertical-align:top;}
th {background:#06389d;color:white;}
tr:hover {background:#f9f9f9;}
/* Module column vertical layout */
.module-container {display:flex;flex-direction:column;gap:6px;}
.module-btn {background:#06389d;color:white;padding:6px 10px;border-radius:5px;text-decoration:none;font-size:13px;transition:0.3s;width:fit-content;}
.module-btn:hover {background:#04276e;}
.status {color:#999;font-style:italic;font-size:13px;}
/* Responsive */
@media(max-width:768px){
  table, thead, tbody, th, td, tr {display:block;width:100%;}
  thead {display:none;}
  tr {margin-bottom:15px;background:white;border-radius:8px;padding:10px;}
  td {border:none;display:flex;flex-direction:column;align-items:flex-start;padding:8px;font-size:14px;}
  td::before {content:attr(data-label);font-weight:bold;color:#06389d;margin-bottom:4px;}
}
</style>
</head>
<body>
<nav>
    <div class="logo">
        <img src="../sblogo.jpeg" alt="Logo">
        <h2>Sharanbasva University</h2>
    </div>
    <a href="student_dashboard.php" class="btn-back"><i class="ri-arrow-left-line"></i> Back</a>
</nav>

<div class="container">
    <h1><?php echo ucfirst($type); ?> Resources</h1>
    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th><?php echo ($type == 'notes') ? 'Modules' : 'File'; ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($subjects->num_rows > 0) {
                while ($sub = $subjects->fetch_assoc()) {
                    $subject = $sub['subject_name'];
                    $table = ($type == 'notes') ? 'notes' : (($type == 'syllabus') ? 'syllabus' : 'pyqs');

                    echo "<tr>
                    <td data-label='Subject Code'>{$sub['subject_code']}</td>
                    <td data-label='Subject Name'>{$sub['subject_name']}</td>
                    <td data-label='".(($type=='notes')?'Modules':'File')."'>";

                    if ($type == 'notes') {
                        echo "<div class='module-container'>";
                        $modules = ['Module 1','Module 2','Module 3','Module 4','Module 5'];
                        foreach ($modules as $mod) {
                            $fileQuery = $conn->prepare("SELECT file_name FROM notes WHERE semester=? AND subject=? AND module=? LIMIT 1");
                            $fileQuery->bind_param("sss", $semester, $subject, $mod);
                            $fileQuery->execute();
                            $fileResult = $fileQuery->get_result();

                            if ($fileResult->num_rows > 0) {
                                $file = $fileResult->fetch_assoc()['file_name'];
                                echo "<a class='module-btn' href='../faculty_panel/uploads/$file' download>$mod</a>";

                            } else {
                                echo "<span class='status'>$mod: Not Uploaded</span>";
                            }
                        }
                        echo "</div>";
                    } else {
                        $fileQuery = $conn->prepare("SELECT file_name FROM $table WHERE semester=? AND subject=? LIMIT 1");
                        $fileQuery->bind_param("ss", $semester, $subject);
                        $fileQuery->execute();
                        $fileResult = $fileQuery->get_result();

                        if ($fileResult->num_rows > 0) {
                            $file = $fileResult->fetch_assoc()['file_name'];
                            echo "<a class='module-btn' href='../faculty_panel/uploads/$file' download>Download</a>";

                            
                        } else {
                            echo "<span class='status'>File Not Uploaded Yet</span>";
                        }
                    }

                    echo "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No subjects found.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
