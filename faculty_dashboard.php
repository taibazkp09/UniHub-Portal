<?php //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

session_start();
if (!isset($_SESSION['fac_id'])) {
    header("Location: faculty_login.php");
    exit;
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);

$fac_id = $_SESSION['fac_id'];
$fac_name = $_SESSION['fac_name'];
$msg = "";

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_type'])) {
    $upload_type = $_POST['upload_type'];
    $semester = $_POST['semester'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $module = $_POST['module'] ?? '';
    $file = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];

   if (empty($semester) || empty($subject) || empty($file)) {
        $msg = "<p class='error'>⚠ All fields are required!</p>";
    } 
    
// For PYQ also PDF only
//elseif ($upload_type == "pyq" && pathinfo($file, PATHINFO_EXTENSION) != 'pdf') {
 //   $msg = "<p class='error'>❌ PYQ must be a PDF file!</p>";
//}
// For syllabus allow ANY file → no restriction

 else {
        $folder = "uploads/" . basename($file);
        if (move_uploaded_file($tmp, $folder)) {
            if ($upload_type == "notes") {
                $stmt = $conn->prepare("INSERT INTO notes (faculty_id, semester, subject, module, file_name) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $fac_id, $semester, $subject, $module, $file);
            } elseif ($upload_type == "pyq") {
                $stmt = $conn->prepare("INSERT INTO pyqs (faculty_id, semester, subject, file_name) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $fac_id, $semester, $subject, $file);
            } elseif ($upload_type == "syllabus") {
                $stmt = $conn->prepare("INSERT INTO syllabus (faculty_id, semester,subject, file_name) VALUES (?, ?,?, ?)");
                $stmt->bind_param("isss", $fac_id, $semester,$subject, $file);
            }
            if ($stmt->execute()) {
                $msg = "<p class='success'>✅ File uploaded successfully!</p>";
            } else {
                $msg = "<p class='error'>❌ Database error!</p>";
            }
            $stmt->close();
        } else {
            $msg = "<p class='error'>❌ Failed to upload file!</p>";
        }
    }
}

// Handle Delete
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $id = intval($_GET['delete']);
    $type = $_GET['type'];
    $table = ($type == 'notes') ? 'notes' : (($type == 'pyq') ? 'pyqs' : 'syllabus');
    $stmt = $conn->prepare("DELETE FROM $table WHERE id=? AND faculty_id=?");
    $stmt->bind_param("ii", $id, $fac_id);
    $stmt->execute();
    header("Location: faculty_dashboard.php");
    exit;
}

