<?php
include_once 'db.php';

$wisata_data = null;
$error_message = '';

if (isset($_GET['id'])) {
    $id_wisata = mysqli_real_escape_string($conn, $_GET['id']);

    $query = "SELECT * FROM wisata WHERE id_wisata = '$id_wisata'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $wisata_data = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Data wisata tidak ditemukan atau ID tidak valid.";
    }
} else {
    $error_message = "ID wisata tidak disediakan.";
}

function sanitizeIframe($iframe_code) {
    if (empty($iframe_code)) {
        return '';
    }
    $iframe_code = strip_tags($iframe_code, '<iframe>');

    if (strpos($iframe_code, 'google.com') !== false && strpos($iframe_code, 'src=') !== false) {
        return $iframe_code;
    }
    
    return '';
}

function getImageArray($gambar_field) {
    if (empty($gambar_field)) {
        return [];
    }

    $images = json_decode($gambar_field, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($images)) {
        return $images;
    }

    $separators = [',', ';', '|'];
    foreach ($separators as $sep) {
        if (strpos($gambar_field, $sep) !== false) {
            return array_map('trim', explode($sep, $gambar_field));
        }
    }

    return [$gambar_field];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title><?= $wisata_data ? htmlspecialchars($wisata_data['nama']) : 'Detail Wisata' ?> - Desa Klero</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2e7d32;
            --secondary-color: #4caf50;
            --accent-color: #81c784;
            --light-green: #f1f8e9;
            --dark-green: #1b5e20;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
            min-height: 100vh;
        }

        .custom-navbar {
             background: linear-gradient(135deg, #14532d 0%, #166534 100%);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .custom-navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }

        .custom-navbar .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 10px;
            padding: 8px 16px !important;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .custom-navbar .nav-link:hover {
            background-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        .custom-navbar .btn-outline-light {
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .custom-navbar .btn-outline-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,255,255,0.3);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 4rem 0 2rem;
            margin-bottom: 2rem;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .gallery-container {
            margin-bottom: 3rem;
        }

       .main-image {
        width: 100%;
        max-width: 800px;
        height: 500px;
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        transition: transform 0.3s ease;
        cursor: pointer;
        }


        .main-image:hover {
            transform: scale(1.02);
        }

        .thumbnail-gallery {
            margin-top: 1rem;
        }

        .thumbnail {
        width: 100px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.3s ease;
        border: 3px solid transparent;
        }


        .thumbnail:hover,
        .thumbnail.active {
            opacity: 1;
            border-color: var(--secondary-color);
            transform: scale(1.05);
        }

        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            border: none;
            transition: transform 0.3s ease;
        }

        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 35px rgba(0,0,0,0.15);
        }

        .content-card h3 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .content-card h3 i {
            color: var(--secondary-color);
        }

        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .map-container iframe {
            width: 100%;
            height: 400px;
            border: none;
        }

        .map-placeholder {
            height: 400px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .map-placeholder i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .review-btn {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 25px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .review-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
            color: white;
        }

        footer {
            background: linear-gradient(135deg, #14532d 0%, #166534 100%);
            color: white;
            padding: 30px 0 15px;
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #d1fae5, #a7f3d0, #d1fae5);
        }

        .footer-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .footer-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #ecfdf5;
        }

        .kontak {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .kontak li {
            background: rgba(255,255,255,0.07);
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            backdrop-filter: blur(6px);
        }

        .kontak li:hover {
            background: rgba(22, 101, 52, 0.4);
            transform: translateY(-3px);
        }

        .kontak i {
            margin-right: 8px;
            color: #a7f3d0;
            font-size: 1rem;
        }

        .kontak .fa-phone {
            color: #25D366;
        }
        
        .kontak .fa-instagram {
            background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .kontak .fa-youtube {
            color: #FF0000;
        }

        .copyright {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #FFFFFF;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .thumbnail {
                width: 80px;
                height: 60px;
            }
            
            .content-card {
                padding: 1.5rem;
            }
        }

        .loading {
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }

        .lightbox {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
        }

        .lightbox-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
        }

        .lightbox img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            z-index: 10000;
        }
        .logo-desa {
            height: 40px;
            width: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="logo-klero.png" alt="Logo Desa" class="logo-desa">
                Desa Klero
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">
                            <i class="fas fa-map-marked-alt"></i> Wisata
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="umkm.php">
                            <i class="fas fa-store"></i> UMKM
                        </a>
                    </li>
                </ul>
                <a href="login.php" class="btn btn-outline-light">
                    <i class="fas fa-user-shield"></i> Login Admin
                </a>
            </div>
        </div>
    </nav>

    <?php if ($wisata_data): ?>
        <div class="hero-section">
            <div class="container text-center">
                <h1 class="hero-title" data-aos="fade-up"><?= htmlspecialchars($wisata_data['nama']) ?></h1>
                <p class="lead" data-aos="fade-up" data-aos-delay="200">Jelajahi keindahan destinasi wisata Desa Klero</p>
            </div>
        </div>

        <div class="container">
            <div class="gallery-container" data-aos="fade-up">
                <?php 
                $images = getImageArray($wisata_data['gambar']);
                if (!empty($images)): 
                ?>
                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <div class="text-center mb-3">
                                <img id="mainImage" 
                                     src="uploads/<?= htmlspecialchars($images[0]) ?>" 
                                     alt="<?= htmlspecialchars($wisata_data['nama']) ?>" 
                                     class="img-fluid main-image"
                                     onerror="this.onerror=null;this.src='https://placehold.co/800x600/cccccc/333333?text=Gambar+Tidak+Tersedia';">
                            </div>
                            
                            <?php if (count($images) > 1): ?>
                                <div class="thumbnail-gallery text-center">
                                    <div class="d-flex justify-content-center flex-wrap gap-2">
                                        <?php foreach ($images as $index => $image): ?>
                                            <img src="uploads/<?= htmlspecialchars($image) ?>" 
                                                 alt="Thumbnail <?= $index + 1 ?>" 
                                                 class="thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                                 onclick="changeMainImage('uploads/<?= htmlspecialchars($image) ?>', this)"
                                                 onerror="this.style.display='none';">
                                        <?php endforeach; ?>
                                    </div>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fas fa-images"></i> <?= count($images) ?> foto tersedia
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-lg-8 mx-auto text-center">
                            <img src="https://placehold.co/800x600/cccccc/333333?text=Gambar+Tidak+Tersedia" 
                                 alt="Gambar tidak tersedia" 
                                 class="img-fluid main-image">
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="content-card" data-aos="fade-right">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Deskripsi
                        </h3>
                        <p class="lead"><?= nl2br(htmlspecialchars($wisata_data['deskripsi'])) ?></p>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="content-card" data-aos="fade-left">
                        <h3>
                            <i class="fas fa-map-marker-alt"></i>
                            Lokasi
                        </h3>
                        <div class="map-container">
                            <?php 
                            $iframe_code = sanitizeIframe($wisata_data['peta_iframe']);
                            if (!empty($iframe_code)): ?>
                                <?= $iframe_code ?>
                            <?php else: ?>
                                <div class="map-placeholder">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <h5>Lokasi peta tidak tersedia</h5>
                                    <p>Silakan hubungi admin untuk informasi lokasi</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mb-5" data-aos="fade-up">
                <a href="https://wa.me/085712998901?text=Halo%20saya%20ingin%20bertanya%20mengenai%20ulasan%20wisata%20<?= urlencode($wisata_data['nama']) ?>." 
                   target="_blank" 
                   class="review-btn">
                    <i class="fab fa-whatsapp"></i>
                    Beri Ulasan
                </a>
            </div>
        </div>

    <?php else: ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert alert-danger text-center" role="alert">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h4>Data Tidak Ditemukan</h4>
                        <p><?= $error_message ?></p>
                        <a href="home.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <footer>
        <div class="container">
            <div class="footer-content">
                <h3 class="footer-title">Hubungi Kami</h3>
                <ul class="kontak">
                    <li>
                        <i class="fas fa-phone"></i>
                        <a href="https://wa.me/085712998901" target="_blank" style="text-decoration: none; color: inherit;">
                            <span>0857-1299-8901</span>
                        </a>
                    </li>
                    <li>
                        <i class="fab fa-instagram"></i>
                        <a href="https://instagram.com/klero_punya_cerita" target="_blank" style="text-decoration: none; color: inherit;">
                            <span>@klero_punya_cerita</span>
                        </a>
                    </li>
                    <li>
                        <i class="fab fa-youtube"></i>
                        <a href="https://www.youtube.com/channel/UC-your-channel-id" target="_blank" style="text-decoration: none; color: inherit;">
                            <span>Desa Klero Official</span>
                        </a>
                    </li>
                </ul>
                <div class="copyright">
                    <p>&copy; 2025 Tim Proyek Website - FTI UKSW</p>
                    <p>Helentina Beta T | Brilliancy Elshaday RSM | Gesang Lukito</p>
                </div>
            </div>
        </div>
    </footer>

    <div id="lightbox" class="lightbox" onclick="closeLightbox()">
        <span class="lightbox-close">×</span>
        <div class="lightbox-content">
            <img id="lightboxImage" src="" alt="">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        function changeMainImage(src, thumbnailElement) {
            const mainImage = document.getElementById('mainImage');
            const currentActive = document.querySelector('.thumbnail.active');
            
            mainImage.classList.add('loading');
            
            if (currentActive) {
                currentActive.classList.remove('active');
            }
            
            thumbnailElement.classList.add('active');
            
            setTimeout(() => {
                mainImage.src = src;
                mainImage.classList.remove('loading');
            }, 200);
        }

        function openLightbox(src) {
            const lightbox = document.getElementById('lightbox');
            const lightboxImage = document.getElementById('lightboxImage');
            lightboxImage.src = src;
            lightbox.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const mainImage = document.getElementById('mainImage');
            if (mainImage) {
                mainImage.addEventListener('click', function() {
                    openLightbox(this.src);
                });
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>