<?php
include '../../config/db.php';
session_start();

if ($_SESSION['user']['role'] !== 'siswa') {
  header("Location: ../../auth/login.php");
  exit;
}

$user_id = $_SESSION['user']['id'];

// Ambil NIS siswa berdasarkan user login
$getSiswa = mysqli_query($conn, "SELECT nis FROM siswa WHERE user_id = $user_id");
$siswa = mysqli_fetch_assoc($getSiswa);
$siswa_nis = $siswa['nis'];

// Cek apakah siswa sudah absen hari ini
$tanggal = date('Y-m-d');
$cek = mysqli_query($conn, "SELECT * FROM absensi WHERE siswa_nis = '$siswa_nis' AND DATE(tanggal) = '$tanggal'");
$sudah_absen = mysqli_num_rows($cek) > 0;

if (isset($_POST['absen']) && !$sudah_absen) {
  $status = $_POST['status'] ?? '';

  // Validasi status harus Hadir, Izin, atau Sakit
    if (in_array($status, ['Hadir', 'Izin', 'Sakit'])) {
      mysqli_query($conn, "
        INSERT INTO absensi (siswa_nis, tanggal, keterangan)
        VALUES ('$siswa_nis', '$tanggal', '$status')
      ");
      header("Location: siswa_form.php?success=1");
      exit;
    } else {
      $error = "Status tidak valid.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Absensi - Sistem Akademik SMK</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
      padding: 10px;
    }
    .form-container {
      background: white;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      margin: 16px auto;
      padding: 24px 18px;
      max-width: 600px;
    }
    .form-header {
      text-align: center;
      margin-bottom: 18px;
    }
    .form-header h1 {
      font-size: 20px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 6px;
    }
    .form-header p {
      color: #6c757d;
      font-size: 13px;
      margin-bottom: 0;
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
      text-align: center;
      margin-bottom: 30px;
    }
    
    .form-header h1 {
      font-size: 24px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 8px;
    }
    
    .form-header p {
      color: #6c757d;
      font-size: 14px;
      margin-bottom: 0;
    }
    
    .status-card {
      background: #f8f9fa;
      border-radius: 12px;
      padding: 24px;
      margin-bottom: 24px;
      border: 1px solid #e9ecef;
    }
    
    .status-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      color: white;
      margin: 0 auto 16px;
    }
    
    .status-success {
      background: #28a745;
    }
    
    .status-info {
      background: #17a2b8;
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
    
    .form-select {
      width: 100%;
      padding: 12px 16px;
      border: 3px solid #e9ecef;
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
    
    .btn-submit {
      width: 100%;
      padding: 12px 24px;
      background: #1a1a1a;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
      background: #000;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .alert {
      padding: 16px;
      border-radius: 8px;
      font-size: 14px;
      margin-bottom: 20px;
      border: none;
    }
    
    .alert-success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-danger {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    .date-info {
      background: #e9ecef;
      border-radius: 8px;
      padding: 12px 16px;
      margin-bottom: 20px;
      font-size: 14px;
      color: #6c757d;
      text-align: center;
    }
    
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 20px;
    }
    
    .quick-btn {
      padding: 12px;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      background: white;
      color: #1a1a1a;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      text-align: center;
      font-size: 14px;
    }
    
    .quick-btn:hover {
      border-color: #1a1a1a;
      background: #f8f9fa;
    }
    
    .quick-btn.active {
      background: #1a1a1a;
      color: white;
      border-color: #1a1a1a;
    }
    
    /* Enhanced Responsive Design */
    @media (max-width: 1200px) {
      .form-container {
        margin: 18px;
        padding: 25px;
      }
      
      .status-card {
        padding: 20px;
        margin-bottom: 20px;
      }
      
      .form-group {
        margin-bottom: 18px;
      }
      
      .date-info {
        padding: 11px 15px;
        margin-bottom: 18px;
      }
    }
    
    @media (max-width: 768px) {
      .form-container {
        margin: 12px;
        padding: 20px;
        border-radius: 10px;
      }
      
      .form-header {
        margin-bottom: 25px;
      }
      
      .form-header h1 {
        font-size: 22px;
      }
      
      .status-card {
        padding: 18px;
        margin-bottom: 18px;
        border-radius: 10px;
      }
      
      .status-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
        margin-bottom: 12px;
      }
      
      .form-group {
        margin-bottom: 16px;
      }
      
      .form-select {
        padding: 11px 15px;
        font-size: 13px;
      }
      
      .date-info {
        padding: 10px 14px;
        margin-bottom: 16px;
        font-size: 13px;
      }
      
      .quick-actions {
        grid-template-columns: 1fr;
        gap: 10px;
        margin-bottom: 16px;
      }
      
      .quick-btn {
        padding: 10px;
        font-size: 13px;
      }
      
      .btn-submit {
        padding: 11px 20px;
        font-size: 13px;
      }
      
      .alert {
        padding: 14px;
        font-size: 13px;
        margin-bottom: 16px;
      }
    }
    
    @media (max-width: 576px) {
      .form-container {
        margin: 8px;
        padding: 15px;
        border-radius: 8px;
      }
      
      .form-header {
        margin-bottom: 20px;
      }
      
      .form-header h1 {
        font-size: 20px;
      }
      
      .form-header p {
        font-size: 13px;
      }
      
      .status-card {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
      }
      
      .status-icon {
        width: 45px;
        height: 45px;
        font-size: 18px;
        margin-bottom: 10px;
      }
      
      .form-group {
        margin-bottom: 14px;
      }
      
      .form-select {
        padding: 10px 14px;
        font-size: 12px;
      }
      
      .date-info {
        padding: 9px 12px;
        margin-bottom: 14px;
        font-size: 12px;
      }
      
      .quick-actions {
        gap: 8px;
        margin-bottom: 14px;
      }
      
      .quick-btn {
        padding: 9px;
        font-size: 12px;
      }
      
      .btn-submit {
        padding: 10px 18px;
        font-size: 12px;
      }
      
      .alert {
        padding: 12px;
        font-size: 12px;
        margin-bottom: 14px;
      }
    }
  </style>
</head>
<body>

<?php include '../../layout/navbar.php'; ?>
  
  <div class="form-container">
    
    <div class="form-header">
      <h1><i class="fas fa-clipboard-check me-3"></i>Form Absensi</h1>
      <p>Isi absensi harian kamu dengan mudah dan cepat</p>
    </div>

    <div class="date-info">
      <i class="fas fa-calendar me-2"></i>
      Tanggal: <?= date('d F Y', strtotime($tanggal)) ?>
    </div>

    <?php if ($sudah_absen): ?>
      <div class="status-card">
        <div class="status-icon status-success">
          <i class="fas fa-check"></i>
        </div>
        <h4 class="text-center mb-2">Absensi Sudah Dicatat!</h4>
        <p class="text-center text-muted mb-0">
          Kamu sudah mengisi absensi untuk hari ini. Terima kasih telah hadir tepat waktu! 👍
        </p>
      </div>
      
      <div class="text-center">
        <a href="../../dashboard/index.php" class="btn-submit" style="text-decoration: none; display: inline-block; width: auto; padding: 10px 20px;">
          <i class="fas fa-home me-2"></i>
          Kembali ke Dashboard
        </a>
      </div>
      
    <?php elseif (isset($_GET['success'])): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        Absensi berhasil dicatat! Terima kasih telah mengisi absensi hari ini.
      </div>
      
      <div class="text-center">
        <a href="../../dashboard/index.php" class="btn-submit" style="text-decoration: none; display: inline-block; width: auto; padding: 10px 20px;">
          <i class="fas fa-home me-2"></i>
          Kembali ke Dashboard
        </a>
      </div>
      
    <?php else: ?>
      <?php if (isset($error)): ?>
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-circle me-2"></i>
          <?= $error ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-user-check me-2"></i>
            Status Kehadiran
          </label>
          
          <div class="quick-actions">
            <button type="button" class="quick-btn" data-status="Y">
              <i class="fas fa-check me-2"></i>
              Hadir
            </button>
            <button type="button" class="quick-btn" data-status="N">
              <i class="fas fa-times me-2"></i>
              Tidak Hadir
            </button>
          </div>
          
          <select name="status" id="status" class="form-select" required>
            <option value="">-- Pilih Status Kehadiran --</option>
            <option value="Hadir">Hadir</option>
            <option value="Sakit">Sakit</option>
            <option value="Izin">Izin</option>
          </select>
        </div>

        <button type="submit" name="absen" class="btn-submit">
          <i class="fas fa-paper-plane me-2"></i>
          Kirim Absensi
        </button>
      </form>
    <?php endif; ?>
</div>

  <?php include '../../layout/close_layout.php'; ?>
<?php include '../../layout/footer.php'; ?>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Quick action buttons
    document.querySelectorAll('.quick-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.quick-btn').forEach(b => b.classList.remove('active'));
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Set the select value
        const keterangan = this.getAttribute('data-status');
        document.getElementById('keterangan').value = keterangan;
      });
    });
  </script>
</body>
</html>
