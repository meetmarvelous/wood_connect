<?php
// Admin Sidebar Navigation - Include this in all admin pages
// Usage: include '../includes/admin-sidebar.php';

// Get counts for badges
$sidebar_stats = [];

try {
    // Pending marketers
    $sidebar_stats['pending_marketers'] = $pdo->query("SELECT COUNT(*) FROM marketers WHERE verification_status = 'pending'")->fetchColumn();
    
    // Pending inquiries
    $sidebar_stats['pending_inquiries'] = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'pending'")->fetchColumn();
    
    // Unread contact messages
    $sidebar_stats['unread_messages'] = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'")->fetchColumn();
} catch (PDOException $e) {
    $sidebar_stats = ['pending_marketers' => 0, 'pending_inquiries' => 0, 'unread_messages' => 0];
}

// Determine current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title text-success">
            <i class="fas fa-cog me-2"></i>Admin Panel
        </h5>
        <div class="list-group list-group-flush">
            <a href="<?php echo url('dashboard/'); ?>" class="list-group-item list-group-item-action <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="<?php echo url('dashboard/marketers.php'); ?>" class="list-group-item list-group-item-action <?php echo $current_page === 'marketers.php' || $current_page === 'marketer-verify.php' ? 'active' : ''; ?>">
                <i class="fas fa-users me-2"></i>Manage Marketers
                <?php if ($sidebar_stats['pending_marketers'] > 0): ?>
                    <span class="badge bg-warning float-end"><?php echo $sidebar_stats['pending_marketers']; ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo url('dashboard/species.php'); ?>" class="list-group-item list-group-item-action <?php echo $current_page === 'species.php' ? 'active' : ''; ?>">
                <i class="fas fa-tree me-2"></i>Timber Species
            </a>
            <a href="<?php echo url('dashboard/inquiries.php'); ?>" class="list-group-item list-group-item-action <?php echo $current_page === 'inquiries.php' ? 'active' : ''; ?>">
                <i class="fas fa-envelope me-2"></i>Customer Inquiries
                <?php if ($sidebar_stats['pending_inquiries'] > 0): ?>
                    <span class="badge bg-warning float-end"><?php echo $sidebar_stats['pending_inquiries']; ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo url('dashboard/contact-messages.php'); ?>" class="list-group-item list-group-item-action <?php echo $current_page === 'contact-messages.php' ? 'active' : ''; ?>">
                <i class="fas fa-inbox me-2"></i>Contact Messages
                <?php if ($sidebar_stats['unread_messages'] > 0): ?>
                    <span class="badge bg-danger float-end"><?php echo $sidebar_stats['unread_messages']; ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo url('dashboard/reports.php'); ?>" class="list-group-item list-group-item-action <?php echo $current_page === 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
            </a>
            <a href="<?php echo url('dashboard/settings.php'); ?>" class="list-group-item list-group-item-action <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-sliders-h me-2"></i>System Settings
            </a>
        </div>
    </div>
</div>