// Fetch Uploads
$notes = $conn->query("SELECT * FROM notes WHERE faculty_id=$fac_id");
$pyqs = $conn->query("SELECT * FROM pyqs WHERE faculty_id=$fac_id");
$syllabus = $conn->query("SELECT * FROM syllabus WHERE faculty_id=$fac_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Faculty Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#f4f7fc;color:#333;}
header{
    background:#06389d;color:#fff;padding:15px;font-size:18px;
    display:flex;justify-content:space-between;align-items:center;
}
header .toggle-btn{font-size:22px;cursor:pointer;display:none;}
.sidebar{
    width:220px;position:fixed;top:60px;left:0;height:100%;background:#052b7b;
    padding-top:20px;transition:0.3s;overflow-y:auto;z-index:999;
}
.sidebar ul{list-style:none;padding:0;}
.sidebar ul li a{
    color:#fff;display:block;padding:12px 20px;text-decoration:none;font-size:15px;
}
.sidebar ul li a:hover{background:#001f5c;}
.sidebar .logout-link{position:absolute;bottom:20px;width:100%;}
.main{margin-left:220px;padding:20px;transition:0.3s;}
.tabs button{
    padding:10px 20px;border:none;background:#06389d;color:#fff;border-radius:5px;cursor:pointer;margin-right:10px;
}
.tabs button.active{background:#052b7b;}
.tab-content{display:none;}
.tab-content.active{display:block;animation:fadeIn 0.5s;}
@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
form{
    background:#fff;padding:20px;border-radius:8px;margin-bottom:20px;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}
form input,form select{width:100%;padding:10px;margin-bottom:10px;border:1px solid #ccc;border-radius:6px;}
form button{width:100%;padding:12px;background:#06389d;color:#fff;border:none;border-radius:6px;cursor:pointer;}
.success{color:green;text-align:center;}
.error{color:red;text-align:center;}
table{width:100%;border-collapse:collapse;background:#fff;margin-bottom:30px;}
th,td{padding:10px;border:1px solid #ddd;text-align:left;}
th{background:#06389d;color:#fff;}
.delete-btn{color:#fff;background:#dc3545;padding:6px 10px;border-radius:4px;text-decoration:none;}
/* Responsive */
@media(max-width:768px){
    .sidebar{width:0;overflow:hidden;}
    .sidebar.active{width:220px;}
    header .toggle-btn{display:block;}
    .main{margin-left:0;}
    table,thead,tbody,th,tr,td{display:block;width:100%;}
    thead{display:none;}
    tr{margin-bottom:15px;background:#fff;border-radius:8px;padding:10px;}
    td{display:flex;justify-content:space-between;padding:8px;border:none;}
    td::before{content:attr(data-label);font-weight:bold;color:#06389d;}
}
</style>
</head>
<body>
<header>
    <span>Welcome, <?= htmlspecialchars($fac_name) ?></span>
    <span class="toggle-btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></span>
</header>
<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="#notes" onclick="showTab('notes')"><i class="fa fa-book"></i> Upload Notes</a></li>
        <li><a href="#pyq" onclick="showTab('pyq')"><i class="fa fa-file"></i> Upload PYQ</a></li>
        <li><a href="#syllabus" onclick="showTab('syllabus')"><i class="fa fa-list"></i> Upload Syllabus</a></li>
        <li><a href="#view" onclick="showTab('view')"><i class="fa fa-eye"></i> View Uploads</a></li>
    </ul>
    <div class="logout-link">
        <a href="logout.php" style="color:white;display:block;padding:12px 20px;background:#dc3545;text-align:center;"><i class="fa fa-sign-out"></i> Logout</a>
    </div>
</div>
<div class="main">
    <?= $msg ?>
    <!-- Notes Upload -->
    <div id="notes" class="tab-content active">
        <h2>Upload Notes</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="upload_type" value="notes">
            <select name="semester" class="semester-select" required>
                <option value="">Select Semester</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
                <option value="3">Semester 3</option>
                <option value="4">Semester 4</option>
                <option value="5">Semester 5</option>
                <option value="6">Semester 6</option>
            </select>
            <select name="subject" class="subject-dropdown" required>
                <option value="">Select Subject</option>
            </select>
            <select name="module" required>
                <option value="">Select Module</option>
                <option value="Module 1">Module 1</option>
                <option value="Module 2">Module 2</option>
                <option value="Module 3">Module 3</option>
                <option value="Module 4">Module 4</option>
                <option value="Module 5">Module 5</option>
            </select>
            <input type="file" name="file" accept="application/pdf" required>
            <button type="submit">Upload</button>
        </form>
    </div>

    <!-- PYQ Upload -->
    <div id="pyq" class="tab-content">
        <h2>Upload PYQ</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="upload_type" value="pyq">
            <select name="semester" class="semester-select" required>
                <option value="">Select Semester</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
                <option value="3">Semester 3</option>
                <option value="4">Semester 4</option>
                <option value="5">Semester 5</option>
                <option value="6">Semester 6</option>
            </select>
            <select name="subject" class="subject-dropdown" required>
                <option value="">Select Subject</option>
            </select>
            <input type="file" name="file" accept="application/pdf" required>
            <button type="submit">Upload</button>
        </form>
    </div>


    <!-- Syllabus Upload -->
<div id="syllabus" class="tab-content">
    <h2>Upload Syllabus</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="upload_type" value="syllabus">

        <!-- Semester Dropdown -->
        <select name="semester" class="semester-select" required>
            <option value="">Select Semester</option>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
            <option value="3">Semester 3</option>
            <option value="4">Semester 4</option>
            <option value="5">Semester 5</option>
            <option value="6">Semester 6</option>
        </select>

        <!-- Subject Dropdown (Dynamic) -->
        <select name="subject" class="subject-dropdown" required>
            <option value="">Select Subject</option>
        </select>

        <!-- File Upload (Allow Any Type) -->
        <input type="file" name="file" required>

        <button type="submit">Upload</button>
    </form>
</div>



    <!-- View Uploads -->
    <div id="view" class="tab-content">
        <h2>Your Uploads</h2>
        <h3>Notes</h3>
        <table>
            <tr><th>ID</th><th>Semester</th><th>Subject</th><th>Module</th><th>File</th><th>Delete</th></tr>
            <?php while($n = $notes->fetch_assoc()): ?>
            <tr>
                <td data-label="ID"><?= $n['id'] ?></td>
                <td data-label="Semester"><?= $n['semester'] ?></td>
                <td data-label="Subject"><?= $n['subject'] ?></td>
                <td data-label="Module"><?= $n['module'] ?></td>
                <td data-label="File"><a href="uploads/<?= $n['file_name'] ?>" target="_blank">View</a></td>
                <td><a class="delete-btn" href="?delete=<?= $n['id'] ?>&type=notes" onclick="return confirm('Delete this file?')">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <h3>PYQs</h3>
        <table>
            <tr><th>ID</th><th>Semester</th><th>Subject</th><th>File</th><th>Delete</th></tr>
            <?php while($p = $pyqs->fetch_assoc()): ?>
            <tr>
                <td data-label="ID"><?= $p['id'] ?></td>
                <td data-label="Semester"><?= $p['semester'] ?></td>
                <td data-label="Subject"><?= $p['subject'] ?></td>
                <td data-label="File"><a href="uploads/<?= $p['file_name'] ?>" target="_blank">View</a></td>
                <td><a class="delete-btn" href="?delete=<?= $p['id'] ?>&type=pyq" onclick="return confirm('Delete this file?')">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <h3>Syllabus</h3>
        <table>
            <tr><th>ID</th><th>Semester</th><th>File</th><th>Delete</th></tr>
            <?php while($s = $syllabus->fetch_assoc()): ?>
            <tr>
                <td data-label="ID"><?= $s['id'] ?></td>
                <td data-label="Semester"><?= $s['semester'] ?></td>
                <td data-label="File"><a href="uploads/<?= $s['file_name'] ?>" target="_blank">View</a></td>
                <td><a class="delete-btn" href="?delete=<?= $s['id'] ?>&type=syllabus" onclick="return confirm('Delete this file?')">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('active');
}
function showTab(id){
    document.querySelectorAll('.tab-content').forEach(div=>div.classList.remove('active'));
    document.getElementById(id).classList.add('active');
}

// Dynamic subject loading
document.querySelectorAll('.semester-select').forEach(select => {
    select.addEventListener('change', function(){
        let semester = this.value;
        let subjectDropdown = this.closest('form').querySelector('.subject-dropdown');

        if(semester){
            fetch('get_subjects.php?semester=' + semester)
            .then(response => response.json())
            .then(data => {
                subjectDropdown.innerHTML = '<option value="">Select Subject</option>';
                data.forEach(subject => {
                    subjectDropdown.innerHTML += `<option value="${subject.subject_name}">${subject.subject_name}</option>`;
                });
            });
        } else {
            subjectDropdown.innerHTML = '<option value="">Select Subject</option>';
        }
    });
});
</script>
</body>
</html>
