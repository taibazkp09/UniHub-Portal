

<?php //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

session_start();

// DB Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";
$identifier = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = trim($_POST['identifier']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($identifier) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Check if user exists (by email or USN)
        $sql = "SELECT * FROM s_info WHERE email = ? OR usn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Password match (Plain text for now, can upgrade to password_verify later)
            if ($password === $user['password']) {
              

                  // Store all necessary details in session
    $_SESSION['usn'] = $user['usn'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['semester'] = $user['semester'];  // ✅ Important for resources.php
    $_SESSION['email'] = $user['email'];
    $_SESSION['phone'] = $user['phone'];
                $success = "✅ Welcome, " . htmlspecialchars($user['full_name']) . "! Redirecting...";
                
                // Redirect to dashboard after 2 seconds
                echo "<script>
                    setTimeout(() => { window.location.href='../student_panel/student_dashboard.php'; }, 2000);
                </script>";
            } else {
                $error = "❌ Incorrect password.";
            }
        } else {
            $error = "❌ No account found with that Email or USN.";
        }

        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
* {margin:0;padding:0;box-sizing:border-box;}
body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #f4f4f4;
}
header {
  display:flex;align-items:center;
  background:#06389d;
 
  height: 50px;
}
#logo {
  height:40px;width:40px;border-radius:50%;margin-right:10px;
}
#heading {
  color:#fff;font-size:20px;padding-top:10px;
}
.container {
  display:flex;justify-content:center;align-items:center;
  min-height:80vh;padding:15px;
}
.form-box {
  background:#fff;
  padding:20px;
  border-radius:15px;
  box-shadow:0 6px 12px rgba(0,0,0,0.15);
  width:100%;max-width:400px;
}
h2 {
  text-align:center;color:#06389d;margin-bottom:20px;
}
.input-group {margin-bottom:15px;position:relative;}
.input-group input {
  width:100%;padding:12px;
  border-radius:30px;
  border:1px solid #ccc;
  font-size:14px;font-weight:bold;
  outline:none;
  transition:0.3s;
}
.input-group input:focus {
  border-color:#06389d;
  box-shadow:0 0 6px rgba(6,56,157,0.4);
}
#btn {
  background:#06389d;color:#fff;
  border:none;border-radius:30px;
  padding:14px;font-size:16px;
  width:100%;cursor:pointer;
  transition:0.3s;
}
#btn:hover {background:#052b7b;}
.message {
  text-align:center;font-weight:bold;margin-bottom:15px;
}
.message.error {color:red;}
.message.success {color:green;}
.password-toggle i {
  position:absolute;right:15px;top:12px;
  color:#999;cursor:pointer;
}
.footer {
  text-align:center;margin:20px 0;color:#333;
}
.footer i {color:red;}
@media(max-width:480px){
  #heading {font-size:16px;}
  .form-box {padding:15px;}
}
</style>
</head>
<body>

<header>
  <img id="logo" src="../images/sblogo.jpeg" alt="Logo">
  <h2 id="heading">Student Panel - Sharanbasva University</h2>
</header>

<div class="container">
  <div class="form-box">
    <form method="POST" action="">
      <h2>Login</h2>
      <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
      <?php elseif ($success): ?>
        <div class="message success"><?= $success ?></div>
      <?php endif; ?>

      <div class="input-group">
        <input type="text" name="identifier" value="<?= htmlspecialchars($identifier) ?>" placeholder="Enter Email or USN" required>
      </div>
      <div class="input-group password-toggle">
        <input type="password" id="password" name="password" placeholder="Enter Password" required>
        <i class="fa fa-eye" id="togglePassword"></i>
      </div>

      <button type="submit" id="btn">Login</button>
      <p style="text-align:center;margin-top:10px;">Don't have an account? <a href="student_register.php" style="color:#06389d;font-weight:bold;">Register</a></p>
    </form>
  </div>
</div>

<div class="footer">
  <p>Made with <i class="fa-solid fa-heart"></i> by Taibaz Khanam</p>
</div>

<script>
const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");
togglePassword.addEventListener("click", () => {
  const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
  passwordInput.setAttribute("type", type);
  togglePassword.classList.toggle("fa-eye-slash");
});
</script>
</body>
</html>
