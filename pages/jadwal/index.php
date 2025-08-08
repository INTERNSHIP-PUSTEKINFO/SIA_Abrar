<?php
include '../../config/db.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: ../../auth/login.php");
  exit;
}

$role = $_SESSION['user']['role'];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'j.hari';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
if ($role == 'siswa') {
  $user_id = $_SESSION['user']['id'];
  $siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM siswa WHERE user_id = $user_id"));
  $nis = $siswa['nis'];
  $siswa_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.jurusan_id, s.kelas_id, j.kode_jurusan, j.nama_jurusan, k.nama_kelas FROM siswa s LEFT JOIN jurusan j ON s.jurusan_id = j.id LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.nis = '$nis'"));
  $jurusan_id = $siswa_info['jurusan_id'];
  $kelas_id = $siswa_info['kelas_id'];
  $base_query = "SELECT j.*, m.nama_mapel, m.kode_mapel, u.nama AS nama_guru, k.nama_kelas FROM jadwal j JOIN mapel m ON j.mapel_id = m.id JOIN guru g ON j.guru_id = g.user_id JOIN users u ON g.user_id = u.id JOIN kelas k ON j.kelas_id = k.id WHERE j.kelas_id = '$kelas_id' AND m.jurusan_id = '$jurusan_id'";
} else {
  $base_query = "SELECT j.*, m.nama_mapel, m.kode_mapel, u.nama AS nama_guru, k.nama_kelas FROM jadwal j JOIN mapel m ON j.mapel_id = m.id JOIN guru g ON j.guru_id = g.user_id JOIN users u ON g.user_id = u.id JOIN kelas k ON j.kelas_id = k.id WHERE 1=1";
}
if ($search !== '') {
  $search_sql = mysqli_real_escape_string($conn, $search);
  $base_query .= " AND (m.nama_mapel LIKE '%$search_sql%' OR m.kode_mapel LIKE '%$search_sql%' OR u.nama LIKE '%$search_sql%' OR k.nama_kelas LIKE '%$search_sql%')";
}
$allowed_sort = ['j.hari','m.nama_mapel','u.nama','k.nama_kelas','j.jam_ke'];
if (!in_array($sort, $allowed_sort)) $sort = 'j.hari';
$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
$base_query .= " ORDER BY $sort $order, j.jam_ke ASC";
$query = mysqli_query($conn, $base_query);
$total_jadwal = mysqli_num_rows($query);
mysqli_data_seek($query, 0);

