<?php
include_once 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$wisata_data = null;
$message = '';

if (isset($_GET['id'])) {
    $id_wisata = mysqli_real_escape_string($conn, $_GET['id']);
    $query_select = "SELECT * FROM wisata WHERE id_wisata='$id_wisata'";
    $result_select = mysqli_query($conn, $query_select);

    if (!$result_select || mysqli_num_rows($result_select) == 0) {
        die("Data wisata tidak ditemukan atau ID tidak valid.");
    }
    $wisata_data = mysqli_fetch_assoc($result_select);
} else {
    die("ID wisata tidak disediakan untuk diedit.");
}


function getExistingImages($gambar_field) {
    if (empty($gambar_field)) {
        return [];
    }
    return array_map('trim', explode(',', $gambar_field));
}

if (isset($_POST['submit'])) {
    $nama = isset($_POST['nama']) ? mysqli_real_escape_string($conn, $_POST['nama']) : $wisata_data['nama'];
    $deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($conn, $_POST['deskripsi']) : $wisata_data['deskripsi'];
    $peta_iframe = isset($_POST['peta_iframe']) ? mysqli_real_escape_string($conn, $_POST['peta_iframe']) : $wisata_data['peta_iframe'];

    $existing_images = getExistingImages($wisata_data['gambar']);
    $updated_images = $existing_images;

    if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $filename) {
            $filename = trim($filename);
            $index = array_search($filename, $updated_images);
            if ($index !== false) {
                $file_path = "uploads/" . $filename;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                unset($updated_images[$index]);
            }
        }
        $updated_images = array_values($updated_images);
    }

    if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $uploaded_count = 0;
        $max_images = 5;
        $remaining_slots = $max_images - count($updated_images);

        for ($i = 0; $i < count($_FILES['new_images']['name']) && $uploaded_count < $remaining_slots; $i++) {
            if ($_FILES['new_images']['error'][$i] == 0) {
                $file_extension = strtolower(pathinfo($_FILES["new_images"]["name"][$i], PATHINFO_EXTENSION));
                $gambar_nama_baru = time() . '_' . $i . '.' . $file_extension;
                $target_file = $target_dir . $gambar_nama_baru;

                $check = getimagesize($_FILES["new_images"]["tmp_name"][$i]);
                if ($check !== false) {
                    if ($_FILES["new_images"]["size"][$i] <= 5000000) {
                        if (in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
                            if (move_uploaded_file($_FILES["new_images"]["tmp_name"][$i], $target_file)) {
                                $updated_images[] = $gambar_nama_baru;
                                $uploaded_count++;
                                $message .= "Gambar " . ($i + 1) . " berhasil diunggah. ";
                            } else {
                                $message .= "Gagal mengunggah gambar " . ($i + 1) . ". ";
                            }
                        } else {
                            $message .= "Format gambar " . ($i + 1) . " tidak diizinkan. ";
                        }
                    } else {
                        $message .= "Ukuran gambar " . ($i + 1) . " terlalu besar (maksimal 5MB). ";
                    }
                } else {
                    $message .= "File " . ($i + 1) . " bukan gambar yang valid. ";
                }
            } else {
                $message .= "Error upload gambar " . ($i + 1) . ". ";
            }
        }

        if ($uploaded_count < count($_FILES['new_images']['name'])) {
            $message .= "Beberapa gambar tidak dapat diunggah karena mencapai batas maksimal 5 gambar. ";
        }
    }

    if (count($updated_images) > 5) {
        $updated_images = array_slice($updated_images, 0, 5);
        $message .= "Maksimal 5 gambar. Gambar berlebih tidak disimpan. ";
    }

    $gambar_string = !empty($updated_images) ? implode(',', array_values($updated_images)) : '';

    $query_update = "UPDATE wisata SET nama='$nama', deskripsi='$deskripsi', gambar='$gambar_string', peta_iframe='$peta_iframe' WHERE id_wisata='$id_wisata'";

    if (mysqli_query($conn, $query_update)) {
        $_SESSION['success_message'] = "Data wisata berhasil diubah!";
        header("Location: admin wisata.php");
        exit();
    } else {
        $message .= "Gagal mengubah data: " . mysqli_error($conn);
        $_SESSION['error_message'] = $message;
    }
}

