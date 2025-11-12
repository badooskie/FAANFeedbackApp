<?php
session_start();
require 'config.php';
require 'db_config.php';

if (!defined('ALLOW_ADMIN_SIGNUP') || !ALLOW_ADMIN_SIGNUP) {
    header('Location: admin_login.php?signup_disabled=1');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pictureUrl = trim($_POST['picture_url'] ?? '');
    if ($username === '' || $password === '') {
        $errors[] = 'Username and password are required.';
    } else {
        try {
            $check = $pdo->prepare('SELECT id FROM admins WHERE username = :u');
            $check->execute([':u' => $username]);
            if ($check->fetch()) {
                $errors[] = 'Username already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $count = $pdo->query('SELECT COUNT(*) AS c FROM admins')->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;
                $role = ($count == 0) ? 'super' : 'admin';
                $ins = $pdo->prepare('INSERT INTO admins (username, password_hash, first_name, last_name, email, phone, picture_url, role) VALUES (:u, :h, :fn, :ln, :em, :ph, :pu, :role)');
                $ins->execute([
                  ':u' => $username,
                  ':h' => $hash,
                  ':fn' => $firstName ?: null,
                  ':ln' => $lastName ?: null,
                  ':em' => $email ?: null,
                  ':ph' => $phone ?: null,
                  ':pu' => $pictureUrl ?: null,
                  ':role' => $role
                ]);
                header('Location: admin_login.php?registered=1');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error creating admin.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Sign Up – NAMA Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="static/styles.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h3 class="mb-3 d-flex align-items-center"><img src="static/nama-logo.png" alt="NAMA" class="brand-logo me-2" onerror="this.style.display='none'"> Admin Sign Up</h3>
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
              <div class="d-flex justify-content-between">
                <a href="admin_login.php" class="btn btn-outline-secondary">Back to Login</a>
                <button type="submit" class="btn btn-primary">Create Account</button>
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
