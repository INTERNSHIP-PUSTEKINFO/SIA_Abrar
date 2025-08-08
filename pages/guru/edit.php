<?php
include '../../config/db.php';
session_start();

$nip = $_GET['nip'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT g.*, u.email 
  FROM guru g 
  JOIN users u ON g.user_id = u.id 
  WHERE g.nip = '$nip'
"));
$users = mysqli_query($conn, "SELECT * FROM users WHERE role = 'guru'");
$mapel = mysqli_query($conn, "SELECT * FROM mapel");

if (isset($_POST['update'])) {
  $tempat    = $_POST['tempat'];
  $tgl       = $_POST['tanggal'];
  $jk        = $_POST['jk'];
  $alamat    = $_POST['alamat'];
  $mapel_id  = $_POST['mapel_id'];
  $email     = $_POST['email'];
  $password  = $_POST['password'];
  $user_id   = $data['user_id']; // ambil user_id dari data sebelumnya

  // update data guru
  mysqli_query($conn, "
    UPDATE guru SET 
      tempat_lahir = '$tempat',
      tanggal_lahir = '$tgl',
      jenis_kelamin = '$jk',
      alamat = '$alamat',
      mapel_id = '$mapel_id'
    WHERE nip = '$nip'
  ");

  // update email
  mysqli_query($conn, "UPDATE users SET email = '$email' WHERE id = '$user_id'");

  // kalau password gak kosong, update password juga (pakai password_hash)
  if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE users SET password = '$hashed' WHERE id = '$user_id'");
  }

  header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Data Guru - Sistem Akademik SMK</title>
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
<?php include '../../layout/navbar.php'; ?>
<?php include '../../layout/header.php'; ?>

<div class="form-container">
  <div class="form-header">
    <h1>Edit Data Guru</h1>
    <p>Update informasi data guru</p>
  </div>

  <form method="POST">
    <div class="row">
      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-id-card me-2"></i>NIP
        </label>
        <input type="text" class="form-control" value="<?= $data['nip'] ?>" disabled>
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-envelope me-2"></i>Email
        </label>
        <input type="email" name="email" class="form-control" value="<?= $data['email'] ?>" required>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-lock me-2"></i>Password
        </label>
        <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-book me-2"></i>Mata Pelajaran
        </label>
        <select name="mapel_id" class="form-control">
          <?php while ($m = mysqli_fetch_assoc($mapel)) { ?>
            <option value="<?= $m['id'] ?>" <?= $m['id'] == $data['mapel_id'] ? 'selected' : '' ?>>
              <?= $m['nama_mapel'] ?>
            </option>
          <?php } ?>
        </select>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-map-marker-alt me-2"></i>Tempat Lahir
        </label>
        <input type="text" name="tempat" class="form-control" value="<?= $data['tempat_lahir'] ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-calendar-alt me-2"></i>Tanggal Lahir
        </label>
        <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal_lahir'] ?>" required>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-venus-mars me-2"></i>Jenis Kelamin
        </label>
        <select name="jk" class="form-control">
          <option value="L" <?= $data['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
          <option value="P" <?= $data['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-map me-2"></i>Alamat
        </label>
        <textarea name="alamat" class="form-control" rows="1" required><?= $data['alamat'] ?></textarea>
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

</body>
</html>
