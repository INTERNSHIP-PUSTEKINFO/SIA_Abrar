<?php
session_start();
include '../config/db.php';

if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
  
  if (mysqli_num_rows($query) === 1) {
    $user = mysqli_fetch_assoc($query);
    $db_pass = $user['password'];

    // Hash match
    if (password_verify($password, $db_pass)) {
      $_SESSION['user'] = $user;
    }
    // Password plain match, upgrade ke hash
    elseif ($password === $db_pass) {
      $new_hashed = password_hash($password, PASSWORD_DEFAULT);
      mysqli_query($conn, "UPDATE users SET password = '$new_hashed' WHERE email = '$email'");
      $_SESSION['user'] = $user;
    } else {
      $error = "Password salah!";
    }

    // Kalau login sukses (hash match atau plain match)
    if (isset($_SESSION['user'])) {
      if ($user['role'] == 'siswa') {
        header("Location: ../pages/absensi/siswa_auto.php");
      } else {
        header("Location: ../dashboard/index.php");
      }
      exit;
    }

  } else {
    $error = "Email tidak ditemukan!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistem Akademik SMK</title>
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      background: #f8f9fa;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .login-container {
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      padding: 40px;
      width: 100%;
      max-width: 400px;
      margin: 20px;
    }
    
    .login-header {
      text-align: center;
      margin-bottom: 32px;
    }
    
    .login-header h1 {
      font-size: 28px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 8px;
    }
    
    .login-header p {
      color: #6c757d;
      font-size: 14px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      color: #1a1a1a;
      margin-bottom: 8px;
    }
    
    .form-input {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s ease;
      background: white;
    }
    
    .form-input:focus {
      outline: none;
      border-color: #1a1a1a;
      box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.1);
    }
    
    .input-group {
      position: relative;
    }
    
    .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
      font-size: 14px;
    }
    
    .input-with-icon {
      padding-left: 44px;
    }
    
    .btn-login {
      width: 100%;
    /* Compact but not too close */
    body {
      padding: 10px;
    }
    .login-container {
      padding: 28px 22px;
      margin: 16px auto;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      max-width: 400px;
    }
    .login-header {
      margin-bottom: 22px;
    }
    .login-header h1 {
      font-size: 24px;
      margin-bottom: 6px;
    }
    .login-header p {
      font-size: 13px;
    }
    .form-group {
      margin-bottom: 14px;
    }
    .form-label {
      margin-bottom: 6px;
      font-size: 13px;
    }
    .form-input {
      padding: 10px 13px;
      font-size: 13px;
      border-radius: 7px;
    }
    .input-with-icon {
      padding-left: 38px;
    }
    .btn-login {
      padding: 16px 24px;
      font-size: 13px;
      border-radius: 7px;
      margin-top: 6px;
    }
      background: #1a1a1a;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 8px;
    }
    
    .btn-login:hover {
      background: #000;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 14px;
      margin-bottom: 20px;
    }
    
    .alert-danger {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    .login-footer {
      text-align: center;
      margin-top: 24px;
      padding-top: 24px;
      border-top: 1px solid #e9ecef;
    }
    
    .login-footer a {
      color: #1a1a1a;
      text-decoration: none;
      font-weight: 500;
    }
    
    .login-footer a:hover {
      text-decoration: underline;
    }
    
    .logo {
      width: 60px;
      height: 60px;
      background: #1a1a1a;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      color: white;
      font-size: 24px;
    }
    
    /* Enhanced Responsive Design */
    @media (max-width: 1200px) {
      .login-container {
        padding: 35px;
        margin: 18px;
      }
    }
    
    @media (max-width: 768px) {
      .login-container {
        margin: 15px;
        padding: 30px 25px;
        border-radius: 12px;
      }
      
      .login-header {
        margin-bottom: 28px;
      }
      
      .login-header h1 {
        font-size: 26px;
      }
      
      .form-group {
        margin-bottom: 18px;
      }
      
      .form-input {
        padding: 11px 15px;
      }
      
      .btn-login {
        padding: 11px 22px;
      }
    }
    
    @media (max-width: 576px) {
      .login-container {
        margin: 12px;
        padding: 25px 20px;
        border-radius: 10px;
      }
      
      .login-header {
        margin-bottom: 24px;
      }
      
      .login-header h1 {
        font-size: 24px;
      }
      
      .login-header p {
        font-size: 13px;
      }
      
      .form-group {
        margin-bottom: 16px;
      }
      
      .form-input {
        padding: 10px 14px;
        font-size: 13px;
      }
      
      .input-with-icon {
        padding-left: 40px;
      }
      
      .btn-login {
        padding: 10px 20px;
        font-size: 13px;
      }
      
      .logo {
        width: 50px;
        height: 50px;
        font-size: 20px;
        margin-bottom: 12px;
      }
      
      .login-footer {
        margin-top: 20px;
        padding-top: 20px;
      }
      
      .alert {
        padding: 10px 14px;
        font-size: 13px;
        margin-bottom: 16px;
      }
    }
    
    @media (max-width: 480px) {
      .login-container {
        margin: 8px;
        padding: 20px 16px;
        border-radius: 8px;
      }
      
      .login-header h1 {
        font-size: 22px;
      }
      
      .form-input {
        padding: 9px 12px;
      }
      
      .input-with-icon {
        padding-left: 36px;
      }
      
      .btn-login {
        padding: 9px 18px;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-header">
      <div class="logo">
        <i class="fas fa-graduation-cap"></i>
      </div>
      <h1>Selamat Datang</h1>
      <p>Masuk ke Sistem Akademik SMK</p>
    </div>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label">Email</label>
        <div class="input-group">
          <i class="fas fa-envelope input-icon"></i>
          <input type="email" name="email" class="form-input input-with-icon" placeholder="Masukkan email Anda" required>
        </div>
      </div>
      
      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-group">
          <i class="fas fa-lock input-icon"></i>
          <input type="password" name="password" class="form-input input-with-icon" placeholder="Masukkan password Anda" required>
        </div>
      </div>
      
      <button type="submit" name="login" class="btn-login">
        <i class="fas fa-sign-in-alt me-2"></i>
        Masuk
      </button>
    </form>

  </div>

</body>
</html>