// Group schedules by day
$jadwal_by_day = [];
while ($j = mysqli_fetch_assoc($query)) {
  $hari = $j['hari'];
  if (!isset($jadwal_by_day[$hari])) {
    $jadwal_by_day[$hari] = [];
  }
  $jadwal_by_day[$hari][] = $j;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Jadwal Pelajaran - Sistem Akademik SMK</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    * {
      font-family: 'Inter', sans-serif;
      box-sizing: border-box;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }
    
    .page-container {
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      margin: 15px;
      padding: 25px;
      min-height: calc(100vh - 30px);
    }
    
    .page-header {
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      color: white;
      border-radius: 16px;
      padding: 30px;
      margin-bottom: 35px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .page-header h1 {
      font-weight: 700;
      margin-bottom: 10px;
      font-size: 28px;
      letter-spacing: -0.5px;
    }
    
    .page-header p {
      opacity: 0.9;
      font-size: 15px;
      margin-bottom: 0;
      font-weight: 400;
    }
    
    .filter-section {
      background: #f8f9fa;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 25px;
      border: 1px solid #e9ecef;
    }
    
    .btn-modern {
      border-radius: 10px;
      padding: 12px 20px;
      font-weight: 600;
      border: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      cursor: pointer;
    }
    
    .btn-modern:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .btn-primary-modern {
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      color: white;
    }
    
    .btn-primary-modern:hover {
      background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
      color: white;
    }
    
    .btn-warning-modern {
      background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
      color: #1a1a1a;
    }
    
    .btn-warning-modern:hover {
      background: linear-gradient(135deg, #e0a800 0%, #ff8f00 100%);
      color: #1a1a1a;
    }
    
    .btn-danger-modern {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
      color: white;
    }
    
    .btn-danger-modern:hover {
      background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
      color: white;
    }
    
    .schedule-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 25px;
      margin-top: 30px;
    }
    
    .schedule-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      border: 1px solid #e9ecef;
      overflow: hidden;
      transition: all 0.3s ease;
    }
    
    .schedule-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .schedule-header {
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      color: white;
      padding: 20px 25px;
      font-weight: 700;
      font-size: 18px;
      display: flex;
      align-items: center;
      gap: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .schedule-body {
      padding: 25px;
    }
    
    .schedule-item {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      padding: 15px 0;
      border-bottom: 1px solid #f1f3f4;
      font-size: 14px;
      transition: all 0.3s ease;
    }
    
    .schedule-item:hover {
      background-color: #f8f9fa;
      margin: 0 10px;
      padding: 15px 10px;
      border-radius: 8px;
    }
    
    .schedule-item:last-child {
      border-bottom: none;
    }
    
    .schedule-time {
      font-weight: 700;
      color: #1a1a1a;
      min-width: 80px;
      background: #f8f9fa;
      padding: 6px 12px;
      border-radius: 8px;
      text-align: center;
      font-size: 13px;
    }
    
    .schedule-subject {
      flex: 1;
      margin: 0 50px;
    }
    
    .schedule-teacher {
      font-size: 12px;
      color: #6c757d;
      text-align: right;
      min-width: 140px;
      font-weight: 500;
    }
    
    .subject-code {
      font-family: 'Courier New', monospace;
      background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 11px;
      color: #495057;
      font-weight: 600;
      margin-bottom: 5px;
      display: inline-block;
    }
    
    .subject-name {
      font-weight: 600;
      color: #1a1a1a;
      font-size: 15px;
    }
    
    .empty-schedule {
      text-align: center;
      padding: 50px 20px;
      color: #6c757d;
      font-style: italic;
    }
    
    .empty-schedule i {
      opacity: 0.5;
      margin-bottom: 15px;
    }
    
    /* Enhanced Responsive Design */
    @media (max-width: 1200px) {
      .page-container {
        margin: 10px;
        padding: 20px;
      }
      
      .page-header {
        padding: 25px;
      }
      
      .page-header h1 {
        font-size: 24px;
      }
      
      .schedule-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
      }
    }
    
    @media (max-width: 768px) {
      .page-container {
        margin: 5px;
        padding: 15px;
        border-radius: 12px;
      }
      
      .page-header {
        padding: 20px;
        text-align: center;
        border-radius: 12px;
      }
      
      .page-header h1 {
        font-size: 22px;
      }
      
      .page-header .row {
        text-align: center;
      }
      
      .page-header .col-md-4 {
        margin-top: 15px;
      }
      
      .filter-section {
        padding: 15px;
      }
      
      .schedule-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .schedule-card {
        border-radius: 12px;
      }
      
      .schedule-header {
        padding: 15px 20px;
        font-size: 16px;
      }
      
      .schedule-body {
        padding: 20px;
      }
      
      .schedule-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 0;
      }
      
      .schedule-time {
        min-width: auto;
        align-self: flex-start;
      }
      
      .schedule-subject {
        margin: 0;
        width: 100%;
      }
      
      .schedule-teacher {
        text-align: left;
        min-width: auto;
        width: 100%;
      }
    }
    
    @media (max-width: 576px) {
      .page-container {
        margin: 2px;
        padding: 10px;
      }
      
      .page-header {
        padding: 15px;
      }
      
      .page-header h1 {
        font-size: 20px;
      }
      
      .filter-section {
        padding: 10px;
      }
      
      .schedule-header {
        padding: 12px 15px;
        font-size: 14px;
      }
      
      .schedule-body {
        padding: 15px;
      }
      
      .btn-modern {
        padding: 10px 15px;
        font-size: 13px;
      }
    }
  </style>
