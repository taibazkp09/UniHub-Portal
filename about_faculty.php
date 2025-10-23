
<?php
session_start();
//include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);

$success = $error = "";

// ✅ Handle Add Faculty Info
if (isset($_POST['add_faculty_about'])) {
    $name = trim($_POST['name']);
    $designation = trim($_POST['designation']);
    $description = trim($_POST['description']);

    $photo = $_FILES['photo']['name'];
    $tmp = $_FILES['photo']['tmp_name'];
    $folder = "uploads/" . basename($photo);

    if (empty($name) || empty($designation) || empty($description) || empty($photo)) {
        $error = "⚠ All fields are required!";
    } else {
        if (move_uploaded_file($tmp, $folder)) {
            $stmt = $conn->prepare("INSERT INTO about_faculty (name, designation, description, photo) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $designation, $description, $photo);
            if ($stmt->execute()) {
                $success = "✅ Faculty added successfully!";
            } else {
                $error = "❌ Database insertion failed.";
            }
        } else {
            $error = "❌ Failed to upload image.";
        }
    }
}

// ✅ Delete Faculty Info
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM about_faculty WHERE id = $id");
    header("Location: about_faculty.php");
    exit();
}

// ✅ Fetch All Faculty Info
$faculty_about = [];
$result = $conn->query("SELECT * FROM about_faculty ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $faculty_about[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage About Faculty</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="manage_faculty.css"> <!-- ✅ Same CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>/* Global Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    background: #f4f7fc;
    color: #333;
}

/* Header */
header {
    background: #06389d;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
}

header h1 {
    font-size: 20px;
    display: flex;
    align-items: center;
}

header h1 img {
    height: 40px;
    width: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.toggle-btn {
    display: none;
    font-size: 22px;
    cursor: pointer;
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 60px;
    left: 0;
    width: 220px;
    height: 100%;
    background: #052b7b;
    padding-top: 20px;
    transition: 0.3s;
    overflow-y: auto;
    z-index: 999;
}

.sidebar ul {
    list-style: none;
}

.sidebar ul li {
    margin: 10px 0;
}

.sidebar ul li a {
    display: flex;
    align-items: center;
    color: #fff;
    text-decoration: none;
    padding: 12px 20px;
    transition: 0.3s;
    font-size: 15px;
}

.sidebar ul li a:hover {
    background: #001f5c;
    border-left: 4px solid #fff;
}

.sidebar ul li a i {
    margin-right: 10px;
}

/* Main Content */
.main-content {
    margin-left: 220px;
    padding: 80px 20px;
    transition: 0.3s;
}

h2 {
    text-align: center;
    color: #06389d;
    margin-bottom: 20px;
    font-size: 24px;
}

.msg {
    text-align: center;
    font-weight: bold;
    margin-bottom: 15px;
}

.success {
    color: green;
}

.error {
    color: red;
}

/* Form */
form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    animation: fadeIn 1s ease;
}

form input,
form select,
form textarea,
form button {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}

form input[type="text"],
form input[type="email"],
form input[type="number"],
form input[type="password"] {
    flex: 1 1 45%;
}

form select {
    flex: 1 1 45%;
}

form textarea {
    flex: 1 1 100%;
    min-height: 80px;
    resize: none;
}

form button {
    background: #06389d;
    color: #fff;
    border: none;
    cursor: pointer;
    flex: 1 1 100%;
    transition: 0.3s;
    font-weight: bold;
}

form button:hover {
    background: #052b7b;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    animation: fadeIn 1s ease;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
    font-size: 14px;
}

th {
    background: #06389d;
    color: #fff;
}

td form {
    display: flex;
    gap: 5px;
}

td input {
    padding: 6px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.update-btn, .delete-btn {
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 13px;
    color: #fff;
    cursor: pointer;
    text-align: center;
    display: inline-block;
}

.update-btn {
    background: #28a745;
}

.delete-btn {
    background: #dc3545;
    text-decoration: none;
}

.update-btn:hover {
    background: #218838;
}

.delete-btn:hover {
    background: #c82333;
}

/* Image in table */
td img {
    border-radius: 6px;
    object-fit: cover;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 768px) {
    .toggle-btn {
        display: block;
    }

    .sidebar {
        width: 0;
        overflow: hidden;
    }

    .sidebar.active {
        width: 220px;
    }

    .main-content {
        margin-left: 0;
        padding: 80px 15px;
    }

    table, thead, tbody, th, tr, td {
        display: block;
        width: 100%;
    }

    thead {
        display: none;
    }

    tr {
        margin-bottom: 15px;
        background: #fff;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    td {
        display: flex;
        justify-content: space-between;
        padding: 8px;
        border: none;
    }

    td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #06389d;
    }

    td input {
        width: 60%;
    }

    .update-btn, .delete-btn {
        width: 48%;
        text-align: center;
    }
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
    <h2>About Faculty</h2>

    <?php if ($success): ?><div class="msg success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="msg error"><?= $error ?></div><?php endif; ?>

    <!-- Add About Faculty Form -->
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="designation" placeholder="Designation" required>
        <input type="file" name="photo" accept="image/*" required>
        <textarea name="description" placeholder="Description" style="flex:1 1 100%;padding:10px;border-radius:6px;border:1px solid #ccc;"></textarea>
        <button type="submit" name="add_faculty_about">Add Faculty Info</button>
    </form>

    <h3 style="color:#06389d;margin:20px 0;">About Faculty List</h3>
    <?php if (empty($faculty_about)): ?>
        <p>No faculty info added yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Photo</th><th>Name</th><th>Designation</th><th>Description</th><th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($faculty_about as $fac): ?>
                <tr>
                    <td data-label="Photo"><img src="uploads/<?= htmlspecialchars($fac['photo']) ?>" width="60"></td>
                    <td data-label="Name"><?= htmlspecialchars($fac['name']) ?></td>
                    <td data-label="Designation"><?= htmlspecialchars($fac['designation']) ?></td>
                    <td data-label="Description"><?= htmlspecialchars($fac['description']) ?></td>
                    <td><a href="?delete=<?= $fac['id'] ?>" class="delete-btn" onclick="return confirm('Delete this info?')">Delete</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('active');
}
</script>
</body>
</html>
