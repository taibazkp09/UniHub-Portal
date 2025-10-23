<?php //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

session_start();
if (!isset($_SESSION['usn'])) exit;

$host='localhost';$user='root';$pass='';$db='clg';
$conn=new mysqli($host,$user,$pass,$db,3307);
$usn=$_SESSION['usn'];
$type=$_GET['type'];
$tables=['notes','syllabus','pyqs'];
if(!in_array($type,$tables)) exit;
$student=$conn->query("SELECT semester FROM s_info WHERE usn='$usn'")->fetch_assoc();
$semester=$student['semester'];
$sql="SELECT * FROM $type WHERE semester='$semester'";
$res=$conn->query($sql);
if($res->num_rows>0){
    while($row=$res->fetch_assoc()){
        echo "<div class='subject-card'>
              <h4>{$row['subject']}</h4>
              <a href='uploads/{$row['file_name']}' download>Download</a>
              </div>";
    }
}else{
    echo "<p>No resources found.</p>";
}
?>
