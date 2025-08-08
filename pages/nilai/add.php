<?php
include '../../config/db.php';
session_start();

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'guru'])) {
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
  
  // Query untuk guru - hanya siswa yang mengambil mapel yang diajar
  $siswa = mysqli_query($conn, "
    SELECT s.nis, u.nama AS nama_siswa, k.nama_kelas
    FROM siswa s
    JOIN users u ON s.user_id = u.id
    JOIN kelas k ON s.kelas_id = k.id
    WHERE s.jurusan_id = '{$mapel_guru['jurusan_id']}'
    ORDER BY u.nama ASC
  ");
  
  // Query mapel untuk guru - hanya mapel yang diajar
  $mapel = mysqli_query($conn, "
    SELECT * FROM mapel 
    WHERE id = '$mapel_id'
    ORDER BY nama_mapel ASC
  ");
} else {
  // Query untuk admin - semua siswa dan mapel
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
  $semester = $_POST['semester'];
  $tahun_ajaran = isset($_POST['tahun_ajaran']) ? $_POST['tahun_ajaran'] : ($_POST['tahun_awal'] . '/' . $_POST['tahun_akhir']);
  $nilai_tugas = $_POST['nilai_tugas'];
  $nilai_uts = $_POST['nilai_uts'];
  $nilai_uas = $_POST['nilai_uas'];

  // Validasi nilai harus 0-100
  if ($nilai_tugas < 0 || $nilai_tugas > 100 || $nilai_uts < 0 || $nilai_uts > 100 || $nilai_uas < 0 || $nilai_uas > 100) {
    echo "<script>alert('Nilai tugas, UTS, dan UAS harus di antara 0-100!'); window.location.href='add.php';</script>";
    exit;
  }

  // Hitung nilai akhir
  $nilai_akhir = ($nilai_tugas + $nilai_uts + $nilai_uas) / 3;

  // Cek apakah sudah ada nilai untuk siswa, mapel, semester, dan tahun ajaran ini
  $cek = mysqli_query($conn, "SELECT * FROM nilai WHERE siswa_nis = '$siswa_nis' AND mapel_id = '$mapel_id' AND semester = '$semester' AND tahun_ajaran = '$tahun_ajaran'");

  if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Nilai untuk siswa, mata pelajaran, semester, dan tahun ajaran ini sudah ada!'); window.location.href='add.php';</script>";
    exit;
  }

  mysqli_query($conn, "INSERT INTO nilai (siswa_nis, mapel_id, semester, tahun_ajaran, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir) VALUES ('$siswa_nis', '$mapel_id', '$semester', '$tahun_ajaran', '$nilai_tugas', '$nilai_uts', '$nilai_uas', '$nilai_akhir')");
  header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Nilai - Sistem Akademik SMK</title>
  
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
      margin: 20px;
      padding: 30px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .form-header {
      background: #1a1a1a;
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
      opacity: 0.8;
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
    
    .form-input, .form-select {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      font-size: 14px;
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
      gap: 32px;
      margin-bottom: 20px;
    }
    
    .form-row .form-group {
      margin-bottom: 0;
    }
    
    .nilai-row {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 32px;
      margin-bottom: 20px;
    }
    
    .nilai-akhir-display {
      background: #f8f9fa;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      padding: 12px 16px;
      font-size: 14px;
      font-weight: 600;
      color: #1a1a1a;
      text-align: center;
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
    
    @media (max-width: 768px) {
      .form-container {
        margin: 10px;
        padding: 20px;
      }
      
      .form-header {
        padding: 20px;
      }
      
      .form-row, .nilai-row {
        grid-template-columns: 1fr;
        gap: 0;
      }
    }
  </style>
</head>
<body>

  <?php include '../../layout/navbar.php'; ?>
  
  <div class="form-container">
    
    <!-- Form Header -->
    <div class="form-header">
      <h1><i class="fas fa-chart-line me-3"></i>&nbsp;Tambah Nilai</h1>
      <p>Isi data nilai siswa untuk mata pelajaran tertentu</p>
    </div>

    <!-- Form -->
    <form method="POST" id="nilaiForm">
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
            <i class="fas fa-calendar me-2"></i>&nbsp;Semester
          </label>
          <select name="semester" class="form-select" required>
            <option value="">Pilih Semester</option>
            <option value="Ganjil">Ganjil</option>
            <option value="Genap">Genap</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-calendar-alt me-2"></i>&nbsp;Tahun Ajaran
          </label>
          <input type="number" name="tahun_ajaran" class="form-input" required placeholder="Contoh: 2023/2024" maxlength="9">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-star me-2"></i>&nbsp;Nilai
        </label>
        <div class="nilai-row">
          <div class="form-group">
            <label class="form-label">Nilai Tugas</label>
            <input type="number" name="nilai_tugas" class="form-input" required min="0" max="100" step="0.01" onchange="hitungNilaiAkhir()">
          </div>
          <div class="form-group">
            <label class="form-label">Nilai UTS</label>
            <input type="number" name="nilai_uts" class="form-input" required min="0" max="100" step="0.01" onchange="hitungNilaiAkhir()">
          </div>
          <div class="form-group">
            <label class="form-label">Nilai UAS</label>
            <input type="number" name="nilai_uas" class="form-input" required min="0" max="100" step="0.01" onchange="hitungNilaiAkhir()">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-calculator me-2"></i>&nbsp;Nilai Akhir
        </label>
        <div class="nilai-akhir-display" id="nilaiAkhirDisplay">
          Masukkan nilai untuk melihat hasil
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" name="simpan" class="btn-submit">
          <i class="fas fa-save"></i>&nbsp;
          Simpan Nilai
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
    function hitungNilaiAkhir() {
      const nilaiTugas = parseFloat(document.querySelector('input[name="nilai_tugas"]').value) || 0;
      const nilaiUts = parseFloat(document.querySelector('input[name="nilai_uts"]').value) || 0;
      const nilaiUas = parseFloat(document.querySelector('input[name="nilai_uas"]').value) || 0;
      
      const nilaiAkhir = (nilaiTugas + nilaiUts + nilaiUas) / 3;
      
      const display = document.getElementById('nilaiAkhirDisplay');
      if (nilaiTugas > 0 || nilaiUts > 0 || nilaiUas > 0) {
        display.textContent = nilaiAkhir.toFixed(2);
      } else {
        display.textContent = 'Masukkan nilai untuk melihat hasil';
      }
    }
  </script>
</body>
</html>
