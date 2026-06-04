<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
require_once '../config/database.php';

$pageTitle = "Manage Users – KasiTrade";
include '../includes/header.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'] ?? '';

    if ($user_id) {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, role=?, password_hash=? WHERE user_id=?");
            $stmt->execute([$full_name, $email, $role, $hash, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, role=? WHERE user_id=?");
            $stmt->execute([$full_name, $email, $role, $user_id]);
        }
        $msg = "User updated.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?,?,?,?)");
        $stmt->execute([$full_name, $email, $hash, $role]);
        $msg = "User created.";
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?");
    $stmt->execute([$_GET['delete']]);
    $msg = "User deactivated.";
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 bg-dark text-white sidebar" id="adminSidebar">
            <h4 class="p-3">
                Admin
            </h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin/index.php">
                        📊 Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin/users.php">
                        👥 Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin/listings.php">
                        📋 Listings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="logout.php">
                        🚪 Logout
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h2>
                Manage Users
            </h2>
            <?php if ($msg): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#userModal" onclick="clearForm()">
                + Add User
            </button>

            <table class="table table-striped table-responsive">
                <thead>
                    <tr>
                        <th>
                            Name
                        </th>
                        <th>
                            Email
                        </th>
                        <th>
                            Role
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
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><span class="badge bg-info"><?php echo $u['role']; ?></span></td>
                        <td><?php echo $u['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editUser(<?php echo $u['user_id']; ?>, '<?php echo htmlspecialchars($u['full_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($u['email'], ENT_QUOTES); ?>', '<?php echo $u['role']; ?>')">
                                Edit
                            </button>
                            <a href="admin/users.php?delete=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deactivate this user?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="admin/users.php">
                <div class="modal-header">
                    <h5 class="modal-title">
                        User Form
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="userId">
                    <div class="mb-3">
                        <label>
                            Full Name
                        </label>
                        <input type="text" name="full_name" id="fullName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>
                            Email
                        </label>
                        <input type="email" name="email" id="userEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>
                            Role
                        </label>
                        <select name="role" id="userRole" class="form-control">
                            <option value="buyer">
                                Buyer
                            </option>
                            <option value="seller">
                                Seller
                            </option>
                            <option value="moderator">
                                Moderator
                            </option>
                            <option value="dispatcher">
                                Dispatcher
                            </option>
                            <option value="admin">
                                Admin
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>
                            Password (leave blank to keep unchanged when editing)
                        </label>
                        <input type="password" name="password" id="userPassword" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function clearForm() {
    document.getElementById('userId').value = '';
    document.getElementById('fullName').value = '';
    document.getElementById('userEmail').value = '';
    document.getElementById('userRole').value = 'buyer';
    document.getElementById('userPassword').value = '';
}
function editUser(id, name, email, role) {
    document.getElementById('userId').value = id;
    document.getElementById('fullName').value = name;
    document.getElementById('userEmail').value = email;
    document.getElementById('userRole').value = role;
    document.getElementById('userPassword').value = '';
    var modal = new bootstrap.Modal(document.getElementById('userModal'));
    modal.show();
}
</script>

<?php include '../includes/footer.php'; ?>