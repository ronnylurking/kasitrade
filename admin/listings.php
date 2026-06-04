<?php
session_start();
require_once '../config/database.php';

if (!in_array($_SESSION['role'], ['admin','moderator'])) {
    header('Location: index.php');
    exit;
}

$msg = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE listings SET status = 'approved' WHERE listing_id = ?");
        $stmt->execute([$id]);
        $msg = "Listing approved.";
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE listings SET status = 'rejected' WHERE listing_id = ?");
        $stmt->execute([$id]);
        $msg = "Listing rejected.";
    }
}

$status_filter = $_GET['status'] ?? 'all';
$query = "SELECT l.*, u.full_name AS seller_name, c.name AS category_name 
          FROM listings l 
          JOIN users u ON l.seller_id = u.user_id 
          LEFT JOIN categories c ON l.category_id = c.category_id";
if ($status_filter !== 'all') {
    $query .= " WHERE l.status = :status";
}
$query .= " ORDER BY l.created_at DESC";

$stmt = $pdo->prepare($query);
if ($status_filter !== 'all') {
    $stmt->execute(['status' => $status_filter]);
} else {
    $stmt->execute();
}
$listings = $stmt->fetchAll();

$pageTitle = "Manage Listings – KasiTrade";
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 bg-dark text-white sidebar" id="adminSidebar">
            <h4 class="p-3">
                Admin
            </h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin/index.php">
                        📊 Dashboard
                    </a>
                </li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="admin/users.php">
                            👥 Users
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin/listings.php">
                        📋 Listings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="logout.php">
                        🚪 Logout
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h2>
                Manage Listings
            </h2>
            <?php if ($msg): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <a href="admin/listings.php?status=all" class="btn btn-sm <?php echo $status_filter=='all'?'btn-primary':'btn-outline-primary'; ?>">
                    All
                </a>
                <a href="admin/listings.php?status=pending" class="btn btn-sm <?php echo $status_filter=='pending'?'btn-warning':'btn-outline-warning'; ?>">
                    Pending
                </a>
                <a href="admin/listings.php?status=approved" class="btn btn-sm <?php echo $status_filter=='approved'?'btn-success':'btn-outline-success'; ?>">
                    Approved
                </a>
                <a href="admin/listings.php?status=rejected" class="btn btn-sm <?php echo $status_filter=='rejected'?'btn-danger':'btn-outline-danger'; ?>">
                    Rejected
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>
                                Image
                            </th>
                            <th>
                                Title
                            </th>
                            <th>
                                Seller
                            </th>
                            <th>
                                Price
                            </th>
                            <th>
                                Category
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listings as $l): ?>
                        <tr>
                            <td>
                                <?php if ($l['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($l['image_url']); ?>" width="50" alt="">
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($l['title']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($l['seller_name']); ?>
                            </td>
                            <td>
                                R<?php echo number_format($l['price'], 2); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($l['category_name'] ?? 'Uncategorized'); ?>
                            </td>
                            <td>
                                <?php
                                $badge = 'bg-secondary';
                                if ($l['status'] === 'approved') $badge = 'bg-success';
                                elseif ($l['status'] === 'pending') $badge = 'bg-warning text-dark';
                                elseif ($l['status'] === 'rejected') $badge = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $badge; ?>">
                                    <?php echo ucfirst($l['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($l['status'] === 'pending'): ?>
                                    <a href="admin/listings.php?action=approve&id=<?php echo $l['listing_id']; ?>&status=<?php echo $status_filter; ?>" class="btn btn-sm btn-success">
                                        Approve
                                    </a>
                                    <a href="admin/listings.php?action=reject&id=<?php echo $l['listing_id']; ?>&status=<?php echo $status_filter; ?>" class="btn btn-sm btn-danger">
                                        Reject
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">
                                        —
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>