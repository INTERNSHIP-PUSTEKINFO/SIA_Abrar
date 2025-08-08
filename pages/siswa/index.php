<?php
include '../../config/db.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: ../../auth/login.php");
  exit;
}

$role = $_SESSION['user']['role'];

// Query untuk mengambil data siswa
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'u.nama';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
$base_query = "SELECT s.nis, s.tempat_lahir, s.tanggal_lahir, s.jenis_kelamin, s.alamat, s.tahun_masuk, s.status, u.nama AS nama_user, u.email, j.nama_jurusan, k.nama_kelas FROM siswa s LEFT JOIN users u ON s.user_id = u.id LEFT JOIN jurusan j ON s.jurusan_id = j.id LEFT JOIN kelas k ON s.kelas_id = k.id WHERE 1=1";
if ($search !== '') {
  $search_sql = mysqli_real_escape_string($conn, $search);
  $base_query .= " AND (u.nama LIKE '%$search_sql%' OR s.nis LIKE '%$search_sql%' OR j.nama_jurusan LIKE '%$search_sql%' OR k.nama_kelas LIKE '%$search_sql%')";
}
$allowed_sort = ['u.nama','s.nis','j.nama_jurusan','k.nama_kelas','s.tahun_masuk','s.status'];
if (!in_array($sort, $allowed_sort)) $sort = 'u.nama';
$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
$base_query .= " ORDER BY $sort $order, u.nama ASC";
$query = mysqli_query($conn, $base_query);
$total_siswa = mysqli_num_rows($query);
mysqli_data_seek($query, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Siswa - Sistem Akademik SMK</title>

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
          margin: 15px auto;
          padding: 20px;
          min-height: calc(100vh - 30px);
          max-width: 95vw;
          width: 100%;
      }
      
      .page-header {
          background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
          color: white;
          border-radius: 16px;
          padding: 24px;
          margin-bottom: 30px;
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
        padding: 15px 20px;
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
      }

      .page-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: white;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      }
      .page-header h1 {
        font-weight: 700;
        margin-bottom: 6px;
        font-size: 16px;
        letter-spacing: -0.5px;
      }
      .page-header p {
        opacity: 0.9;
        font-size: 11px;
        margin-bottom: 0;
        font-weight: 400;
      }
      .filter-section {
        background: #f8f9fa;
        border-radius: 7px;
        padding: 7px 10px;
        margin-bottom: 12px;
        border: 1px solid #e9ecef;
        font-size: 12px;
      }
      .btn-modern {
        border-radius: 7px;
        padding: 6px 10px;
        font-weight: 600;
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        cursor: pointer;
      }
      .table-modern {
        background: white;
        border-radius: 7px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
      }
      .table-modern th {
        border: none;
        padding: 7px 6px;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      .table-modern td {
        border: none;
        border-bottom: 1px solid #f1f3f4;
        padding: 7px 6px;
        vertical-align: middle;
        font-size: 11px;
        font-weight: 500;

        padding: 20px;
        text-align: center;
        border-radius: 12px;
      }
        
      .page-header h1 {
        font-size: 20px;
      }
      
      .page-header .row {
        text-align: left;
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
        margin: 5px;
        padding: 15px;
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
          <h1><i class="fas fa-user-graduate me-3"></i>Data Siswa</h1>
          <p>Kelola data siswa SMK dengan informasi lengkap</p>
        </div>
        <div class="col-md-4 text-end">
          <div class="d-flex align-items-center justify-content-end">
            <div class="me-3">
              <small class="d-block text-white-50">Total Siswa</small>
              <strong class="text-white fs-4"><?= $total_siswa ?></strong>
            </div>
            <div class="bg-white bg-opacity-20 rounded-circle p-3">
              <i class="fas fa-user-graduate fa-2x text-white"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <?php if ($role === 'admin'): ?>
      <div class="mb-4">
        <a href="add.php" class="btn-modern btn-primary-modern">
          <i class="fas fa-plus"></i>
          Tambah Siswa
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
          <input type="text" name="search" class="form-control" placeholder="Cari nama, NIS, jurusan, kelas..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-lg-3 col-md-6 col-12">
          <label class="form-label fw-semibold text-dark">
            <i class="fas fa-sort me-1"></i>Urutkan Berdasarkan
          </label>
          <select name="sort" class="form-select">
            <option value="u.nama" <?= $sort=='u.nama'?'selected':'' ?>>Nama Siswa</option>
            <option value="s.nis" <?= $sort=='s.nis'?'selected':'' ?>>NIS</option>
            <option value="j.nama_jurusan" <?= $sort=='j.nama_jurusan'?'selected':'' ?>>Jurusan</option>
            <option value="k.nama_kelas" <?= $sort=='k.nama_kelas'?'selected':'' ?>>Kelas</option>
            <option value="s.tahun_masuk" <?= $sort=='s.tahun_masuk'?'selected':'' ?>>Tahun Masuk</option>
            <option value="s.status" <?= $sort=='s.status'?'selected':'' ?>>Status</option>
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
            <th>NIS</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Tempat, Tgl Lahir</th>
            <th>JK</th>
            <th>Alamat</th>
            <th>Jurusan</th>
            <th>Kelas</th>
            <th>Tahun Masuk</th>
            <th>Status</th>
            <?php if ($role === 'admin'): ?>
              <th>Aksi</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php while ($s = mysqli_fetch_assoc($query)) : ?>
            <tr>
              <td><strong><?= htmlspecialchars($s['nis']) ?></strong></td>
              <td><?= htmlspecialchars($s['nama_user'] ?? '-') ?></td>
              <td><?= htmlspecialchars($s['email'] ?? '-') ?></td>
              <td><?= htmlspecialchars($s['tempat_lahir']) ?>, <?= date('d M Y', strtotime($s['tanggal_lahir'])) ?></td>
              <td>
                <span class="badge-modern <?= $s['jenis_kelamin'] === 'L' ? 'badge-info-modern' : 'badge-pink-modern' ?>">
                  <i class="fas fa-<?= $s['jenis_kelamin'] === 'L' ? 'male' : 'female' ?> me-1"></i>
                </span>
              </td>
              <td><?= nl2br(htmlspecialchars($s['alamat'])) ?></td>
              <td><?= htmlspecialchars($s['nama_jurusan'] ?? '-') ?></td>
              <td><?= htmlspecialchars($s['nama_kelas'] ?? '-') ?></td>
              <td><?= $s['tahun_masuk'] ?></td>
              <td>
                <?php
                  $status = strtolower($s['status']);
                  $badgeClass = 'badge-secondary-modern';
                  $icon = 'times';
                  if ($status === 'aktif') {
                    $badgeClass = 'badge-success-modern';
                    $icon = 'check';
                  } elseif ($status === 'lulus') {
                    $badgeClass = 'badge-info-modern';
                    $icon = 'graduation-cap';
                  } elseif ($status === 'keluar') {
                    $badgeClass = 'badge-pink-modern';
                    $icon = 'sign-out-alt';
                  }
                ?>
                <span class="badge-modern <?= $badgeClass ?>">
                  <i class="fas fa-<?= $icon ?> me-1"></i>
                  <?= ucfirst($s['status']) ?>
                </span>
              </td>
              <?php if ($role === 'admin'): ?>
                <td>
                  <div class="btn-group-modern">
                    <a href="edit.php?nis=<?= $s['nis'] ?>" class="btn-modern btn-warning-modern">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="delete.php?nis=<?= $s['nis'] ?>" class="btn-modern btn-danger-modern" onclick="return confirm('Yakin mau hapus siswa ini?')">
                      <i class="fas fa-trash"></i> Hapus
                    </a>
                  </div>
                </td>
              <?php endif; ?>
            </tr>
          <?php endwhile; ?>
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