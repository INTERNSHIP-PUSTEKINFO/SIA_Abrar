<?php
include '../../config/db.php';
session_start();

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM nilai WHERE id = '$id'");
header('Location: index.php');
exit;
