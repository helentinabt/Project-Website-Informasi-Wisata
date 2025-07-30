<?php
include_once 'db.php';

if (!$conn) {
    die("Koneksi database gagal di home.php: " . mysqli_connect_error());
}

$query_wisata = "SELECT id_wisata, nama, gambar FROM wisata ORDER BY nama ASC";
$result_wisata = mysqli_query($conn, $query_wisata);

if (!$result_wisata) {
    die("Error mengambil data wisata: " . mysqli_error($conn) . "<br>Query: " . $query_wisata);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Beranda Desa Klero</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      line-height: 1.6;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      color: #2c3e50;
      overflow-x: hidden;
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

    .hero {
      position: relative;
      height: 500px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, rgba(39, 174, 96, 0.7), rgba(46, 204, 113, 0.5));
      z-index: 2;
    }

    .foto-desa-img {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      filter: brightness(0.8) contrast(1.1);
      z-index: 1;
      transition: transform 20s ease-in-out;
    }

    .foto-desa-img:hover {
      transform: scale(1.05);
    }

    .hero-content {
      position: relative;
      z-index: 3;
      text-align: center;
      color: white;
      animation: fadeInUp 1s ease-out;
    }

    .hero h1 {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
      background: linear-gradient(45deg, #fff, #f8f9fa);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-subtitle {
      font-size: 1.2rem;
      font-weight: 300;
      opacity: 0.9;
      animation: fadeInUp 1s ease-out 0.3s both;
    }

    .gallery-section {
      padding: 80px 0;
      background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
      position: relative;
    }

    .gallery-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 100px;
      background: linear-gradient(180deg, rgba(245,247,250,1) 0%, rgba(255,255,255,0) 100%);
    }

    .section-title {
      text-align: center;
      margin-bottom: 60px;
      animation: fadeInUp 0.8s ease-out;
    }

    .section-title h2 {
      font-size: 2.5rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 15px;
      position: relative;
    }

    .section-title h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, #27ae60, #2ecc71);
      border-radius: 2px;
    }

    .section-title p {
      color: #7f8c8d;
      font-size: 1.1rem;
      max-width: 600px;
      margin: 0 auto;
    }

    .galeri {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 30px;
      padding: 0 20px;
    }

    .foto-wisata {
      width: 350px;
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      overflow: hidden;
      text-decoration: none;
      color: inherit;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      animation: fadeInUp 0.8s ease-out;
    }

    .foto-wisata:hover {
      transform: translateY(-15px) scale(1.02);
      box-shadow: 0 20px 50px rgba(39, 174, 96, 0.2);
      text-decoration: none;
      color: inherit;
    }

    .foto-wisata::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, rgba(39, 174, 96, 0.1), rgba(46, 204, 113, 0.1));
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: 1;
    }

    .foto-wisata:hover::before {
      opacity: 1;
    }

    .wisata-image-container {
      position: relative;
      overflow: hidden;
      height: 250px;
    }

    .foto-wisata img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .foto-wisata:hover img {
      transform: scale(1.1);
    }

    .image-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, rgba(39, 174, 96, 0.8), rgba(46, 204, 113, 0.6));
      opacity: 0;
      transition: opacity 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2;
    }

    .foto-wisata:hover .image-overlay {
      opacity: 1;
    }

    .overlay-icon {
      color: white;
      font-size: 2rem;
      transform: scale(0);
      transition: transform 0.3s ease 0.1s;
    }

    .foto-wisata:hover .overlay-icon {
      transform: scale(1);
    }

    .foto-wisata .caption {
      padding: 25px;
      text-align: center;
      position: relative;
      z-index: 2;
    }

    .caption h3 {
      font-size: 1.4rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 8px;
      transition: color 0.3s ease;
    }

    .foto-wisata:hover .caption h3 {
      color: #27ae60;
    }

    .caption p {
      color: #7f8c8d;
      font-size: 0.9rem;
      line-height: 1.5;
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

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.5rem;
      }
      
      .hero-subtitle {
        font-size: 1rem;
      }

      .foto-wisata {
        width: 90%;
      }

      .kontak {
        flex-direction: column;
        align-items: center;
        gap: 15px;
      }

      .section-title h2 {
        font-size: 2rem;
      }
    }

    @media (max-width: 480px) {
      .hero h1 {
        font-size: 2rem;
      }
      
      .gallery-section {
        padding: 50px 0;
      }
      
      .section-title h2 {
        font-size: 1.8rem;
      }
    }

    .foto-wisata {
      animation-fill-mode: both;
    }

    .foto-wisata:nth-child(1) { animation-delay: 0.1s; }
    .foto-wisata:nth-child(2) { animation-delay: 0.2s; }
    .foto-wisata:nth-child(3) { animation-delay: 0.3s; }
    .foto-wisata:nth-child(4) { animation-delay: 0.4s; }
    .foto-wisata:nth-child(5) { animation-delay: 0.5s; }
    .foto-wisata:nth-child(6) { animation-delay: 0.6s; }

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

  <section class="hero">
    <img src="kantor2.jpg" alt="Foto Desa Klero" class="foto-desa-img" />
    <div class="hero-content">
      <h1>Selamat Datang di Desa Klero</h1>
      <p class="hero-subtitle">Jelajahi keindahan wisata dan kekayaan budaya desa kami</p>
    </div>
  </section>

  <section class="gallery-section">
    <div class="container">
      <div class="section-title">
        <h2>Destinasi Wisata</h2>
        <p>Temukan berbagai tempat menarik dan eksotis yang ada di Desa Klero</p>
      </div>
      
      <div class="galeri">
        <?php
        if (mysqli_num_rows($result_wisata) > 0) {
            while ($row = mysqli_fetch_assoc($result_wisata)) {
                  $gambar_path = 'https://placehold.co/350x250/27ae60/ffffff?text=Gambar+Tidak+Tersedia';
                  if (!empty($row['gambar'])) {
                      $gambar_array = explode(',', $row['gambar']);
                      $gambar_pertama = trim($gambar_array[0]);
                      $gambar_path = 'uploads/' . htmlspecialchars($gambar_pertama);
                  }
                ?>
                <a href="deskripsi wisata.php?id=<?= htmlspecialchars($row['id_wisata']) ?>" class="foto-wisata">
                    <div class="wisata-image-container">
                        <img src="<?= $gambar_path ?>" alt="<?= htmlspecialchars($row['nama']) ?>" onerror="this.onerror=null;this.src='https://placehold.co/350x250/27ae60/ffffff?text=Gambar+Tidak+Tersedia';" />
                        <div class="image-overlay">
                            <i class="fas fa-eye overlay-icon"></i>
                        </div>
                    </div>
                    <div class="caption">
                        <h3><?= htmlspecialchars($row['nama']) ?></h3>
                        <p>Klik untuk melihat detail lengkap</p>
                    </div>
                </a>
                <?php
            }
        } else {
            echo "<div class='col-12 text-center'><p class='lead text-muted'>Belum ada data wisata yang tersedia.</p></div>";
        }
        ?>
      </div>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    window.addEventListener('load', function() {
      document.body.style.opacity = '0';
      document.body.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        document.body.style.transition = 'all 0.6s ease';
        document.body.style.opacity = '1';
        document.body.style.transform = 'translateY(0)';
      }, 100);
    });

    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.animationPlayState = 'running';
        }
      });
    }, observerOptions);

    document.querySelectorAll('.foto-wisata').forEach(item => {
      item.style.animationPlayState = 'paused';
      observer.observe(item);
    });
  </script>

</body>
</html>