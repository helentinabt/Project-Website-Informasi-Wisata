<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$message_type = '';

if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $peta_iframe = mysqli_real_escape_string($conn, $_POST['peta_iframe']);

    $gambar_names = array();
    $upload_success = true;
    $upload_errors = array();
    
    if (isset($_FILES['gambar']) && !empty($_FILES['gambar']['name'][0])) {
        $target_dir = "uploads/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $total_files = count($_FILES['gambar']['name']);
        
        if ($total_files > 5) {
            $message = "Maksimal 5 gambar yang dapat diunggah.";
            $message_type = 'error';
            $upload_success = false;
        } else {
            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['gambar']['error'][$i] == 0) {
                    $original_name = basename($_FILES["gambar"]["name"][$i]);
                    $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                    
                    $unique_name = time() . '_' . $i . '_' . uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $unique_name;
                    
                    $check = getimagesize($_FILES["gambar"]["tmp_name"][$i]);
                    if ($check !== false) {
                        if ($_FILES["gambar"]["size"][$i] > 5000000) {
                            $upload_errors[] = "File " . $original_name . " terlalu besar (maksimal 5MB).";
                            $upload_success = false;
                            continue;
                        }
                        
                        if (!in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $upload_errors[] = "File " . $original_name . " format tidak didukung.";
                            $upload_success = false;
                            continue;
                        }
                        if (move_uploaded_file($_FILES["gambar"]["tmp_name"][$i], $target_file)) {
                            $gambar_names[] = $unique_name;
                        } else {
                            $upload_errors[] = "Gagal mengunggah " . $original_name;
                            $upload_success = false;
                        }
                    } else {
                        $upload_errors[] = $original_name . " bukan file gambar.";
                        $upload_success = false;
                    }
                }
            }
        }
        
        if (!empty($upload_errors)) {
            $message = implode('<br>', $upload_errors);
            $message_type = 'error';
        }
    }

    if ($upload_success) {
        $gambar_string = implode(',', $gambar_names);
        
        $query = "INSERT INTO wisata (nama, deskripsi, gambar, peta_iframe) 
                  VALUES ('$nama', '$deskripsi', '$gambar_string', '$peta_iframe')";
        
        $result = mysqli_query($conn, $query);

        if ($result) {
            $message = "Wisata berhasil ditambahkan dengan " . count($gambar_names) . " gambar!";
            $message_type = 'success';
        } else {
            $message = "Error: " . mysqli_error($conn);
            $message_type = 'error';
        }
    }

    if (isset($result) && $result) {
        echo "<script>
          alert('Data berhasil disimpan!');
          window.location='admin wisata.php';
        </script>";
    } else {
        if (!empty($upload_errors)) {
            echo "<script>
              alert('Terjadi kesalahan saat upload: " . addslashes(implode(', ', $upload_errors)) . "');
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Tambah Wisata - Admin Desa Klero</title>
  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #1e8449;
      --primary-light: #27ae60;
      --primary-gradient: linear-gradient(135deg,  #27ae60 0%,  #1e8449 100%);
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
    }

    .sidebar {
      width: 280px;
      background: var(--primary-gradient);
      backdrop-filter: blur(10px);
      border-right: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: var(--shadow-lg);
      position: relative;
      overflow: hidden;
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
      display: flex;
      flex-direction: column;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
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
      margin-left: auto;
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
      backdrop-filter: blur(10px);
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

    .file-input-wrapper {
      position: relative;
      display: inline-block;
      cursor: pointer;
      width: 100%;
    }

    .file-input-custom {
      display: none;
    }

    .file-input-label {
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--secondary-color);
      border: 2px dashed var(--primary-color);
      border-radius: 12px;
      padding: 2rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      color: var(--primary-color);
      font-weight: 500;
    }

    .file-input-label:hover {
      background: rgba(40, 167, 69, 0.1);
      border-color: var(--primary-dark);
    }

    .file-input-label i {
      font-size: 2rem;
      margin-bottom: 0.5rem;
      display: block;
      width: 100%;
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

    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 1rem 1.5rem;
      border-radius: 12px;
      color: white;
      font-weight: 500;
      box-shadow: var(--shadow-lg);
      z-index: 1000;
      opacity: 0;
      transform: translateX(100%);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      min-width: 300px;
    }

    .notification.show {
      opacity: 1;
      transform: translateX(0);
    }

    .notification i {
      margin-right: 0.75rem;
      font-size: 1.2rem;
    }

    .notification.success {
      background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
    }

    .notification.error {
      background: linear-gradient(135deg, var(--error-color) 0%, #e74c3c 100%);
    }

    .notification.warning {
      background: linear-gradient(135deg, var(--warning-color) 0%, #f39c12 100%);
      color: var(--text-dark);
    }

    .notification .close-btn {
      margin-left: auto;
      background: none;
      border: none;
      color: inherit;
      cursor: pointer;
      font-size: 1.2rem;
      opacity: 0.7;
      transition: opacity 0.3s ease;
    }

    .notification .close-btn:hover {
      opacity: 1;
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

      .notification {
        right: 10px;
        left: 10px;
        min-width: auto;
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
      <div class="flex-grow-1">
        <div class="sidebar-header">
          <h4><i class="fas fa-leaf me-2"></i>Desa Klero</h4>
          <div class="subtitle">Management System</div>
        </div>
        <ul class="menu">
          <li>
            <a href="admin wisata.php" class="active">
              <i class="fas fa-map-marked-alt"></i>
              Kelola Wisata
            </a>
          </li>
          <li>
            <a href="admin umkm.php">
              <i class="fas fa-store"></i>
              Kelola UMKM
            </a>
          </li>
        </ul>
      </div>
      <div class="logout-section">
        <a href="logout.php" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i>
          Log Out
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
            <i class="fas fa-plus-circle"></i>Tambah Wisata Baru
          </h1>
          
          <form method="POST" enctype="multipart/form-data" id="wisataForm">
            <div class="form-group">
              <label for="nama" class="form-label">
                <i class="fas fa-map-marker-alt"></i>
                Nama Wisata
              </label>
              <input type="text" id="nama" name="nama" class="form-control" 
                     placeholder="Masukkan nama destinasi wisata" required />
            </div>

            <div class="form-group">
              <label for="deskripsi" class="form-label">
                <i class="fas fa-align-left"></i>
                Deskripsi Wisata
              </label>
              <textarea id="deskripsi" name="deskripsi" class="form-control" 
                        placeholder="Deskripsikan keindahan dan keunikan destinasi wisata..." required></textarea>
              <div class="form-text">
                <i class="fas fa-info-circle"></i>
                Berikan deskripsi yang menarik dan informatif tentang destinasi wisata
              </div>
            </div>
            <div class="form-group">
              <label for="gambar" class="form-label">
                <i class="fas fa-image"></i>
                Gambar Wisata
              </label>
              <div class="file-input-wrapper">
                <input type="file" id="gambar" name="gambar[]" accept="image/*" class="file-input-custom" multiple hidden />
                <label for="gambar" id="fileLabel" class="file-input-label">
                  <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                  <div>
                    <strong>Klik untuk upload gambar</strong><br>
                    <span>atau drag & drop file di sini</span>
                  </div>
                </label>
              </div>

              <div class="form-text">
                <i class="fas fa-info-circle"></i>
                Format yang didukung: JPG, JPEG, PNG, GIF (maksimal 5MB, maksimal 5 file)
              </div>

              <div id="previewContainer" class="mt-3 d-flex flex-wrap"></div>

              <small id="fileCountInfo" class="text-muted">
                <i class="fas fa-file-image"></i>
                Gambar dipilih: <span id="currentCount">0</span> / 5
              </small>
            </div>
            
            <div class="form-group">
              <label for="peta_iframe" class="form-label">
                <i class="fas fa-map"></i>
                Embed Peta Google Maps
              </label>
              <input type="text" id="peta_iframe" name="peta_iframe" class="form-control" 
                     placeholder="Paste kode iframe Google Maps di sini..." />
              <div class="form-text">
                <i class="fas fa-lightbulb"></i>
                Contoh: &lt;iframe src="https://www.google.com/maps/embed?..."&gt;&lt;/iframe&gt;
              </div>
              <div class="map-iframe-preview" id="map-preview">
                <div>
                  <i class="fas fa-map-marked-alt fa-3x mb-3"></i><br>
                  Pratinjau peta akan muncul di sini
                </div>
              </div>
            </div>

            <button type="submit" name="submit" class="submit-btn">
              <i class="fas fa-save"></i>
              Simpan Wisata
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php if (!empty($message)): ?>
  <div class="notification <?= $message_type ?>" id="notification">
    <i class="fas <?= $message_type === 'success' ? 'fa-check-circle' : ($message_type === 'error' ? 'fa-exclamation-circle' : 'fa-exclamation-triangle') ?>"></i>
    <span><?= htmlspecialchars($message) ?></span>
    <button type="button" class="close-btn" onclick="closeNotification()">
      <i class="fas fa-times"></i>
    </button>
  </div>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<script>
let selectedFiles = [];
const maxFiles = 5;
const maxFileSize = 5 * 1024 * 1024;
const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('gambar');
    const fileLabel = document.getElementById('fileLabel') || document.querySelector('.file-input-label');
    const previewContainer = document.getElementById('previewContainer');
    const fileCountInfo = document.getElementById('fileCountInfo');
    const currentCountSpan = document.getElementById('currentCount');
    const petaIframeInput = document.getElementById('peta_iframe');
    const mapPreviewDiv = document.getElementById('map-preview');


    fileInput.addEventListener('change', function(e) {
        handleFileSelection(e.target.files);
    });

    fileLabel.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--primary-dark)';
        this.style.background = 'rgba(40, 167, 69, 0.2)';
    });

    fileLabel.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--primary-color)';
        this.style.background = 'var(--secondary-color)';
    });

    fileLabel.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--primary-color)';
        this.style.background = 'var(--secondary-color)';
        handleFileSelection(e.dataTransfer.files);
    });

    function handleFileSelection(files) {
        const newFiles = Array.from(files);

        if (selectedFiles.length + newFiles.length > maxFiles) {
            showNotification(`Maksimal ${maxFiles} gambar yang dapat dipilih!`, 'error');
            return;
        }

        for (let file of newFiles) {
            if (validateFile(file)) {
                selectedFiles.push(file);
            }
        }

        updateFileInput();
        updatePreview();
        updateFileCount();
    }

    function validateFile(file) {
        if (!allowedTypes.includes(file.type)) {
            showNotification(`Format file ${file.name} tidak didukung!`, 'error');
            return false;
        }
        if (file.size > maxFileSize) {
            showNotification(`File ${file.name} terlalu besar (maksimal 5MB)!`, 'error');
            return false;
        }
        return true;
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function updatePreview() {
        previewContainer.innerHTML = '';
        selectedFiles.forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '80px';
                img.style.margin = '5px';
                img.style.objectFit = 'cover';
                img.style.border = '1px solid #ccc';
                img.style.borderRadius = '8px';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }

    function updateFileCount() {
        if (currentCountSpan) currentCountSpan.textContent = selectedFiles.length;
    }

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
                mapPreviewDiv.innerHTML = `<iframe src="${src}" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
            } else {
                mapPreviewDiv.innerHTML = `<div style="color: #dc3545;"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><br>Format iframe tidak valid</div>`;
            }
        } else {
            mapPreviewDiv.innerHTML = `<div><i class="fas fa-map-marked-alt fa-3x mb-3"></i><br>Pratinjau peta akan muncul di sini</div>`;
        }
    }

    petaIframeInput.addEventListener('input', updateMapPreview);
    updateMapPreview();

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileLabel.innerHTML = `
                <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
                <div><strong>${this.files.length} file terpilih</strong></div>
            `;
            fileLabel.style.borderColor = 'var(--success-color)';
            fileLabel.style.background = 'rgba(40, 167, 69, 0.1)';
        }
    });

    const notification = document.getElementById('notification');
    if (notification) {
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        setTimeout(() => {
            closeNotification();
        }, 5000);

        <?php if (!empty($message_type) && $message_type === 'success'): ?>
        setTimeout(() => {
            window.location.href = 'admin w.php';
        }, 2000);
        <?php endif; ?>
    }
});

function closeNotification() {
    const notification = document.getElementById('notification');
    if (notification) {
        notification.classList.remove('show');
        notification.style.display = 'none';
    }
}

function showNotification(message, type = 'info') {
    let container = document.getElementById('notification');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification';
        document.body.appendChild(container);
    }
    container.className = `notification ${type}`;
    container.innerHTML = `<span>${message}</span><button onclick="closeNotification()">×</button>`;
    container.classList.add('show');

    setTimeout(() => {
        closeNotification();
    }, 5000);
}
</script>
