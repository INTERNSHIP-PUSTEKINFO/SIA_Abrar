<?php
include '../../config/db.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: ../../auth/login.php");
  exit;
}

$role = $_SESSION['user']['role'];

if ($role == 'guru') {
  $user_id = $_SESSION['user']['id'];
  $guru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM guru WHERE user_id = $user_id"));
  $nip = $guru['nip'];
  
  // Ambil mapel yang diajar oleh guru ini
  $mapel_guru = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT g.mapel_id, m.nama_mapel, m.kode_mapel, m.jurusan_id, j.kode_jurusan, j.nama_jurusan
    FROM guru g
    JOIN mapel m ON g.mapel_id = m.id
    JOIN jurusan j ON m.jurusan_id = j.id
    WHERE g.nip = '$nip'
  "));
  
  $mapel_id = $mapel_guru['mapel_id'];
  
  // Query untuk guru - hanya absensi mapel yang diajar
  $query = mysqli_query($conn, "
    SELECT a.*, s.nis, u.nama AS nama_siswa, m.nama_mapel, m.kode_mapel, k.nama_kelas, DATE(a.tanggal) as tgl
    FROM absensi a
    JOIN siswa s ON a.siswa_nis = s.nis
    JOIN users u ON s.user_id = u.id
    JOIN mapel m ON a.mapel_id = m.id
    JOIN kelas k ON s.kelas_id = k.id
    WHERE a.mapel_id = '$mapel_id'
    ORDER BY a.tanggal DESC, u.nama ASC
  ");
} else {
  // Query untuk admin - semua absensi
  $query = mysqli_query($conn, "
    SELECT a.*, s.nis, u.nama AS nama_siswa, m.nama_mapel, m.kode_mapel, k.nama_kelas
    FROM absensi a
    JOIN siswa s ON a.siswa_nis = s.nis
    JOIN users u ON s.user_id = u.id
    JOIN mapel m ON a.mapel_id = m.id
    JOIN kelas k ON s.kelas_id = k.id
    ORDER BY a.tanggal DESC, u.nama ASC
  ");
}

// Count total attendance
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'a.tanggal';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Ubah query utama agar bisa filter dan sorting
$base_query = ($role == 'guru') ?
    "SELECT a.*, s.nis, u.nama AS nama_siswa, m.nama_mapel, m.kode_mapel, k.nama_kelas
    FROM absensi a
    JOIN siswa s ON a.siswa_nis = s.nis
    JOIN users u ON s.user_id = u.id
    JOIN mapel m ON a.mapel_id = m.id
    JOIN kelas k ON s.kelas_id = k.id
    WHERE a.mapel_id = '$mapel_id'"
  :
    "SELECT a.*, s.nis, u.nama AS nama_siswa, m.nama_mapel, m.kode_mapel, k.nama_kelas
    FROM absensi a
    JOIN siswa s ON a.siswa_nis = s.nis
    JOIN users u ON s.user_id = u.id
    JOIN mapel m ON a.mapel_id = m.id
    JOIN kelas k ON s.kelas_id = k.id
    WHERE 1=1";

if ($search !== '') {
  $search_sql = mysqli_real_escape_string($conn, $search);
    $base_query .= " AND (u.nama LIKE '%$search_sql%' OR s.nis LIKE '%$search_sql%' OR m.nama_mapel LIKE '%$search_sql%' OR m.kode_mapel LIKE '%$search_sql%' OR k.nama_kelas LIKE '%$search_sql%' OR DATE_FORMAT(a.tanggal, '%d/%m/%Y') LIKE '%$search_sql%')";
}

$allowed_sort = ['a.tanggal','u.nama','m.nama_mapel','k.nama_kelas'];
if (!in_array($sort, $allowed_sort)) $sort = 'a.tanggal';
$order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
$base_query .= " ORDER BY $sort $order, m.nama_mapel ASC, u.nama ASC";
$query = mysqli_query($conn, $base_query);
$total_absensi = mysqli_num_rows($query);
mysqli_data_seek($query, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Absensi - Sistem Akademik SMK</title>

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
    
    .table-modern {
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      border: 1px solid #e9ecef;
    }
    
    .table-modern thead {
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      color: white;
    }
    
    .table-modern th {
      border: none;
      padding: 18px 15px;
      font-weight: 700;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .table-modern td {
      border: none;
      border-bottom: 1px solid #f1f3f4;
      padding: 15px;
      vertical-align: middle;
      font-size: 14px;
      font-weight: 500;
    }
    
    .table-modern tbody tr {
      transition: all 0.3s ease;
    }
    
    .table-modern tbody tr:hover {
      background-color: #f8f9fa;
      transform: scale(1.005);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .btn-group-modern {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }
    
    .btn-group-modern .btn {
      border-radius: 8px;
      font-size: 12px;
      padding: 8px 12px;
      font-weight: 600;
    }
    
    .status-badge {
      padding: 8px 16px;
      border-radius: 25px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    
    .status-hadir {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .status-tidak-hadir {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    .status-izin {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      color: #856404;
      border: 1px solid #ffeaa7;
    }
    
    .status-sakit {
      background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
      color: #0c5460;
      border: 1px solid #bee5eb;
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
      
      .table-responsive {
        border-radius: 12px;
        overflow: hidden;
        margin: 0 -5px;
      }
      
      .table-modern th,
      .table-modern td {
        padding: 10px 8px;
        font-size: 13px;
      }
      
      .btn-group-modern {
        flex-direction: column;
        gap: 5px;
      }
      
      .btn-group-modern .btn {
        width: 100%;
        justify-content: center;
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
      
      .table-modern th,
      .table-modern td {
        padding: 8px 5px;
        font-size: 12px;
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
          <h1><i class="fas fa-clipboard-check me-3"></i>Data Absensi</h1>
          <p>
            <?php if ($role == 'guru'): ?>
              Absensi mata pelajaran yang Anda ajar
            <?php else: ?>
              Kelola data absensi siswa di SMK
            <?php endif; ?>
          </p>
        </div>
        <div class="col-md-4 text-end">
          <div class="d-flex align-items-center justify-content-end">
            <div class="me-3">
              <small class="d-block text-white-50">Total Absensi</small>
              <strong class="text-white fs-4"><?= $total_absensi ?></strong>
            </div>
            <div class="bg-white bg-opacity-20 rounded-circle p-3">
              <i class="fas fa-clipboard-check fa-2x text-white"></i>
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
        Tambah Absensi
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
          <input type="text" name="search" class="form-control" placeholder="Cari siswa, mata pelajaran, kelas..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-lg-3 col-md-6 col-12">
          <label class="form-label fw-semibold text-dark">
            <i class="fas fa-sort me-1"></i>Urutkan Berdasarkan
          </label>
          <select name="sort" class="form-select">
            <option value="a.tanggal" <?= $sort=='a.tanggal'?'selected':'' ?>>Tanggal</option>
            <option value="u.nama" <?= $sort=='u.nama'?'selected':'' ?>>Nama Siswa</option>
            <option value="m.nama_mapel" <?= $sort=='m.nama_mapel'?'selected':'' ?>>Mata Pelajaran</option>
            <option value="k.nama_kelas" <?= $sort=='k.nama_kelas'?'selected':'' ?>>Kelas</option>
          </select>
        </div>
        <div class="col-lg-2 col-md-6 col-12">
          <label class="form-label fw-semibold text-dark">
            <i class="fas fa-sort-amount-down me-1"></i>Urutan
          </label>
          <select name="order" class="form-select">
            <option value="DESC" <?= $order=='DESC'?'selected':'' ?>>Terbaru → Terlama</option>
            <option value="ASC" <?= $order=='ASC'?'selected':'' ?>>Terlama → Terbaru</option>
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

    <!-- Data Table -->
    <div class="table-responsive">
      <table class="table table-modern">
        <thead>
          <tr>
            <th><i class="fas fa-user me-2"></i>Nama Siswa</th>
            <th><i class="fas fa-id-card me-2"></i>NIS</th>
            <th><i class="fas fa-book me-2"></i>Mata Pelajaran</th>
            <th><i class="fas fa-chalkboard me-2"></i>Kelas</th>
            <th><i class="fas fa-calendar me-2"></i>Tanggal</th>
            <th><i class="fas fa-clipboard-check me-2"></i>Keterangan</th>
            <?php if (in_array($role, ['admin', 'guru'])): ?>
            <th><i class="fas fa-cogs me-2"></i>Aksi</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php while ($a = mysqli_fetch_assoc($query)) { ?>
            <tr>
              <td><strong><?= htmlspecialchars($a['nama_siswa']) ?></strong></td>
              <td><code><?= htmlspecialchars($a['nis']) ?></code></td>
              <td><?= htmlspecialchars($a['nama_mapel']) ?> (<?= htmlspecialchars($a['kode_mapel']) ?>)</td>
              <td><span class="badge bg-secondary"><?= htmlspecialchars($a['nama_kelas']) ?></span></td>
              <td><?= date('d/m/Y', strtotime($a['tanggal'])) ?></td>
              <td>
                <?php 
                $status_class = '';
                $status_text = '';
                $icon = '';
                
                switch($a['keterangan']) {
                  case 'Hadir':
                    $status_class = 'status-hadir';
                    $status_text = 'Hadir';
                    $icon = 'check';
                    break;
                  case 'Izin':
                    $status_class = 'status-izin'; 
                    $status_text = 'Izin';
                    $icon = 'exclamation';
                    break;
                  case 'Sakit':
                    $status_class = 'status-sakit';
                    $status_text = 'Sakit'; 
                    $icon = 'thermometer-half';
                    break;
                  case 'Alpha':
                    $status_class = 'status-tidak-hadir';
                    $status_text = 'Alpha';
                    $icon = 'times';
                    break;
                }
                ?>
                <span class="status-badge <?= $status_class ?>">
                  <i class="fas fa-<?= $icon ?> me-2"></i>&nbsp;<?= $status_text ?>
                </span>
               </td>
              <?php if (in_array($role, ['admin', 'guru'])): ?>
              <td>
                <div class="btn-group-modern">
                  <a href="edit.php?id=<?= $a['id'] ?>" class="btn-modern btn-warning-modern">
                    <i class="fas fa-edit"></i>
                    Edit
                  </a>
                  <?php if ($role == 'admin'): ?>
                  <a href="delete.php?id=<?= $a['id'] ?>" class="btn-modern btn-danger-modern" 
                     onclick="return confirm('Yakin mau hapus absensi ini?')">
                    <i class="fas fa-trash"></i>
                    Hapus
                  </a>
                  <?php endif; ?>
                </div>
              </td>
              <?php endif; ?>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php include '../../layout/close_layout.php'; ?>
  <?php include '../../layout/footer.php'; ?>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
