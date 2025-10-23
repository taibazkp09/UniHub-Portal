

<?php //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

session_start();
if (!isset($_SESSION['usn'])) {
    header("Location: student_login.php");
    exit();
}

// Student details from session
$full_name = $_SESSION['full_name'] ?? "Student";
$semester = $_SESSION['semester'] ?? "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#f4f4f4;color:#333;}

/* NAVBAR */
nav{background:#06389d;color:#fff;display:flex;justify-content:space-between;align-items:center;padding:10px 20px;position:sticky;top:0;z-index:1000;}
.logo{display:flex;align-items:center;}
.logo img{height:40px;border-radius:50%;margin-right:10px;}
.logo h2{font-size:18px;}
.nav-links{display:flex;gap:20px;align-items:center;}
.nav-links a{color:#fff;text-decoration:none;font-size:16px;transition:color 0.3s;}
.nav-links a:hover{color:#ffcc00;}
.profile-icon{font-size:24px;cursor:pointer;}
.menu-icon{display:none;font-size:28px;color:#fff;cursor:pointer;}
.mobile-menu{display:none;flex-direction:column;background:#06389d;position:absolute;top:60px;right:0;width:220px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.2);}
.mobile-menu a{padding:12px;color:#fff;text-decoration:none;border-bottom:1px solid #04276e;}
.mobile-menu a:hover{background:#04276e;}
#menu-toggle{display:none;}

/* WELCOME */
.welcome{text-align:center;padding:30px 10px;animation:fadeIn 1s ease;}
.welcome h1{color:#06389d;font-size:28px;}
.welcome p{margin-top:10px;font-size:16px;}

/* CARDS */
.cards{display:flex;justify-content:center;gap:20px;padding:30px;flex-wrap:wrap;}
.card{background:#fff;width:280px;border-radius:15px;box-shadow:0 6px 12px rgba(0,0,0,0.1);text-align:center;padding:20px;cursor:pointer;transition:0.4s;}
.card:hover{transform:translateY(-8px);box-shadow:0 12px 25px rgba(0,0,0,0.2);}
.card i{font-size:40px;color:#06389d;margin-bottom:10px;}
.card h3{margin-bottom:10px;color:#06389d;}

/* ABOUT & CONTACT */
section.info{padding:40px 20px;text-align:center;}
.info h2{color:#06389d;margin-bottom:15px;}
.info p{max-width:800px;margin:0 auto 20px;font-size:16px;color:#444;}
.contact-info p{margin:8px 0;font-size:15px;text-align: center;marin-left:30px}

/* MODAL */
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);justify-content:center;align-items:center;}
.modal-content{background:#fff;padding:20px;border-radius:15px;width:90%;max-width:400px;text-align:center;animation:slideIn 0.5s;}
.close{float:right;font-size:24px;cursor:pointer;color:#333;}

/* RESPONSIVE */
@media(max-width:768px){
.nav-links{display:none;}
.menu-icon{display:block;}
#menu-toggle:checked ~ .mobile-menu{display:flex;animation:fadeIn 0.5s;}
.cards{flex-direction:column;align-items:center;}
.mobile-menu 
{
    display: none;
    flex-direction: column;
    background: #06389d;
    position: absolute;
    top: 60px;
    right: 0;
    width: 220px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    z-index: 1000; /* Below modal */
}

.modal 
{
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
    z-index: 9999; /* Above everything */
}

}

/* ANIMATIONS */
@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
@keyframes slideIn{from{transform:translateY(-50px);opacity:0;}to{transform:translateY(0);opacity:1;}}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav>
  <div class="logo">
    <img src="../images/sblogo.jpeg" alt="Logo">
    <h2>Sharanbasva University</h2>
  </div>
  <div class="nav-links">
    <a href="#home">Home</a>
    <a href="#courses">Courses</a>
    <a href="about.php">About Us</a>
    <a href="contact.php">Contact</a>
    <i class="ri-user-3-fill profile-icon" onclick="openProfile()"></i>
    <a href="logout.php">Logout</a>
  </div>
  <input type="checkbox" id="menu-toggle">
  <label for="menu-toggle" class="menu-icon"><i class="ri-menu-line"></i></label>
  <div class="mobile-menu">
    <a href="#home">Home</a>
    <a href="#courses">Courses</a>
    <a href="about.php">About Us</a>
    <a href="contact.php">Contact</a>
   
    <a href="logout.php">Logout</a>
     <a href="#" onclick="openProfile()">Profile</a>
  </div>
</nav>

<!-- WELCOME -->
<section class="welcome" id="home">
  <h1>Welcome, <?php echo htmlspecialchars($full_name); ?> 👋</h1>
  <p>Your semester: <?php echo htmlspecialchars($semester); ?> | Explore your resources below</p>
</section>

<!-- RESOURCES -->
<section class="cards" id="courses">
  <div class="card" onclick="loadResources('notes')">
    <i class="ri-book-open-fill"></i>
    <h3>Notes</h3>
    <p>Download subject-wise notes.</p>
  </div>
  <div class="card" onclick="loadResources('syllabus')">
    <i class="ri-file-list-3-fill"></i>
    <h3>Syllabus</h3>
    <p>Check your complete syllabus.</p>
  </div>
  <div class="card" onclick="loadResources('pyq')">
    <i class="ri-question-answer-fill"></i>
    <h3>Previous Year Questions</h3>
    <p>Access past exam papers.</p>
  </div>
</section>

<!-- ABOUT US -->
<section class="info" id="about">
  <h2>About BCA Department</h2>
  <p>The BCA department of Sharanbasva University focuses on providing quality education in Computer Applications, offering hands-on experience with modern technologies and projects.</p>
</section>

<!-- CONTACT -->
<section class="info contact-info" id="contact">
  <h2>Contact Us</h2>
  <p>Email: bcadept@sbu.edu</p>
  <p>Phone: +91 9876543210</p>
  <p>Address: Sharanbasva University, Kalaburagi</p>
</section>

<!-- PROFILE MODAL -->
<div id="profile-modal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeProfile()">&times;</span>
    <h2>Your Profile</h2>
    <div id="profile-info"></div>
  </div>
</div>

<script>
const semester = "<?php echo $semester; ?>";

function openProfile(){
  fetch('get_profile.php')
    .then(res => res.text())
    .then(data=>{
      document.getElementById('profile-info').innerHTML=data;
      document.getElementById('profile-modal').style.display='flex';
    });
}
function closeProfile(){
  document.getElementById('profile-modal').style.display='none';
}
function loadResources(type){
  window.location.href = `resources.php?type=${type}`;
}
</script>
</body>
</html>
