<?php
require_once 'includes/config.php';

if ($auth->isLoggedIn()) {
  header('Location: /timber-connect/dashboard/');
  exit;
}

$page_title = "Login - WOOD CONNECT";
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
  $username = sanitizeInput($_POST['username']);
  $password = $_POST['password'];

  try {
    if ($auth->loginAdmin($username, $password)) {
      header('Location: /timber-connect/dashboard/');
      exit;
    } else {
      $error_message = "Invalid username or password for admin account.";
    }
  } catch (Exception $e) {
    $error_message = "Login failed: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?></title>

  <!-- Bootstrap 5.3.2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome 6.4.0 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Inter Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="/timber-connect/assets/css/style.css" rel="stylesheet">

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <?php
  $page_title = "Login - WOOD CONNECT";
  include 'includes/header.php';
  ?>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow">
          <div class="card-header bg-success text-white text-center py-4">
            <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Admin Login - WOOD CONNECT</h4>
          </div>
          <div class="card-body p-4">
            <?php if ($error_message): ?>
              <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
              <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

              <div class="mb-3">
                <label for="username" class="form-label fw-semibold">Username</label>
                <input type="text" class="form-control form-control-lg" id="username" name="username" required
                  value="<?php echo $_POST['username'] ?? ''; ?>" placeholder="Enter your username">
              </div>

              <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control form-control-lg" id="password" name="password" required placeholder="Enter your password">
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg py-3 fw-semibold">Login to Admin Account</button>
              </div>
            </form>

            <div class="text-center mt-4">
              <p class="mb-2">
                <a href="/timber-connect/forgot-password.php" class="text-success text-decoration-none">Forgot your password?</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
</body>

</html>