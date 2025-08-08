<?php
session_start();
include '../config/db.php';
include '../config/check_attendance.php';

if (!isset($_SESSION['user'])) {
  header("Location: ../auth/login.php");
  exit;
}

$role = $_SESSION['user']['role'];

// Check if student needs to do attendance
if ($role === 'siswa') {
  check_attendance($conn, $_SESSION['user']['id']);
}
$email = $_SESSION['user']['email'];
$nama = $_SESSION['user']['nama'] ?? 'Pengguna';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Sistem Akademik SMK</title>

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
      margin: 0;
      padding: 0;
    }
    
    .dashboard-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin: 20px;
      padding: 30px;
      height: fit-content;
    }
    
    .welcome-section {
      background: #1a1a1a;
      color: white;
      border-radius: 12px;
      padding: 30px;
      margin-bottom: 30px;
    }
    
    .welcome-section h1 {
      font-weight: 600;
      margin-bottom: 8px;
      font-size: 24px;
    }
    
    .welcome-section p {
      opacity: 0.8;
      font-size: 14px;
      margin-bottom: 0;
    }
    
    .stats-card {
      background: white;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      border: 1px solid #e9ecef;
      text-align: center;
      margin-bottom: 20px;
    }
    
    .stats-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .stats-icon {
      width: 50px;
      height: 50px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      color: white;
      margin: 0 auto 16px;
      background: #1a1a1a;
    }
    
    .stats-number {
      font-size: 28px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 4px;
    }
    
    .stats-label {
      color: #6c757d;
      font-weight: 500;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .feature-card {
      background: white;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      border: 1px solid #e9ecef;
      height: 100%;
      position: relative;
    }
    
    .feature-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      border-color: #1a1a1a;
    }
    
    .feature-icon {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: white;
      margin-bottom: 16px;
      background: #1a1a1a;
    }
    
    .feature-title {
      font-size: 16px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 8px;
    }
    
    .feature-description {
      color: #6c757d;
      margin-bottom: 20px;
      line-height: 1.5;
      font-size: 14px;
    }
    
    .btn-modern {
      border-radius: 8px;
      padding: 10px 16px;
      font-weight: 500;
      border: none;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 14px;
    }
    
    .btn-modern:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    .btn-primary-modern {
      background: #1a1a1a;
      color: white;
    }
    
    .btn-primary-modern:hover {
      background: #000;
      color: white;
    }
    
    .section-title {
      font-size: 20px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 20px;
      text-align: center;
    }
    
    /* Enhanced Responsive Design */
    @media (max-width: 1200px) {
      .dashboard-container {
        margin: 15px;
        padding: 25px;
      }
      
      .welcome-section {
        padding: 25px;
        margin-bottom: 25px;
      }
      
      .stats-card {
        padding: 20px;
        margin-bottom: 15px;
      }
      
      .feature-card {
        padding: 20px;
      }
    }
    
    @media (max-width: 768px) {
      .dashboard-container {
        margin: 10px;
        padding: 20px;
        border-radius: 10px;
      }
      
      .welcome-section {
        padding: 20px;
        text-align: center;
        border-radius: 10px;
        margin-bottom: 20px;
      }
      
      .welcome-section h1 {
        font-size: 22px;
      }
      
      .stats-card {
        padding: 18px;
        margin-bottom: 12px;
        border-radius: 10px;
      }
      
      .stats-number {
        font-size: 24px;
      }
      
      .feature-card {
        padding: 18px;
        border-radius: 10px;
        margin-bottom: 15px;
      }
      
      .feature-title {
        font-size: 15px;
      }
      
      .feature-description {
        font-size: 13px;
      }
    }
    
    @media (max-width: 576px) {
      .dashboard-container {
        margin: 5px;
        padding: 15px;
        border-radius: 8px;
      }
      
      .welcome-section {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
      }
      
      .welcome-section h1 {
        font-size: 20px;
      }
      
      .welcome-section p {
        font-size: 13px;
      }
      
      .stats-card {
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 8px;
      }
      
      .stats-number {
        font-size: 22px;
      }
      
      .stats-label {
        font-size: 11px;
      }
      
      .feature-card {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
      }
      
      .feature-title {
        font-size: 14px;
      }
      
      .feature-description {
        font-size: 12px;
        margin-bottom: 15px;
      }
      
      .btn-modern {
        padding: 8px 14px;
        font-size: 13px;
      }
      
      .section-title {
        font-size: 18px;
        margin-bottom: 15px;
      }
    }
  </style>
