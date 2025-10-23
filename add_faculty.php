
<?php
//include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// DB Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);

$success = $error = "";

// ✅ Add Faculty
if (isset($_POST['add_faculty'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $fac_code = strtoupper(trim($_POST['fac_code']));
    $subject = trim($_POST['subject']);
    $semester = trim($_POST['semester']);
    $password = trim($_POST['password']);

    if (empty($full_name) || empty($email) || empty($phone) || empty($fac_code) || empty($subject) || empty($semester) || empty($password)) {
        $error = "⚠ All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email format.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "❌ Phone must be 10 digits.";
    } else {
        $check = $conn->prepare("SELECT * FROM faculty_info WHERE email = ? OR phone = ? OR fac_code = ?");
        $check->bind_param("sss", $email, $phone, $fac_code);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            $error = "❌ Email, phone or faculty code already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO faculty_info (full_name, email, phone, fac_code, subject, semester, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $full_name, $email, $phone, $fac_code, $subject, $semester, $password);
            if ($stmt->execute()) {
                $success = "✅ Faculty added successfully!";
            } else {
                $error = "❌ Something went wrong.";
            }
            $stmt->close();
        }
    }
}

// ✅ Update Faculty
if (isset($_POST['update_faculty'])) {
    $id = $_POST['id'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $semester = trim($_POST['semester']);

    $stmt = $conn->prepare("UPDATE faculty_info SET full_name=?, email=?, phone=?, subject=?, semester=? WHERE id=?");
    $stmt->bind_param("sssssi", $full_name, $email, $phone, $subject, $semester, $id);
    if ($stmt->execute()) {
        $success = "✅ Faculty updated successfully!";
    } else {
        $error = "❌ Failed to update faculty.";
    }
    $stmt->close();
}

// ✅ Delete Faculty
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM faculty_info WHERE id=$id");
    header("Location: add_faculty.php");
    exit();
}

// ✅ Fetch all faculty
$faculty_data = [];
$result = $conn->query("SELECT * FROM faculty_info ORDER BY full_name ASC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $faculty_data[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Faculty | Admin Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<!-- ✅ Reuse your CSS from previous snippet (it works perfectly) -->
<link rel="stylesheet" href="your-css-file.css">
<style>
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

/* Success & Error Messages */
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

/* Form Styling */
form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    animation: fadeIn 1s ease;
}

form input,
form select,
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

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    animation: fadeIn 1s ease;
}

th,
td {
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

.update-btn,
.delete-btn {
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 13px;
    color: #fff;
    cursor: pointer;
    text-decoration: none;
}

.update-btn {
    background: #28a745;
}

.delete-btn {
    background: #dc3545;
}

.update-btn:hover {
    background: #218838;
}

.delete-btn:hover {
    background: #c82333;
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ✅ Responsive Design */
@media(max-width: 768px) {
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

    table,
    thead,
    tbody,
    th,
    tr,
    td {
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
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
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

    .update-btn,
    .delete-btn {
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
    <h2>Manage Faculty</h2>
    <?php if ($success): ?><div class="msg success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="msg error"><?= $error ?></div><?php endif; ?>

    <!-- ✅ Add Faculty Form -->
    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="number" name="phone" placeholder="Phone Number" required>
        <input type="text" name="fac_code" placeholder="Faculty Code" required>
        <input type="text" name="subject" placeholder="Subject" required>
        <select name="semester" required>
            <option value="">-- Select Semester --</option>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
            <option value="3">Semester 3</option>
            <option value="4">Semester 4</option>
            <option value="5">Semester 5</option>
            <option value="6">Semester 6</option>
        </select>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="add_faculty">Add Faculty</button>
    </form>

    <!-- ✅ Faculty List -->
    <h3 style="color:#06389d;margin:20px 0;">Faculty List</h3>
    <?php if (empty($faculty_data)): ?>
        <p>No faculty added yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Semester</th><th>Update</th><th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($faculty_data as $fac): ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $fac['id'] ?>">
                        <td data-label="ID"><?= $fac['id'] ?></td>
                        <td data-label="Name"><input type="text" name="full_name" value="<?= htmlspecialchars($fac['full_name']) ?>"></td>
                        <td data-label="Email"><input type="text" name="email" value="<?= htmlspecialchars($fac['email']) ?>"></td>
                        <td data-label="Phone"><input type="text" name="phone" value="<?= htmlspecialchars($fac['phone']) ?>"></td>
                        <td data-label="Subject"><input type="text" name="subject" value="<?= htmlspecialchars($fac['subject']) ?>"></td>
                        <td data-label="Semester"><input type="text" name="semester" value="<?= htmlspecialchars($fac['semester']) ?>"></td>
                        <td><button type="submit" name="update_faculty" class="update-btn">Update</button></td>
                        <td><a href="?delete=<?= $fac['id'] ?>" class="delete-btn" onclick="return confirm('Delete this faculty?')">Delete</a></td>
                    </form>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function toggleSidebar(){ document.getElementById('sidebar').classList.toggle('active'); }
</script>
</body>
</html>
