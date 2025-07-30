<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_umkm = mysqli_real_escape_string($conn, $_GET['id']);

    $query_get_gambar = "SELECT gambar FROM umkm WHERE id_umkm='$id_umkm'";
    $result_get_gambar = mysqli_query($conn, $query_get_gambar);
    $gambar_data = mysqli_fetch_assoc($result_get_gambar);

    $gambar_path = '';
    if ($gambar_data && !empty($gambar_data['gambar'])) {
        $gambar_path = "uploads/" . $gambar_data['gambar'];
    }

    $query_delete = "DELETE FROM umkm WHERE id_umkm='$id_umkm'";

    if (mysqli_query($conn, $query_delete)) {
        if (!empty($gambar_path) && file_exists($gambar_path)) {
            unlink($gambar_path);
        }
        echo "<script>alert('UMKM berhasil dihapus!'); window.location.href='admin umkm.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error menghapus UMKM: " . mysqli_error($conn) . "'); window.location.href='admin umkm.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID UMKM tidak disediakan untuk dihapus.'); window.location.href='admin umkm.php';</script>";
    exit;
}
?>
