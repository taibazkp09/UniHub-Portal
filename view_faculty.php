<?php
//include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$faculty_data = [];
$result = $conn->query("SELECT * FROM faculty_info ORDER BY full_name ASC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $faculty_data[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Faculty | Admin Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#f4f7fc;color:#333;}
header{background:#06389d;color:#fff;display:flex;justify-content:space-between;
align-items:center;padding:10px 20px;position:fixed;width:100%;top:0;z-index:1000;}
header h1{font-size:20px;display:flex;align-items:center;}
header h1 img{height:40px;width:40px;border-radius:50%;margin-right:10px;}
.toggle-btn{display:none;font-size:22px;cursor:pointer;}
.sidebar{position:fixed;top:60px;left:0;width:220px;height:100%;background:#052b7b;
padding-top:20px;transition:0.3s;overflow-y:auto;z-index:999;}
.sidebar ul{list-style:none;}
.sidebar ul li a{display:flex;align-items:center;color:#fff;text-decoration:none;padding:12px 20px;
transition:0.3s;font-size:15px;}
.sidebar ul li a:hover{background:#001f5c;border-left:4px solid #fff;}
.main-content{margin-left:220px;padding:80px 20px;transition:0.3s;}
h2{text-align:center;color:#06389d;margin-bottom:20px;}
.faculty-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;}
.faculty-card{
    background:#fff;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.1);
    padding:20px;transition:0.3s;animation:fadeIn 0.8s ease;
}
.faculty-card:hover{transform:translateY(-5px);box-shadow:0 6px 12px rgba(0,0,0,0.15);}
.faculty-card h3{margin-bottom:10px;color:#06389d;font-size:18px;}
.faculty-card p{margin:6px 0;font-size:14px;color:#333;}
.no-data{text-align:center;font-size:18px;margin-top:20px;color:#777;}
@keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
@media(max-width:768px){
.toggle-btn{display:block;}
.sidebar{width:0;overflow:hidden;}
.sidebar.active{width:220px;}
.main-content{margin-left:0;padding:80px 15px;}
}
</style>
</head>
<body>
<header>
    <h1><img src="../images/sblogo.jpeg" alt="Logo">Sharanbasva University</h1>
    <span class="toggle-btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></span>
</header>
<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="admin_dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
        <li><a href="add_faculty.php"><i class="fa fa-user-plus"></i> Add Faculty</a></li>
        <li><a href="add_subjects.php"><i class="fa fa-book"></i> Manage Subjects</a></li>
        <li><a href="view_students.php"><i class="fa fa-users"></i> View Students</a></li>
        <li><a href="about_faculty.php"><i class="fa fa-chalkboard-teacher"></i>About Faculty</a></li>
           <li><a href="about_achivements.php"><i class="fa fa-trophy"></i>Achievements</a></li>
               <li><a href="add_contact.php"><i class="fa-solid fa-address-book"></i>Add Contacts</a></li>
        <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
<div class="main-content">
    <h2>Faculty List</h2>
    <div class="faculty-container">
        <?php if (empty($faculty_data)): ?>
            <p class="no-data">No faculty members found.</p>
        <?php else: ?>
            <?php foreach ($faculty_data as $faculty): ?>
                <div class="faculty-card">
                    <h3><?= htmlspecialchars($faculty['full_name']) ?></h3>
                    <p><strong>Email:</strong> <?= htmlspecialchars($faculty['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($faculty['phone']) ?></p>
                    <p><strong>Subject:</strong> <?= htmlspecialchars($faculty['subject']) ?></p>
                    <p><strong>Semester:</strong> <?= htmlspecialchars($faculty['semester']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script>
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('active');}
</script>
</body>
</html>
