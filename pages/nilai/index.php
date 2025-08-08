<?php
include '../../config/db.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: ../../auth/login.php");
  exit;
}

$role = $_SESSION['user']['role'];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'm.nama_mapel';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

if ($role == 'siswa') {
  $user_id = $_SESSION['user']['id'];
        $siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.*, u.nama, k.nama_kelas, j.nama_jurusan FROM siswa s JOIN users u ON s.user_id = u.id JOIN kelas k ON s.kelas_id = k.id JOIN jurusan j ON s.jurusan_id = j.id WHERE s.user_id = $user_id"));
  $nis = $siswa['nis'];
        $base_query = "SELECT n.*, m.nama_mapel, u.nama 
                      FROM nilai n 
                      JOIN mapel m ON n.mapel_id = m.id 
                      JOIN siswa s ON n.siswa_nis = s.nis
                      JOIN users u ON s.user_id = u.id
                      WHERE n.siswa_nis = '$nis'";
} else {
        // Dropdown filter
        $filter_nis = isset($_GET['filter_nis']) ? $_GET['filter_nis'] : '';
        $filter_mapel = isset($_GET['filter_mapel']) ? $_GET['filter_mapel'] : '';
        $filter_kelas = isset($_GET['filter_kelas']) ? $_GET['filter_kelas'] : '';
        $base_query = "SELECT n.*, s.nis, u.nama AS nama_siswa, m.nama_mapel, k.nama_kelas FROM nilai n JOIN siswa s ON n.siswa_nis = s.nis JOIN users u ON s.user_id = u.id JOIN mapel m ON n.mapel_id = m.id JOIN kelas k ON s.kelas_id = k.id WHERE 1=1";
        if ($filter_nis) $base_query .= " AND s.nis = '" . mysqli_real_escape_string($conn, $filter_nis) . "'";
        if ($filter_mapel) $base_query .= " AND m.id = '" . mysqli_real_escape_string($conn, $filter_mapel) . "'";
        if ($filter_kelas) $base_query .= " AND k.id = '" . mysqli_real_escape_string($conn, $filter_kelas) . "'";
}
if ($search !== '') {
  $search_sql = mysqli_real_escape_string($conn, $search);
  $base_query .= " AND (u.nama LIKE '%$search_sql%' OR s.nis LIKE '%$search_sql%' OR m.nama_mapel LIKE '%$search_sql%' OR m.kode_mapel LIKE '%$search_sql%')";
}
$allowed_sort = ['m.nama_mapel','n.semester','n.tahun_ajaran','n.nilai_akhir'];
if (!in_array($sort, $allowed_sort)) $sort = 'm.nama_mapel';
$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