</head>
<body>

  <?php include '../../layout/navbar.php'; ?>
  
  <div class="page-container">
    
    <!-- Page Header -->
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h1><i class="fas fa-calendar-alt me-3"></i>Jadwal Pelajaran</h1>
          <p>
            <?php if ($role == 'siswa'): ?>
              Jadwal pelajaran kelas Anda
            <?php else: ?>
              Kelola jadwal pelajaran di SMK
            <?php endif; ?>
          </p>
        </div>
        <div class="col-md-4 text-end">
          <div class="d-flex align-items-center justify-content-end">
            <div class="me-3">
              <small class="d-block text-white-50">Total Jadwal</small>
              <strong class="text-white fs-4"><?= $total_jadwal ?></strong>
            </div>
            <div class="bg-white bg-opacity-20 rounded-circle p-3">
              <i class="fas fa-calendar-alt fa-2x text-white"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <?php if ($role !== 'siswa'): ?>
    <div class="mb-4">
      <a href="add.php" class="btn-modern btn-primary-modern">
        <i class="fas fa-plus"></i>
        Tambah Jadwal
      </a>
    </div>
      <?php endif; ?>

    <!-- Enhanced Filter Section -->
    <div class="filter-section">
      <div class="d-flex align-items-center mb-3">
        <i class="fas fa-filter me-2 text-muted"></i>
        <h6 class="mb-0 text-muted fw-bold">FILTER & PENCARIAN DATA</h6>
      </div>
      <form class="row g-3" method="get">
        <div class="col-lg-4 col-md-6 col-12">
          <label class="form-label fw-semibold text-dark">
            <i class="fas fa-search me-1"></i>Pencarian
          </label>
          <input type="text" name="search" class="form-control" placeholder="Cari mata pelajaran, guru, kelas..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-lg-3 col-md-6 col-12">
          <label class="form-label fw-semibold text-dark">
            <i class="fas fa-sort me-1"></i>Urutkan Berdasarkan
          </label>
          <select name="sort" class="form-select">
            <option value="j.hari" <?= $sort=='j.hari'?'selected':'' ?>>Hari</option>
            <option value="m.nama_mapel" <?= $sort=='m.nama_mapel'?'selected':'' ?>>Mata Pelajaran</option>
            <option value="u.nama" <?= $sort=='u.nama'?'selected':'' ?>>Nama Guru</option>
            <option value="k.nama_kelas" <?= $sort=='k.nama_kelas'?'selected':'' ?>>Kelas</option>
            <option value="j.jam_ke" <?= $sort=='j.jam_ke'?'selected':'' ?>>Jam Pelajaran</option>
          </select>
        </div>
        <div class="col-lg-2 col-md-6 col-12">
          <label class="form-label fw-semibold text-dark">
            <i class="fas fa-sort-amount-down me-1"></i>Urutan
          </label>
          <select name="order" class="form-select">
            <option value="ASC" <?= $order=='ASC'?'selected':'' ?>>A → Z (Naik)</option>
            <option value="DESC" <?= $order=='DESC'?'selected':'' ?>>Z → A (Turun)</option>
          </select>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
          <label class="form-label fw-semibold text-dark">
            <i class="fas fa-cogs me-1"></i>Aksi
          </label>
          <div class="d-flex gap-2">
            <button class="btn btn-dark flex-fill" type="submit">
              <i class="fas fa-search me-1"></i> Filter
            </button>
            <a href="index.php" class="btn btn-outline-secondary flex-fill">
              <i class="fas fa-redo me-1"></i> Reset
            </a>
          </div>
        </div>
      </form>
    </div>

    <!-- Schedule Grid -->
    <div class="schedule-grid">
          <?php 
      $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
          foreach ($hari_list as $hari): 
        $jadwal_hari = isset($jadwal_by_day[$hari]) ? $jadwal_by_day[$hari] : [];
      ?>
        <div class="schedule-card">
          <div class="schedule-header">
            <i class="fas fa-calendar-day"></i>
            <?= $hari ?>
          </div>
          <div class="schedule-body">
            <?php if (empty($jadwal_hari)): ?>
              <div class="empty-schedule">
                <i class="fas fa-calendar-times fa-2x mb-3"></i>
                <p>Tidak ada jadwal</p>
              </div>
            <?php else: ?>
              <?php foreach ($jadwal_hari as $j): ?>
                <div class="schedule-item">
                  <div class="schedule-time">
                    Jam <?= $j['jam_ke'] ?>
                  </div>
                  <div class="schedule-subject">
                    <div class="subject-code"><?= htmlspecialchars($j['kode_mapel']) ?></div>
                    <div class="subject-name"><?= htmlspecialchars($j['nama_mapel']) ?></div>
                  </div>
                  <?php if ($role !== 'siswa'): ?>
                  <div class="schedule-teacher">
                    <span class="badge bg-secondary"><?= htmlspecialchars($j['nama_kelas']) ?></span>
                      </div>
                                      <?php endif; ?>
                                  </div>
                              <?php endforeach; ?>
                          <?php endif; ?>
                  </div>
              </div>
          <?php endforeach; ?>
      </div>
  </div>

  <?php include '../../layout/close_layout.php'; ?>
<?php include '../../layout/footer.php'; ?>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
