<?php
include '../../config/db.php';
session_start();

if ($_SESSION['user']['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

$kelas = mysqli_query($conn, "SELECT * FROM kelas");
$mapel = mysqli_query($conn, "SELECT * FROM mapel");
$guru = mysqli_query($conn, "SELECT g.*, u.nama FROM guru g JOIN users u ON g.user_id = u.id");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kelas_id = $_POST['kelas_id'];
    $mapel_id = $_POST['mapel_id'];
    $guru_id = $_POST['guru_id'];
    $hari = $_POST['hari'];
    $jam_ke = $_POST['jam_ke'];

    mysqli_query($conn, "INSERT INTO jadwal (kelas_id, mapel_id, guru_id, hari, jam_ke) VALUES ('$kelas_id', '$mapel_id', '$guru_id', '$hari', '$jam_ke')");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Jadwal - Sistem Akademik SMK</title>
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    * {
      font-family: 'Inter', sans-serif;
    }
    
    body {
      background: #f8f9fa;
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }
    
    .form-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin: 20px;
      padding: 30px;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .form-header {
      background: #1a1a1a;
      color: white;
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 30px;
      text-align: center;
    }
    
    .form-header h1 {
      font-weight: 600;
      margin-bottom: 8px;
      font-size: 24px;
    }
    
    .form-header p {
      opacity: 0.8;
      font-size: 14px;
      margin-bottom: 0;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-label {
      font-weight: 500;
      color: #1a1a1a;
      margin-bottom: 8px;
      display: block;
      font-size: 14px;
    }
    
    .form-input, .form-select {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s ease;
      background: white;
    }
    
    .form-input:focus, .form-select:focus {
      outline: none;
      border-color: #1a1a1a;
      box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.1);
    }
    
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    
    .form-row .form-group {
      margin-bottom: 0;
    }
    
    .full-width {
      grid-column: 1 / -1;
    }
    
    .btn-submit {
      background: #1a1a1a;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 500;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    
    .btn-submit:hover {
      background: #000;
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    .btn-secondary {
      background: #6c757d;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 500;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-left: 12px;
    }
    
    .btn-secondary:hover {
      background: #5a6268;
      color: white;
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    .form-actions {
      margin-top: 30px;
      text-align: center;
    }
    
    @media (max-width: 768px) {
      .form-container {
        margin: 10px;
        padding: 20px;
      }
      
      .form-header {
        padding: 20px;
      }
      
      .form-row {
        grid-template-columns: 1fr;
        gap: 0;
      }
    }
  </style>
</head>
<body>

  <?php include '../../layout/navbar.php'; ?>
  
  <div class="form-container">
    
    <!-- Form Header -->
    <div class="form-header">
      <h1><i class="fas fa-calendar-alt me-3"></i>Tambah Jadwal Pelajaran</h1>
      <p>Atur jadwal pelajaran untuk kelas tertentu</p>
    </div>

    <!-- Form -->
    <form method="POST">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-chalkboard me-2"></i>Kelas
          </label>
          <select name="kelas_id" class="form-select" required>
            <?php while ($k = mysqli_fetch_assoc($kelas)) : ?>
              <option value="<?= $k['id'] ?>"><?= $k['nama_kelas'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-book me-2"></i>Mata Pelajaran
          </label>
          <select name="mapel_id" class="form-select" required>
            <?php while ($m = mysqli_fetch_assoc($mapel)) : ?>
              <option value="<?= $m['id'] ?>"><?= $m['nama_mapel'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-chalkboard-teacher me-2"></i>Guru
          </label>
          <select name="guru_id" class="form-select" required>
            <?php while ($g = mysqli_fetch_assoc($guru)) : ?>
              <option value="<?= $g['user_id'] ?>"><?= $g['nama'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-calendar-day me-2"></i>Hari
          </label>
          <select name="hari" class="form-select" required>
            <option value="Senin">Senin</option>
            <option value="Selasa">Selasa</option>
            <option value="Rabu">Rabu</option>
            <option value="Kamis">Kamis</option>
            <option value="Jumat">Jumat</option>
          </select>
        </div>
      </div>

      <div class="form-group full-width">
        <label class="form-label">
          <i class="fas fa-clock me-2"></i>Jam Ke-
        </label>
        <input type="number" name="jam_ke" class="form-input" required min="1" max="10" placeholder="1-10">
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">
          <i class="fas fa-save"></i>
          Simpan Jadwal
        </button>
        <a href="index.php" class="btn-secondary">
          <i class="fas fa-arrow-left"></i>
          Kembali
        </a>
      </div>
    </form>
  </div>

  <?php include '../../layout/close_layout.php'; ?>
  <?php include '../../layout/footer.php'; ?>
</body>
</html>
