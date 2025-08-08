<?php
include '../../config/db.php';
session_start();

if ($_SESSION['user']['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

$mapel = mysqli_query($conn, "SELECT * FROM mapel");

if (isset($_POST['simpan'])) {
  $nip      = mysqli_real_escape_string($conn, $_POST['nip']);
  $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
  $email    = mysqli_real_escape_string($conn, $_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $tempat   = mysqli_real_escape_string($conn, $_POST['tempat']);
  $tgl      = $_POST['tanggal'];
  $jk       = $_POST['jk'];
  $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
  $mapel_id = $_POST['mapel_id'];

  // Cek apakah email sudah ada
  $cek = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
  if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Email sudah digunakan!'); window.location.href='add.php';</script>";
    exit;
  }

  // Transaksi untuk insert ke dua tabel
  mysqli_begin_transaction($conn);
  try {
    // Insert ke tabel users (tambahkan kolom nama jika ada)
    mysqli_query($conn, "INSERT INTO users (nama, email, password, role, created_at, updated_at) 
                     VALUES ('$nama', '$email', '$password', 'guru', NOW(), NOW())");

    $user_id = mysqli_insert_id($conn); // Ambil user_id terakhir

    // Insert ke tabel guru
    mysqli_query($conn, "INSERT INTO guru (nip, user_id, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, mapel_id)
                         VALUES ('$nip', '$user_id', '$tempat', '$tgl', '$jk', '$alamat', '$mapel_id')");

    mysqli_commit($conn); // Semua sukses → commit
    header("Location: index.php");
  } catch (Exception $e) {
    mysqli_rollback($conn); // Gagal → rollback
    echo "Gagal menyimpan data: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Guru - Sistem Akademik SMK</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
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
      box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
      margin: 20px auto;
      padding: 40px;
      max-width: 900px;
      width: 95%;
      padding: 30px;
      max-width: 800px;
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
    .form-input, .form-select { 
      width: 100%; 
      padding: 5px 6px; 
      border: 1px solid #e9ecef; 
      border-radius: 8px; 
      font-size: 14px; 
      transition: all 0.3s ease; 
      background: white;
      height: 45px;
    }
    
    textarea.form-input {
      height: auto;
      min-height: 100px;
    }
    .form-input:focus, .form-select:focus { 
      outline: none; 
      border-color: #1a1a1a; 
      box-shadow: 0 0 0 3px rgba(26,26,26,0.1); 
    }
    .form-row { 
      display: grid; 
      grid-template-columns: 1fr 1fr; 
      gap: 20px; 
    }
    .full-width { 
      grid-column: 1 / -1; 
    }
    .btn-submit { 
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; 
      border: none;
      padding: 10px 20px; 
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
      transform: translateY(-2px); 
      box-shadow: 0 3px 10px rgba(0,0,0,0.15); 
    }
    .btn-secondary { 
      background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
      color: white; 
      border: none;
      padding: 10px 20px; 
      border-radius: 8px; 
      font-weight: 500; 
      font-size: 14px; 
      cursor: pointer; 
      transition: all 0.3s ease; 
      display: inline-flex; 
      align-items: center; 
      gap: 8px;
      text-decoration: none;
      margin-left: 10px;
    }
    .btn-secondary:hover { 
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
      .form-row { 
        grid-template-columns: 1fr; 
        gap: 15px; 
      }
      .form-control, .form-select, .btn {
        font-size: 13px;
        padding: 8px 15px;
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
    }
  </style>
</head>
<body>

  <?php include '../../layout/navbar.php'; ?>
  
  <div class="form-container"><div class="form-header">
      <h1><i class="fas fa-chalkboard-teacher me-3"></i>&nbsp;Tambah Guru Baru</h1>
      <p>Isi data lengkap guru untuk menambahkan ke sistem akademik</p>
    </div><form method="POST">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-envelope me-2"></i>&nbsp;Email
          </label>
          <input type="email" name="email" class="form-input" required placeholder="Masukkan email guru">
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-lock me-2"></i>&nbsp;Password
          </label>
          <input type="password" name="password" class="form-input" required placeholder="Masukkan password">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-hashtag me-2"></i>&nbsp;NIP
          </label>
          <input type="text" name="nip" class="form-input" required placeholder="Masukkan NIP">
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-user me-2"></i>&nbsp;Nama Lengkap
          </label>
          <input type="text" name="nama" class="form-input" required placeholder="Masukkan nama lengkap">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-map-marker-alt me-2"></i>&nbsp;Tempat Lahir
          </label>
          <input type="text" name="tempat" class="form-input" placeholder="Masukkan tempat lahir">
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-calendar me-2"></i>&nbsp;Tanggal Lahir
          </label>
          <input type="date" name="tanggal" class="form-input">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-venus-mars me-2"></i>&nbsp;Jenis Kelamin
          </label>
          <select name="jk" class="form-select">
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-book me-2"></i>&nbsp;Mata Pelajaran
          </label>
          <select name="mapel_id" class="form-select">
            <?php while ($m = mysqli_fetch_assoc($mapel)) { ?>
              <option value="<?= $m['id'] ?>"><?= $m['nama_mapel'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="form-group full-width">
        <label class="form-label">
          <i class="fas fa-map-marker-alt me-2"></i>&nbsp;Alamat
        </label>
        <textarea name="alamat" class="form-input" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" name="simpan" class="btn-submit">
          <i class="fas fa-save"></i>
          Simpan Data
        </button>
        <a href="index.php" class="btn-secondary">
          <i class="fas fa-arrow-left"></i>
          Kembali
        </a>
      </div>
    </form>
  </div>

  <?php include '../../layout/close_layout.php'; ?>
  <?php include '../../layout/footer.php'; ?>
</body>
</html>