// Untuk siswa, tidak perlu sort by nama karena hanya menampilkan nilai sendiri
if ($role == 'siswa') {
    $base_query .= " ORDER BY $sort $order";
} else {
    $base_query .= " ORDER BY $sort $order, u.nama ASC";
}
$query = mysqli_query($conn, $base_query);
$siswa_list = mysqli_query($conn, "SELECT s.nis, u.nama, k.nama_kelas FROM siswa s JOIN users u ON s.user_id = u.id JOIN kelas k ON s.kelas_id = k.id ORDER BY u.nama ASC");
$mapel_list = mysqli_query($conn, "SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel ASC");
$kelas_list = mysqli_query($conn, "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC");
$total_nilai = mysqli_num_rows($query);
mysqli_data_seek($query, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Nilai - Sistem Akademik SMK</title>

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
    
    .table-responsive {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      border-radius: 12px;
      margin-bottom: 25px;
      /* Remove max-width to allow table to fit container */
    }

    .table-modern {
      background: white;
      border-radius: 10px;
      overflow: auto;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      border: 1px solid #e9ecef;
      width: 100%;
      table-layout: auto;
    }

    .table-modern thead {
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      color: white;
    }

    .table-modern th {
      border: none;
      padding: 14px 18px;
      font-weight: 700;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      white-space: nowrap;
    }

    .table-modern th .fa {
      margin-right: 7px;
      font-size: 14px;
      vertical-align: middle;
    }

    .table-modern td {
      border: none;
      border-bottom: 1px solid #f1f3f4;
      padding: 13px 14px;
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
      gap: 10px;
      flex-wrap: wrap;
    }
    
    .btn-group-modern .btn {
      border-radius: 8px;
      font-size: 12px;
      padding: 8px 12px;
      font-weight: 600;
    }
    
    .nilai-badge {
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .nilai-a {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .nilai-b {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      color: #856404;
      border: 1px solid #ffeaa7;
    }
    
    .nilai-c {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    .nilai-d {
      background: linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%);
      color: #383d41;
      border: 1px solid #d6d8db;
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
          <h1><i class="fas fa-chart-line me-3"></i>Data Nilai</h1>
          <p>
            <?php if ($role == 'siswa'): ?>
              Nilai mata pelajaran Anda
            <?php else: ?>
              Kelola nilai siswa di SMK
            <?php endif; ?>
          </p>
          <div class="mt-2">
            <?php if ($role == 'siswa'): ?>
              <span class="fw-bold">Nama:</span> <?= htmlspecialchars($siswa['nama']) ?> &nbsp;|
              <span class="fw-bold">Kelas:</span> <?= htmlspecialchars($siswa['nama_kelas']) ?> &nbsp;|
              <span class="fw-bold">NIS:</span> <?= htmlspecialchars($siswa['nis']) ?>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-md-4 text-end">
          <div class="d-flex align-items-center justify-content-end">
            <div class="me-3">
              <small class="d-block text-white-50">Total Nilai</small>
              <strong class="text-white fs-4"><?= $total_nilai ?></strong>
            </div>
            <div class="bg-white bg-opacity-20 rounded-circle p-3">
              <i class="fas fa-chart-line fa-2x text-white"></i>
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
        Tambah Nilai
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
          <input type="text" name="search" class="form-control" placeholder="Cari siswa, mata pelajaran..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-lg-3 col-md-6 col-12">
          <label class="form-label fw-semibold text-dark">
            <i class="fas fa-sort me-1"></i>Urutkan Berdasarkan
          </label>
          <select name="sort" class="form-select">
            <option value="m.nama_mapel" <?= $sort=='m.nama_mapel'?'selected':'' ?>>Mata Pelajaran</option>
            <?php if ($role !== 'siswa'): ?>
            <option value="u.nama" <?= $sort=='u.nama'?'selected':'' ?>>Nama Siswa</option>
            <?php endif; ?>
            <option value="n.semester" <?= $sort=='n.semester'?'selected':'' ?>>Semester</option>
            <option value="n.tahun_ajaran" <?= $sort=='n.tahun_ajaran'?'selected':'' ?>>Tahun Ajaran</option>
            <option value="n.nilai_akhir" <?= $sort=='n.nilai_akhir'?'selected':'' ?>>Nilai Akhir</option>
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

    <!-- Data Table -->
    <div class="table-responsive">
      <table class="table table-modern">
        <thead>
          <tr>
            <th>No</th>
            <th>Mata Pelajaran</th>
            <th>Nilai Tugas</th>
            <th>Nilai UTS</th>
            <th>Nilai UAS</th>
            <th>Nilai Akhir</th>
            <th>Grade</th>
            <?php if ($role !== 'siswa'): ?>
              <th>Aksi</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody
          <?php $no=1; while ($n = mysqli_fetch_assoc($query)) { ?>
            <?php 
              $nilai_akhir = round(($n['nilai_tugas'] + $n['nilai_uts'] + $n['nilai_uas'])/3,2);
              if ($nilai_akhir >= 85) {
                $grade = 'A'; $badge_class = 'nilai-a';
              } elseif ($nilai_akhir >= 75) {
                $grade = 'B'; $badge_class = 'nilai-b';
              } elseif ($nilai_akhir >= 60) {
                $grade = 'C'; $badge_class = 'nilai-c';
              } else {
                $grade = 'D'; $badge_class = 'nilai-d';
              }
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($n['nama_mapel']) ?></td>
              <td><?= $n['nilai_tugas'] ?></td>
              <td><?= $n['nilai_uts'] ?></td>
              <td><?= $n['nilai_uas'] ?></td>
              <td><span class="nilai-badge <?= $badge_class ?>"><?= $nilai_akhir ?></span></td>
              <td><span class="nilai-badge <?= $badge_class ?>"><?= $grade ?></span></td>
              <?php if ($role !== 'siswa'): ?>
                <td>
                  <div class="btn-group-modern">
                    <a href="edit.php?id=<?= $n['id'] ?>" class="btn-modern btn-warning-modern">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="delete.php?id=<?= $n['id'] ?>" class="btn-modern btn-danger-modern" onclick="return confirm('Yakin mau hapus nilai ini?')">
                      <i class="fas fa-trash"></i> Hapus
                    </a>
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
