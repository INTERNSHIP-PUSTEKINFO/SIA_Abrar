<?php
include '../../config/db.php';
session_start();
if ($_SESSION['user']['role'] !== 'admin') { header("Location: ../../auth/login.php"); exit; }

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM mapel WHERE id_mapel = $id");
header("Location: index.php");
