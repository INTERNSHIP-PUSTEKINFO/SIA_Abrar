<?php
include '../../config/db.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal WHERE id = $id"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kelas_id = $_POST['kelas_id'];
    $mapel_id = $_POST['mapel_id'];
    $guru_id  = $_POST['guru_id'];
    $hari     = $_POST['hari'];
    $jam_ke   = $_POST['jam_ke'];

    $query = "UPDATE jadwal SET 
                kelas_id = '$kelas_id', 
                mapel_id = '$mapel_id', 
                guru_id = '$guru_id', 
                hari = '$hari', 
                jam_ke = '$jam_ke' 
              WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?updated=1");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Jadwal - Sistem Akademik SMK</title>
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
  <div class="form-container">
    <div class="form-header">
      <h1>Edit Jadwal</h1>
      <p>Update informasi jadwal pelajaran</p>
    </div>

    <form method="POST">
      <div class="row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-chalkboard me-2"></i>Kelas
          </label>
          <input type="number" name="kelas_id" class="form-control" value="<?= $data['kelas_id'] ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-book me-2"></i>Mapel
          </label>
          <input type="number" name="mapel_id" class="form-control" value="<?= $data['mapel_id'] ?>" required>
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-user-tie me-2"></i>Guru
          </label>
          <input type="number" name="guru_id" class="form-control" value="<?= $data['guru_id'] ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-calendar-alt me-2"></i>Hari
          </label>
          <select name="hari" class="form-control" required>
            <?php
              $hari_list = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
              foreach ($hari_list as $hari) {
                $selected = ($hari == $data['hari']) ? 'selected' : '';
                echo "<option value='$hari' $selected>$hari</option>";
              }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">
          <i class="fas fa-clock me-2"></i>Jam ke-
        </label>
        <input type="number" name="jam_ke" class="form-control" value="<?= $data['jam_ke'] ?>" required>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-success">
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