$current_images = getExistingImages($wisata_data['gambar']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Wisata - Admin Desa Klero</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
    --primary-color: #1e8449;
    --primary-light: #27ae60;
    --primary-gradient: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
    --secondary-color: #e8f5e8;
    --accent-color: #17a2b8;
    --text-dark: #2c3e50;
    --text-muted: #6c757d;
    --border-color: #e9ecef;
    --success-color: #28a745;
    --error-color: #dc3545;
    --warning-color: #ffc107;
    --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f8fffe 0%, #f0f9f0 100%);
    min-height: 100vh;
}

.container-fluid {
    height: 100vh;
    overflow: hidden;
    display: flex;
}

.sidebar {
    width: 280px;
    background: var(--primary-gradient);
    backdrop-filter: blur(10px);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.sidebar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.sidebar-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    color: white;
    padding: 1.5rem;
    text-align: center;
    border-radius: 15px;
    margin: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
}

.sidebar-header h4 {
    font-weight: 600;
    font-size: 1.2rem;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.sidebar-header .subtitle {
    font-size: 0.85rem;
    opacity: 0.9;
    margin-top: 0.5rem;
}

.menu {
    list-style: none;
    padding: 0 1.5rem;
    flex-grow: 1;
}

.menu li {
    margin-bottom: 0.5rem;
}

.menu li a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 500;
    position: relative;
    overflow: hidden;
}

.menu li a i {
    margin-right: 0.75rem;
    width: 20px;
    text-align: center;
    font-size: 1.1rem;
}

.menu li a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.menu li a:hover::before {
    left: 100%;
}

.menu li a:hover,
.menu li a.active {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.logout-section {
    padding: 1.5rem;
    margin-top: auto;
}

.logout-btn {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border: none;
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: var(--shadow-md);
}

.logout-btn i {
    margin-right: 0.5rem;
}

.logout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
    color: white;
}

.main-content {
    flex: 1;
    flex-direction: column;
    background-color: transparent;
}

.topbar {
    background: var(--primary-gradient);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    box-shadow: var(--shadow-md);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.profile {
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    margin-left: auto;
}

.profile i {
    margin-right: 0.5rem;
    font-size: 1.2rem;
}

.content {
    padding: 2rem;
    flex-grow: 1;
    overflow-y: auto;
}

.form-container {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
    animation: fadeInUp 0.6s ease-out;
}

.page-title {
    margin-bottom: 2rem;
    color: var(--text-dark);
    font-weight: 700;
    font-size: 2rem;
    position: relative;
    padding-bottom: 1rem;
    text-align: center;
}

.page-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: var(--primary-gradient);
    border-radius: 2px;
}

.page-title i {
    margin-right: 0.75rem;
    color: var(--primary-color);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    font-size: 0.95rem;
}

.form-label i {
    margin-right: 0.5rem;
    color: var(--primary-color);
    width: 20px;
}

.form-control {
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 0.875rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.9);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    background: white;
}

