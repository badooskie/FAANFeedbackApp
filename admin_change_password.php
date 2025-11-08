<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['admin_id'])) {
  header('Location: admin_login.php');
  exit;
}

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $current = trim($_POST['current'] ?? '');
  $new = trim($_POST['new'] ?? '');
  $confirm = trim($_POST['confirm'] ?? '');
  if ($new === '' || $confirm === '') {
    $errors[] = 'New password and confirmation are required.';
  } elseif ($new !== $confirm) {
    $errors[] = 'New password and confirmation do not match.';
  } else {
    try {
      $stmt = $pdo->prepare('SELECT password_hash FROM admins WHERE id = :id');
      $stmt->execute([':id' => $_SESSION['admin_id']]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$row || !password_verify($current, $row['password_hash'])) {
        $errors[] = 'Current password is incorrect.';
      } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $upd = $pdo->prepare('UPDATE admins SET password_hash = :h WHERE id = :id');
        $upd->execute([':h' => $hash, ':id' => $_SESSION['admin_id']]);
        $success = 'Password updated successfully.';
      }
    } catch (PDOException $e) {
      $errors[] = 'Error updating password.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Change Password – FAAN Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="static/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="static/faan-logo.png" alt="FAAN" class="brand-logo me-2" onerror="this.style.display='none'">
        <span class="brand-gradient">FAAN</span> Admin Tools
      </a>
      <div class="d-flex gap-2 align-items-center">
        <a class="btn btn-outline-light" href="dashboard.php">Dashboard</a>
        <a class="btn btn-outline-light" href="admin_manage.php">Manage Admins</a>
        <span class="text-light small ms-2">Logged in as <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
        <a class="btn btn-light" href="admin_logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-white"><strong>Change Password</strong></div>
          <div class="card-body">
            <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" name="current" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="new" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary">Update Password</button>
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
