<?php
session_start();
$pageTitle = "Login / Register – KasiTrade";
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login">
                                Login
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#register">
                                Register
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="login">
                            <form action="login_process.php" method="post">
                                <div class="mb-3">
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
                        </div>

                        <div class="tab-pane fade" id="register">
                            <form action="register_process.php" method="post">
                                <div class="mb-3">
                                    <label>
                                        Full Name
                                    </label>
                                    <input type="text" name="full_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
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
                                <div class="mb-3">
                                    <label>
                                        I am a
                                    </label>
                                    <select name="role" class="form-select">
                                        <option value="buyer">
                                            Buyer
                                        </option>
                                        <option value="seller">
                                            Seller
                                        </option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-kasi w-100">
                                    Create Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>