<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','moderator','dispatcher'])) {
    header('Location: ../login.php');
    exit;
}
$pageTitle = "Admin Dashboard – KasiTrade";
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
                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'moderator'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin/users.php">
                        👥 Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin/listings.php">
                        📋 Listings
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="logout.php">
                        🚪 Logout
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h2>
                Dashboard
            </h2>
            <p>
                Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>.
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>