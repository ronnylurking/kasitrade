<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT listing_id, title, price FROM listings WHERE listing_id IN ($placeholders) AND status = 'approved'");
$stmt->execute($ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cart_items = [];
$total = 0;
foreach ($products as $product) {
    $qty = $_SESSION['cart'][$product['listing_id']];
    $subtotal = $product['price'] * $qty;
    $total += $subtotal;
    $cart_items[] = array_merge($product, ['qty' => $qty, 'subtotal' => $subtotal]);
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_address = trim($_POST['delivery_address']);
    $payment_method = $_POST['payment_method'] ?? 'ozow';

    if (empty($delivery_address)) {
        $msg = "Please enter a delivery address.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO orders (buyer_id, total_amount, status, payment_status, delivery_address) VALUES (?, ?, 'pending', 'paid', ?)");
        $stmt->execute([$_SESSION['user_id'], $total, $delivery_address]);
        $order_id = $pdo->lastInsertId();

        // Create order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, listing_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $stmt->execute([$order_id, $item['listing_id'], $item['qty'], $item['price']]);
        }

        $stmt = $pdo->prepare("INSERT INTO payments (order_id, gateway, transaction_ref, amount, status) VALUES (?, 'Ozow', ?, ?, 'completed')");
        $ref = 'OZOW-' . strtoupper(uniqid());
        $stmt->execute([$order_id, $ref, $total]);

        $_SESSION['cart'] = [];

        $msg = "Order placed successfully! Your order ID is #" . $order_id . ". <a href='index.php'>Return home</a>.";
        // We'll show the message and stop further output
        $pageTitle = "Order Confirmed – KasiTrade";
        include 'includes/header.php';
        echo '<div class="container my-5"><div class="alert alert-success">' . $msg . '</div></div>';
        include 'includes/footer.php';
        exit;
    }
}

$pageTitle = "Checkout – KasiTrade";
include 'includes/header.php';
?>

<div class="container my-5">
    <h2>
        Checkout
    </h2>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="row">
            <div class="col-md-7">
                <h4>
                    Delivery Details
                </h4>
                <div class="mb-3">
                    <label>
                        Delivery Address
                    </label>
                    <textarea name="delivery_address" class="form-control" rows="3" required placeholder="Enter your full address, including township"></textarea>
                </div>
                <div class="mb-3">
                    <label>
                        Payment Method
                    </label>
                    <select name="payment_method" class="form-select">
                        <option value="ozow">
                            Ozow (Instant EFT)
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <h4>
                    Order Summary
                </h4>
                <div class="card p-3">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo htmlspecialchars($item['title']); ?> x <?php echo $item['qty']; ?></span>
                            <span>R<?php echo number_format($item['subtotal'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>R<?php echo number_format($total, 2); ?></span>
                    </div>
                    <button type="submit" class="btn btn-kasi w-100 mt-3">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>