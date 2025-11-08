<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - FAAN Passenger Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link href="static/styles.css" rel="stylesheet">
 </head>
 <body>
   <div class="dashboard-layout">
     <aside class="sidebar">
       <div class="d-flex align-items-center mb-3">
         <img src="static/faan-logo.png" alt="FAAN" class="brand-logo me-2" onerror="this.style.display='none'">
         <span> <img src="/static/Faan.logo_.png" alt="" width="100px"></span>
         <span class="ms-2">Feedback Admin</span>
       </div>
       <div class="small mb-3">Welcome, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></div>
       <nav class="nav flex-column gap-1">
         <a class="nav-link active" href="dashboard.php">Dashboard</a>
         <a class="nav-link" href="feedback.php">Give Feedback</a>
         <a class="nav-link" href="index.php">Home</a>
         <?php if ((isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super')): ?>
           <a class="nav-link" href="admin_manage.php">Manage Admins</a>
         <?php endif; ?>
         <a class="nav-link" href="admin_change_password.php">Change Password</a>
         <a class="nav-link" href="admin_logout.php">Logout</a>
       </nav>
     </aside>
     <main class="dashboard-content">
       <div class="container-fluid py-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-body filter-group">
                <div class="row g-2 align-items-end">
                    <div class="col-sm-3">
                        <label for="fromDate" class="form-label">From</label>
                        <input type="date" id="fromDate" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label for="toDate" class="form-label">To</label>
                        <input type="date" id="toDate" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Category</label>
                        <select id="categorySelect" class="form-select">
                            <option value="overall" selected>Overall</option>
                            <option value="cleanliness">Cleanliness</option>
                            <option value="staff">Staff</option>
                            <option value="security">Security</option>
                        </select>
                    </div>
                    <div class="col-sm-3 d-flex gap-2">
                        <button id="applyFilters" class="btn btn-primary">Apply Filters</button>
                        <a id="exportCsv" class="btn btn-outline-primary" href="#">Export CSV</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <div class="text-muted">Total Feedback</div>
                        <div id="totalFeedback" class="display-6">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <div class="text-muted">Avg. Cleanliness</div>
                        <div id="avgCleanliness" class="display-6">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <div class="text-muted">Avg. Staff</div>
                        <div id="avgStaff" class="display-6">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <div class="text-muted">Avg. Security</div>
                        <div id="avgSecurity" class="display-6">0</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-7">
                <div class="card shadow-sm chart-card">
                    <div class="card-header bg-white">
                        <strong>Ratings Distribution</strong>
                    </div>
                    <div class="card-body">
                        <canvas id="ratingsChart" height="160"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Recent Comments</strong>
                            <input id="commentSearch" type="text" class="form-control form-control-sm" placeholder="Search comments" style="max-width: 200px;" />
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="commentsTable">
                                <thead>
                                    <tr>
                                        <th scope="col">Comment</th>
                                        <th scope="col">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm chart-card">
                    <div class="card-header bg-white">
                        <strong>Daily Feedback Trend</strong>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyTrendChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>
       </div>
     </main>
   </div>

<script src="static/dashboard.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="static/app.js"></script>
</body>
</html>
