<?php
require_once 'includes/config.php';

if ($auth->isLoggedIn()) {
    header('Location: ' . url('/'));
    exit;
}

$page_title = "Register - TIMBER CONNECT";
$user_type = $_GET['type'] ?? 'buyer'; // Default to buyer
$success_message = '';
$error_message = '';

// Validate user type
if (!in_array($user_type, ['buyer', 'marketer'])) {
    $user_type = 'buyer';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'])) {
    try {
        if ($_POST['user_type'] === 'buyer') {
            $buyer_id = $auth->registerBuyer($_POST);
            $success_message = "Buyer registration successful! You can now login to your account.";
            $_POST = []; // Clear form
        } elseif ($_POST['user_type'] === 'marketer') {
            $marketer_id = $auth->registerMarketer($_POST);
            $success_message = "Marketer registration successful! Your account is pending verification.";
            $_POST = []; // Clear form
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
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
    <link href="<?php echo asset('css/style.css'); ?>" rel="stylesheet">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php 
    $page_title = "Register - TIMBER CONNECT";
    include 'includes/header.php'; 
    ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white py-4">
                        <h4 class="mb-0 text-center">
                            <i class="fas fa-user-plus me-2"></i>
                            Register as <?php echo ucfirst($user_type); ?>
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success">
                                <h5 class="alert-heading">Registration Successful!</h5>
                                <?php echo $success_message; ?>
                                <div class="mt-3">
                                    <a href="<?php echo url('login.php'); ?>" class="btn btn-success">Proceed to Login</a>
                                    <a href="<?php echo url('/'); ?>" class="btn btn-outline-success">Return to Home</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>

                            <!-- User Type Selection -->
                            <div class="text-center mb-4">
                                <div class="btn-group" role="group">
                                    <a href="<?php echo url('register.php?type=buyer'); ?>" 
                                       class="btn btn-<?php echo $user_type === 'buyer' ? 'success' : 'outline-success'; ?>">
                                        <i class="fas fa-shopping-cart me-2"></i>Register as Buyer
                                    </a>
                                    <a href="<?php echo url('register.php?type=marketer'); ?>" 
                                       class="btn btn-<?php echo $user_type === 'marketer' ? 'success' : 'outline-success'; ?>">
                                        <i class="fas fa-store me-2"></i>Register as Marketer
                                    </a>
                                </div>
                            </div>

                            <form method="POST" id="registerForm">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="user_type" value="<?php echo $user_type; ?>">
                                
                                <?php if ($user_type === 'buyer'): ?>
                                    <!-- Buyer Registration Form -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="full_name" class="form-label fw-semibold">Full Name *</label>
                                                <input type="text" class="form-control form-control-lg" id="full_name" name="full_name" 
                                                       value="<?php echo $_POST['full_name'] ?? ''; ?>" required placeholder="Enter your full name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="company_name" class="form-label fw-semibold">Company Name</label>
                                                <input type="text" class="form-control form-control-lg" id="company_name" name="company_name" 
                                                       value="<?php echo $_POST['company_name'] ?? ''; ?>" placeholder="Enter your company name (optional)">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label fw-semibold">Email Address *</label>
                                                <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                                       value="<?php echo $_POST['email'] ?? ''; ?>" required placeholder="your@email.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="phone" class="form-label fw-semibold">Phone Number *</label>
                                                <input type="tel" class="form-control form-control-lg" id="phone" name="phone" 
                                                       value="<?php echo $_POST['phone'] ?? ''; ?>" required placeholder="08012345678">
                                                <div class="form-text">Format: 08012345678 or +2348012345678</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label fw-semibold">Address</label>
                                        <textarea class="form-control form-control-lg" id="address" name="address" rows="2" 
                                                  placeholder="Enter your address"><?php echo $_POST['address'] ?? ''; ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="city" class="form-label fw-semibold">City</label>
                                                <input type="text" class="form-control form-control-lg" id="city" name="city" 
                                                       value="<?php echo $_POST['city'] ?? ''; ?>" placeholder="Enter your city">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="state" class="form-label fw-semibold">State</label>
                                                <select class="form-select form-control-lg" id="state" name="state">
                                                    <option value="">Select State</option>
                                                    <?php foreach ($nigerian_states as $state => $lgas): ?>
                                                        <option value="<?php echo $state; ?>" 
                                                            <?php echo ($_POST['state'] ?? '') === $state ? 'selected' : ''; ?>>
                                                            <?php echo $state; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                <?php else: ?>
                                    <!-- Marketer Registration Form (your existing form) -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="business_name" class="form-label fw-semibold">Business Name *</label>
                                                <input type="text" class="form-control form-control-lg" id="business_name" name="business_name" 
                                                       value="<?php echo $_POST['business_name'] ?? ''; ?>" required placeholder="Enter your business name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="owner_name" class="form-label fw-semibold">Owner/Manager Name *</label>
                                                <input type="text" class="form-control form-control-lg" id="owner_name" name="owner_name" 
                                                       value="<?php echo $_POST['owner_name'] ?? ''; ?>" required placeholder="Enter owner name">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ... rest of your existing marketer registration form ... -->
                                    
                                <?php endif; ?>

                                <!-- Common Fields for Both -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label fw-semibold">Password *</label>
                                            <input type="password" class="form-control form-control-lg" id="password" name="password" required 
                                                   placeholder="Create a password (min. 6 characters)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label fw-semibold">Confirm Password *</label>
                                            <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" required 
                                                   placeholder="Confirm your password">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                    <label class="form-check-label" for="agree_terms">
                                        I agree to the <a href="<?php echo url('terms.php'); ?>" target="_blank" class="text-success">Terms and Conditions</a> 
                                        and <a href="<?php echo url('privacy.php'); ?>" target="_blank" class="text-success">Privacy Policy</a>
                                    </label>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg py-3 fw-semibold">
                                        Register as <?php echo ucfirst($user_type); ?>
                                    </button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <p class="mb-0">
                                    Already have an account? 
                                    <a href="<?php echo url('login.php'); ?>" class="text-success fw-semibold">Login here</a>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registerForm');
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    });
    </script>
</body>
</html>