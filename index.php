<?php
session_start();
require_once 'config/database.php';
$pageTitle = "KasiTrade – Shop Township Treasures";
include 'includes/header.php';
?>

<section class="bg-success text-white py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">
            Shop Township Treasures
        </h1>
        <p class="lead">
            Buy & sell directly with people in your community
        </p>
        <div class="row justify-content-center mt-4">
            <div class="col-md-6">
                <form class="d-flex" action="buy.php" method="get">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search products...">
                    <button class="btn btn-warning" type="submit">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="container my-5">
    <h2 class="mb-4">
        Categories
    </h2>
    <div class="row g-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="buy.php?category=clothing" class="card text-center p-3 shadow-sm text-decoration-none text-dark">
                👗<br>Clothing
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="buy.php?category=electronics" class="card text-center p-3 shadow-sm text-decoration-none text-dark">
                📱<br>Electronics
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="buy.php?category=fresh-produce" class="card text-center p-3 shadow-sm text-decoration-none text-dark">
                🥑<br>Fresh Produce
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="buy.php?category=home-living" class="card text-center p-3 shadow-sm text-decoration-none text-dark">
                🛋️<br>Home & Living
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="buy.php?category=baby-kids" class="card text-center p-3 shadow-sm text-decoration-none text-dark">
                🧸<br>Baby & Kids
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="buy.php?category=handmade" class="card text-center p-3 shadow-sm text-decoration-none text-dark">
                🎨<br>Handmade
            </a>
        </div>
    </div>
</section>

<section class="container my-5">
    <h2 class="mb-4">
        Featured This Week
    </h2>
    <div class="row">

        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card listing-card">
                <img src="assets/images/necklace.png" class="card-img-top" alt="Necklace" style="height:200px; object-fit:cover;">
                <div class="card-body">
                    <h5 class="card-title">
                        Necklace
                    </h5>
                    <p class="card-text">
                        R150.00
                    </p>
                    <a href="product.php?id=2" class="btn btn-kasi btn-sm">
                        View
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card listing-card">
                <img src="assets/images/jacket.png" class="card-img-top" alt="Jacket" style="height:200px; object-fit:cover;">
                <div class="card-body">
                    <h5 class="card-title">
                        Leather Jacket
                    </h5>
                    <p class="card-text">
                        R180.00
                    </p>
                    <a href="product.php?id=3" class="btn btn-kasi btn-sm">
                        View
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card listing-card">
                <img src="assets/images/sandals.png" class="card-img-top" alt="Sandals" style="height:200px; object-fit:cover;">
                <div class="card-body">
                    <h5 class="card-title">
                        Beaded Sandals
                    </h5>
                    <p class="card-text">
                        R250.00
                    </p>
                    <a href="product.php?id=4" class="btn btn-kasi btn-sm">
                        View
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3 mb-4">
            <div class="card listing-card">
                <img src="assets/images/basket.png" class="card-img-top" alt="Bag" style="height:200px; object-fit:cover;">
                <div class="card-body">
                    <h5 class="card-title">
                        Handwoven Bag
                    </h5>
                    <p class="card-text">
                        R120.00
                    </p>
                    <a href="product.php?id=5" class="btn btn-kasi btn-sm">
                        View
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>