.form-control::placeholder {
    color: var(--text-muted);
    opacity: 0.7;
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.form-text {
    color: var(--text-muted);
    font-size: 0.85rem;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
}

.form-text i {
    margin-right: 0.375rem;
    font-size: 0.8rem;
}

.image-upload-section {
    background: var(--secondary-color);
    border-radius: 15px;
    padding: 20px;
    border: 2px dashed var(--primary-color);
    transition: all 0.3s ease;
}

.image-upload-section:hover {
    background: rgba(40, 167, 69, 0.1);
}

.current-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.image-item {
    position: relative;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
}

.image-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.image-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.image-item .image-controls {
    position: absolute;
    top: 10px;
    right: 10px;
}

.delete-image-btn {
    background: rgba(220, 53, 69, 0.9);
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.delete-image-btn:hover {
    background: rgba(220, 53, 69, 1);
    transform: scale(1.1);
}

.delete-image-btn input[type="checkbox"] {
    display: none;
}

.file-upload-area {
    border: 2px dashed var(--primary-color);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    background: rgba(25, 135, 84, 0.05);
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    background: rgba(25, 135, 84, 0.1);
    transform: translateY(-2px);
}

.file-upload-area i {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.marked-for-deletion {
    opacity: 0.3;
    transform: scale(0.95);
    filter: grayscale(100%);
}

.preview-images-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.preview-image-item {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.preview-image-item img {
    width: 100%;
    height: 100px;
    object-fit: cover;
}

.preview-remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    cursor: pointer;
}

.map-iframe-preview {
    width: 100%;
    height: 300px;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    margin-top: 1rem;
    background: var(--secondary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-style: italic;
    transition: all 0.3s ease;
}

.map-iframe-preview iframe {
    width: 100%;
    height: 100%;
    border: none;
    border-radius: 10px;
}

.map-iframe-preview:has(iframe) {
    border-color: var(--primary-color);
}

.submit-btn {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    cursor: pointer;
    font-size: 1.1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 200px;
    box-shadow: var(--shadow-md);
    margin: 2rem auto 0;
}

.submit-btn i {
    margin-right: 0.5rem;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

@media (max-width: 768px) {
    .container-fluid {
        flex-direction: column;
        height: auto;
    }
    
    .sidebar {
        width: 100%;
        height: auto;
        border-right: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-header {
        margin: 1rem;
    }
    
    .menu {
        padding: 0 1rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .menu li {
        margin-bottom: 0;
        flex: 1;
        min-width: 150px;
    }
    
    .logout-section {
        padding: 1rem;
    }
    
    .logout-btn {
        width: auto;
        align-self: center;
        margin: 0 auto;
        max-width: 200px;
    }
    
    .content {
        padding: 1rem;
    }
    
    .form-container {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }

    .current-images-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.content::-webkit-scrollbar {
    width: 8px;
}

.content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.content::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
}

.content::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}
    </style>
</head>
<body>
    <div class="container-fluid d-flex p-0">
        <div class="sidebar d-flex flex-column">
            <div class="sidebar-header">
                <h4>Desa Klero</h4>
                <p class="subtitle">Management System</p>
            </div>
            <ul class="menu">
                <li><a href="admin wisata.php" class="active"><i class="fas fa-mountain"></i> Kelola Wisata</a></li>
                <li><a href="admin umkm.php"><i class="fas fa-store"></i> Kelola UMKM</a></li>
            </ul>
            <div class="logout-section">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </a>
            </div>
        </div>
        <div class="main-content">
            <div class="topbar">
                <div class="profile">
                    <i class="fas fa-user-shield"></i>
                    Halo, Admin!
                </div>
            </div>
            <div class="content">
                <div class="form-container">
                    <h1 class="page-title">
                        <i class="fas fa-edit"></i>Edit Wisata
                    </h1>
                    
                    <?php if (!empty($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                            echo $_SESSION['error_message'];
                            unset($_SESSION['error_message']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-info">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" id="editWisataForm">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-tag"></i>
                                Nama Wisata
                            </label>
                            <input type="text" class="form-control" name="nama" 
                                   value="<?= htmlspecialchars($wisata_data['nama']) ?>" 
                                   placeholder="Masukkan nama wisata..." required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-align-left"></i>
                                Deskripsi Wisata
                            </label>
                            <textarea class="form-control" name="deskripsi" rows="5" 
                                      placeholder="Masukkan deskripsi wisata..." required><?= htmlspecialchars($wisata_data['deskripsi']) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-image"></i>
                                Gambar Wisata (Maksimal 5 gambar)
                            </label>
                            
                            <div class="image-upload-section">
                                <?php if (!empty($current_images)): ?>
                                    <h6><i class="fas fa-images"></i> Gambar Saat Ini:</h6>
                                    <div class="current-images-grid" id="currentImagesGrid">
                                        <?php foreach ($current_images as $index => $image): ?>
                                            <div class="image-item" data-index="<?= $index ?>">
                                                <img src="uploads/<?= htmlspecialchars($image) ?>" alt="Gambar <?= $index + 1 ?>" style="width: 200px; height: 150px; object-fit: cover;">
                                                <div class="image-controls">
                                                    <div class="delete-image-btn" onclick="toggleDeleteImage('<?= htmlspecialchars($image) ?>')" title="Hapus gambar">
                                                        <input type="checkbox" name="delete_images[]" value="<?= htmlspecialchars($image) ?>" id="delete_<?= $index ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="file-upload-area" onclick="document.getElementById('new_images').click();">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h5>Klik untuk upload gambar baru</h5>
                                    <p class="text-muted mb-0">Pilih hingga <?= 5 - count($current_images) ?> gambar baru (JPG, PNG, GIF - Max 5MB)</p>
                                </div>
                                <input type="file" id="new_images" name="new_images[]" multiple accept="image/*" style="display: none;">
                                
                                <div id="newImagesPreview" class="preview-images-container" style="display: none;"></div>
                                
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle"></i>
                                    Klik ikon tempat sampah untuk menandai gambar yang akan dihapus
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="peta_iframe" class="form-label">
                                <i class="fas fa-map"></i>
                                Embed Peta Google Maps
                            </label>
                            <input type="text" class="form-control mb-3" id="peta_iframe" name="peta_iframe" 
                                   value="<?= htmlspecialchars($wisata_data['peta_iframe']) ?>" 
                                   placeholder="Paste kode iframe Google Maps di sini...">
                            <div class="form-text mb-3">
                                <i class="fas fa-lightbulb"></i>
                                Contoh: &lt;iframe src="https://www.google.com/maps/embed?..."&gt;&lt;/iframe&gt;
                            </div>
                            
                            <div class="map-iframe-preview" id="map-preview" style="width: 100%; height: 300px; border: 2px solid #ccc; border-radius: 10px;">
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666;">
                                    <div style="text-align: center;">
                                        <i class="fas fa-map-marked-alt fa-3x mb-3"></i><br>
                                        Pratinjau peta akan muncul di sini
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="submit" class="submit-btn btn btn-primary">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const petaIframeInput = document.getElementById('peta_iframe');
        const mapPreviewDiv = document.getElementById('map-preview');
        const fileInput = document.getElementById('new_images');
        const newImagesPreview = document.getElementById('newImagesPreview');
        let selectedFiles = [];

        window.toggleDeleteImage = function (filename) {
            const checkbox = document.querySelector(`input[name="delete_images[]"][value="${filename}"]`);
            const imageItem = checkbox.closest('.image-item');
            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                imageItem.classList.add('marked-for-deletion');
                imageItem.style.opacity = '0.5';
                imageItem.style.transform = 'scale(0.95)';
            } else {
                imageItem.classList.remove('marked-for-deletion');
                imageItem.style.opacity = '1';
                imageItem.style.transform = 'scale(1)';
            }
            updateFileUploadAreaText();
        };

        function extractIframeSrc(iframeString) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(iframeString, 'text/html');
            const iframeElement = doc.querySelector('iframe');
            return iframeElement ? iframeElement.src : '';
        }

        function updateMapPreview() {
            const iframeCode = petaIframeInput.value.trim();
            if (iframeCode) {
                const src = extractIframeSrc(iframeCode);
                if (src) {
                    mapPreviewDiv.innerHTML = `<iframe src="${src}" style="width: 100%; height: 100%; border: none; border-radius: 8px;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
                } else {
                    mapPreviewDiv.innerHTML = `
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #dc3545;">
                            <div style="text-align: center;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 3rem;"></i>
                                <p style="margin-top: 10px;">Format iframe tidak valid</p>
                            </div>
                        </div>`;
                }
            } else {
                mapPreviewDiv.innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666;">
                        <div style="text-align: center;">
                            <i class="fas fa-map-marked-alt" style="font-size: 3rem;"></i>
                            <p style="margin-top: 10px;">Pratinjau peta akan muncul di sini</p>
                        </div>
                    </div>`;
            }
        }

        if (petaIframeInput) {
            petaIframeInput.addEventListener('input', updateMapPreview);
            updateMapPreview();
        }

        function updateFileUploadAreaText() {
            const currentImagesCount = document.querySelectorAll('.image-item:not(.marked-for-deletion)').length;
            const remainingSlots = 5 - currentImagesCount - selectedFiles.length;
            const fileUploadAreaText = document.querySelector('.file-upload-area p');
            const fileUploadArea = document.querySelector('.file-upload-area');

            if (remainingSlots > 0) {
                fileUploadAreaText.textContent = `Pilih hingga ${remainingSlots} gambar baru (JPG, PNG, GIF - Max 5MB)`;
                fileInput.disabled = false;
                fileUploadArea.style.cursor = 'pointer';
                fileUploadArea.style.opacity = '1';
            } else {
                fileUploadAreaText.textContent = `Maksimal 5 gambar sudah tercapai. Hapus beberapa gambar yang ada untuk menambahkan yang baru.`;
                fileInput.disabled = true;
                fileUploadArea.style.cursor = 'not-allowed';
                fileUploadArea.style.opacity = '0.7';
            }
        }

        if (fileInput) {
            fileInput.addEventListener('change', function (event) {
                const incomingFiles = Array.from(event.target.files);
                const currentOldImagesCount = document.querySelectorAll('.image-item:not(.marked-for-deletion)').length;
                const maxNewFilesAllowed = 5 - (currentOldImagesCount + selectedFiles.length);

                let filesToProcess = [];

                if (incomingFiles.length > maxNewFilesAllowed) {
                    alert(`Anda hanya dapat menambahkan ${maxNewFilesAllowed} gambar baru lagi. File yang berlebih tidak akan ditambahkan.`);
                    filesToProcess = incomingFiles.slice(0, maxNewFilesAllowed);
                } else {
                    filesToProcess = incomingFiles;
                }

                selectedFiles.push(...filesToProcess);
                event.target.value = '';

                updateFileInput();
                displayNewImagesPreview();
                updateFileUploadAreaText();
            });
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            fileInput.files = dataTransfer.files;
        }

        function displayNewImagesPreview() {
            newImagesPreview.innerHTML = '';

            if (selectedFiles.length > 0) {
                newImagesPreview.style.display = 'block';
                newImagesPreview.style.display = 'grid';
                newImagesPreview.style.gridTemplateColumns = 'repeat(auto-fill, minmax(150px, 1fr))';
                newImagesPreview.style.gap = '15px';
                newImagesPreview.style.marginTop = '15px';

                selectedFiles.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const previewItem = document.createElement('div');
                        previewItem.style.position = 'relative';
                        previewItem.style.borderRadius = '10px';
                        previewItem.style.overflow = 'hidden';
                        previewItem.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}" style="width: 100%; height: 100px; object-fit: cover;">
                            <button type="button" onclick="removeNewImage(${index})" title="Hapus gambar ini" 
                                    style="position: absolute; top: 5px; right: 5px; background: rgba(220, 53, 69, 0.9); border: none; border-radius: 50%; width: 25px; height: 25px; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                <i class="fas fa-times"></i>
                            </button>`;
                        newImagesPreview.appendChild(previewItem);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                newImagesPreview.style.display = 'none';
            }
        }

        window.removeNewImage = function (index) {
            selectedFiles.splice(index, 1);
            updateFileInput();
            displayNewImagesPreview();
            updateFileUploadAreaText();
        };

        updateFileUploadAreaText();
    });
</script>

</body>
</html>