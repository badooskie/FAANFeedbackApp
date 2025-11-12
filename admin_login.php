<?php
session_start();
require 'db_config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $errors[] = 'Username and password are required.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, username, password_hash, role FROM admins WHERE username = :u');
            $stmt->execute([':u' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
                header('Location: dashboard.php');
                exit;
            } else {
                $errors[] = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error logging in.';
        }
    }
}
$registered = isset($_GET['registered']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login – NAMA Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="static/styles.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h3 class="mb-3 d-flex align-items-center">
              <img src="static/nama-logo.png" alt="NAMA" class="brand-logo me-2" onerror="this.style.display='none'"> Admin Login
            </h3>
            <?php if ($registered): ?>
              <div class="alert alert-success">Registration successful. Please log in.</div>
            <?php endif; ?>
            <?php if ($errors): ?>
              <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
            <?php endif; ?>
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
                <div>
                  <a href="admin_signup.php" class="btn btn-outline-primary me-2">Sign Up</a>
                  <button type="submit" class="btn btn-primary">Login</button>
                </div>
              </div>
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
