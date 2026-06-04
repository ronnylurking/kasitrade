<?php
session_start();
require_once 'config/database.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$max_price = $_GET['max_price'] ?? 5000;

$query = "SELECT l.*, u.full_name AS seller_name, u.township, c.name AS category_name 
          FROM listings l 
          JOIN users u ON l.seller_id = u.user_id 
          LEFT JOIN categories c ON l.category_id = c.category_id 
          WHERE l.status = 'approved'";
$params = [];

if (!empty($search)) {
    $query .= " AND l.title LIKE :search";
    $params['search'] = "%$search%";
}
if (!empty($category)) {
    $query .= " AND c.slug = :category";
    $params['category'] = $category;
}
if ($max_price) {
    $query .= " AND l.price <= :max_price";
    $params['max_price'] = $max_price;
}
$query .= " ORDER BY l.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$listings = $stmt->fetchAll();

$cats = $pdo->query("SELECT * FROM categories")->fetchAll();

$pageTitle = "Browse Products – KasiTrade";
include 'includes/header.php';
?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-3">
            <h5>
                Filter by
            </h5>
            <form method="get" action="buy.php">
                <div class="mb-3">
                    <label class="form-label">
                        Search
                    </label>
                    <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Product name...">
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        Category
                    </label>
                    <select name="category" class="form-select">
                        <option value="">
                            All
                        </option>
                        <?php foreach ($cats as $cat): ?>
                            <option value="<?php echo $cat['slug']; ?>" <?php echo $category === $cat['slug'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Max Price: R<?php echo $max_price; ?></label>
                    <input type="range" class="form-range" min="0" max="5000" step="50" name="max_price" value="<?php echo $max_price; ?>" onchange="this.nextElementSibling.value=this.value">
                    <output><?php echo $max_price; ?></output>
                </div>
                <button type="submit" class="btn btn-outline-success w-100">
                    Apply Filters
                </button>
            </form>
        </div>

        <div class="col-md-9">
            <h2>
                Products <?php echo $category ? ' - ' . htmlspecialchars($category) : ''; ?>
            </h2>
            <div class="row">
                <?php if (count($listings) > 0): ?>
                    <?php foreach ($listings as $item): ?>
                    <div class="col-6 col-lg-4 mb-4">
                        <div class="card listing-card h-100">
                            <?php if ($item['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>" style="height:180px; object-fit:cover;">
                            <?php else: ?>
                                <div class="bg-light text-center py-5">
                                    No Image
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h6>
                                <p class="card-text text-success fw-bold">R<?php echo number_format($item['price'], 2); ?></p>
                                <p class="small text-muted"><?php echo htmlspecialchars($item['township'] ?? 'Unknown'); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-warning text-dark">
                                        ★ New
                                    </span>
                                    <a href="product.php?id=<?php echo $item['listing_id']; ?>" class="btn btn-sm btn-outline-success">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-muted">
                            No approved listings match your filters.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>