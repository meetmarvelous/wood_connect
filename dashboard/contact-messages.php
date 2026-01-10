<?php
require_once '../includes/config.php';
$auth->requireAdmin();

$page_title = "Contact Messages - WOOD CONNECT Admin";

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
    
    $message_id = (int)$_POST['message_id'];
    
    if ($_POST['action'] === 'mark_read') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
        $stmt->execute([$message_id]);
    } elseif ($_POST['action'] === 'mark_replied') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'replied' WHERE id = ?");
        $stmt->execute([$message_id]);
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$message_id]);
    } elseif ($_POST['action'] === 'add_note') {
        $note = sanitizeInput($_POST['admin_notes']);
        $stmt = $pdo->prepare("UPDATE contact_messages SET admin_notes = ? WHERE id = ?");
        $stmt->execute([$note, $message_id]);
    }
    
    header("Location: contact-messages.php");
    exit;
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query
$where = "";
if ($filter === 'unread') {
    $where = "WHERE status = 'unread'";
} elseif ($filter === 'read') {
    $where = "WHERE status = 'read'";
} elseif ($filter === 'replied') {
    $where = "WHERE status = 'replied'";
}

// Get messages
$messages_stmt = $pdo->query("SELECT * FROM contact_messages $where ORDER BY created_at DESC");
$messages = $messages_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get counts
$counts_stmt = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'unread' THEN 1 ELSE 0 END) as unread,
        SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_count,
        SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied
    FROM contact_messages
");
$counts = $counts_stmt->fetch(PDO::FETCH_ASSOC);

// Get single message for modal
$selected_message = null;
if (isset($_GET['view'])) {
    $view_id = (int)$_GET['view'];
    $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$view_id]);
    $selected_message = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Mark as read if unread
    if ($selected_message && $selected_message['status'] === 'unread') {
        $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?")->execute([$view_id]);
        $selected_message['status'] = 'read';
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <?php include '../includes/admin-sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-success"><i class="fas fa-inbox me-2"></i>Contact Messages</h2>
            </div>

            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'all' ? 'active' : ''; ?>" href="?filter=all">
                        All <span class="badge bg-secondary"><?php echo $counts['total']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'unread' ? 'active' : ''; ?>" href="?filter=unread">
                        Unread <span class="badge bg-danger"><?php echo $counts['unread']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'read' ? 'active' : ''; ?>" href="?filter=read">
                        Read <span class="badge bg-info"><?php echo $counts['read_count']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'replied' ? 'active' : ''; ?>" href="?filter=replied">
                        Replied <span class="badge bg-success"><?php echo $counts['replied']; ?></span>
                    </a>
                </li>
            </ul>

            <!-- Messages List -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <?php if (empty($messages)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No messages found</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Status</th>
                                        <th>From</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($messages as $msg): ?>
                                        <tr class="<?php echo $msg['status'] === 'unread' ? 'table-warning' : ''; ?>">
                                            <td>
                                                <?php if ($msg['status'] === 'unread'): ?>
                                                    <span class="badge bg-danger"><i class="fas fa-circle"></i> Unread</span>
                                                <?php elseif ($msg['status'] === 'read'): ?>
                                                    <span class="badge bg-info"><i class="fas fa-eye"></i> Read</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><i class="fas fa-reply"></i> Replied</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($msg['name']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($msg['email']); ?></small>
                                            </td>
                                            <td>
                                                <?php 
                                                $subject_labels = [
                                                    'general' => 'General Inquiry',
                                                    'support' => 'Technical Support',
                                                    'partnership' => 'Partnership',
                                                    'verification' => 'Verification',
                                                    'other' => 'Other'
                                                ];
                                                echo $subject_labels[$msg['subject']] ?? $msg['subject'];
                                                ?>
                                            </td>
                                            <td>
                                                <small><?php echo date('M j, Y g:i A', strtotime($msg['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <a href="?view=<?php echo $msg['id']; ?>&filter=<?php echo $filter; ?>" class="btn btn-sm btn-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo urlencode($subject_labels[$msg['subject']] ?? $msg['subject']); ?>" 
                                                   class="btn btn-sm btn-success" title="Reply by Email">
                                                    <i class="fas fa-reply"></i>
                                                </a>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this message?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Detail Modal -->
<?php if ($selected_message): ?>
<div class="modal fade show" id="messageModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-envelope-open me-2"></i>Message Details
                </h5>
                <a href="?filter=<?php echo $filter; ?>" class="btn-close btn-close-white"></a>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">From</label>
                        <p class="fw-bold mb-0"><?php echo htmlspecialchars($selected_message['name']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Email</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="senderEmail" value="<?php echo htmlspecialchars($selected_message['email']); ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyEmail()" title="Copy Email">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Subject</label>
                        <p class="mb-0">
                            <?php 
                            $subject_labels = [
                                'general' => 'General Inquiry',
                                'support' => 'Technical Support',
                                'partnership' => 'Partnership Opportunity',
                                'verification' => 'Marketer Verification',
                                'other' => 'Other'
                            ];
                            echo $subject_labels[$selected_message['subject']] ?? $selected_message['subject'];
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Received</label>
                        <p class="mb-0"><?php echo date('F j, Y \a\t g:i A', strtotime($selected_message['created_at'])); ?></p>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Message</label>
                    <div class="p-3 bg-light rounded">
                        <?php echo nl2br(htmlspecialchars($selected_message['message'])); ?>
                    </div>
                </div>
                
                <!-- Admin Notes -->
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="message_id" value="<?php echo $selected_message['id']; ?>">
                    <input type="hidden" name="action" value="add_note">
                    <div class="mb-3">
                        <label class="form-label text-muted">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="2" placeholder="Add internal notes..."><?php echo htmlspecialchars($selected_message['admin_notes'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-save me-1"></i>Save Notes</button>
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-flex gap-2 w-100">
                    <?php if ($selected_message['status'] !== 'replied'): ?>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <input type="hidden" name="message_id" value="<?php echo $selected_message['id']; ?>">
                            <input type="hidden" name="action" value="mark_replied">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i>Mark as Replied
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="mailto:<?php echo htmlspecialchars($selected_message['email']); ?>?subject=Re: <?php echo urlencode($subject_labels[$selected_message['subject']] ?? $selected_message['subject']); ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-reply me-1"></i>Reply by Email
                    </a>
                    <a href="?filter=<?php echo $filter; ?>" class="btn btn-outline-secondary ms-auto">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyEmail() {
    const emailInput = document.getElementById('senderEmail');
    emailInput.select();
    document.execCommand('copy');
    
    // Show feedback
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check text-success"></i>';
    setTimeout(() => {
        btn.innerHTML = originalHTML;
    }, 1500);
}
</script>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
</body>
</html>
