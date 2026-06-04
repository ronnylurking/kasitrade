<?php
session_start();
$pageTitle = "Shopping Cart – KasiTrade";
include 'includes/header.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Update quantities
        foreach ($_POST['qty'] as $listing_id => $qty) {
            if ($qty > 0) {
                $_SESSION['cart'][$listing_id] = (int)$qty;
            } else {
                unset($_SESSION['cart'][$listing_id]);
            }
        }
    } elseif (isset($_POST['remove'])) {
        $remove_id = $_POST['remove'];
        unset($_SESSION['cart'][$remove_id]);
    }
}

$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    require_once 'config/database.php';
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT listing_id, title, price, image_url FROM listings WHERE listing_id IN ($placeholders) AND status = 'approved'");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['listing_id']];
        $subtotal = $product['price'] * $qty;
        $total += $subtotal;
        $cart_items[] = [
            'listing_id' => $product['listing_id'],
            'title' => $product['title'],
            'price' => $product['price'],
            'image_url' => $product['image_url'],
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }
}
?>

<div class="container my-5">
    <h2>
        Shopping Cart
    </h2>

    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">
            Your cart is empty. 
            <a href="buy.php">
                Continue shopping
            </a>.
        </div>
    <?php else: ?>
        <form method="post">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>
                                Product
                            </th>
                            <th>
                                Price
                            </th>
                            <th>
                                Quantity
                            </th>
                            <th>
                                Subtotal
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <?php if ($item['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" width="60" class="me-2 rounded" alt="">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($item['title']); ?>
                            </td>
                            <td>R<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <input type="number" name="qty[<?php echo $item['listing_id']; ?>]" value="<?php echo $item['qty']; ?>" min="1" class="form-control w-50" style="min-width:70px">
                            </td>
                            <td class="fw-bold">R<?php echo number_format($item['subtotal'], 2); ?></td>
                            <td>
                                <button type="submit" name="remove" value="<?php echo $item['listing_id']; ?>" class="btn btn-sm btn-outline-danger">🗑 Remove</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" name="update" class="btn btn-outline-secondary">
                    Update Cart
                </button>
                <div class="text-end">
                    <h4>Total: R<?php echo number_format($total, 2); ?></h4>
                    <a href="checkout.php" class="btn btn-kasi btn-lg">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>