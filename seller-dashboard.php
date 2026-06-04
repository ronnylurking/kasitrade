<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: login.php');
    exit;
}

$seller_id = $_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $price = $_POST['price'];
        $category_id = $_POST['category_id'] ?? null;
        $listing_id = $_POST['listing_id'] ?? null;

        $image_url = $_POST['existing_image'] ?? '';
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $filename = time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $filename);
            $image_url = $target_dir . $filename;
        }

        if ($listing_id) {
            $stmt = $pdo->prepare("UPDATE listings SET title=?, description=?, price=?, category_id=?, image_url=? WHERE listing_id=? AND seller_id=?");
            $stmt->execute([$title, $description, $price, $category_id, $image_url, $listing_id, $seller_id]);
            $msg = "Listing updated!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO listings (seller_id, title, description, price, category_id, image_url, status) VALUES (?,?,?,?,?,?, 'pending')");
            $stmt->execute([$seller_id, $title, $description, $price, $category_id, $image_url]);
            $msg = "Listing submitted for approval!";
        }
    }
}

if (isset($_GET['delete'])) {
    $delId = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM listings WHERE listing_id=? AND seller_id=?");
    $stmt->execute([$delId, $seller_id]);
    $msg = "Listing removed.";
}

$listings = $pdo->prepare("SELECT l.*, c.name AS category_name FROM listings l LEFT JOIN categories c ON l.category_id = c.category_id WHERE l.seller_id = ? ORDER BY l.created_at DESC");
$listings->execute([$seller_id]);
$allListings = $listings->fetchAll();

$cats = $pdo->query("SELECT * FROM categories")->fetchAll();

$review_query = $pdo->prepare("
    SELECT r.rating, r.comment, r.created_at, l.title, u.full_name AS reviewer
    FROM reviews r
    JOIN listings l ON r.listing_id = l.listing_id
    JOIN users u ON r.user_id = u.user_id
    WHERE l.seller_id = ?
    ORDER BY r.created_at DESC
");
$review_query->execute([$seller_id]);
$seller_reviews = $review_query->fetchAll();

$pageTitle = "My Shop – KasiTrade";
include 'includes/header.php';
?>

<div class="container my-4">
    <h2>
        My Shop
    </h2>
    <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card p-3 text-center">
                <h4>
                    <?php echo count($allListings); ?>
                </h4>
            <p>
                Listings
            </p>
        </div></div>
        <div class="col-md-3"><div class="card p-3 text-center"><h4><?php echo count($seller_reviews); ?></h4><p>Reviews</p></div></div>
    </div>

    <button class="btn btn-kasi mb-3" data-bs-toggle="modal" data-bs-target="#listingModal" onclick="clearForm()">+ Add New Listing</button>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>
                        Image
                    </th>
                    <th>
                        Title
                    </th>
                    <th>
                        Price
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allListings as $item): ?>
                <tr>
                    <td>
                        <?php if ($item['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" width="50" alt="">
                        <?php else: ?>
                            <span class="text-muted">No img</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td>R<?php echo number_format($item['price'], 2); ?></td>
                    <td>
                        <?php
                        $badgeClass = 'bg-secondary';
                        if ($item['status'] === 'approved') $badgeClass = 'bg-success';
                        elseif ($item['status'] === 'pending') $badgeClass = 'bg-warning text-dark';
                        elseif ($item['status'] === 'rejected') $badgeClass = 'bg-danger';
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($item['status']); ?></span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editListing(<?php echo $item['listing_id']; ?>, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($item['description'], ENT_QUOTES); ?>', '<?php echo $item['price']; ?>', '<?php echo $item['category_id']; ?>', '<?php echo htmlspecialchars($item['image_url'] ?? '', ENT_QUOTES); ?>')">
                            Edit
                        </button>
                        <a href="?delete=<?php echo $item['listing_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this listing?')">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (count($allListings) == 0): ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            No listings yet. Add your first product!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <h5>
                Recent Reviews on My Products
            </h5>
        </div>
        <div class="card-body">
            <?php if (count($seller_reviews) > 0): ?>
                <?php foreach (array_slice($seller_reviews, 0, 5) as $rev): ?>
                    <div class="border-bottom mb-2 pb-2">
                        <strong><?php echo htmlspecialchars($rev['reviewer']); ?></strong>
                        on <em><?php echo htmlspecialchars($rev['title']); ?></em>
                        <span class="ms-2">
                            <?php for ($i=1; $i<=5; $i++) echo ($i <= $rev['rating']) ? '⭐' : '☆'; ?>
                        </span>
                        <p class="mb-0 small"><?php echo htmlspecialchars($rev['comment']); ?></p>
                        <small class="text-muted"><?php echo date('d M Y', strtotime($rev['created_at'])); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">
                    No reviews yet.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="listingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        Add New Listing
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="listing_id" id="listingId">
                    <input type="hidden" name="existing_image" id="existingImage">
                    <div class="mb-3">
                        <label>
                            Title
                        </label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>
                            Description
                        </label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>
                                Price (R)
                            </label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>
                                Category
                            </label>
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">
                                    Select
                                </option>
                                <?php foreach ($cats as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>
                            Image
                        </label>
                        <input type="file" name="image" id="image" class="form-control">
                        <small class="text-muted">
                            Leave blank to keep existing image when editing.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" name="action" value="save" class="btn btn-kasi">
                        Save Listing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function clearForm() {
    document.getElementById('listingId').value = '';
    document.getElementById('existingImage').value = '';
    document.getElementById('title').value = '';
    document.getElementById('description').value = '';
    document.getElementById('price').value = '';
    document.getElementById('category_id').value = '';
    document.getElementById('modalTitle').innerText = 'Add New Listing';
}
function editListing(id, title, desc, price, catId, img) {
    document.getElementById('listingId').value = id;
    document.getElementById('existingImage').value = img;
    document.getElementById('title').value = title;
    document.getElementById('description').value = desc;
    document.getElementById('price').value = price;
    document.getElementById('category_id').value = catId;
    document.getElementById('modalTitle').innerText = 'Edit Listing';
    var modal = new bootstrap.Modal(document.getElementById('listingModal'));
    modal.show();
}
</script>

<?php include 'includes/footer.php'; ?>