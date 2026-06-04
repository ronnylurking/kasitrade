<?php
session_start();
require_once 'config/database.php';

$listing_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT l.*, u.full_name AS seller_name, u.township FROM listings l JOIN users u ON l.seller_id = u.user_id WHERE l.listing_id = ? AND l.status = 'approved'");
$stmt->execute([$listing_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: buy.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $qty = (int)$_POST['qty'];
    if (isset($_SESSION['cart'][$product['listing_id']])) {
        $_SESSION['cart'][$product['listing_id']] += $qty;
    } else {
        $_SESSION['cart'][$product['listing_id']] = $qty;
    }
    $added = true;
}

$review_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
        $review_msg = '<div class="alert alert-danger">You must be logged in as a buyer to leave a review.</div>';
    } else {
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);

        $check = $pdo->prepare("SELECT review_id FROM reviews WHERE user_id = ? AND listing_id = ?");
        $check->execute([$_SESSION['user_id'], $listing_id]);
        if ($check->fetch()) {
            $review_msg = '<div class="alert alert-warning">You have already reviewed this product.</div>';
        } elseif ($rating < 1 || $rating > 5) {
            $review_msg = '<div class="alert alert-danger">Please select a rating between 1 and 5.</div>';
        } else {
            $stmt = $pdo->prepare("INSERT INTO reviews (user_id, listing_id, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $listing_id, $rating, $comment]);
            $review_msg = '<div class="alert alert-success">Thank you for your review!</div>';
        }
    }
}

$review_stmt = $pdo->prepare("SELECT r.rating, r.comment, r.created_at, u.full_name 
                              FROM reviews r 
                              JOIN users u ON r.user_id = u.user_id 
                              WHERE r.listing_id = ? 
                              ORDER BY r.created_at DESC");
$review_stmt->execute([$listing_id]);
$reviews = $review_stmt->fetchAll();

$pageTitle = $product['title'] . " – KasiTrade";
include 'includes/header.php';
?>

<div class="container my-5">
    <?php if (isset($added)): ?>
        <div class="alert alert-success">
            Added to cart! 
            <a href="cart.php">
                View Cart
            </a>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <?php if ($product['image_url']): ?>
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['title']); ?>">
            <?php else: ?>
                <div class="bg-light text-center py-5">
                    No Image
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($product['title']); ?></h2>
            <p class="text-muted">
                Sold by <strong><?php echo htmlspecialchars($product['seller_name']); ?></strong>
                <?php if ($product['township']): ?>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($product['township']); ?></span>
                <?php endif; ?>
            </p>
            <h3 class="text-success">R<?php echo number_format($product['price'], 2); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <form method="post">
                <div class="mb-3">
                    <label>
                        Quantity:
                    </label>
                    <input type="number" name="qty" value="1" min="1" max="10" class="form-control w-25">
                </div>
                <button type="submit" name="add_to_cart" class="btn btn-kasi btn-lg me-2">
                    Add to Cart
                </button>
                <a href="buy.php" class="btn btn-outline-success btn-lg">
                    Continue Shopping
                </a>
            </form>

            <hr>
            <div class="d-flex align-items-center">
                <span class="badge bg-info me-2">
                    Secure Payment via Ozow
                </span>
                <span class="badge bg-warning text-dark">
                    Delivery Available
                </span>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <h3>
                Customer Reviews
            </h3>
            <?php echo $review_msg; ?>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'buyer'): ?>
                <div class="card my-3">
                    <div class="card-body">
                        <h5>
                            Leave a Review
                        </h5>
                        <form method="post">
                            <div class="mb-3">
                                <label>
                                    Rating
                                </label>
                                <select name="rating" class="form-select w-25" required>
                                    <option value="">
                                        Select
                                    </option>
                                    <option value="5">
                                        5 - Excellent
                                    </option>
                                    <option value="4">
                                        4 - Good
                                    </option>
                                    <option value="3">
                                        3 - Average
                                    </option>
                                    <option value="2">
                                        2 - Poor
                                    </option>
                                    <option value="1">
                                        1 - Terrible
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>
                                    Comment
                                </label>
                                <textarea name="comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                            </div>
                            <button type="submit" name="submit_review" class="btn btn-kasi">
                                Submit Review
                            </button>
                        </form>
                    </div>
                </div>
            <?php elseif (!isset($_SESSION['user_id'])): ?>
                <div class="alert alert-light border">
                    Please 
                    <a href="login.php">
                        login
                    </a>
                     as a buyer to leave a review.
                    </div>
            <?php endif; ?>

            <?php if (count($reviews) > 0): ?>
                <?php foreach ($reviews as $rev): ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <strong><?php echo htmlspecialchars($rev['full_name']); ?></strong>
                                <small class="text-muted"><?php echo date('d M Y', strtotime($rev['created_at'])); ?></small>
                            </div>
                            <div class="mb-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $rev['rating']): ?>
                                        ⭐
                                    <?php else: ?>
                                        ☆
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($rev['comment'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">
                    No reviews yet. Be the first to review this product!
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>