

<?php //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

session_start();

// Database connection
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "⚠ Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_info WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            header("Location:../admin_panel/admin_dashboard.php");
            exit();
        } else {
            $error = "❌ Incorrect email or password.";
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
<title>Admin Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
* {margin: 0; padding: 0; box-sizing: border-box;}
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
 
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
header {
  display: flex;
 flex-direction: row;
  background-color: #06389d;
  color: white;
 height: 50px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.3);
  animation: slideDown 0.8s ease-in-out;
}
#logo {
  height: 45px;
  width: 45px;
  border-radius: 50%;
  margin-right: 10px;
}
#heading {
  font-size: 22px;
  font-weight: 600;
  color: white;
  padding-top: 10px;
}
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  flex: 1;
  padding: 20px;
}
.form-box {
  background-color: #fff;
  padding: 30px;
  border-radius: 15px;
  max-width: 400px;
  width: 100%;
  text-align: center;
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  animation: fadeIn 1s ease;
}
h2 {
  margin-bottom: 20px;
  color: #06389d;
}
.input-group {
  margin-bottom: 20px;
  position: relative;
}
.input-group input {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ccc;
  border-radius: 25px;
  font-size: 16px;
  transition: 0.3s;
}
.input-group input:focus {
  border-color: #06389d;
  box-shadow: 0 0 10px rgba(6,56,157,0.3);
  outline: none;
}
.input-group span {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
}
#btn {
  background: #06389d;
  color: white;
  width: 100%;
  border: none;
  border-radius: 25px;
  padding: 12px;
  font-size: 18px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s ease;
}
#btn:hover {
  background: #052b7b;
  transform: scale(1.05);
}
.msg {
  margin-bottom: 15px;
  padding: 10px;
  font-weight: bold;
  border-radius: 5px;
  animation: fadeIn 0.6s ease;
}
.error {background-color: #ffdada; color: red;}
.success {background-color: #d4f7d4; color: green;}
.footer {
  text-align: center;
  padding: 10px;
  color: white;
  background: #06389d;
  font-size: 14px;
}
.footer i {color: red;}
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(-10px);}
  to {opacity: 1; transform: translateY(0);}
}
@keyframes slideDown {
  from {transform: translateY(-50px); opacity: 0;}
  to {transform: translateY(0); opacity: 1;}
}
@media (max-width: 480px) {
  header h2 {font-size: 16px;}
  .form-box {padding: 20px;}
  #heading {font-size: 18px;}
}
</style>
</head>
<body>

<header>
  <img id="logo" src="../images/sblogo.jpeg" alt="Logo">
  <h2 id="heading">Admin Panel – Sharanbasva University</h2>
</header>

<div class="container">
  <div class="form-box">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
      <div class="msg error"><?= $error ?></div>
    <?php elseif ($success): ?>
      <div class="msg success"><?= $success ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="input-group">
        <input type="email" name="email" placeholder="Enter Admin Email" required>
      </div>
      <div class="input-group">
        <input type="password" name="password" id="pass" placeholder="Enter Password" required>
        <span onclick="togglePassword()">👁️</span>
      </div>
      <button type="submit" id="btn">Login</button>
    </form>
  </div>
</div>

<div class="footer">
  <p>Made with <i>❤</i> by Taibaz Khanam</p>
</div>

<script>
function togglePassword() {
  var x = document.getElementById("pass");
  x.type = x.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
