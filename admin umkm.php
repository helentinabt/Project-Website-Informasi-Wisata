<?php
include_once 'db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$query_umkm = "SELECT id_umkm, nama, deskripsi, gambar FROM umkm ORDER BY nama ASC";
$result_umkm = mysqli_query($conn, $query_umkm);

if (!$result_umkm) {
    die("Error mengambil data UMKM: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Dashboard Admin - Kelola UMKM</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #1e8449;
      --primary-light: #27ae60;
      --primary-gradient: linear-gradient(135deg,  #27ae60 0%,  #1e8449 100%);
      --secondary-color: #e0f2fe;
      --accent-color: #1d4ed8;
      --success-color: #10b981;
      --danger-color: #ef4444;
      --text-dark: #1f2937;
      --text-muted: #6b7280;
      --border-color: #e5e7eb;
      --background-light: #f8fafc;
      --white: #ffffff;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--background-light);
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
    }

    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: var(--white);
    }

    .topbar {
      background: var(--primary-gradient);
      color: white;
      display: flex;
      justify-content: flex-end;
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
    }

    .profile i {
      margin-right: 0.5rem;
      font-size: 1.2rem;
    }

    .content {
      padding: 2rem;
      flex-grow: 1;
      overflow-y: auto;
      background: var(--background-light);
    }

    .page-title {
      margin-bottom: 2rem;
      color: var(--text-dark);
      font-weight: 700;
      font-size: 2rem;
      position: relative;
      padding-bottom: 1rem;
    }

    .page-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 4px;
      background: var(--primary-gradient);
      border-radius: 2px;
    }

    .actions-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding: 1.5rem;
      background: var(--white);
      border-radius: 15px;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
    }

    .add-btn {
      background: var(--success-color);
      color: white;
      border: none;
      padding: 0.875rem 1.5rem;
      border-radius: 12px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 600;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      box-shadow: var(--shadow-md);
    }

    .add-btn i {
      margin-right: 0.5rem;
    }

    .add-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
      color: white;
      background: #059669;
    }

    .stats-info {
      background: var(--secondary-color);
      color: var(--primary-color);
      padding: 0.75rem 1.25rem;
      border-radius: 10px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .table-container {
      background: var(--white);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border-color);
    }

    .table-header {
      background: var(--background-light);
      border-bottom: 2px solid var(--border-color);
    }

    .table {
      margin-bottom: 0;
      font-size: 0.9rem;
    }

    .table thead th {
      background: var(--background-light);
      color: var(--text-dark);
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.05em;
      padding: 1.25rem 1rem;
      border-bottom: 2px solid var(--border-color);
      border-top: none;
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .table thead th i {
      margin-right: 0.5rem;
      color: var(--primary-color);
    }

    .table tbody tr {
      transition: all 0.2s ease;
      border-bottom: 1px solid var(--border-color);
    }

    .table tbody tr:hover {
      background: #f8fafc;
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .table tbody tr:last-child {
      border-bottom: none;
    }

    .table tbody td {
      padding: 1.25rem 1rem;
      vertical-align: middle;
      border-top: none;
    }

    .table tbody td:first-child {
      font-weight: 600;
      color: var(--primary-color);
      font-size: 1rem;
    }

    .umkm-name {
      font-weight: 600;
      color: var(--text-dark);
      font-size: 1.1rem;
    }

    .description-text {
      color: var(--text-muted);
      line-height: 1.5;
      max-width: 300px;
    }

    .action-btn {
      padding: 0.6rem 1.2rem;
      margin-right: 0.5rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      color: white;
      font-size: 0.875rem;
      font-weight: 500;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      min-width: 80px;
      justify-content: center;
    }

    .action-btn i {
      margin-right: 0.375rem;
      font-size: 0.875rem;
    }

    .edit-btn {
      background: var(--primary-color);
      box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }

    .edit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
      color: white;
      background: var(--primary-light);
    }

    .delete-btn {
      background: var(--danger-color);
      box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }

    .delete-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
      color: white;
      background: #dc2626;
    }

    .img-thumbnail-custom {
      width: 100px;
      height: 80px;
      object-fit: cover;
      border-radius: 12px;
      border: 2px solid var(--border-color);
      transition: all 0.3s ease;
    }

    .img-thumbnail-custom:hover {
      transform: scale(1.05);
      box-shadow: var(--shadow-md);
    }

    .empty-state {
      text-align: center;
      padding: 3rem;
      color: var(--text-muted);
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: var(--primary-color);
      opacity: 0.5;
    }

    .empty-state h4 {
      margin-bottom: 0.5rem;
      color: var(--text-dark);
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
      
      .page-title {
        font-size: 1.5rem;
      }
      
      .actions-section {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
      }
      
      .table-container {
        overflow-x: auto;
      }
      
      .table {
        min-width: 800px;
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
      background: var(--primary-light);
    }

    .action-btn-wrapper {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

      .action-btn {
        padding: 0.5rem 1.2rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        color: white;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 100px;
      }

      .action-btn i {
        margin-right: 0.375rem;
        font-size: 0.875rem;
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
            <a href="admin wisata.php">
              <i class="fas fa-map-marked-alt"></i>
              Kelola Wisata
            </a>
          </li>
          <li>
            <a href="admin umkm.php" class="active">
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
        <h1 class="page-title">
          <i class="fas fa-store me-3"></i>Kelola UMKM
        </h1>

        <div class="actions-section">
          <a href="add umkm.php" class="add-btn">
            <i class="fas fa-plus"></i>
            Tambah UMKM Baru
          </a>
          <div class="stats-info">
            <i class="fas fa-info-circle"></i>
            Total: <?= mysqli_num_rows($result_umkm) ?> UMKM
          </div>
        </div>

        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th style="width: 80px;"><i class="fas fa-hashtag"></i> NO</th>
                <th style="width: 200px;"><i class="fas fa-store"></i> NAMA UMKM</th>
                <th><i class="fas fa-align-left"></i> DESKRIPSI</th>
                <th style="width: 150px;"><i class="fas fa-image"></i> GAMBAR</th>
                <th style="width: 200px;"><i class="fas fa-cogs"></i> AKSI</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              if (mysqli_num_rows($result_umkm) > 0) {
                  while ($row = mysqli_fetch_assoc($result_umkm)) {
                      $gambar_display = !empty($row['gambar']) 
                            ? '<img src="uploads/' . htmlspecialchars($row['gambar']) . '" alt="Gambar UMKM" class="img-thumbnail-custom">' 
                            : '<div class="text-muted"><i class="fas fa-image"></i><br>Tidak ada gambar</div>';
                      ?>
                      <tr>
                          <td><?= $no++ ?></td>
                          <td>
                              <div class="umkm-name"><?= htmlspecialchars($row['nama']) ?></div>
                          </td>
                          <td>
                              <div class="description-text">
                                  <?= nl2br(htmlspecialchars(substr($row['deskripsi'], 0, 150))) ?>
                                  <?php if (strlen($row['deskripsi']) > 150) echo '<span class="text-muted">...</span>'; ?>
                              </div>
                          </td>
                          <td><?= $gambar_display ?></td>
                          <td>
                              <div class="action-btn-wrapper">
                                <a href="edit umkm.php?id=<?= htmlspecialchars($row['id_umkm']) ?>" class="action-btn edit-btn">
                                  <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete umkm.php?id=<?= htmlspecialchars($row['id_umkm']) ?>" class="action-btn delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus UMKM ini?')">
                                  <i class="fas fa-trash"></i> Hapus
                                </a>
                              </div>
                            </td>
                      </tr>
                      <?php
                  }
              } else {
                  echo '<tr><td colspan="5" class="empty-state">
                            <div>
                                <i class="fas fa-store"></i>
                                <h4>Belum Ada Data UMKM</h4>
                                <p>Mulai tambahkan data UMKM pertama Anda dengan mengklik tombol "Tambah UMKM Baru" di atas.</p>
                            </div>
                        </td></tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>