
<?php // include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Fetch Faculty Data
$facultyData = [];
$facultyQuery = "SELECT name, designation, photo, description FROM about_faculty";
$facultyResult = $conn->query($facultyQuery);
if ($facultyResult->num_rows > 0) {
    while ($row = $facultyResult->fetch_assoc()) {
        $facultyData[] = $row;
    }
}

// Fetch Achievements Data
$achievementsQuery = "SELECT achievement FROM achievements";
$achievementsResult = $conn->query($achievementsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us - BCA Department</title>
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
.nav-links a{color:#fff;text-decoration:none;font-size:16px;transition:0.3s;}
.nav-links a:hover{color:#ffcc00;}
.menu-icon{display:none;font-size:28px;color:#fff;cursor:pointer;}
.mobile-menu{display:none;flex-direction:column;background:#06389d;position:absolute;top:60px;right:0;width:220px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.2);}
.mobile-menu a{padding:12px;color:#fff;text-decoration:none;border-bottom:1px solid #04276e;}
.mobile-menu a:hover{background:#04276e;}
#menu-toggle{display:none;}

/* CONTAINER */
.container{padding:20px;max-width:1200px;margin:auto;}
.section-title{text-align:center;font-size:28px;color:#06389d;margin:30px 0;}

/* FACULTY GRID */
.faculty-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;}
.card{background:#fff;border-radius:15px;box-shadow:0 6px 12px rgba(0,0,0,0.1);text-align:center;padding:20px;transition:0.3s;}
.card:hover{transform:translateY(-8px);box-shadow:0 10px 20px rgba(0,0,0,0.15);}
.card img{width:100px;height:100px;border-radius:50%;margin-bottom:15px;object-fit:cover;}
.card h3{color:#06389d;margin-bottom:5px;}
.card p{font-size:14px;color:#444;}

/* SLIDESHOW FOR MOBILE */
.slideshow{display:none;position:relative;width:100%;max-width:300px;margin:20px auto;text-align:center;}
.slide{display:none;background:#fff;padding:20px;border-radius:15px;text-align:center;box-shadow:0 6px 12px rgba(0,0,0,0.1);}
.slide img{width:100px;height:100px;border-radius:50%;margin-bottom:10px;object-fit:cover;}
.slide h3{color:#06389d;margin-bottom:5px;}
.prev,.next{position:absolute;top:50%;transform:translateY(-50%);font-size:24px;color:#06389d;cursor:pointer;background:#fff;border-radius:50%;padding:8px;box-shadow:0 4px 8px rgba(0,0,0,0.2);}
.prev{left:-35px;}
.next{right:-35px;}

/* ACHIEVEMENTS */
.achievement-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-top:20px;}
.achievement-container .card h3{margin-bottom:8px;}

/* RESPONSIVE */
@media(max-width:768px){
    .nav-links{display:none;}
    .menu-icon{display:block;}
    #menu-toggle:checked ~ .mobile-menu{display:flex;}
    .faculty-container{display:none;}
    .slideshow{display:block;}
}
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
    <a href="student_dashboard.php">Home</a>
    <a href="student_dashboard.php">Courses</a>
    <a href="about.php" style="color:#ffcc00;">About Us</a>
    <a href="contact.php">Contact</a>
  </div>
  <input type="checkbox" id="menu-toggle">
  <label for="menu-toggle" class="menu-icon"><i class="ri-menu-line"></i></label>
  <div class="mobile-menu">
    <a href="student_dashboard.php">Home</a>
    <a href="student_dashboard.php">Courses</a>
    <a href="about.php">About Us</a>
    <a href="contact.php">Contact</a>
  </div>
</nav>

<div class="container">
    <h2 class="section-title">Our Faculty</h2>

    <!-- Desktop Grid -->
    <div class="faculty-container">
        <?php foreach($facultyData as $row): ?>
            <div class="card">
                <img src="../admin_panel/uploads/<?php echo $row['photo']; ?>" alt="Faculty">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><b><?php echo htmlspecialchars($row['designation']); ?></b></p>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Mobile Slideshow -->
    <div class="slideshow">
        <?php foreach($facultyData as $row): ?>
            <div class="slide">
                <img src="../admin_panel/uploads/<?php echo $row['photo']; ?>" alt="Faculty">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><b><?php echo htmlspecialchars($row['designation']); ?></b></p>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
            </div>
        <?php endforeach; ?>
        <span class="prev" onclick="plusSlides(-1)">&#10094;</span>
        <span class="next" onclick="plusSlides(1)">&#10095;</span>
    </div>

    <h2 class="section-title">Our Achievements</h2>
    <div class="achievement-container">
        <?php if($achievementsResult->num_rows > 0): ?>
            <?php while($row = $achievementsResult->fetch_assoc()): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($row['achievement']); ?></h3>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No Achievements Yet</p>
        <?php endif; ?>
    </div>
</div>

<div style="text-align:center;padding:20px;color:#444;">
    <p>Made with <i class="ri-heart-fill" style="color:red;"></i> by Taibaz Khanam</p>
</div>

<script>
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function showSlides(n) {
  let slides = document.getElementsByClassName("slide");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (let i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  slides[slideIndex-1].style.display = "block";
}
</script>
</body>
</html>
