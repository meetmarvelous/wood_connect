<?php
// This file should be included in all pages
// Use url() function for absolute paths
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'WOOD CONNECT'; ?></title>

    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo asset('css/style.css'); ?>" rel="stylesheet">

    <!-- JS Files (deferred) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?php echo asset('js/main.js'); ?>" defer></script>

    <!-- Chart.js - Only load on pages that need it -->

    <?php if (isset($load_charts) && $load_charts): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="<?php echo url('/'); ?>">
                <i class="fas fa-tree me-2"></i>WOOD CONNECT
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('/'); ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('marketplace/'); ?>">Marketplace</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('species/directory.php'); ?>">Timber Species</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('about.php'); ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('contact.php'); ?>">Contact</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <?php if (isset($auth) && $auth->isLoggedIn()): ?>
                        <?php
                        $userInfo = $auth->getUserInfo();
                        if ($userInfo):
                        ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i>
                                    <?php
                                    if ($auth->isBuyer()) {
                                        echo $userInfo['name'];
                                    } elseif ($auth->isMarketer()) {
                                        echo $userInfo['name'];
                                    } elseif ($auth->isAdmin()) {
                                        echo $userInfo['name'];
                                    }
                                    ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <?php if ($auth->isBuyer()): ?>
                                        <li><span class="dropdown-item-text text-muted small">
                                                <i class="fas fa-shopping-cart me-2"></i>Timber Buyer
                                            </span></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <!-- <li><a class="dropdown-item" href="<?php echo url('buyer/profile.php'); ?>">
                                                <i class="fas fa-user me-2"></i>My Profile
                                            </a></li> -->
                                        <!-- <li><a class="dropdown-item" href="<?php echo url('buyer/inquiries.php'); ?>">
                                                <i class="fas fa-envelope me-2"></i>My Inquiries
                                            </a></li> -->

                                    <?php elseif ($auth->isMarketer()): ?>
                                        <li><span class="dropdown-item-text text-muted small">
                                                <i class="fas fa-store me-2"></i>Timber Marketer
                                            </span></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="<?php echo url('dashboard/marketer/'); ?>">
                                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                            </a></li>
                                        <li><a class="dropdown-item" href="<?php echo url('dashboard/marketer/inventory.php'); ?>">
                                                <i class="fas fa-boxes me-2"></i>Inventory
                                            </a></li>
                                        <li><a class="dropdown-item" href="<?php echo url('dashboard/marketer/profile.php'); ?>">
                                                <i class="fas fa-user me-2"></i>Business Profile
                                            </a></li>

                                    <?php elseif ($auth->isAdmin()): ?>
                                        <li><span class="dropdown-item-text text-muted small">
                                                <i class="fas fa-cog me-2"></i>Administrator
                                            </span></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="<?php echo url('dashboard/'); ?>">
                                                <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                                            </a></li>
                                    <?php endif; ?>

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="<?php echo url('logout.php'); ?>">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('login.php'); ?>">Login</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="registerDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Register
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="registerDropdown">
                                <li><a class="dropdown-item" href="<?php echo url('register.php?type=buyer'); ?>">
                                        <i class="fas fa-shopping-cart me-2"></i>As Buyer
                                    </a></li>
                                <li><a class="dropdown-item" href="<?php echo url('register.php?type=marketer'); ?>">
                                        <i class="fas fa-store me-2"></i>As Marketer
                                    </a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>