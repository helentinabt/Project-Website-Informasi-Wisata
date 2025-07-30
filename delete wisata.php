<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_wisata = mysqli_real_escape_string($conn, $_GET['id']);

    $query_get_gambar = "SELECT gambar FROM wisata WHERE id_wisata='$id_wisata'";
    $result_get_gambar = mysqli_query($conn, $query_get_gambar);
    $gambar_data = mysqli_fetch_assoc($result_get_gambar);

    $gambar_path = '';
    if ($gambar_data && !empty($gambar_data['gambar'])) {
        $gambar_path = "uploads/" . $gambar_data['gambar'];
    }

    $query_delete = "DELETE FROM wisata WHERE id_wisata='$id_wisata'";

    if (mysqli_query($conn, $query_delete)) {
        if (!empty($gambar_path) && file_exists($gambar_path)) {
            unlink($gambar_path);
        }
        echo "<script>alert('Wisata berhasil dihapus!'); window.location.href='admin wisata.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error menghapus wisata: " . mysqli_error($conn) . "'); window.location.href='admin wisata.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID wisata tidak disediakan untuk dihapus.'); window.location.href='admin wisata.php';</script>";
    exit;
}
?>
