<?php
include '../../config/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
  header("Location: ../../auth/login.php");
  exit;
}

$user_id = $_SESSION['user']['id'];
$siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM siswa WHERE user_id = $user_id"));
$nis = $siswa['nis'];

// Ambil jurusan siswa
$jurusan_siswa = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT s.jurusan_id, j.kode_jurusan, j.nama_jurusan
  FROM siswa s
  LEFT JOIN jurusan j ON s.jurusan_id = j.id
  WHERE s.nis = '$nis'
"));

$jurusan_id = $jurusan_siswa['jurusan_id'];

// Ambil mapel sesuai jurusan siswa
$mapel = mysqli_query($conn, "
  SELECT * FROM mapel 
  WHERE jurusan_id = '$jurusan_id'
  ORDER BY nama_mapel ASC
");

// Cek apakah sudah absen hari ini untuk mapel yang dipilih
$today = date('Y-m-d');

if (isset($_POST['submit'])) {
  if (!isset($_POST['keterangan']) || !isset($_POST['mapel_id']) || empty($_POST['mapel_id']) || empty($_POST['keterangan'])) {
    $message = "Mohon pilih mata pelajaran dan keterangan kehadiran.";
    $message_type = "warning";
  } else {
    $keterangan = $_POST['keterangan'];
    $mapel_id = (int)$_POST['mapel_id'];
    
    // Cek apakah sudah absen untuk mapel ini hari ini
    $cek = mysqli_query($conn, "SELECT * FROM absensi WHERE siswa_nis = '$nis' AND mapel_id = $mapel_id AND DATE(tanggal) = '$today'");
    
    if (mysqli_num_rows($cek) > 0) {
      $message = "Anda sudah melakukan absensi untuk mata pelajaran ini hari ini.";
      $message_type = "warning";
    } else {
      mysqli_query($conn, "INSERT INTO absensi (siswa_nis, mapel_id, tanggal, keterangan) VALUES ('$nis', $mapel_id, '$today', '$keterangan')");
      $message = "Absensi berhasil disimpan!";
      $message_type = "success";
    header("refresh:2;url=../../dashboard/index.php"); // Redirect to dashboard
  }
}
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Absensi Siswa - Sistem Akademik SMK</title>
  
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
    }
    .absensi-container {
      background: white;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      margin: 16px auto;
      padding: 24px 18px;
      max-width: 600px;
    }
    .absensi-header {
      background: #1a1a1a;
      color: white;
      border-radius: 14px;
      padding: 18px;
      margin-bottom: 18px;
      text-align: center;
    }
    .absensi-header h1 {
      font-weight: 600;
      margin-bottom: 6px;
      font-size: 20px;
    }
    .absensi-header p {
      opacity: 0.8;
      font-size: 13px;
      margin-bottom: 0;
    }
    .info-card {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 18px;
      border-left: 4px solid #1a1a1a;
    }
    
    .info-card h3 {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 10px;
      color: #1a1a1a;
    }
    
    .info-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 14px;
    }
    
    .info-label {
      font-weight: 500;
      color: #6c757d;
    }
    
    .info-value {
      font-weight: 600;
      color: #1a1a1a;
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
    
    .form-select {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s ease;
      background: white;
    }
    
    .form-select:focus {
      outline: none;
      border-color: #1a1a1a;
      box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.1);
    }
    
         .status-buttons {
       display: grid;
       grid-template-columns: repeat(2, 1fr);
       gap: 15px;
       margin-top: 10px;
     }
     
     .btn-status {
       padding: 15px;
       border: 2px solid #e9ecef;
       border-radius: 8px;
       background: white;
       color: #1a1a1a;
       font-weight: 500;
       font-size: 14px;
       cursor: pointer;
       transition: all 0.3s ease;
       display: flex;
       align-items: center;
       justify-content: center;
       gap: 8px;
     }
     
     .btn-status:hover {
       border-color: #1a1a1a;
       background: #1a1a1a;
       color: white;
       transform: translateY(-1px);
     }
     
     .btn-status.active {
       border-color: #1a1a1a;
       background: #1a1a1a;
       color: white;
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
      width: 100%;
      justify-content: center;
    }
    
    .btn-submit:hover {
      background: #000;
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    .btn-submit:disabled {
      background: #6c757d;
      cursor: not-allowed;
      transform: none;
    }
    
    .alert {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
    }
    
    .alert-success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-warning {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeaa7;
    }
    
    /* Enhanced Responsive Design */
    @media (max-width: 1200px) {
      .absensi-container {
        margin: 24px auto;
        padding: 25px;
      }
      
      .absensi-header {
        padding: 22px;
        margin-bottom: 25px;
      }
      
      .info-card {
        padding: 18px;
        margin-bottom: 25px;
      }
      
      .form-group {
        margin-bottom: 18px;
      }
    }
    
    @media (max-width: 768px) {
      .absensi-container {
        margin: 20px auto;
        padding: 20px;
        border-radius: 10px;
      }
      
      .absensi-header {
        padding: 18px;
        margin-bottom: 20px;
        border-radius: 10px;
      }
      
      .absensi-header h1 {
        font-size: 22px;
      }
      
      .info-card {
        padding: 16px;
        margin-bottom: 20px;
        border-radius: 6px;
      }
      
      .info-card h3 {
        font-size: 15px;
      }
      
      .info-item {
        font-size: 13px;
        margin-bottom: 6px;
      }
      
      .form-group {
        margin-bottom: 16px;
      }
      
      .form-select {
        padding: 11px 15px;
        font-size: 13px;
      }
      
      .status-buttons {
        grid-template-columns: 1fr;
        gap: 12px;
      }
      
      .btn-status {
        padding: 12px;
        font-size: 13px;
      }
      
      .btn-submit {
        padding: 11px 20px;
        font-size: 13px;
      }
      
      .alert {
        padding: 12px;
        font-size: 13px;
        margin-bottom: 16px;
      }
    }
    
    @media (max-width: 576px) {
      .absensi-container {
        margin: 16px auto;
        padding: 15px;
        border-radius: 8px;
      }
      
      .absensi-header {
        padding: 15px;
        margin-bottom: 18px;
        border-radius: 8px;
      }
      
      .absensi-header h1 {
        font-size: 20px;
      }
      
      .absensi-header p {
        font-size: 13px;
      }
      
      .info-card {
        padding: 14px;
        margin-bottom: 18px;
      }
      
      .info-card h3 {
        font-size: 14px;
        margin-bottom: 8px;
      }
      
      .info-item {
        font-size: 12px;
        margin-bottom: 5px;
      }
      
      .form-group {
        margin-bottom: 14px;
      }
      
      .form-select {
        padding: 10px 14px;
        font-size: 12px;
      }
      
      .status-buttons {
        gap: 10px;
      }
      
      .btn-status {
        padding: 10px;
        font-size: 12px;
      }
      
      .btn-submit {
        padding: 10px 18px;
        font-size: 12px;
      }
      
      .alert {
        padding: 10px;
        font-size: 12px;
        margin-bottom: 14px;
      }
    }
  </style>
</head>
<body>

  
  
  <div class="absensi-container">
    
    <!-- Absensi Header -->
    <div class="absensi-header">
      <h1><i class="fas fa-clipboard-check me-3"></i>Absensi Siswa</h1>
      <p>Silakan pilih mata pelajaran dan status kehadiran Anda untuk hari ini</p>
    </div>

    <!-- Info Siswa -->
    <div class="info-card">
      <h3><i class="fas fa-user me-2"></i>Informasi Siswa</h3>
      <div class="info-item">
        <span class="info-label">Nama:</span>
        <span class="info-value"><?= htmlspecialchars($_SESSION['user']['nama']) ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">NIS:</span>
        <span class="info-value"><?= htmlspecialchars($nis) ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">Jurusan:</span>
        <span class="info-value"><?= htmlspecialchars($jurusan_siswa['kode_jurusan']) ?> - <?= htmlspecialchars($jurusan_siswa['nama_jurusan']) ?></span>
      </div>
      <div class="info-item">
        <span class="info-label">Tanggal:</span>
        <span class="info-value"><?= date('d/m/Y') ?></span>
      </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($message)): ?>
      <div class="alert alert-<?= $message_type ?>">
        <i class="fas fa-<?= $message_type == 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
        <?= $message ?>
      </div>
    <?php endif; ?>

    <!-- Absensi Form -->
    <form method="POST" id="absensiForm">
      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-book me-2"></i>Mata Pelajaran
        </label>
        <select name="mapel_id" class="form-select" required>
          <option value="">Pilih Mata Pelajaran</option>
          <?php while ($m = mysqli_fetch_assoc($mapel)) { ?>
            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['kode_mapel']) ?> - <?= htmlspecialchars($m['nama_mapel']) ?></option>
          <?php } ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-clipboard-check me-2"></i>Status Kehadiran
        </label>
        <div class="status-buttons">
          <button type="button" class="btn-status" data-value="Hadir">
            <i class="fas fa-check-circle me-2"></i>
            Hadir
          </button>
          <button type="button" class="btn-status" data-value="Izin">
            <i class="fas fa-exclamation-circle me-2"></i>
            Izin
          </button>
          <button type="button" class="btn-status" data-value="Sakit">
            <i class="fas fa-thermometer-half me-2"></i>
            Sakit
          </button>
          <button type="button" class="btn-status" data-value="Alpha">
            <i class="fas fa-times-circle me-2"></i>
            Alpha
          </button>
        </div>
        <input type="hidden" name="keterangan" id="status" required>
      </div>

      <button type="submit" name="submit" class="btn-submit" id="submitBtn" disabled>
        <i class="fas fa-save"></i>
        Simpan Absensi
      </button>
    </form>
  </div>

  <?php include '../../layout/close_layout.php'; ?>

  <script>
    // Handle status buttons
    const statusButtons = document.querySelectorAll('.btn-status');
    const statusInput = document.getElementById('status');
    const submitBtn = document.getElementById('submitBtn');
    const mapelSelect = document.querySelector('select[name="mapel_id"]');

    statusButtons.forEach(button => {
      button.addEventListener('click', function() {
        // Remove active class from all buttons
        statusButtons.forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');
        // Set the value
        statusInput.value = this.dataset.value;
        checkForm();
      });
    });

    mapelSelect.addEventListener('change', checkForm);

    function checkForm() {
      const mapelSelected = mapelSelect.value !== '';
      const statusSelected = statusInput.value !== '';
      
      submitBtn.disabled = !(mapelSelected && statusSelected);
    }
  </script>
</body>
</html>
