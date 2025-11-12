<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['admin_id'])) {
  header('Location: admin_login.php');
  exit;
}

try {
  $roleStmt = $pdo->prepare('SELECT role FROM admins WHERE id = :id');
  $roleStmt->execute([':id' => $_SESSION['admin_id']]);
  $roleRow = $roleStmt->fetch(PDO::FETCH_ASSOC);
  if (!$roleRow || strtolower($roleRow['role']) !== 'super') {
    http_response_code(403);
    echo 'Access denied: super administrator role required.';
    exit;
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo 'Failed to verify role.';
  exit;
}

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'create') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $picture = trim($_POST['picture_url'] ?? '');
    $role = trim($_POST['role'] ?? 'admin');
    if ($username === '' || $password === '') {
      $errors[] = 'Username and password are required.';
    } else {
      try {
        $exists = $pdo->prepare('SELECT id FROM admins WHERE username = :u');
        $exists->execute([':u' => $username]);
        if ($exists->fetch()) {
          $errors[] = 'Username already exists.';
        } else {
          $hash = password_hash($password, PASSWORD_DEFAULT);
          $ins = $pdo->prepare('INSERT INTO admins (username, password_hash, first_name, last_name, email, phone, picture_url, role) VALUES (:u, :h, :fn, :ln, :em, :ph, :pu, :role)');
          $ins->execute([':u' => $username, ':h' => $hash, ':fn' => $first ?: null, ':ln' => $last ?: null, ':em' => $email ?: null, ':ph' => $phone ?: null, ':pu' => $picture ?: null, ':role' => $role]);
          $success = 'Admin created successfully.';
        }
      } catch (PDOException $e) {
        $errors[] = 'Error creating admin.';
      }
    }
  } else if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
      $errors[] = 'Invalid admin id.';
    } else {
      if ($id == $_SESSION['admin_id']) {
        $errors[] = 'You cannot delete your own account while logged in.';
      } else {
        try {
          $del = $pdo->prepare('DELETE FROM admins WHERE id = :id');
          $del->execute([':id' => $id]);
          $success = 'Admin deleted.';
        } catch (PDOException $e) {
          $errors[] = 'Error deleting admin.';
        }
      }
    }
  }
}

$admins = [];
try {
$stmt = $pdo->query('SELECT id, username, first_name, last_name, email, phone, picture_url, role, created_at FROM admins ORDER BY created_at DESC');
  $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $errors[] = 'Failed to load admins list.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Admins – NAMA Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="static/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="static/nama-logo.png" alt="NAMA" class="brand-logo me-2" onerror="this.style.display='none'">
        <img src="static/coat-of-arms.png" alt="Nigerian Coat of Arms" class="brand-logo me-2" onerror="this.style.display='none'">
        <span class="brand-gradient">NAMA</span> Admin Tools
      </a>
      <div class="d-flex gap-2 align-items-center">
        <a class="btn btn-outline-light" href="dashboard.php">Dashboard</a>
        <a class="btn btn-outline-light" href="admin_change_password.php">Change Password</a>
        <span class="text-light small ms-2">Logged in as <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
        <a class="btn btn-light" href="admin_logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <div class="row g-3">
      <div class="col-lg-7">
        <div class="card shadow-sm">
          <div class="card-header bg-white"><strong>Admins</strong></div>
          <div class="card-body">
            <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead><tr><th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Created</th><th></th></tr></thead>
                <tbody>
                  <?php foreach ($admins as $a): ?>
                    <tr>
                      <td><?= (int)$a['id'] ?></td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <?php if (!empty($a['picture_url'])): ?>
                            <img src="<?= htmlspecialchars($a['picture_url']) ?>" alt="<?= htmlspecialchars($a['username']) ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;"/>
                          <?php endif; ?>
                          <span><?= htmlspecialchars($a['username']) ?></span>
                        </div>
                      </td>
                      <td><?= htmlspecialchars(trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? ''))) ?></td>
                      <td><?= htmlspecialchars($a['email'] ?? '') ?></td>
                      <td><?= htmlspecialchars($a['phone'] ?? '') ?></td>
                      <td><span class="badge bg-<?php echo (strtolower($a['role'])==='super')?'success':'secondary'; ?>"><?= htmlspecialchars($a['role']) ?></span></td>
                      <td><?= htmlspecialchars($a['created_at']) ?></td>
                      <td>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete admin <?= htmlspecialchars($a['username']) ?>?');">
                          <input type="hidden" name="action" value="delete">
                          <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                          <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card shadow-sm">
          <div class="card-header bg-white"><strong>Create Admin</strong></div>
          <div class="card-body">
            <form method="POST">
              <input type="hidden" name="action" value="create">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="row g-2">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control">
                  </div>
                </div>
              </div>
              <div class="row g-2">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control">
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Picture URL</label>
                <input type="url" name="picture_url" class="form-control" placeholder="https://...">
              </div>
              <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                  <option value="admin" selected>Admin</option>
                  <option value="super">Super Admin</option>
                </select>
              </div>
              <button class="btn btn-primary" type="submit">Add Admin</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="static/app.js"></script>
</body>
</html>
