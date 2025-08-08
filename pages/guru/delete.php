<?php
include '../../config/db.php';
session_start();

$nip = $_GET['nip'];
mysqli_query($conn, "DELETE FROM guru WHERE nip='$nip'");
header("Location: index.php");
