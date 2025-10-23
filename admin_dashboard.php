
<?php
session_start();
//include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

// Optional authentication check
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Sharanbasva University</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
/* ---------- RESET ---------- */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#f5f7fa;color:#333;}

/* ---------- HEADER ---------- */
header{
    display:flex;justify-content:space-between;align-items:center;
    background:#06389d;color:#fff;padding:10px 20px;position:fixed;
    top:0;left:0;width:100%;z-index:1000;box-shadow:0 2px 8px rgba(0,0,0,0.2);
}
header h1{display:flex;align-items:center;font-size:20px;font-weight:600;}
header h1 img{width:40px;height:40px;border-radius:50%;margin-right:10px;}
header .toggle-btn{display:none;font-size:22px;cursor:pointer;}

/* ---------- SIDEBAR ---------- */
.sidebar{
    position:fixed;top:60px;left:0;width:220px;height:100%;
    background:#052b7b;color:#fff;padding-top:15px;transition:0.3s;
}
.sidebar ul{list-style:none;}
.sidebar ul li a{
    display:flex;align-items:center;padding:12px 20px;color:#fff;text-decoration:none;
    transition:0.3s;font-size:15px;font-weight:500;
}
.sidebar ul li a:hover{background:#001f5c;border-left:4px solid #fff;}
.sidebar ul li a i{margin-right:10px;}

/* ---------- MAIN CONTENT ---------- */
.main-content{
    margin-left:220px;padding:80px 20px;transition:0.3s;
}
.fade-in{animation:fadeIn 1s ease;}
@keyframes fadeIn{0%{opacity:0;transform:translateY(20px);}100%{opacity:1;transform:translateY(0);}}

/* ---------- BUTTONS ---------- */
.btn{
    display:inline-block;background:#06389d;color:#fff;border:none;
    padding:12px 20px;border-radius:25px;cursor:pointer;
    font-size:15px;margin:10px;text-align:center;text-decoration:none;
    transition:background 0.3s;
}
.btn:hover{background:#041e5c;}

/* ---------- RESPONSIVE ---------- */
@media(max-width:768px){
    .sidebar{left:-220px;}
    .sidebar.active{left:0;}
    header .toggle-btn{display:block;}
    .main-content{margin-left:0;}
}
</style>
</head>
<body>

<header>
    <h1><img src="../images/sblogo.jpeg" alt="Logo"> Admin Panel - Sharanbasva University</h1>
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

<div class="main-content fade-in">
    <h2 style="color:#06389d;margin-bottom:10px;text-align:center;">Welcome Admin 👋</h2>
    <p style="text-align:center;margin-bottom:30px;">Manage faculty, subjects, and students easily from here.</p>
    <div style="display:flex;flex-wrap:wrap;justify-content:center;">
        <a href="add_faculty.php" class="btn"><i class="fa fa-user-plus"></i> Add Faculty</a>
        <a href="manage_subjects.php" class="btn"><i class="fa fa-book"></i> Manage Subjects</a>
        <a href="view_students.php" class="btn"><i class="fa fa-users"></i> View Students</a>
        <a href="view_faculty.php" class="btn"><i class="fa fa-chalkboard-teacher"></i> View Faculty</a>
    </div>
</div>

<script>
function toggleSidebar(){
    document.getElementById("sidebar").classList.toggle("active");
}
</script>
</body>
</html>
