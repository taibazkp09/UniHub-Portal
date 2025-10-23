
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome - Sharanbasva University</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --primary: #06389d;
      --secondary: #f4f4f4;
      --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: var(--font);
    }

    body {
      background: var(--secondary);
      color: var(--primary);
      overflow-x: hidden;
    }

    header {
      background: var(--primary);
      color: white;
      padding: 15px 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }

    header img {
      height: 50px;
      width: 50px;
      border-radius: 50%;
      margin-right: 15px;
    }

    h1 {
      font-size: 22px;
    }

    .container {
      padding: 50px 20px;
      text-align: center;
      animation: fadeIn 1.2s ease-in;
    }

    .container h2 {
      font-size: 28px;
      margin-bottom: 20px;
    }

    .role-box {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
      justify-content: center;
      margin-top: 30px;
    }

    .role {
      background: white;
      padding: 30px 20px;
      border: 2px solid var(--primary);
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      cursor: pointer;
      transition: all 0.4s ease-in-out;
      width: 240px;
    }

    .role:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-8px);
    }

    .role i {
      font-size: 45px;
      margin-bottom: 10px;
      color: var(--primary);
      transition: 0.4s;
    }

    .role:hover i {
      color: white;
    }
    .role:hover p{
        color: white;
    }

    .role h3 {
      margin: 10px 0;
      font-size: 20px;
    }

    .role p {
      font-size: 14px;
      color: #000000;
    }

    .footer {
      text-align: center;
      padding: 15px;
      background-color: var(--primary);
      color: white;
      font-size: 14px;
      margin-top: 40px;
    }

    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      h1 {
        font-size: 18px;
      }

      .container h2 {
        font-size: 22px;
      }

      .role {
        width: 90%;
        margin-bottom: 20px;
      }

      .role-box {
        flex-direction: column;
        gap: 20px;
      }

      .role h3 {
        font-size: 18px;
      }

      .role i {
        font-size: 35px;
      }
    }
  </style>
</head>
<body>

  <header>
    <img src="images/sblogo.jpeg" alt="Logo" />
    <h1>Welcome to Sharanbasva University</h1>
  </header>

  <div class="container">
    <h2>Select Your Login Panel</h2>
    <div class="role-box">
      <a class="role" href="auth/admin_login.php">
        <i class="fa-solid fa-user-shield"></i>
        <h3>Admin</h3>
        <p>Manage faculty, uploads, updates & data</p>
      </a>

      <a class="role" href="auth/faculty_login.php">
        <i class="fa-solid fa-chalkboard-teacher"></i>
        <h3>Faculty</h3>
        <p>Upload notes, view students, update subjects</p>
      </a>

      <a class="role" href="auth/student_register.php">
        <i class="fa-solid fa-user-graduate"></i>
        <h3>Student</h3>
        <p>Access notes, syllabus, announcements</p>
      </a>
    </div>
  </div>

  <div class="footer">
    Made with ❤️ by Taibaz Khanam 
  </div>

</body>
</html>
