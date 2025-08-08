    body {
      background: #f8f9fa;
      min-height: 100vh;
      padding: 10px;
    }
    .main-container {
      background: white;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      padding: 24px 18px;
      margin: 16px auto;
      max-width: 600px;
    }
    .form-header {
      background: #1a1a1a;
      color: white;
      border-radius: 14px;
      padding: 18px;
      margin-bottom: 18px;
      text-align: center;
    }
    .form-header h1 {
      font-weight: 600;
      margin-bottom: 6px;
      font-size: 20px;
    }
    .form-header p {
      opacity: 0.8;
      font-size: 13px;
      margin-bottom: 0;
    }
<?php
include '../../config/db.php';
session_start();

if ($_SESSION['user']['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

$nis = $_GET['nis'];
mysqli_query($conn, "DELETE FROM siswa WHERE nis='$nis'");

header("Location: index.php");