<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_user = isset($_SESSION['user_id']) ? $_SESSION : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/kasitrade/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'KasiTrade'; ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <?php if ($current_user): ?>
    <script>
        // Pass PHP session data to JavaScript (for script.js / RBAC)
        const userData = {
            email: '<?php echo htmlspecialchars($current_user['email'] ?? '', ENT_QUOTES); ?>',
            role: '<?php echo htmlspecialchars($current_user['role'] ?? '', ENT_QUOTES); ?>'
        };
        localStorage.setItem('kasiAdmin', JSON.stringify(userData));
    </script>
    <?php endif; ?>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-warning sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="assets/images/logo.png" alt="KasiTrade" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="buy.php">
                        Buy
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="seller-dashboard.php">
                        Sell
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        🌐 English
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#">
                                English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                isiZulu
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                isiXhosa
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                Afrikaans
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">🛒
                        <span class="badge bg-danger" id="cartCount">
                        <?php
                        $cart_count = 0;
                        if (isset($_SESSION['cart'])) {
                            $cart_count = array_sum($_SESSION['cart']);
                        }
                        echo $cart_count;
                        ?>
                    </span>
                </a>
                </li>
                <li class="nav-item">
                    <?php if ($current_user): ?>
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($current_user['full_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if (in_array($current_user['role'], ['admin','moderator','dispatcher'])): ?>
                                <li>
                                    <a class="dropdown-item" href="admin/index.php">
                                        Admin Panel
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" href="orders.php">
                                    My Orders
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="logout.php">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <a class="nav-link" href="login.php">
                            Login
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>