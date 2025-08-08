<?php
include '../../config/db.php';
session_start();

if ($_SESSION['user']['role'] !== 'admin') {
  header("Location: ../../auth/login.php");
  exit;
}

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mapel WHERE id = $id"));
$jurusan = mysqli_query($conn, "SELECT * FROM jurusan");

if (isset($_POST['update'])) {
  $kode_mapel = mysqli_real_escape_string($conn, $_POST['kode_mapel']);
  $nama_mapel = mysqli_real_escape_string($conn, $_POST['nama_mapel']);
  $jurusan_id = $_POST['jurusan_id'];

  // Cek apakah kode mapel sudah ada (kecuali untuk data yang sedang diedit)
  $cek = mysqli_query($conn, "SELECT * FROM mapel WHERE kode_mapel = '$kode_mapel' AND id != $id");
  if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Kode mata pelajaran sudah digunakan!'); window.location.href='edit.php?id=$id';</script>";
    exit;
  }

  mysqli_query($conn, "UPDATE mapel SET kode_mapel='$kode_mapel', nama_mapel='$nama_mapel', jurusan_id='$jurusan_id' WHERE id=$id");
  header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Mata Pelajaran - Sistem Akademik SMK</title>
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
    }
    
    .form-input:focus, .form-select:focus {
      outline: none;
      border-color: #1a1a1a;
      box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.1);
    }
    
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    
    .form-row .form-group {
      margin-bottom: 0;
    }
    
    .full-width {
      grid-column: 1 / -1;
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
      
      .form-row {
        grid-template-columns: 1fr;
        gap: 0;
      }
    }
  </style>
</head>
<body>

  <?php include '../../layout/navbar.php'; ?>
  
  <div class="form-container">
    <div class="form-header">
      <h1>Edit Mata Pelajaran</h1>
      <p>Ubah data mata pelajaran yang sudah ada</p>
    </div>

    <form method="POST">
      <div class="row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-hashtag me-2"></i>Kode Mapel
          </label>
          <input type="text" name="kode_mapel" class="form-control" required placeholder="Contoh: RPL001" maxlength="10" value="<?= htmlspecialchars($data['kode_mapel']) ?>">
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-graduation-cap me-2"></i>Jurusan
          </label>
          <select name="jurusan_id" class="form-control" required>
            <option value="">Pilih Jurusan</option>
            <?php while ($j = mysqli_fetch_assoc($jurusan)) { ?>
              <option value="<?= $j['id'] ?>" <?= $data['jurusan_id'] == $j['id'] ? 'selected' : '' ?>>
                <?= $j['kode_jurusan'] ?> - <?= $j['nama_jurusan'] ?>
              </option>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-book me-2"></i>Nama Mata Pelajaran
        </label>
        <input type="text" name="nama_mapel" class="form-control" required placeholder="Masukkan nama mata pelajaran" maxlength="100" value="<?= htmlspecialchars($data['nama_mapel']) ?>">
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
