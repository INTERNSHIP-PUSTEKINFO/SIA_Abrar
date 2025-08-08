<?php
include '../../config/db.php';
session_start();

if ($_SESSION['user']['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

$jurusan = mysqli_query($conn, "SELECT * FROM jurusan");
$kelas = mysqli_query($conn, "SELECT * FROM kelas");

if (isset($_POST['simpan'])) {
  $nis      = mysqli_real_escape_string($conn, $_POST['nis']);
  $email    = mysqli_real_escape_string($conn, $_POST['email']);
  $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $tempat   = mysqli_real_escape_string($conn, $_POST['tempat']);
  $tgl      = $_POST['tanggal'];
  $jk       = $_POST['jk'];
  $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
  $jurusan_id = $_POST['jurusan_id'];
  $kelas_id   = $_POST['kelas_id'];
  $tahun      = $_POST['tahun'];
  $status     = $_POST['status'] ?? 'aktif';

  // Cek apakah email udah dipake
  $cek = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
  if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Email sudah digunakan!'); window.location.href='add.php';</script>";
    exit;
  }

  mysqli_begin_transaction($conn);
  try {
    mysqli_query($conn, "INSERT INTO users (nama, email, password, role, created_at, updated_at) 
                         VALUES ('$nama', '$email', '$password', 'siswa', NOW(), NOW())");

    $user_id = mysqli_insert_id($conn);

    mysqli_query($conn, "INSERT INTO siswa (nis, user_id, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, jurusan_id, kelas_id, tahun_masuk, status)
                         VALUES ('$nis', '$user_id', '$tempat', '$tgl', '$jk', '$alamat', '$jurusan_id', '$kelas_id', '$tahun', '$status')");

    mysqli_commit($conn);
    header("Location: index.php");
  } catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Gagal menyimpan data: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Siswa - Sistem Akademik SMK</title>
  
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
      margin: 20px auto;
      padding: 40px;
      max-width: 900px;
      width: 95%;
    }
    
    .form-header {
      background: #1a1a1a;
      color: white;
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 35px;
      text-align: center;
      margin-left: 15px;
      margin-right: 15px;
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
      margin-bottom: 30px;
    }
    
    .form-label {
      font-weight: 500;
      color: #1a1a1a;
      margin-bottom: 12px;
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
      height: 45px;
    }
    
    .form-input:focus, .form-select:focus {
      outline: none;
      border-color: #1a1a1a;
      box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.1);
    }
    
    textarea.form-input {
      height: auto;
      min-height: 100px;
    }
    
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      margin-bottom: 15px;
      padding: 0 15px;
    }
    
    .form-row .form-group {
      margin-bottom: 0;
    }
    
    .full-width {
      grid-column: 1 / -1;
      padding: 0 15px;
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
      margin-top: 35px;
      text-align: center;
      padding: 0 15px;
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
        gap: 15px;
        margin-bottom: 5px;
      }
      
      .form-group {
        margin-bottom: 20px;
      }
    }
  </style>
</head>
<body>

  <?php include '../../layout/navbar.php'; ?>
  
  <div class="form-container">
    
    <!-- Form Header -->
    <div class="form-header">
      <h1><i class="fas fa-user-plus me-3"></i>&nbsp;Tambah Siswa Baru</h1>
      <p>Isi data lengkap siswa untuk menambahkan ke sistem akademik</p>
    </div>

    <!-- Form -->
    <form method="POST">
      <div class="form-row">
      <div class="form-group">
        <label class="form-label">
        <i class="fas fa-envelope me-2"></i>&nbsp;Email
        </label>
        <input type="email" name="email" class="form-input" required placeholder="Masukkan email siswa">
      </div>

      <div class="form-group">
        <label class="form-label">
        <i class="fas fa-lock me-2"></i>&nbsp;Password
        </label>
        <input type="password" name="password" class="form-input" required placeholder="Masukkan password">
      </div>
      </div>

      <div class="form-row">
      <div class="form-group">
        <label class="form-label">
        <i class="fas fa-hashtag me-2"></i>&nbsp;NIS
        </label>
        <input type="text" name="nis" class="form-input" required placeholder="Masukkan NIS">
      </div>

      <div class="form-group">
        <label class="form-label">
        <i class="fas fa-user me-2"></i>&nbsp;Nama Lengkap
        </label>
        <input type="text" name="nama" class="form-input" required placeholder="Masukkan nama lengkap">
      </div>
      </div>

      <div class="form-row">
      <div class="form-group">
        <label class="form-label">
        <i class="fas fa-map-marker-alt me-2"></i>&nbsp;Tempat Lahir
        </label>
        <input type="text" name="tempat" class="form-input" placeholder="Masukkan tempat lahir">
      </div>

      <div class="form-group">
        <label class="form-label">
        <i class="fas fa-calendar me-2"></i>&nbsp;Tanggal Lahir
        </label>
        <input type="date" name="tanggal" class="form-input">
      </div>
      </div>

      <div class="form-row">
      <div class="form-group">
        <label class="form-label">
        <i class="fas fa-venus-mars me-2"></i>&nbsp;Jenis Kelamin
        </label>
        <select name="jk" class="form-select">
        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">
        <i class="fas fa-calendar-alt me-2"></i>&nbsp;Tahun Masuk
        </label>
        <input type="number" name="tahun" class="form-input" min="2000" max="2030" placeholder="Contoh: 2023">
      </div>
      </div>

      <div class="form-group full-width">
      <label class="form-label">
        <i class="fas fa-map-marker-alt me-2"></i>&nbsp;Alamat
      </label>
      <textarea name="alamat" class="form-input" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
      </div>

      <div class="form-row">
      <div class="form-group">
        <label class="form-label">
        <i class="fas fa-graduation-cap me-2"></i>&nbsp;Jurusan
        </label>
        <select name="jurusan_id" class="form-select">
        <?php while ($j = mysqli_fetch_assoc($jurusan)) { ?>
          <option value="<?= $j['id'] ?>"><?= $j['nama_jurusan'] ?></option>
        <?php } ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">
        <i class="fas fa-chalkboard me-2"></i>&nbsp;Kelas
        </label>
        <select name="kelas_id" class="form-select">
        <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
          <option value="<?= $k['id'] ?>"><?= $k['nama_kelas'] ?></option>
        <?php } ?>
        </select>
      </div>
      </div>

      <div class="form-actions">
      <button type="submit" name="simpan" class="btn-submit">
        <i class="fas fa-save"></i>&nbsp;Simpan Data
      </button>
      <a href="index.php" class="btn-secondary">
        <i class="fas fa-arrow-left"></i>&nbsp;Kembali
      </a>
      </div>
    </form>
  </div>

  <?php include '../../layout/close_layout.php'; ?>
  <?php include '../../layout/footer.php'; ?>
</body>
</html>