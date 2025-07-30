<?php
include_once 'db.php';
session_start();

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = $_POST['password'];

  $query = "SELECT * FROM admin WHERE username='$username'";
  $result = mysqli_query($conn, $query);

  if (!$result) {
    echo "<script>alert('Error database: " . mysqli_error($conn) . "');</script>";
  } else {
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      if (password_verify($password, $row['password'])) {
        $_SESSION['admin'] = $row['username'];
        header("Location: admin wisata.php");
        exit;
      } else {
        $error_message = "Username atau password salah!";
      }
    } else {
      $error_message = "Username atau password salah!";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Login Admin - Desa Klero</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      height: 100%;
      overflow: hidden;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #D9D9D9 0%, #00AA55 100%);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    body::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(39, 174, 96, 0.1) 2px, transparent 2px);
      background-size: 50px 50px;
      animation: float 20s ease-in-out infinite;
      pointer-events: none;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(180deg); }
    }

    .login-container {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 420px;
      margin: 0 auto;
      padding: 15px;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 25px;
      box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(255, 255, 255, 0.1);
      padding: 40px;
      position: relative;
      overflow: visible;
      animation: slideUp 0.8s ease-out;
    }

    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, #27ae60, #2ecc71, #27ae60);
      border-radius: 25px 25px 0 0;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .login-icon {
      width: 70px;
      height: 70px;
      background: linear-gradient(135deg, #27ae60, #2ecc71);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      box-shadow: 0 10px 25px rgba(39, 174, 96, 0.3);
      animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
      40% { transform: translateY(-10px); }
      60% { transform: translateY(-5px); }
    }

    .login-icon i {
      color: white;
      font-size: 1.8rem;
    }

    .login-title {
      font-size: 1.8rem;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 8px;
    }

    .login-subtitle {
      color: #7f8c8d;
      font-size: 0.9rem;
      font-weight: 400;
    }

    .form-floating {
      margin-bottom: 20px;
    }

    .form-floating > .form-control {
      border: 2px solid #e9ecef;
      border-radius: 15px;
      padding: 18px 15px 8px;
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }

    .form-floating > .form-control:focus {
      border-color: #27ae60;
      box-shadow: 0 0 0 0.25rem rgba(39, 174, 96, 0.15);
      background: white;
    }

    .form-floating > label {
      color: #6c757d;
      font-weight: 500;
      padding: 18px 15px 8px;
      font-size: 0.9rem;
    }

    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
      color: #27ae60;
      font-weight: 600;
    }

    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #6c757d;
      cursor: pointer;
      z-index: 5;
      padding: 8px;
      border-radius: 50%;
      transition: all 0.3s ease;
    }

    .password-toggle:hover {
      background: rgba(39, 174, 96, 0.1);
      color: #27ae60;
    }

    .btn-login {
      background: linear-gradient(135deg, #27ae60, #2ecc71);
      border: none;
      border-radius: 15px;
      padding: 12px;
      font-size: 1rem;
      font-weight: 600;
      color: white;
      width: 100%;
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
      box-shadow: 0 8px 20px rgba(39, 174, 96, 0.3);
    }

    .btn-login::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }

    .btn-login:hover::before {
      left: 100%;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 30px rgba(39, 174, 96, 0.4);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .back-home {
      text-align: center;
      margin-top: 25px;
    }

    .back-home a {
      color: #7f8c8d;
      text-decoration: none;
      font-weight: 500;
      font-size: 0.9rem;
      transition: color 0.3s ease;
    }

    .back-home a:hover {
      color: #27ae60;
    }

    .back-home i {
      margin-right: 8px;
    }

    .loading-spinner {
      display: none;
      width: 18px;
      height: 18px;
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s ease-in-out infinite;
      margin-right: 8px;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .error-notification {
      background: linear-gradient(135deg, #ff4757, #ff3742);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 14px 18px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      font-weight: 600;
      font-size: 0.9rem;
      box-shadow: 0 8px 25px rgba(255, 71, 87, 0.4);
      animation: slideDown 0.5s ease-out, shake 0.6s ease-in-out;
      border-left: 4px solid #ff1744;
      position: relative;
    }

    .error-notification::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), transparent);
      border-radius: 12px;
      pointer-events: none;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
      20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .error-notification i {
      margin-right: 10px;
      font-size: 1.1rem;
      animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    .error-notification .close-btn {
      margin-left: auto;
      background: none;
      border: none;
      color: white;
      cursor: pointer;
      padding: 0 5px;
      opacity: 0.8;
      transition: opacity 0.3s ease;
    }

    .error-notification .close-btn:hover {
      opacity: 1;
    }

    @media (max-width: 576px) {
      .login-container {
        padding: 10px;
      }
      
      .login-card {
        padding: 30px 25px;
      }
      
      .login-title {
        font-size: 1.5rem;
      }
      
      .login-icon {
        width: 60px;
        height: 60px;
      }
      
      .login-icon i {
        font-size: 1.5rem;
      }
    }


      .login-icon {
        width: 60px;
        height: 60px;
      }
      
      .login-icon i {
        font-size: 1.5rem;
      }
      
      .login-title {
        font-size: 1.6rem;
      }
      
      .login-header {
        margin-bottom: 25px;
      }
      
      .form-floating {
        margin-bottom: 18px;
      }
    
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <div class="login-icon">
          <i class="fas fa-user-shield"></i>
        </div>
        <h1 class="login-title">Admin Portal</h1>
        <p class="login-subtitle">Masuk ke panel administrasi Desa Klero</p>
      </div>

      <?php if (!empty($error_message)): ?>
      <div class="error-notification" id="errorNotification">
        <i class="fas fa-exclamation-triangle"></i>
        <span><?php echo htmlspecialchars($error_message); ?></span>
        <button type="button" class="close-btn" onclick="closeErrorNotification()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <?php endif; ?>

      <form method="POST" action="" id="loginForm">
        <div class="form-floating">
          <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autocomplete="username">
          <label for="username">
            <i class="fas fa-user me-2"></i>Username
          </label>
        </div>

        <div class="form-floating position-relative">
          <input type="password" class="form-control" id="password" name="password" placeholder="Password" required autocomplete="current-password">
          <label for="password">
            <i class="fas fa-lock me-2"></i>Password
          </label>
          <button type="button" class="password-toggle" onclick="togglePassword()">
            <i class="fas fa-eye" id="eyeIcon"></i>
          </button>
        </div>

        <button type="submit" class="btn btn-login" onclick="showLoading()">
          <span class="loading-spinner" id="loadingSpinner"></span>
          <span id="buttonText">
            <i class="fas fa-sign-in-alt me-2"></i>Masuk
          </span>
        </button>
      </form>

      <div class="back-home">
        <a href="home.php">
          <i class="fas fa-arrow-left"></i>
          Kembali ke Beranda
        </a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.className = 'fas fa-eye-slash';
      } else {
        passwordInput.type = 'password';
        eyeIcon.className = 'fas fa-eye';
      }
    }

    function showLoading() {
      const spinner = document.getElementById('loadingSpinner');
      const buttonText = document.getElementById('buttonText');
      
      spinner.style.display = 'inline-block';
      buttonText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
    }

    function closeErrorNotification() {
      const errorNotification = document.getElementById('errorNotification');
      if (errorNotification) {
        errorNotification.style.opacity = '0';
        errorNotification.style.transform = 'translateY(-20px)';
        setTimeout(() => {
          errorNotification.style.display = 'none';
        }, 300);
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      const formControls = document.querySelectorAll('.form-control');
      
      formControls.forEach((input, index) => {
        input.style.animationDelay = `${index * 0.1}s`;
      });

      formControls.forEach(input => {
        input.addEventListener('focus', function() {
          this.parentElement.style.transform = 'translateY(-2px)';
          this.parentElement.style.transition = 'transform 0.3s ease';
        });
        
        input.addEventListener('blur', function() {
          this.parentElement.style.transform = 'translateY(0)';
        });
      });

      const errorNotification = document.getElementById('errorNotification');
      if (errorNotification) {
        setTimeout(() => {
          closeErrorNotification();
        }, 5000);
      }

      setTimeout(() => {
        document.getElementById('username').focus();
      }, 500);
    });

    document.getElementById('loginForm').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        showLoading();
        this.submit();
      }
    });

    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }

    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();
      
      if (!username || !password) {
        e.preventDefault();
        
        showErrorNotification('Harap isi username dan password!');
        
        return false;
      }
    });

    function showErrorNotification(message) {
      const existingError = document.querySelector('.error-notification');
      if (existingError) {
        existingError.remove();
      }

      const errorDiv = document.createElement('div');
      errorDiv.className = 'error-notification';
      errorDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle"></i>
        <span>${message}</span>
        <button type="button" class="close-btn" onclick="closeErrorNotification()">
          <i class="fas fa-times"></i>
        </button>
      `;

      const loginCard = document.querySelector('.login-card');
      const loginHeader = document.querySelector('.login-header');
      loginCard.insertBefore(errorDiv, loginHeader.nextSibling);

      setTimeout(() => {
        closeErrorNotification();
      }, 6000);
    }
  </script>
</body>
</html>