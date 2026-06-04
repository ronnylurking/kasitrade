<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header('Location: login.php');
    exit;
}

$buyer_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT o.order_id, o.total_amount, o.status, o.delivery_address, o.created_at,
           oi.listing_id, oi.quantity, oi.unit_price,
           l.title, l.image_url
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN listings l ON oi.listing_id = l.listing_id
    WHERE o.buyer_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$buyer_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$orders = [];
foreach ($rows as $row) {
    $oid = $row['order_id'];
    if (!isset($orders[$oid])) {
        $orders[$oid] = [
            'order_id' => $oid,
            'total_amount' => $row['total_amount'],
            'status' => $row['status'],
            'delivery_address' => $row['delivery_address'],
            'created_at' => $row['created_at'],
            'items' => []
        ];
    }
    $orders[$oid]['items'][] = [
        'title' => $row['title'],
        'quantity' => $row['quantity'],
        'unit_price' => $row['unit_price'],
        'image_url' => $row['image_url']
    ];
}

$pageTitle = "My Orders – KasiTrade";
include 'includes/header.php';
?>

<div class="container my-5">
    <h2>My Orders</h2>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            You haven't placed any orders yet.
            <a href="buy.php">
                Start shopping
            </a>.
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <strong>
                            Order #<?php echo $order['order_id']; ?>
                        </strong>
                        <span class="badge bg-<?php echo $order['status'] == 'delivered' ? 'success' : 'warning'; ?> ms-2">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small class="text-muted"><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>
                                    Product
                                </th>
                                <th>
                                    Price
                                </th>
                                <th>
                                    Qty
                                </th>
                                <th>
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td>
                                    <?php if ($item['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" width="40" class="me-2">
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </td>
                                <td>R<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>R<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end">
                    <strong>Total: R<?php echo number_format($order['total_amount'], 2); ?></strong>
                </div>
                <div class="mt-2">
                    <small class="text-muted">Delivery to: <?php echo htmlspecialchars($order['delivery_address']); ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>