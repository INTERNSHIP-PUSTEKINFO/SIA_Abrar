<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['user']['role'] ?? 'guest';
$nama = $_SESSION['user']['nama'] ?? 'Tamu';
?>

<style>
  .main-layout {
    display: flex;
    min-height: 100vh;
  }

  .sidebar {
    width: 200px;
    min-height: 100vh;
    background: #1a1a1a;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
  }

  .sidebar a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    border-radius: 0 8px 8px 0;
    margin: 4px 0;
    font-weight: 500;
    font-size: 14px;
  }

  .sidebar a:hover, .sidebar a.active {
    background: #333;
    color: white;
    transform: translateX(4px);
  }

  .sidebar a i {
    margin-right: 12px;
    width: 20px;
    text-align: center;
    font-size: 16px;
  }

  .sidebar-header {
    height: 70px;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 600;
    color: white;
    border-bottom: 1px solid #333;
  }

  .sidebar-header i {
    margin-right: 10px;
    font-size: 20px;
  }

  .main-content {
    flex: 1;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  .content-wrapper {
    flex: 1;
    padding: 20px;
  }

  .user-info {
    padding: 20px 24px;
    border-top: 1px solid #333;
    margin-top: 20px;
  }

  .user-info .user-name {
    font-weight: 600;
    color: white;
    margin-bottom: 4px;
    font-size: 14px;
  }

  .user-info .user-role {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.6);
    text-transform: capitalize;
  }

  .logout-link {
    background: #dc3545;
    color: white !important;
    border-radius: 8px !important;
    margin: 16px 24px;
    text-align: center;
    font-weight: 500;
  }

  .logout-link:hover {
    background: #c82333 !important;
    transform: translateY(-1px);
  }

  .nav-divider {
    height: 1px;
    background: #333;
    margin: 20px 24px;
  }

  /* Enhanced Responsive Design */
  @media (max-width: 1200px) {
    .content-wrapper {
      padding: 15px;
    }
    
    .sidebar a {
      padding: 14px 20px;
      font-size: 13px;
    }
    
    .user-info {
      padding: 18px 20px;
    }
  }
  
  @media (max-width: 768px) {
    .main-layout {
      flex-direction: column;
    }
    
    .sidebar {
      width: 100%;
      min-height: auto;
      order: 2;
    }
    
    .main-content {
      order: 1;
    }
    
    .content-wrapper {
      padding: 12px;
    }
    
    .sidebar a {
      border-radius: 8px;
      margin: 2px 12px;
      padding: 12px 16px;
      font-size: 13px;
    }
    
    .sidebar a:hover {
      transform: translateY(-1px);
    }
    
    .user-info {
      padding: 15px 16px;
    }
    
    .logout-link {
      margin: 12px 16px;
      padding: 10px 16px;
    }
  }
  
  @media (max-width: 576px) {
    .content-wrapper {
      padding: 8px;
    }
    
    .sidebar a {
      margin: 1px 8px;
      padding: 10px 12px;
      font-size: 12px;
    }
    
    .user-info {
      padding: 12px;
    }
    
    .logout-link {
      margin: 8px 12px;
      padding: 8px 12px;
      font-size: 13px;
    }
    
    .sidebar-header {
      height: 60px;
      font-size: 16px;
      padding: 0 10px;
    }
  }
</style>

<div class="main-layout">
  <div class="sidebar">
    <div class="sidebar-header">
      <i class="fas fa-graduation-cap me-2"></i>
      AkademikApp
    </div>
    
    <a href="../../dashboard/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'dashboard') !== false ? 'active' : '' ?>">
      <i class="fas fa-home me-2"></i> Beranda
    </a>

    <?php if ($role === 'admin'): ?>
      <a href="/akademik_smk/pages/absensi/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'absensi') !== false ? 'active' : '' ?>">
        <i class="fas fa-clipboard-check me-2"></i> Absensi
      </a>
      <a href="/akademik_smk/pages/siswa/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'siswa') !== false ? 'active' : '' ?>">
        <i class="fas fa-user-graduate me-2"></i> Data Siswa
      </a>
      <a href="/akademik_smk/pages/guru/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'guru') !== false ? 'active' : '' ?>">
        <i class="fas fa-chalkboard-teacher me-2"></i> Data Guru
      </a>
      <a href="/akademik_smk/pages/mapel/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'mapel') !== false ? 'active' : '' ?>">
        <i class="fas fa-book me-2"></i> Mata Pelajaran
      </a>
      <a href="/akademik_smk/pages/nilai/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'nilai') !== false ? 'active' : '' ?>">
        <i class="fas fa-chart-line me-2"></i> Nilai Siswa
      </a>
      <a href="/akademik_smk/pages/jadwal/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'jadwal') !== false ? 'active' : '' ?>">
        <i class="fas fa-calendar-alt me-2"></i> Jadwal
      </a>

    <?php elseif ($role === 'guru'): ?>
      <a href="/akademik_smk/pages/siswa/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'siswa') !== false ? 'active' : '' ?>">
        <i class="fas fa-user-graduate me-2"></i> Data Siswa
      </a>
      <a href="/akademik_smk/pages/nilai/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'nilai') !== false ? 'active' : '' ?>">
        <i class="fas fa-edit me-2"></i> Input Nilai
      </a>
      <a href="/akademik_smk/pages/absensi/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'absensi') !== false ? 'active' : '' ?>">
        <i class="fas fa-clipboard-check me-2"></i> Absensi
      </a>
      <a href="/akademik_smk/pages/jadwal/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'jadwal') !== false ? 'active' : '' ?>">
        <i class="fas fa-calendar-alt me-2"></i> Lihat Jadwal
      </a>

    <?php elseif ($role === 'siswa'): ?>
      <a href="/akademik_smk/pages/jadwal/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'jadwal') !== false ? 'active' : '' ?>">
        <i class="fas fa-calendar-alt me-2"></i> Lihat Jadwal
      </a>
      <a href="/akademik_smk/pages/nilai/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'nilai') !== false ? 'active' : '' ?>">
        <i class="fas fa-chart-line me-2"></i> Lihat Nilai
      </a>
    <?php endif; ?>

    <div class="nav-divider"></div>
    
    <div class="user-info">
      <div class="user-name"><?= htmlspecialchars($nama) ?></div>
      <div class="user-role"><?= ucfirst($role) ?></div>
    </div>
    
    <a href="/akademik_smk/auth/logout.php" class="logout-link">
      <i class="fas fa-sign-out-alt me-2"></i> Logout
    </a>
  </div>

  <div class="main-content">
    <div class="content-wrapper">
