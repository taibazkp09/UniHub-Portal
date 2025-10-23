<?php
session_start();

// Destroy session
session_unset();
session_destroy();

// Message to show before redirect
$logoutMessage = "✅ You have been logged out successfully!";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Logout</title>
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
  align-items: center;
  background-color: #06389d;
  color: white;
  height: 50px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.3);
  animation: slideDown 0.8s ease-in-out;
  padding-left: 10px;
}
#logo {
  height: 45px;
  width: 45px;
  border-radius: 50%;
  margin-right: 10px;
}
#heading {
  font-size: 20px;
  font-weight: 600;
}
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  flex: 1;
  padding: 20px;
}
.box {
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
  margin-bottom: 10px;
  color:white;
}
.msg {
  margin-bottom: 20px;
  padding: 12px;
  font-weight: bold;
  border-radius: 5px;
  background-color: #d4f7d4;
  color: green;
  animation: fadeIn 0.6s ease;
}
button {
  background: #06389d;
  color: white;
  width: 100%;
  border: none;
  border-radius: 25px;
  padding: 12px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s ease;
}
button:hover {
  background: #052b7b;
  transform: scale(1.05);
}
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
</style>
</head>
<body>

<header>
  <img id="logo" src="../images/sblogo.jpeg" alt="Logo">
  <h2 id="heading">Admin Panel – Sharanbasva University</h2>
</header>

<div class="container">
  <div class="box">
    <h2>Logged Out</h2>
    <div class="msg"><?php echo $logoutMessage; ?></div>
    <form action="../auth/admin_login.php" method="get">
      <button type="submit">🔑 Login Again</button>
    </form>
  </div>
</div>

<div class="footer">
  <p>Made with <i>❤</i> by Taibaz Khanam</p>
</div>

<script>
// Auto redirect to login page after 3 seconds
setTimeout(function(){
    window.location.href = "../auth/admin_login.php";
}, 3000);
</script>

</body>
</html>
