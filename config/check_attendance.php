<?php
if (!function_exists('check_attendance')) {
    function check_attendance($conn, $user_id) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
            return true; // Not a student, no need to check attendance
        }

        $today = date('Y-m-d');
        
        // Get student NIS
        $siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM siswa WHERE user_id = $user_id"));
        if (!$siswa) return true; // Not found in siswa table
        
        $nis = $siswa['nis'];
        
        // Check if already attended today
        $cek_absen = mysqli_query($conn, "
            SELECT * FROM absensi 
            WHERE siswa_nis = '$nis' 
            AND DATE(tanggal) = '$today'
        ");
        
        if (mysqli_num_rows($cek_absen) == 0) {
            // Haven't attended today, redirect to attendance page
            header("Location: /akademik_smk/pages/absensi/siswa_auto.php");
            exit;
        }
        
        return true;
    }
}
?>