</head>
<body>

  <?php include '../layout/navbar.php'; ?>
  
  <div class="dashboard-container">
    
    <!-- Welcome Section -->
    <div class="welcome-section">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h1><i class="fas fa-sun me-3"></i>Selamat Datang, <?= htmlspecialchars($nama) ?>!</h1>
          <p>Selamat datang di Sistem Akademik SMK. Kelola data akademik dengan mudah dan efisien.</p>
        </div>
        <div class="col-md-4 text-end">
          <div class="d-flex align-items-center justify-content-end">
            <div class="me-3">
              <small class="d-block text-white-50">Role</small>
              <strong class="text-white"><?= ucfirst($role) ?></strong>
            </div>
            <div class="bg-white bg-opacity-20 rounded-circle p-3">
              <i class="fas fa-user fa-2x text-white"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

      <?php if ($role === 'siswa'): ?>
      
      <h2 class="section-title">Menu Siswa</h2>
      <div class="row g-4">

        <!-- Nilai Saya -->
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <h5 class="feature-title">Nilai Saya</h5>
            <p class="feature-description">Lihat semua nilai kamu berdasarkan mata pelajaran dengan detail lengkap.</p>
            <a href="../pages/nilai/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-eye"></i>
              Lihat Nilai
            </a>
          </div>
        </div>

        <!-- Jadwal -->
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-calendar-alt"></i>
            </div>
            <h5 class="feature-title">Jadwal Pelajaran</h5>
            <p class="feature-description">Lihat jadwal pelajaran hari ini atau minggu ini dengan detail waktu.</p>
            <a href="../pages/jadwal/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-calendar"></i>
              Lihat Jadwal
            </a>
          </div>
        </div>

        </div>

      <?php elseif ($role === 'guru'): ?>
      
      <h2 class="section-title">Menu Guru</h2>
      <div class="row g-4">

        <!-- Jadwal Mengajar -->
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h5 class="feature-title">Jadwal Mengajar</h5>
            <p class="feature-description">Cek jadwal mengajar kamu di sekolah dengan detail kelas dan mata pelajaran.</p>
            <a href="../pages/jadwal/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-calendar"></i>
              Lihat Jadwal
            </a>
          </div>
        </div>

        <!-- Absensi -->
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-clipboard-check"></i>
            </div>
            <h5 class="feature-title">Rekap Absensi</h5>
            <p class="feature-description">Pantau absensi siswa berdasarkan mata pelajaran yang kamu ajar.</p>
            <a href="../pages/absensi/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-clipboard"></i>
              Lihat Absensi
            </a>
          </div>
        </div>

        <!-- Nilai -->
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-edit"></i>
            </div>
            <h5 class="feature-title">Input Nilai</h5>
            <p class="feature-description">Input dan kelola nilai siswa yang kamu ajar dengan mudah.</p>
            <a href="../pages/nilai/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-edit"></i>
              Input Nilai
            </a>
          </div>
        </div>

        <!-- Data Siswa -->
        <div class="col-md-6">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-users"></i>
            </div>
            <h5 class="feature-title">Data Siswa</h5>
            <p class="feature-description">Lihat informasi siswa berdasarkan kelas atau pelajaran yang kamu ajar.</p>
            <a href="../pages/siswa/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-users"></i>
              Lihat Siswa
            </a>
          </div>
        </div>

        </div>

      <?php elseif ($role === 'admin'): ?>

        <?php
        $jumlah_siswa = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM siswa"));
        $jumlah_guru = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM guru"));
        $jumlah_jadwal = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM jadwal"));
        $jumlah_nilai = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM nilai"));
        $jumlah_absensi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM absensi"));
        ?>

      <h2 class="section-title">Statistik Sistem</h2>
      <div class="row g-4 mb-5">
        
        <div class="col-md-2 col-sm-4 col-6">
          <div class="stats-card">
            <div class="stats-icon">
              <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stats-number"><?= $jumlah_guru ?></div>
            <div class="stats-label">Guru</div>
          </div>
        </div>
        
        <div class="col-md-2 col-sm-4 col-6">
          <div class="stats-card">
            <div class="stats-icon">
              <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stats-number"><?= $jumlah_siswa ?></div>
            <div class="stats-label">Siswa</div>
          </div>
        </div>
        
        <div class="col-md-2 col-sm-4 col-6">
          <div class="stats-card">
            <div class="stats-icon">
              <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stats-number"><?= $jumlah_jadwal ?></div>
            <div class="stats-label">Jadwal</div>
          </div>
        </div>
        
        <div class="col-md-2 col-sm-4 col-6">
          <div class="stats-card">
            <div class="stats-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <div class="stats-number"><?= $jumlah_nilai ?></div>
            <div class="stats-label">Nilai</div>
          </div>
        </div>
        
        <div class="col-md-2 col-sm-4 col-6">
          <div class="stats-card">
            <div class="stats-icon">
              <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stats-number"><?= $jumlah_absensi ?></div>
            <div class="stats-label">Absensi</div>
          </div>
        </div>
        
      </div>

      <h2 class="section-title">Menu Admin</h2>
      <div class="row g-4">
        
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-users"></i>
            </div>
            <h5 class="feature-title">Kelola Siswa</h5>
            <p class="feature-description">Tambah, edit, dan hapus data siswa dengan mudah.</p>
            <a href="../pages/siswa/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-users"></i>
              Kelola Siswa
            </a>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h5 class="feature-title">Kelola Guru</h5>
            <p class="feature-description">Kelola data guru dan akses sistem.</p>
            <a href="../pages/guru/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-chalkboard"></i>
              Kelola Guru
            </a>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-book"></i>
            </div>
            <h5 class="feature-title">Kelola Mata Pelajaran</h5>
            <p class="feature-description">Kelola data mata pelajaran dan kurikulum.</p>
            <a href="../pages/mapel/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-book"></i>
              Kelola Mapel
            </a>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-calendar-alt"></i>
            </div>
            <h5 class="feature-title">Kelola Jadwal</h5>
            <p class="feature-description">Atur jadwal pelajaran dan kegiatan sekolah.</p>
            <a href="../pages/jadwal/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-calendar"></i>
              Kelola Jadwal
            </a>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <h5 class="feature-title">Kelola Nilai</h5>
            <p class="feature-description">Kelola nilai siswa dan laporan akademik.</p>
            <a href="../pages/nilai/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-chart"></i>
              Kelola Nilai
            </a>
          </div>
        </div>

        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-clipboard-check"></i>
            </div>
            <h5 class="feature-title">Kelola Absensi</h5>
            <p class="feature-description">Pantau dan kelola data absensi siswa.</p>
            <a href="../pages/absensi/index.php" class="btn-modern btn-primary-modern">
              <i class="fas fa-clipboard"></i>
              Kelola Absensi
            </a>
    </div>
  </div>
        
</div>

    <?php endif; ?>
    
  </div>

  <?php include '../layout/close_layout.php'; ?>
<?php include '../layout/footer.php'; ?>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
