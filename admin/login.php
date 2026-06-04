<?php
session_start();
if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin','moderator','dispatcher'])) {
    header('Location: index.php');
    exit;
}
$pageTitle = "Admin Login – KasiTrade";
include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">
                        Admin Login
                    </h3>
                        <form action="/kasitrade/login_process.php" method="post">                        <div class="mb-3">
                            <label>
                                Email
                            </label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>
                                Password
                            </label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-kasi w-100">
                            Login
                        </button>
                    </form>
                    <div class="mt-3 text-center">
                        <small>
                            Demo: admin@test.com / 1234 (admin), mod@test.com / 1234 (moderator)
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>