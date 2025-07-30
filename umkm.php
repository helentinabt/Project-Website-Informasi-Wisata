<?php
include_once 'db.php';

if (!$conn) {
    die("Koneksi database gagal di umkm.php: " . mysqli_connect_error());
}

$query_umkm = "SELECT id_umkm, nama, deskripsi, gambar FROM umkm ORDER BY nama ASC";
$result_umkm = mysqli_query($conn, $query_umkm);

if (!$result_umkm) {
    die("Error mengambil data UMKM: " . mysqli_error($conn) . "<br>Query: " . $query_umkm);
}

$umkm_data = [];
while ($row = mysqli_fetch_assoc($result_umkm)) {
    $umkm_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Daftar UMKM Desa Klero</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-green: #27ae60;
      --secondary-green: #2ecc71;
      --dark-green: #1e8449;
      --light-green: #a9dfbf;
      --accent-green: #58d68d;
      --text-dark: #2c3e50;
      --text-light: #7f8c8d;
      --white: #ffffff;
      --light-bg: #f8f9fa;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      line-height: 1.6;
      color: var(--text-dark);
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
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

         .logo-desa {
            height: 40px;
            width: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    .hero-section {
      position: relative;
      height: 50vh;
      min-height: 400px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hero-bg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        135deg,
        rgba(39, 174, 96, 0.8) 0%,
        rgba(46, 204, 113, 0.8) 100%
      ), url('umkm.jpg');
      background-size: cover;
      background-position: center;
      filter: brightness(0.9);
    }

    .hero-content {
      position: relative;
      z-index: 2;
      text-align: center;
      color: var(--white);
    }

    .hero-title {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
      text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
      background: linear-gradient(45deg, #fff, #e8f5e8);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-subtitle {
      font-size: 1.3rem;
      font-weight: 300;
      opacity: 0.95;
      text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.3);
    }

    .umkm-section {
      padding: 5rem 0;
      background: var(--white);
      position: relative;
    }

    .umkm-section::before {
      content: '';
      position: absolute;
      top: -50px;
      left: 0;
      width: 100%;
      height: 100px;
      background: linear-gradient(135deg, transparent 0%, var(--white) 100%);
      transform: skewY(-2deg);
    }

    .section-title {
      text-align: center;
      margin-bottom: 4rem;
      position: relative;
    }

    .section-title h2 {
      font-size: 2.8rem;
      font-weight: 700;
      color: var(--primary-green);
      margin-bottom: 1rem;
      position: relative;
      display: inline-block;
    }

    .section-title h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-green), var(--secondary-green));
      border-radius: 2px;
    }

    .section-title p {
      font-size: 1.1rem;
      color: var(--text-light);
      max-width: 600px;
      margin: 0 auto;
    }

    .carousel-control-prev,
    .carousel-control-next {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
      border-radius: 50%;
      top: 50%;
      transform: translateY(-50%);
      opacity: 0.8;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
    }

    .carousel-control-prev {
      left: -30px;
    }

    .carousel-control-next {
      right: -30px;
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
      opacity: 1;
      transform: translateY(-50%) scale(1.1);
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
      width: 24px;
      height: 24px;
    }

    .umkm-card {
      background: var(--white);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border: none;
      height: 100%;
      position: relative;
    }

    .umkm-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-green), var(--secondary-green));
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .umkm-card:hover {
      transform: translateY(-15px) scale(1.02);
      box-shadow: 0 20px 60px rgba(39, 174, 96, 0.2);
    }

    .umkm-card:hover::before {
      opacity: 1;
    }

    .card-img-container {
      position: relative;
      overflow: hidden;
      height: 280px;
    }

    .umkm-card img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .umkm-card:hover img {
      transform: scale(1.1);
    }

    .card-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(
        135deg,
        rgba(39, 174, 96, 0.8) 0%,
        rgba(46, 204, 113, 0.8) 100%
      );
      opacity: 0;
      transition: opacity 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .umkm-card:hover .card-overlay {
      opacity: 1;
    }

    .overlay-icon {
      color: var(--white);
      font-size: 3rem;
      transform: translateY(20px);
      transition: transform 0.3s ease;
    }

    .umkm-card:hover .overlay-icon {
      transform: translateY(0);
    }

    .card-body {
      padding: 2rem;
      background: var(--white);
      position: relative;
    }

    .card-title {
      font-size: 1.4rem;
      font-weight: 600;
      color: var(--primary-green);
      margin-bottom: 1rem;
      line-height: 1.3;
    }

    .card-description {
      color: var(--text-light);
      font-size: 0.95rem;
      line-height: 1.6;
      margin-bottom: 1.5rem;
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: 100px;
    }

    .card-badge {
      position: absolute;
      top: 1rem;
      right: 1rem;
      background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
      color: var(--white);
      padding: 0.3rem 0.8rem;
      border-radius: 15px;
      font-size: 0.8rem;
      font-weight: 500;
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

.kontak li a {
  color: white !important;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.kontak li a:hover {
  color: white !important;
}

.kontak li .fa-phone {
  color: #25D366 !important;
  font-size: 1rem;
  margin-right: 8px;
}

.kontak li .fa-instagram {
  background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-size: 1rem;
  margin-right: 8px;
}

.kontak li .fa-youtube {
  color: #FF0000 !important;
  font-size: 1rem;
  margin-right: 8px;
}

.copyright {
  margin-top: 20px;
  padding-top: 10px;
  border-top: 1px solid rgba(255,255,255,0.1);
  color: #d1fae5;
  font-size: 0.85rem;
}

    @media (max-width: 992px) {
      .hero-title {
        font-size: 2.8rem;
      }
      
      .carousel-control-prev,
      .carousel-control-next {
        display: none;
      }
    }

    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.2rem;
      }
      
      .hero-subtitle {
        font-size: 1.1rem;
      }
      
      .section-title h2 {
        font-size: 2.2rem;
      }
      
      .contact-list {
        flex-direction: column;
        gap: 1rem;
      }
      
      .umkm-section {
        padding: 3rem 0;
      }
    }

    @media (max-width: 576px) {
      .hero-title {
        font-size: 1.8rem;
      }
      
      .card-body {
        padding: 1.5rem;
      }
      
      .card-img-container {
        height: 220px;
      }
    }

    .img-loading {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      animation: loading 1.5s infinite;
    }

    @keyframes loading {
      0% {
        background-position: 200% 0;
      }
      100% {
        background-position: -200% 0;
      }
    }

    .carousel-indicators {
      bottom: -50px;
    }

    .carousel-indicators [data-bs-target] {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background-color: var(--primary-green);
      opacity: 0.5;
      transition: all 0.3s ease;
    }

    .carousel-indicators .active {
      opacity: 1;
      transform: scale(1.2);
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

  <section class="hero-section">
    <div class="hero-bg"></div>
    <div class="hero-content">
      <h1 class="hero-title">UMKM Desa Klero</h1>
      <p class="hero-subtitle">Produk Unggulan dari Tangan Kreatif Masyarakat Desa</p>
    </div>
  </section>

  <section class="umkm-section">
    <div class="container">
      <div class="section-title">
        <h2>Produk UMKM Terbaik</h2>
        <p>Temukan berbagai produk berkualitas dari usaha mikro, kecil, dan menengah yang dikembangkan oleh masyarakat Desa Klero dengan penuh dedikasi dan keahlian.</p>
      </div>

      <?php if (!empty($umkm_data)): ?>
        <div id="umkmCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php
            $chunks = array_chunk($umkm_data, 3);
            foreach ($chunks as $index => $chunk):
            ?>
              <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <div class="row g-4">
                  <?php foreach ($chunk as $umkm): ?>
                    <div class="col-lg-4 col-md-6">
                      <div class="umkm-card">
                        <div class="card-badge">
                          <i class="fas fa-star me-1"></i>Terpilih
                        </div>
                        <div class="card-img-container">
                          <?php
                          $gambar_path = !empty($umkm['gambar']) ? 'uploads/' . htmlspecialchars($umkm['gambar']) : 'https://placehold.co/400x280/27ae60/ffffff?text=UMKM+Desa+Klero';
                          ?>
                          <img src="<?= $gambar_path ?>" 
                               alt="<?= htmlspecialchars($umkm['nama']) ?>" 
                               class="img-loading"
                               onerror="this.onerror=null;this.src='https://placehold.co/400x280/27ae60/ffffff?text=UMKM+Desa+Klero';"
                               onload="this.classList.remove('img-loading')" />
                          <div class="card-overlay">
                            <i class="fas fa-eye overlay-icon"></i>
                          </div>
                        </div>
                        <div class="card-body">
                          <h5 class="card-title"><?= htmlspecialchars($umkm['nama']) ?></h5>
                          <p class="card-description"><?= nl2br(htmlspecialchars($umkm['deskripsi'])) ?></p>
                          <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                              <i class="fas fa-map-marker-alt me-1"></i>Desa Klero
                            </small>
                            <div class="btn-group" role="group">
                              <button type="button" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-heart"></i>
                              </button>
                              <button type="button" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-share"></i>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <?php if (count($chunks) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#umkmCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#umkmCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>

            <div class="carousel-indicators">
              <?php for ($i = 0; $i < count($chunks); $i++): ?>
                <button type="button" data-bs-target="#umkmCarousel" data-bs-slide-to="<?= $i ?>" 
                        <?= $i === 0 ? 'class="active"' : '' ?>></button>
              <?php endfor; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="text-center py-5">
          <div class="mb-4">
            <i class="fas fa-store" style="font-size: 4rem; color: var(--light-green);"></i>
          </div>
          <h4 class="text-muted">Belum Ada Data UMKM</h4>
          <p class="text-muted">Data UMKM akan segera hadir untuk memamerkan produk-produk unggulan dari Desa Klero.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

    <footer>
    <div class="container">
      <div class="footer-content">
        <h3 class="footer-title">Hubungi Kami</h3>
        <ul class="kontak">
          <li>
            <i class="fas fa-phone"></i>
            <a href="https://wa.me/085712998901" target="_blank" style="text-decoration: none;">
              <span>0857-1299-8901</span>
            </a>
          </li>
          <li>
            <i class="fab fa-instagram"></i>
            <a href="https://instagram.com/klero_punya_cerita" target="_blank" style="text-decoration: none;">
              <span>@klero_punya_cerita</span>
            </a>
          </li>
          <li>
            <i class="fab fa-youtube"></i>
            <a href="https://www.youtube.com/results?search_query=Desa+Klero+Official" target="_blank" style="text-decoration: none;">
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    const carousel = new bootstrap.Carousel(document.getElementById('umkmCarousel'), {
      interval: 5000,
      ride: 'carousel'
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });

    document.querySelectorAll('img').forEach(img => {
      img.addEventListener('load', function() {
        this.classList.remove('img-loading');
      });
    });
  </script>

</body>
</html>