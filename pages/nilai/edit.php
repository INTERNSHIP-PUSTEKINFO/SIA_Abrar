<?php
include '../../config/db.php';
session_start();

$id = $_GET['id'];
$nilai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM nilai WHERE id = '$id'"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nilai_tugas = $_POST['nilai_tugas'];
  $nilai_uts = $_POST['nilai_uts'];
  $nilai_uas = $_POST['nilai_uas'];
  $nilai_akhir = round(($nilai_tugas + $nilai_uts + $nilai_uas) / 3, 2);

  mysqli_query($conn, "UPDATE nilai SET 
    nilai_tugas = '$nilai_tugas',
    nilai_uts = '$nilai_uts',
    nilai_uas = '$nilai_uas',
    nilai_akhir = '$nilai_akhir'
    WHERE id = '$id'");
  header('Location: index.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Nilai - Sistem Akademik SMK</title>
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
      <h1>Edit Nilai</h1>
      <p>Update nilai siswa pada form di bawah ini</p>
    </div>
    
    <form method="POST">
      <div class="form-group">
        <label for="nilai_tugas" class="form-label">Nilai Tugas</label>
        <input type="number" name="nilai_tugas" id="nilai_tugas" class="form-control" step="0.01" min="0" max="100" value="<?= $nilai['nilai_tugas'] ?>" required>
      </div>

      <div class="form-group">
        <label for="nilai_uts" class="form-label">Nilai UTS</label>
        <input type="number" name="nilai_uts" id="nilai_uts" class="form-control" step="0.01" min="0" max="100" value="<?= $nilai['nilai_uts'] ?>" required>
      </div>

      <div class="form-group">
        <label for="nilai_uas" class="form-label">Nilai UAS</label>
        <input type="number" name="nilai_uas" id="nilai_uas" class="form-control" step="0.01" min="0" max="100" value="<?= $nilai['nilai_uas'] ?>" required>
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
