<?php
include '../../config/db.php';

$id = $_GET['id'];
$query = "DELETE FROM jadwal WHERE id = $id";

if (mysqli_query($conn, $query)) {
    header("Location: index.php?deleted=1");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
