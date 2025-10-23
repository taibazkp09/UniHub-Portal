<?php //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Fetch contact details
$sql = "SELECT * FROM contact_info LIMIT 1";
$result = $conn->query($sql);
$contact = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact - BCA Department</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
* {margin:0;padding:0;box-sizing:border-box;font-family:'Roboto',sans-serif;}
body {background:#f4f4f4;color:#333;}

/* Navbar */
nav {background:#06389d;color:#fff;display:flex;justify-content:space-between;align-items:center;padding:10px 20px;position:sticky;top:0;z-index:1000;}
.logo {display:flex;align-items:center;}
.logo img {height:40px;width:40px;border-radius:50%;margin-right:10px;}
.logo h2 {font-size:18px;}
.nav-links {display:flex;gap:20px;align-items:center;}
.nav-links a {color:#fff;text-decoration:none;font-size:16px;transition:0.3s;}
.nav-links a:hover {color:#ffcc00;}
.menu-icon {display:none;font-size:28px;color:#fff;cursor:pointer;}
.mobile-menu {display:none;flex-direction:column;background:#06389d;position:absolute;top:60px;right:0;width:220px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.2);}
.mobile-menu a {padding:12px;color:#fff;text-decoration:none;border-bottom:1px solid #04276e;}
.mobile-menu a:hover {background:#04276e;}
#menu-toggle {display:none;}

/* Contact Container */
.container {padding:20px;max-width:1000px;margin:auto;}
.section-title {text-align:center;font-size:28px;color:#06389d;margin:20px 0;font-weight:bold;}

/* Contact Info */
.contact-box {display:flex;flex-direction:row;justify-content:space-between;gap:20px;background:#fff;padding:20px;border-radius:10px;box-shadow:0 6px 15px rgba(0,0,0,0.1);margin-top:20px;}
.contact-details {flex:1;}
.contact-details p {font-size:16px;margin-bottom:8px;}
.contact-details i {color:#06389d;margin-right:8px;}
iframe {width:100%;height:300px;border:none;border-radius:8px;margin-top:15px;}

/* Mobile Responsive */
@media(max-width:768px){
    .nav-links {display:none;}
    .menu-icon {display:block;}
    #menu-toggle:checked ~ .mobile-menu {display:flex;}
    .contact-box {flex-direction:column;}
    iframe {height:250px;}
}
</style>
</head>
<body>

<!-- Navbar -->
<nav>
    <div class="logo">
        <img src="../images/sblogo.jpeg" alt="Logo">
        <h2>Sharanbasva University</h2>
    </div>
    <div class="nav-links">
        <a href="student_dashboard.php">Home</a>
        <a href="course.php">Courses</a>
        <a href="about.php">About Us</a>
        <a href="contact.php" style="color:#ffcc00;">Contact</a>
    </div>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="menu-icon"><i class="ri-menu-line"></i></label>
    <div class="mobile-menu">
        <a href="student_dashboard.php">Home</a>
        <a href="course.php">Courses</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
    </div>
</nav>

<!-- Contact Section -->
<div class="container">
    <h2 class="section-title">Contact Us</h2>

    <?php if($contact): ?>
        <div class="contact-box">
            <div class="contact-details">
                <p><i class="fa-solid fa-envelope"></i><strong>Email 1:</strong> <?php echo htmlspecialchars($contact['email1']); ?></p>
                <p><i class="fa-solid fa-envelope"></i><strong>Email 2:</strong> <?php echo htmlspecialchars($contact['email2'] ?: 'Not Provided'); ?></p>
                <p><i class="fa-solid fa-phone"></i><strong>Phone 1:</strong> <?php echo htmlspecialchars($contact['phone1']); ?></p>
                <p><i class="fa-solid fa-phone"></i><strong>Phone 2:</strong> <?php echo htmlspecialchars($contact['phone2'] ?: 'Not Provided'); ?></p>
                <p><i class="fa-solid fa-location-dot"></i><strong>Location:</strong> <?php echo htmlspecialchars($contact['location']); ?></p>
                <iframe src="<?php echo htmlspecialchars($contact['map_link']); ?>" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    <?php else: ?>
        <p style="text-align:center;color:#555;">Contact details are not available right now.</p>
    <?php endif; ?>
</div>

<div style="text-align:center;padding:20px;color:#444;"> 
    <p>Made with <i class="ri-heart-fill" style="color:red;"></i> by Taibaz Khanam</p>
</div>

</body>
</html>
