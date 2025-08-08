<?php
include '../../config/db.php';
session_start();

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'guru'])) {
  header("Location: ../../auth/login.php");
  exit;
}

$role = $_SESSION['user']['role'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data absensi yang akan diedit
$absen = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM absensi WHERE id = $id"));
if (!$absen) {
  echo "<script>alert('Data absensi tidak ditemukan!');window.location.href='index.php';</script>";
  exit;
}

// Set default keterangan jika null
if ($absen['keterangan'] === null) {
    $absen['keterangan'] = 'Alpha';
}

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
  // Hanya boleh edit absensi mapel yang diajar
  if ($absen['mapel_id'] != $mapel_id) {
    echo "<script>alert('Anda tidak berhak mengedit absensi ini!');window.location.href='index.php';</script>";
    exit;
  }
  // Query siswa hanya dari jurusan yang sesuai
  $siswa = mysqli_query($conn, "
    SELECT s.nis, u.nama AS nama_siswa, k.nama_kelas
    FROM siswa s
    JOIN users u ON s.user_id = u.id
    JOIN kelas k ON s.kelas_id = k.id
    WHERE s.jurusan_id = '{$mapel_guru['jurusan_id']}'
    ORDER BY u.nama ASC
  ");
  // Query mapel hanya mapel yang diajar
  $mapel = mysqli_query($conn, "SELECT * FROM mapel WHERE id = '$mapel_id'");
} else {
  // Admin: semua siswa dan mapel
  $siswa = mysqli_query($conn, "
    SELECT s.nis, u.nama AS nama_siswa, k.nama_kelas
    FROM siswa s
    JOIN users u ON s.user_id = u.id
    JOIN kelas k ON s.kelas_id = k.id
    ORDER BY u.nama ASC
  ");
  $mapel = mysqli_query($conn, "SELECT * FROM mapel ORDER BY nama_mapel ASC");
}

if (isset($_POST['update'])) {
  // Validasi input
  if (!isset($_POST['siswa_nis']) || empty($_POST['siswa_nis']) ||
      !isset($_POST['mapel_id']) || empty($_POST['mapel_id']) ||
      !isset($_POST['tanggal']) || empty($_POST['tanggal']) ||
      !isset($_POST['status']) || empty($_POST['status'])) {
    echo "<script>alert('Semua field harus diisi!');window.history.back();</script>";
    exit;
  }

  // Validasi siswa_nis ada di database
  $siswa_nis = mysqli_real_escape_string($conn, $_POST['siswa_nis']);
  $check_siswa = mysqli_query($conn, "SELECT nis FROM siswa WHERE nis = '$siswa_nis'");
  if (mysqli_num_rows($check_siswa) === 0) {
    echo "<script>alert('NIS siswa tidak valid!');window.history.back();</script>";
    exit;
  }

  // Validasi mapel_id ada di database
  $mapel_id = (int)$_POST['mapel_id'];
  $check_mapel = mysqli_query($conn, "SELECT id FROM mapel WHERE id = $mapel_id");
  if (mysqli_num_rows($check_mapel) === 0) {
    echo "<script>alert('ID mata pelajaran tidak valid!');window.history.back();</script>";
    exit;
  }

  $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
  $status = mysqli_real_escape_string($conn, $_POST['status']);
  
  // Cek duplikasi (kecuali data ini sendiri)
  $cek = mysqli_query($conn, "SELECT * FROM absensi WHERE siswa_nis = '$siswa_nis' AND mapel_id = $mapel_id AND tanggal = '$tanggal' AND id != $id");
  if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Absensi untuk siswa, mapel, dan tanggal ini sudah ada!');window.location.href='edit.php?id=$id';</script>";
    exit;
  }
  
  // Update data
  $query = "UPDATE absensi SET 
            siswa_nis = '$siswa_nis', 
            mapel_id = $mapel_id, 
            tanggal = '$tanggal', 
            keterangan = '$status' 
            WHERE id = $id";
            
  if (!mysqli_query($conn, $query)) {
    echo "<script>alert('Gagal mengupdate data: " . mysqli_error($conn) . "');window.history.back();</script>";
    exit;
  }
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Absensi - Sistem Akademik SMK</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Inter', sans-serif; }
    body { background: #f8f9fa; min-height: 100vh; margin: 0; padding: 0; }
    .form-container { 
      background: white; 
      border-radius: 12px; 
      box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
      margin: 20px;
      padding: 30px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
    }
    .form-header { 
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
      color: white;
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 30px;
      text-align: center;
    }
    .form-header h1 { 
      font-weight: 600; 
      margin-bottom: 8px; 
      font-size: 24px; 
    }
    .form-header p { 
      opacity: 0.9; 
      font-size: 14px; 
      margin-bottom: 0; 
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
    .form-control, .form-select { 
      width: 100%; 
      padding: 10px 12px; 
      border: 1px solid #e9ecef; 
      border-radius: 8px; 
      font-size: 14px; 
      transition: all 0.3s ease; 
      background: white;
    }
    .form-control:focus, .form-select:focus { 
      outline: none; 
      border-color: #1a1a1a; 
      box-shadow: 0 0 0 3px rgba(26,26,26,0.1); 
    }
    .row { 
      display: grid; 
      grid-template-columns: 1fr 1fr; 
      gap: 20px; 
    }
    .status-buttons {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-top: 10px;
    }
    .btn-status {
      padding: 10px;
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
    }
    .btn-status:hover {
      border-color: #1a1a1a;
      background: #1a1a1a;
      color: white;
      transform: translateY(-1px);
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .btn-status.active {
      border-color: #1a1a1a;
      background: #1a1a1a;
      color: white;
    }
    .btn { 
      padding: 10px 20px; 
      border-radius: 8px; 
      font-weight: 500; 
      font-size: 14px; 
      cursor: pointer; 
      transition: all 0.3s ease; 
      display: inline-flex; 
      align-items: center; 
      gap: 8px; 
      border: none;
      text-decoration: none;
    }
    .btn-success { 
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; 
    }
    .btn-secondary { 
      background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
      color: white; 
      margin-left: 10px;
    }
    .btn:hover { 
      transform: translateY(-2px); 
      box-shadow: 0 3px 10px rgba(0,0,0,0.15); 
    }
    .form-actions { 
      margin-top: 30px; 
      text-align: center; 
    }
    @media (max-width: 768px) {
      .form-container { 
        margin: 15px; 
        padding: 20px; 
      }
      .form-header { 
        padding: 20px; 
        margin-bottom: 25px; 
      }
      .form-header h1 { 
        font-size: 20px; 
      }
      .row { 
        grid-template-columns: 1fr; 
        gap: 15px; 
      }
      .form-control, .form-select, .btn {
        font-size: 13px;
        padding: 8px 15px;
      }
      .status-buttons {
        grid-template-columns: 1fr;
      }
    }
    @media (max-width: 576px) {
      .form-container { 
        margin: 10px; 
        padding: 15px; 
      }
      .form-header { 
        padding: 15px; 
        margin-bottom: 20px; 
      }
      .form-header h1 { 
        font-size: 18px; 
      }
      .form-control, .form-select, .btn {
        font-size: 12px;
        padding: 7px 12px;
      }
      .status-buttons {
        gap: 8px;
      }
      .btn-status {
        font-size: 11px;
        padding: 8px;
      }
    }
  </style>
</head>
<body>
  <?php include '../../layout/navbar.php'; ?>
  <div class="form-container">
    <div class="form-header">
      <h1>Edit Absensi</h1>
      <p>Update data kehadiran siswa</p>
    </div>
    
    <form method="POST" id="absensiForm">
      <div class="row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-user me-2"></i>Siswa
          </label>
          <?php if ($role == 'guru'): ?>
            <!-- Hidden inputs untuk guru -->
            <input type="hidden" name="siswa_nis" value="<?= htmlspecialchars($absen['siswa_nis']) ?>">
            <select class="form-control" disabled>
              <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                <option <?= $absen['siswa_nis'] == $s['nis'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($s['nama_siswa']) ?> (<?= htmlspecialchars($s['nis']) ?>) - <?= htmlspecialchars($s['nama_kelas']) ?>
                </option>
              <?php } ?>
            </select>
          <?php else: ?>
            <!-- Select untuk admin -->
            <select name="siswa_nis" class="form-control" required>
              <option value="">Pilih Siswa</option>
              <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                <option value="<?= $s['nis'] ?>" <?= $absen['siswa_nis'] == $s['nis'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($s['nama_siswa']) ?> (<?= htmlspecialchars($s['nis']) ?>) - <?= htmlspecialchars($s['nama_kelas']) ?>
                </option>
              <?php } ?>
            </select>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-book me-2"></i>Mata Pelajaran
          </label>
          <?php if ($role == 'guru'): ?>
            <!-- Hidden input untuk guru -->
            <input type="hidden" name="mapel_id" value="<?= htmlspecialchars($mapel_id) ?>">
            <select class="form-control" disabled>
              <?php while ($m = mysqli_fetch_assoc($mapel)) { ?>
                <option <?= $absen['mapel_id'] == $m['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['kode_mapel']) ?> - <?= htmlspecialchars($m['nama_mapel']) ?>
                </option>
              <?php } ?>
            </select>
          <?php else: ?>
            <!-- Select untuk admin -->
            <select name="mapel_id" class="form-control" required>
              <option value="">Pilih Mata Pelajaran</option>
              <?php while ($m = mysqli_fetch_assoc($mapel)) { ?>
                <option value="<?= $m['id'] ?>" <?= $absen['mapel_id'] == $m['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['kode_mapel']) ?> - <?= htmlspecialchars($m['nama_mapel']) ?>
                </option>
              <?php } ?>
            </select>
          <?php endif; ?>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-calendar me-2"></i>Tanggal
          </label>
          <input type="date" name="tanggal" class="form-control" required value="<?= htmlspecialchars($absen['tanggal']) ?>">
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-clipboard-check me-2"></i>Status Kehadiran
          </label>
          <div class="status-buttons">
            <button type="button" class="btn-status<?= $absen['keterangan']=='Hadir' ? ' active' : '' ?>" data-value="Hadir">
              <i class="fas fa-check-circle"></i>Hadir
            </button>
            <button type="button" class="btn-status<?= $absen['keterangan']=='Izin' ? ' active' : '' ?>" data-value="Izin">
              <i class="fas fa-exclamation-circle"></i>Izin
            </button>
            <button type="button" class="btn-status<?= $absen['keterangan']=='Sakit' ? ' active' : '' ?>" data-value="Sakit">
              <i class="fas fa-thermometer-half"></i>Sakit
            </button>
            <button type="button" class="btn-status<?= $absen['keterangan']=='Alpha' ? ' active' : '' ?>" data-value="Alpha">
              <i class="fas fa-times-circle"></i>Alpha
            </button>
          </div>
          <input type="hidden" name="status" id="status" required value="<?= htmlspecialchars($absen['keterangan']) ?>">
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" name="update" class="btn btn-success">
          <i class="fas fa-save"></i> Update
        </button>
        <a href="index.php" class="btn btn-secondary">
          <i class="fas fa-times"></i> Batal
        </a>
      </div>
    </form>
  </div>
                <script>
                // Handle status buttons
                const statusButtons = document.querySelectorAll('.btn-status');
                const statusInput = document.getElementById('status');
                statusButtons.forEach(button => {
                  button.addEventListener('click', function() {
                    statusButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    statusInput.value = this.dataset.value;
                  });
                });
              </script>
</body>
</html>