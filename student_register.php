<?php //include $_SERVER['DOCUMENT_ROOT'] . '/db_connect.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'clg';
$conn = new mysqli($host, $user, $pass, $db, 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$full_name = $email = $phone = $usn = $semester = $password = "";
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $usn = strtoupper(trim($_POST['usn']));
    $semester = trim($_POST['semester']);
    $password = trim($_POST['password']);

    if (empty($full_name) || empty($email) || empty($phone) || empty($usn) || empty($semester) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone number must be 10 digits.";
    } elseif (!preg_match('/^SG\d{2}BCA\d{3}$/i', $usn)) {
        $error = "USN format should be like SG23BCA123.";
    } else {
        $check = $conn->prepare("SELECT * FROM valid_usns WHERE usn = ? AND semester = ?");
        $check->bind_param("ss", $usn, $semester);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            $error = "USN not found in selected semester.";
        } else {
            $checkDup = $conn->prepare("SELECT * FROM s_info WHERE email = ? OR phone = ? OR usn = ?");
            $checkDup->bind_param("sss", $email, $phone, $usn);
            $checkDup->execute();
            $dupResult = $checkDup->get_result();

            if ($dupResult->num_rows > 0) {
                $error = "Email, phone, or USN already registered.";
            } else {
                $insert = $conn->prepare("INSERT INTO s_info (full_name, email, phone, usn, semester, password) VALUES (?, ?, ?, ?, ?, ?)");
                $insert->bind_param("ssssss", $full_name, $email, $phone, $usn, $semester, $password);
                if ($insert->execute()) {
                    $success = "✅ Registration successful! Redirecting to login...";
                    echo "<script>
                        setTimeout(() => { window.location.href='student_login.php'; }, 3000);
                    </script>";
                    $full_name = $email = $phone = $usn = $semester = $password = "";
                } else {
                    $error = "Something went wrong.";
                }
                $insert->close();
            }
            $checkDup->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Registration</title>
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
      color:#fff;font-size:20px;font-weight:bold;padding-top: 10px;
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
    .input-group input,
    .input-group select {
      width:100%;padding:12px;
      border-radius:30px;
      border:1px solid #ccc;
      font-size:14px;font-weight:bold;
      outline:none;
      transition:0.3s;
    }
    .input-group input:focus,
    .input-group select:focus {
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
      <h2>Sign Up</h2>
      <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
      <?php elseif ($success): ?>
        <div class="message success"><?= $success ?></div>
      <?php endif; ?>
      <div class="input-group">
        <input type="text" name="full_name" value="<?= htmlspecialchars($full_name) ?>" placeholder="Enter Full Name" required>
      </div>
      <div class="input-group">
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Enter Email" required>
      </div>
      <div class="input-group">
        <input type="number" name="phone" value="<?= htmlspecialchars($phone) ?>" placeholder="Enter Phone Number" required>
      </div>
      <div class="input-group">
        <input type="text" name="usn" value="<?= htmlspecialchars($usn) ?>" placeholder="Enter USN (e.g. SG23BCA123)" required>
      </div>
      <div class="input-group">
        <select name="semester" required>
          <option value="">-- Select Semester --</option>
          <option value="1" <?= $semester=='1'?'selected':''; ?>>Semester 1</option>
          <option value="2" <?= $semester=='2'?'selected':''; ?>>Semester 2</option>
          <option value="3" <?= $semester=='3'?'selected':''; ?>>Semester 3</option>
          <option value="4" <?= $semester=='4'?'selected':''; ?>>Semester 4</option>
          <option value="5" <?= $semester=='5'?'selected':''; ?>>Semester 5</option>
          <option value="6" <?= $semester=='6'?'selected':''; ?>>Semester 6</option>
        </select>
      </div>
      <div class="input-group password-toggle">
        <input type="password" id="password" name="password" placeholder="Enter Password" required>
        <i class="fa fa-eye" id="togglePassword"></i>
      </div>
      <button type="submit" id="btn">Register</button>
      <p style="text-align:center;margin-top:10px;">Already have an account? <a href="student_login.php" style="color:#06389d;font-weight:bold;">Login</a></p>
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
