<?php
include '../../config/db.php';

$nis = $_GET['nis'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT s.*, u.email 
  FROM siswa s 
  JOIN users u ON s.user_id = u.id 
  WHERE s.nis = '$nis'
"));

$jurusan = mysqli_query($conn, "SELECT * FROM jurusan");
$kelas   = mysqli_query($conn, "SELECT * FROM kelas");

if (isset($_POST['update'])) {
  $tempat      = $_POST['tempat'];
  $tanggal     = $_POST['tanggal'];
  $jk          = $_POST['jk'];
  $alamat      = $_POST['alamat'];
  $jurusan_id  = $_POST['jurusan_id'];
  $kelas_id    = $_POST['kelas_id'];
  $tahun       = $_POST['tahun'];
  $email       = $_POST['email'];
  $status      = $_POST['status'];

  mysqli_query($conn, "
    UPDATE siswa SET 
      tempat_lahir = '$tempat',
      tanggal_lahir = '$tanggal',
      jenis_kelamin = '$jk',
      alamat = '$alamat',
      jurusan_id = '$jurusan_id',
      kelas_id = '$kelas_id',
      tahun_masuk = '$tahun',
      status = '$status'
    WHERE nis = '$nis'
  ");

  mysqli_query($conn, "UPDATE users SET email = '$email' WHERE id = '{$data['user_id']}'");

  header("Location: index.php");
  exit(); 
}


include '../../layout/header.php';
include '../../layout/navbar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Siswa - Sistem Akademik SMK</title>
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
    }
  </style>
</head>
<body>

  <div class="form-container">
    <div class="form-header">
      <h1>Edit Data Siswa</h1>
      <p>Update informasi siswa pada form di bawah ini</p>
    </div>
    
    <form method="POST">
      <div class="row">
        <div class="form-group">
          <label class="form-label">NIS</label>
          <input type="text" class="form-control" value="<?= $data['nis'] ?>" disabled>
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= $data['email'] ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Password <small>(Kosongkan jika tidak ingin mengubah)</small></label>
          <input type="password" name="password" class="form-control">
        </div>


        <div class="col-md-6">
          <label class="form-label">Tempat Lahir</label>
          <input type="text" name="tempat" class="form-control" value="<?= $data['tempat_lahir'] ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Tanggal Lahir</label>
          <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal_lahir'] ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Jenis Kelamin</label>
          <select name="jk" class="form-select">
            <option value="L" <?= $data['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="P" <?= $data['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Alamat</label>
          <textarea name="alamat" class="form-control" rows="3"><?= $data['alamat'] ?></textarea>
        </div>

        <div class="col-md-6">
          <label class="form-label">Jurusan</label>
          <select name="jurusan_id" class="form-select">
            <?php while ($j = mysqli_fetch_assoc($jurusan)) { ?>
              <option value="<?= $j['id'] ?>" <?= $j['id'] == $data['jurusan_id'] ? 'selected' : '' ?>>
                <?= $j['nama_jurusan'] ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Kelas</label>
          <select name="kelas_id" class="form-select">
            <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
              <option value="<?= $k['id'] ?>" <?= $k['id'] == $data['kelas_id'] ? 'selected' : '' ?>>
                <?= $k['nama_kelas'] ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Tahun Masuk</label>
          <input type="number" name="tahun" class="form-control" value="<?= $data['tahun_masuk'] ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="aktif" <?= $data['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
            <option value="lulus" <?= $data['status'] == 'lulus' ? 'selected' : '' ?>>Lulus</option>
            <option value="keluar" <?= $data['status'] == 'keluar' ? 'selected' : '' ?>>Keluar</option>
          </select>
        </div>

        <div class="col-12">
          <button type="submit" name="update" class="btn btn-success">Update</button>
          <a href="index.php" class="btn btn-secondary">Kembali</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

