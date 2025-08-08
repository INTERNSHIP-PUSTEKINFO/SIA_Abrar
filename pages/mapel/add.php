<?php
include '../../config/db.php';
session_start();

if ($_SESSION['user']['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

$jurusan = mysqli_query($conn, "SELECT * FROM jurusan");

if (isset($_POST['simpan'])) {
  $kode_mapel = mysqli_real_escape_string($conn, $_POST['kode_mapel']);
  $nama_mapel = mysqli_real_escape_string($conn, $_POST['nama_mapel']);
  $jurusan_id = $_POST['jurusan_id'];

  // Cek apakah kode mapel sudah ada
  $cek = mysqli_query($conn, "SELECT * FROM mapel WHERE kode_mapel = '$kode_mapel'");
  if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Kode mata pelajaran sudah digunakan!'); window.location.href='add.php';</script>";
    exit;
  }

  mysqli_query($conn, "INSERT INTO mapel (kode_mapel, nama_mapel, jurusan_id) VALUES ('$kode_mapel', '$nama_mapel', '$jurusan_id')");
  header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Mata Pelajaran - Sistem Akademik SMK</title>
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    * {
      font-family: 'Inter', sans-serif;
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background: #f8f9fa;
      min-height: 100vh;
    }
    .form-container {
      background: white;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      margin: 24px auto;
      padding: 24px 18px;
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
    .form-group {
      margin-bottom: 12px;
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
      <h1><i class="fas fa-book me-3"></i>&nbsp;Tambah Mata Pelajaran</h1>
      <p>Isi data mata pelajaran baru untuk ditambahkan ke sistem</p>
    </div>

    <!-- Form -->
    <form method="POST">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-hashtag me-2"></i>&nbsp;Kode Mapel
          </label>
          <input type="text" name="kode_mapel" class="form-input" required placeholder="Contoh: RPL001" maxlength="10">
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-graduation-cap me-2"></i>&nbsp;Jurusan
          </label>
          <select name="jurusan_id" class="form-select" required>
            <option value="">Pilih Jurusan</option>
            <?php while ($j = mysqli_fetch_assoc($jurusan)) { ?>
              <option value="<?= $j['id'] ?>"><?= $j['kode_jurusan'] ?> - <?= $j['nama_jurusan'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="form-group full-width">
        <label class="form-label">
          <i class="fas fa-book me-2"></i>&nbsp;Nama Mata Pelajaran
        </label>
        <input type="text" name="nama_mapel" class="form-input" required placeholder="Masukkan nama mata pelajaran" maxlength="100">
      </div>

      <div class="form-actions">
        <button type="submit" name="simpan" class="btn-submit">
          <i class="fas fa-save"></i>&nbsp;
          Simpan Data
        </button>
        <a href="index.php" class="btn-secondary">
          <i class="fas fa-arrow-left"></i>&nbsp;
          Kembali
        </a>
      </div>
    </form>
  </div>

  <?php include '../../layout/close_layout.php'; ?>
  <?php include '../../layout/footer.php'; ?>
</body>
</html>
