<?php
include '../../config/db.php';
session_start();

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'guru'])) {
  header("Location: ../../auth/login.php");
  exit;
}

$role = $_SESSION['user']['role'];

if ($role == 'guru') {
  // Untuk guru - hanya siswa dalam mapel yang diajar
  $user_id = $_SESSION['user']['id'];
  $guru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM guru WHERE user_id = $user_id"));
  $nip = $guru['nip'];
  
  // Ambil mapel yang diajar guru
  $mapel_guru = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT g.mapel_id, m.nama_mapel, m.jurusan_id 
    FROM guru g 
    JOIN mapel m ON g.mapel_id = m.id 
    WHERE g.nip = '$nip'
  "));
  
  $mapel_id = $mapel_guru['mapel_id'];
  $jurusan_id = $mapel_guru['jurusan_id'];
  
  // Query siswa sesuai jurusan mapel yang diajar
  $siswa = mysqli_query($conn, "
    SELECT s.nis, u.nama AS nama_siswa, k.nama_kelas
    FROM siswa s
    JOIN users u ON s.user_id = u.id
    JOIN kelas k ON s.kelas_id = k.id
    WHERE s.jurusan_id = '$jurusan_id'
    ORDER BY k.nama_kelas, u.nama ASC
  ");
} else {
  // Untuk admin - semua siswa
  $siswa = mysqli_query($conn, "
    SELECT s.nis, u.nama AS nama_siswa, k.nama_kelas
    FROM siswa s
    JOIN users u ON s.user_id = u.id
    JOIN kelas k ON s.kelas_id = k.id
    ORDER BY u.nama ASC
  ");
  
  $mapel = mysqli_query($conn, "SELECT * FROM mapel ORDER BY nama_mapel ASC");
}

if (isset($_POST['simpan'])) {
  $siswa_nis = $_POST['siswa_nis'];
  $mapel_id = $_POST['mapel_id'];
  $tanggal = $_POST['tanggal'];
  $status = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

  // Validasi keterangan tidak boleh kosong
  if (empty($status)) {
    echo "<script>alert('Keterangan absensi harus dipilih!'); window.location.href='add.php';</script>";
    exit;
  }

  // Cek apakah sudah ada absensi untuk siswa dan tanggal ini
  $cek = mysqli_query($conn, "SELECT * FROM absensi WHERE siswa_nis = '$siswa_nis' AND tanggal = '$tanggal'");
  
  if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Absensi untuk siswa pada tanggal ini sudah ada!'); window.location.href='add.php';</script>";
    exit;
  }
  
  mysqli_query($conn, "INSERT INTO absensi (siswa_nis, tanggal, keterangan) VALUES ('$siswa_nis', '$tanggal', '$status')");
  header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Absensi - Sistem Akademik SMK</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    * {
      font-family: 'Inter', sans-serif;
      box-sizing: border-box;
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
      margin: 30px auto;
      padding: 35px;
      max-width: 800px;
      width: calc(100% - 60px);
    }
    
    .form-header {
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      color: white;
      border-radius: 12px;
      padding: 25px 30px;
      margin-bottom: 35px;
      text-align: center;
    }
    
    .form-header h1 {
      font-weight: 600;
      margin: 0 0 8px 0;
      font-size: 24px;
      line-height: 1.3;
    }
    
    .form-header p {
      opacity: 0.9;
      font-size: 14px;
      margin: 0;
      line-height: 1.5;
    }
    
    .form-group {
      margin-bottom: 25px;
      width: 100%;
    }
    
    .form-label {
      font-weight: 500;
      color: #1a1a1a;
      margin-bottom: 10px;
      display: block;
      font-size: 14px;
      line-height: 1.4;
    }
    
    .form-input, .form-select {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      font-size: 14px;
      line-height: 1.5;
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
      gap: 25px;
      margin-bottom: 10px;
    }
    
    .form-row .form-group {
      margin-bottom: 15px;
    }
    
    .status-buttons {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 12px;
      margin-top: 12px;
      width: 100%;
    }
    
    .btn-status {
      padding: 12px 15px;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      background: white;
      color: #1a1a1a;
      font-weight: 500;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      width: 100%;
    }
    
    .btn-status:hover {
      border-color: #1a1a1a;
      background: #1a1a1a;
      color: white;
      transform: translateY(-1px);
    }
    
    .btn-keterangan.active {
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
    
    /* Enhanced Responsive Design */
    @media (max-width: 1200px) {
      .form-container {
        margin: 18px;
        padding: 25px;
      }
      
      .form-header {
        padding: 22px;
        margin-bottom: 25px;
      }
      
      .form-group {
        margin-bottom: 18px;
      }
      
      .form-row {
        gap: 18px;
      }
    }
    
    @media (max-width: 768px) {
      .form-container {
        margin: 12px;
        padding: 20px;
        border-radius: 10px;
      }
      
      .form-header {
        padding: 18px;
        margin-bottom: 20px;
        border-radius: 10px;
      }
      
      .form-header h1 {
        font-size: 22px;
      }
      
      .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .form-group {
        margin-bottom: 16px;
      }
      
      .status-buttons {
        grid-template-columns: 1fr;
        gap: 12px;
      }
      
      .btn-status {
        padding: 12px;
        font-size: 13px;
      }
      
      .form-input, .form-select {
        padding: 11px 15px;
        font-size: 13px;
      }
      
      .btn-submit, .btn-secondary {
        padding: 11px 20px;
        font-size: 13px;
      }
      
      .form-actions {
        margin-top: 25px;
      }
    }
    
    @media (max-width: 576px) {
      .form-container {
        margin: 8px;
        padding: 15px;
        border-radius: 8px;
      }
      
      .form-header {
        padding: 15px;
        margin-bottom: 18px;
        border-radius: 8px;
      }
      
      .form-header h1 {
        font-size: 20px;
      }
      
      .form-header p {
        font-size: 13px;
      }
      
      .form-group {
        margin-bottom: 14px;
      }
      
      .form-row {
        gap: 12px;
      }
      
      .status-buttons {
        gap: 10px;
      }
      
      .btn-status {
        padding: 10px;
        font-size: 12px;
      }
      
      .form-input, .form-select {
        padding: 10px 14px;
        font-size: 12px;
      }
      
      .btn-submit, .btn-secondary {
        padding: 10px 18px;
        font-size: 12px;
      }
      
      .form-actions {
        margin-top: 20px;
      }
    }
  </style>
</head>
<body>

  <?php include '../../layout/navbar.php'; ?>
  
  <div class="form-container"><div class="form-header">
      <h1><i class="fas fa-clipboard-check me-3"></i>&nbsp;Tambah Absensi</h1>
      <p>Isi data absensi siswa untuk mata pelajaran tertentu</p>
    </div><form method="POST" id="absensiForm">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-user me-2"></i>&nbsp;Siswa
          </label>
          <select name="siswa_nis" class="form-select" required>
            <option value="">Pilih Siswa</option>
            <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
              <option value="<?= $s['nis'] ?>"><?= htmlspecialchars($s['nama_siswa']) ?> (<?= htmlspecialchars($s['nis']) ?>) - <?= htmlspecialchars($s['nama_kelas']) ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-book me-2"></i>&nbsp;Mata Pelajaran
          </label>
          <select name="mapel_id" class="form-select" required>
            <option value="">Pilih Mata Pelajaran</option>
            <?php while ($m = mysqli_fetch_assoc($mapel)) { ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['kode_mapel']) ?> - <?= htmlspecialchars($m['nama_mapel']) ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">
        <i class="fas fa-calendar me-2"></i>&nbsp;Tanggal
          </label>
          <input type="date" name="tanggal" class="form-input" required value="<?= date('Y-m-d') ?>">
        </div>

        <div class="form-group">
          <label class="form-label">
        <i class="fas fa-clipboard-check me-2"></i>&nbsp;Keterangan
          </label>
          <div class="status-buttons">
        <button type="button" class="btn-status" data-value="Hadir">
          <i class="fas fa-check-circle me-2"></i>&nbsp;
          Hadir
        </button>
        <button type="button" class="btn-status" data-value="Izin">
          <i class="fas fa-exclamation-circle me-2"></i>&nbsp;
          Izin
        </button>
        <button type="button" class="btn-status" data-value="Sakit">
          <i class="fas fa-thermometer-half me-2"></i>&nbsp;
          Sakit
        </button>
          </div>
          <input type="hidden" name="keterangan" id="status" required>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" name="simpan" class="btn-submit">
          <i class="fas fa-save"></i>&nbsp;
          Simpan Absensi
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

  <script>
    // Handle status buttons
    const statusButtons = document.querySelectorAll('.btn-status');
    const statusInput = document.getElementById('status');

    statusButtons.forEach(button => {
      button.addEventListener('click', function() {
        // Remove active class from all buttons
        statusButtons.forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');
        // Set the value
        statusInput.value = this.dataset.value;
      });
    });
  </script>
</body>
</html> 