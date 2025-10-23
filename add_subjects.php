





<?php

//include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);

// ADD subject
if (isset($_POST['add_subject'])) {
    $semester = trim($_POST['semester']);
    $subject_code = strtoupper(trim($_POST['subject_code']));
    $subject_name = trim($_POST['subject_name']);

    if (!empty($semester) && !empty($subject_code) && !empty($subject_name)) {
        $stmt = $conn->prepare("INSERT INTO subjects (semester, subject_code, subject_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $semester, $subject_code, $subject_name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: add_subjects.php");
    exit();
}

// DELETE subject
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM subjects WHERE id=$id");
    header("Location: add_subjects.php");
    exit();
}

// UPDATE subject
if (isset($_POST['update_subject'])) {
    $id = $_POST['id'];
    $updated_code = strtoupper(trim($_POST['updated_code']));
    $updated_name = trim($_POST['updated_name']);

    if (!empty($updated_code) && !empty($updated_name)) {
        $conn->query("UPDATE subjects SET subject_code='$updated_code', subject_name='$updated_name' WHERE id=$id");
    }
    header("Location: manage_subjects.php");
    exit();
}

// FETCH subjects
$result = $conn->query("SELECT * FROM subjects ORDER BY semester, subject_code");
$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[$row['semester']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Subjects | Admin Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#f4f7fc;color:#333;}
header{
    background:#06389d;color:#fff;display:flex;justify-content:space-between;
    align-items:center;padding:10px 20px;position:fixed;width:100%;top:0;z-index:1000;
}
header h1{font-size:20px;display:flex;align-items:center;}
header h1 img{height:40px;width:40px;border-radius:50%;margin-right:10px;}
.toggle-btn{display:none;font-size:22px;cursor:pointer;}
.sidebar{
    position:fixed;top:60px;left:0;width:220px;height:100%;background:#052b7b;
    padding-top:20px;transition:0.3s;overflow-y:auto;z-index:999;
}
.sidebar ul{list-style:none;}
.sidebar ul li{margin:10px 0;}
.sidebar ul li a{
    display:flex;align-items:center;color:#fff;text-decoration:none;padding:12px 20px;
    transition:0.3s;font-size:15px;
}
.sidebar ul li a:hover{background:#001f5c;border-left:4px solid #fff;}
.sidebar ul li a i{margin-right:10px;}
.main-content{
    margin-left:220px;padding:80px 20px;transition:0.3s;
}
h2{text-align:center;color:#06389d;margin-bottom:20px;font-size:24px;}
form{
    background:#fff;padding:20px;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.1);
    margin-bottom:30px;display:flex;flex-wrap:wrap;gap:10px;animation:fadeIn 1s ease;
}
form select,form input,form button{
    padding:10px;border:1px solid #ccc;border-radius:6px;font-size:14px;
}
form select{flex:1 1 100%;}
form input[type="text"]{flex:1 1 45%;}
form button{
    background:#06389d;color:#fff;border:none;cursor:pointer;flex:1 1 100%;
    transition:0.3s;font-weight:bold;
}
form button:hover{background:#052b7b;}
table{
    width:100%;border-collapse:collapse;background:#fff;border-radius:8px;
    overflow:hidden;box-shadow:0 4px 8px rgba(0,0,0,0.1);animation:fadeIn 1s ease;
}
th,td{padding:12px;border-bottom:1px solid #ddd;text-align:left;font-size:14px;}
th{background:#06389d;color:#fff;}
td form{display:flex;gap:5px;}
td input{padding:6px;border-radius:4px;border:1px solid #ccc;}
.update-btn,.delete-btn{
    padding:8px 12px;border-radius:4px;font-size:13px;color:#fff;cursor:pointer;
}
.update-btn{background:#28a745;}
.delete-btn{background:#dc3545;text-decoration:none;}
.update-btn:hover{background:#218838;}
.delete-btn:hover{background:#c82333;}
@keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
/* Responsive */
@media(max-width:768px){
    .toggle-btn{display:block;}
    .sidebar{width:0;overflow:hidden;}
    .sidebar.active{width:220px;}
    .main-content{margin-left:0;padding:80px 15px;}
    table,thead,tbody,th,tr,td{display:block;width:100%;}
    thead{display:none;}
    tr{margin-bottom:15px;background:#fff;border-radius:8px;padding:10px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
    td{display:flex;justify-content:space-between;padding:8px;border:none;}
    td::before{content:attr(data-label);font-weight:bold;color:#06389d;}
    td input{width:60%;}
    .update-btn,.delete-btn{width:48%;text-align:center;}
}
</style>
</head>
<body>
<header>
    <h1><img src="../images/sblogo.jpeg" alt="Logo">Sharanbasva University</h1>
    <span class="toggle-btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></span>
</header>
<div class="sidebar" id="sidebar">
    <ul> <li><a href="admin_dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
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
    <h2>Manage Subjects</h2>
    <!-- Add Subject Form -->
    <form method="POST">
        <select name="semester" required>
            <option value="">-- Select Semester --</option>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
            <option value="3">Semester 3</option>
            <option value="4">Semester 4</option>
            <option value="5">Semester 5</option>
            <option value="6">Semester 6</option>
        </select>
        <input type="text" name="subject_code" placeholder="Subject Code (e.g. 22BCA41)" required>
        <input type="text" name="subject_name" placeholder="Subject Name" required>
        <button type="submit" name="add_subject">Add Subject</button>
    </form>

    <!-- Subjects List -->
    <?php foreach ($subjects as $sem => $sub_list): ?>
        <h3 style="color:#06389d;margin:20px 0;">Semester <?= htmlspecialchars($sem) ?></h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Code</th><th>Subject Name</th><th>Update</th><th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($sub_list as $subject): ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $subject['id'] ?>">
                        <td data-label="ID"><?= $subject['id'] ?></td>
                        <td data-label="Code"><input type="text" name="updated_code" value="<?= htmlspecialchars($subject['subject_code']) ?>"></td>
                        <td data-label="Subject"><input type="text" name="updated_name" value="<?= htmlspecialchars($subject['subject_name']) ?>"></td>
                        <td data-label="Update"><button type="submit" name="update_subject" class="update-btn">Update</button></td>
                        <td data-label="Delete"><a href="?delete=<?= $subject['id'] ?>" class="delete-btn" onclick="return confirm('Delete this subject?')">Delete</a></td>
                    </form>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php endforeach ?>
</div>
<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('active');
}
</script>
</body>
</html>
