<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Give Feedback – NAMA Passenger Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="static/styles.css" rel="stylesheet">
  <style>
    .star-row { direction: rtl; margin-top: 0; }
    .star-row input { display:none; }
    .star-row label { font-size: 1.5rem; color:#c7ced6; cursor:pointer; padding: 0 .08rem; line-height: 1; }
    .star-row label:hover,
    .star-row label:hover ~ label { color:#f5c518; }
    .star-row input:checked ~ label { color:#f5c518; }
    .rating-block label.form-label { margin-bottom: .25rem; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="static/nama-logo.png" alt="NAMA" class="brand-logo me-2" onerror="this.style.display='none'">
        <img src="static/coat-of-arms.png" alt="Nigerian Coat of Arms" class="brand-logo me-2" onerror="this.style.display='none'">
        <span class="brand-gradient">NAMA</span> Passenger Feedback
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div id="nav" class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="feedback.php">Give Feedback</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-lg-7 col-xl-6">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h3 class="h4 mb-2">Share Your Experience</h3>
            <p class="text-muted mb-3">Your feedback helps NAMA improve services across Nigeria.</p>
            <form action="submit.php" method="POST">
              <div class="row g-2 mb-3">
                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="text" id="fb_name" name="name" class="form-control" placeholder="Your full name" required>
                    <label for="fb_name">Name</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="email" id="fb_email" name="email" class="form-control" placeholder="you@example.com" required>
                    <label for="fb_email">Email</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="tel" id="fb_phone" name="phone" class="form-control" placeholder="+234 801 234 5678" pattern="[+0-9\s-]{7,20}" required>
                    <label for="fb_phone">Phone</label>
                  </div>
                </div>
              </div>

              <div class="row g-2 mb-2">
                <div class="col-md-4 rating-block">
                  <label class="form-label small fw-semibold mb-1">Cleanliness</label>
                  <div class="star-row" aria-label="Rate cleanliness 1 to 5">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                      <input type="radio" name="rating_cleanliness" id="cleanliness<?= $i ?>" value="<?= $i ?>" <?= $i === 1 ? 'required' : '' ?>>
                      <label for="cleanliness<?= $i ?>" title="<?= $i ?> star<?= $i>1?'s':'' ?>">★</label>
                    <?php endfor; ?>
                  </div>
                </div>
                <div class="col-md-4 rating-block">
                  <label class="form-label small fw-semibold mb-1">Staff</label>
                  <div class="star-row" aria-label="Rate staff 1 to 5">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                      <input type="radio" name="rating_staff" id="staff<?= $i ?>" value="<?= $i ?>" <?= $i === 1 ? 'required' : '' ?>>
                      <label for="staff<?= $i ?>" title="<?= $i ?> star<?= $i>1?'s':'' ?>">★</label>
                    <?php endfor; ?>
                  </div>
                </div>
                <div class="col-md-4 rating-block">
                  <label class="form-label small fw-semibold mb-1">Security</label>
                  <div class="star-row" aria-label="Rate security 1 to 5">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                      <input type="radio" name="rating_security" id="security<?= $i ?>" value="<?= $i ?>" <?= $i === 1 ? 'required' : '' ?>>
                      <label for="security<?= $i ?>" title="<?= $i ?> star<?= $i>1?'s':'' ?>">★</label>
                    <?php endfor; ?>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <div class="form-floating">
                  <textarea id="comment" name="comment" class="form-control" placeholder="Leave a comment" style="height: 120px"></textarea>
                  <label for="comment">Comments (optional)</label>
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer border-top bg-white">
    <div class="container d-flex justify-content-between align-items-center py-2">
      <div class="d-flex align-items-center">
        <img src="static/nama-logo.png" alt="NAMA" class="brand-logo me-2" onerror="this.style.display='none'">
        <small>© <?php echo date('Y'); ?> Nigerian Airspace Management Agency</small>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="static/app.js"></script>
</body>
</html>
