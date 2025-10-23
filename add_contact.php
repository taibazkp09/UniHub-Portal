<?php //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);
$isLocalhost = in_array($_SERVER['SERVER_NAME'], ['127.0.0.1', 'localhost']);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

$sql = "SELECT * FROM contact_info LIMIT 1 ";
$result = $conn->query($sql);
$contact = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email1 = isset($_POST['email1']) ? filter_var($_POST['email1'], FILTER_SANITIZE_EMAIL) : '';
$email2 = isset($_POST['email2']) ? filter_var($_POST['email2'], FILTER_SANITIZE_EMAIL) : '';
$phone1 = isset($_POST['phone1']) ? trim($_POST['phone1']) : '';
$phone2 = isset($_POST['phone2']) ? trim($_POST['phone2']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$map_link = isset($_POST['map_link']) ? trim($_POST['map_link']) : '';

  /*  $email1 = filter_var($_POST['email1'], FILTER_SANITIZE_EMAIL);
    $email2 = filter_var($_POST['email2'], FILTER_SANITIZE_EMAIL);
    $phone1 = trim($_POST['phone1']);
    $phone2 = trim($_POST['phone2']);
    $location = trim($_POST['location']);
    $map_link = trim($_POST['map_link']);
*/
    // Validate inputs
    if (!filter_var($email1, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid Primary Email Address!";
    } elseif (!empty($email2) && !filter_var($email2, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid Secondary Email Address!";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone1)) {
        $message = "Primary Phone should be 10 digits!";
    } elseif (!empty($phone2) && !preg_match('/^[0-9]{10}$/', $phone2)) {
        $message = "Secondary Phone should be 10 digits!";
    } elseif (empty($location)) {
        $message = "Location cannot be empty!";
    } elseif (empty($map_link) || strpos($map_link, "https://www.google.com/maps") === false) {
        $message = "Please enter a valid Google Maps Embed link!";
    } else {
        if ($contact) {
            $update = "UPDATE contact_info SET email1=?, email2=?, phone1=?, phone2=?, location=?, map_link=? WHERE id=?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("ssssssi", $email1, $email2, $phone1, $phone2, $location, $map_link, $contact['id']);
            if ($stmt->execute()) {
                $message = "Contact Information Updated Successfully!";
            }
        } else {
            $insert = "INSERT INTO contact_info (email1, email2, phone1, phone2, location, map_link) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param("ssssss", $email1, $email2, $phone1, $phone2, $location, $map_link);
            if ($stmt->execute()) {
                $message = "Contact Information Added Successfully!";
            }
        }
    }
}
// Handle Delete Request
if (isset($_POST['delete_contact']) && $contact) {
    $deleteQuery = "DELETE FROM contact_info WHERE id=?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $contact['id']);
    if ($stmt->execute()) {
        $message = "Contact Information Deleted Successfully!";
        $contact = null; // Reset contact details
    } else {
        $message = "Failed to delete contact details!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Contact Info</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
* {margin:0;padding:0;box-sizing:border-box;font-family:'Roboto',sans-serif;}
body {background:#f4f4f4;}
nav {background:#06389d;color:#fff;display:flex;justify-content:space-between;align-items:center;padding:10px 20px;}
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
nav h2 {font-size:20px;}
nav .links {display:flex;gap:15px;}
nav a {color:#fff;text-decoration:none;font-size:16px;}
nav a:hover {color:#ffcc00;}
.container {max-width:700px;margin:50px auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 6px 15px rgba(0,0,0,0.1);}
h2 {text-align:center;color:#06389d;margin-bottom:15px;}
.message {text-align:center;margin-bottom:10px;color:green;font-weight:bold;}
form input, form textarea {width:100%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:6px;font-size:16px;}
button {width:100%;background:#06389d;color:white;padding:12px;border:none;border-radius:6px;font-size:18px;cursor:pointer;}
button:hover {background:#042a7d;}
/* VIEW CONTACT DETAILS SECTION */
.view-contact-container {
    background: #fff;
    padding: 20px;
    margin-top: 20px;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    animation: fadeIn 0.8s ease-in-out;
}

.view-contact-container h2 {
    text-align: center;
    color: #06389d;
    margin-bottom: 15px;
    font-size: 22px;
}

.contact-details {
    font-size: 16px;
    line-height: 1.8;
    color: #333;
}

.contact-details p {
    margin: 8px 0;
}

.contact-details strong {
    color: #06389d;
}

.contact-map {
    margin-top: 15px;
    width: 100%;
    height: 220px;
    border-radius: 8px;
    border: none;
}
.delete-btn {
    background: #e63946;
    color: #fff;
    padding: 10px 18px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s ease;
}

.delete-btn:hover {
    background: #b71c1c;
}


/* Hover effect on section */
.view-contact-container:hover {
    transform: translateY(-4px);
    transition: 0.3s ease;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .view-contact-container {
        padding: 15px;
    }
    .delete-btn{
        width:90px;
    }

    .contact-details {
        font-size: 14px;
    }

    .contact-map {
        height: 180px;
    }
    .container{
        padding-top: 50px;
    }
}

/* Fade-in Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media(max-width:768px){
    nav h2 {font-size:18px;}
    nav .links {display:none;}

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

</nav>

<div class="container">
    <h2>Manage Contact Information</h2>
    <?php if($message): ?><p class="message"><?php echo $message; ?></p><?php endif; ?>
    <form method="POST" onsubmit="return validateForm()">
        <input type="email" name="email1" placeholder="Primary Email" value="<?php echo $contact['email1'] ?? ''; ?>" required>
        <input type="email" name="email2" placeholder="Secondary Email" value="<?php echo $contact['email2'] ?? ''; ?>">
        <input type="text" name="phone1" placeholder="Primary Phone (10 digits)" value="<?php echo $contact['phone1'] ?? ''; ?>" required>
        <input type="text" name="phone2" placeholder="Secondary Phone (10 digits)" value="<?php echo $contact['phone2'] ?? ''; ?>">
        <textarea name="location" placeholder="Full Address" required><?php echo $contact['location'] ?? ''; ?></textarea>
       <input type="text" name="map_link" placeholder="Google Maps Embed Link" value="<?php echo $contact['map_link'] ?? ''; ?>"required>
       <iframe src=""></iframe>
       <?php if(!$isLocalhost): ?>
    <iframe src="<?php echo htmlspecialchars($contact['map_link']); ?>" 
            width="100%" height="200" style="border:0;border-radius:8px;" 
            allowfullscreen="" loading="lazy"></iframe>
<?php else: ?>
    <p style="color:red;text-align:center;">Map preview is disabled on localhost. It will work after deployment.</p>
<?php endif; ?>

        <button type="submit">Save Contact Info</button>
    </form>
</div>

<script>
function validateForm(){
    let phone1 = document.querySelector('[name="phone1"]').value;
    let phone2 = document.querySelector('[name="phone2"]').value;
    if(!/^\d{10}$/.test(phone1)){
        alert("Primary phone number must be 10 digits");
        return false;
    }
    if(phone2 && !/^\d{10}$/.test(phone2)){
        alert("Secondary phone number must be 10 digits");
        return false;
    }
    return true;
}
</script>
<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('active');
}
</script>

    
<?php if ($message): ?>
    <form method="POST" onsubmit="return confirmDelete()" style="margin-top:15px;text-align:center;">
        <button type="submit" name="delete_contact" class="delete-btn">Delete Contact</button>
    </form>
<?php endif; ?>

<script>
function confirmDelete() {
    return confirm("Are you sure you want to delete this contact information?");
}
</script>

</body>
</html>
