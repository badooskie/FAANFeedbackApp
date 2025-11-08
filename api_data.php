<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require 'db_config.php';
header('Content-Type: application/json');
$from = isset($_GET['from']) ? $_GET['from'] : null;
$to = isset($_GET['to']) ? $_GET['to'] : null;

try {
    $sql = 'SELECT id, rating_cleanliness, rating_staff, rating_security, comment, timestamp FROM feedback';
    $params = [];
    $clauses = [];
    if ($from) { $clauses[] = 'timestamp >= :from'; $params[':from'] = $from . ' 00:00:00'; }
    if ($to) { $clauses[] = 'timestamp <= :to'; $params[':to'] = $to . ' 23:59:59'; }
    if ($clauses) { $sql .= ' WHERE ' . implode(' AND ', $clauses); }
    $sql .= ' ORDER BY timestamp DESC';

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = count($rows);
    $sumClean = 0; $sumStaff = 0; $sumSec = 0;
    $distOverall = [0, 0, 0, 0, 0];
    $distClean = [0, 0, 0, 0, 0];
    $distStaff = [0, 0, 0, 0, 0];
    $distSec = [0, 0, 0, 0, 0];
    $recentComments = [];
    $dailyCounts = [];
    $dailyAvgOverall = [];

    foreach ($rows as $row) {
        $rc = (int)$row['rating_cleanliness'];
        $rs = (int)$row['rating_staff'];
        $rsec = (int)$row['rating_security'];
        $sumClean += $rc;
        $sumStaff += $rs;
        $sumSec += $rsec;

        $overall = (int)round(($rc + $rs + $rsec) / 3);
        $overall = max(1, min(5, $overall));
        $distOverall[$overall - 1] += 1;
        $distClean[$rc - 1] += 1;
        $distStaff[$rs - 1] += 1;
        $distSec[$rsec - 1] += 1;

        $date = substr($row['timestamp'], 0, 10);
        if (!isset($dailyCounts[$date])) { $dailyCounts[$date] = 0; }
        $dailyCounts[$date] += 1;
        if (!isset($dailyAvgOverall[$date])) { $dailyAvgOverall[$date] = ['sum' => 0, 'n' => 0]; }
        $dailyAvgOverall[$date]['sum'] += $overall;
        $dailyAvgOverall[$date]['n'] += 1;

        if (!empty($row['comment'])) {
            $recentComments[] = [
                'comment' => $row['comment'],
                'timestamp' => $row['timestamp']
            ];
        }
    }

    $avgClean = $total > 0 ? round($sumClean / $total, 2) : 0;
    $avgStaff = $total > 0 ? round($sumStaff / $total, 2) : 0;
    $avgSec = $total > 0 ? round($sumSec / $total, 2) : 0;

    if (count($recentComments) > 50) {
        $recentComments = array_slice($recentComments, 0, 50);
    }

    ksort($dailyCounts);
    ksort($dailyAvgOverall);
    $dailyLabels = array_keys($dailyCounts);
    $dailySeriesCount = array_values($dailyCounts);
    $dailySeriesAvg = array_map(function($v){ return $v['n'] ? round($v['sum'] / $v['n'], 2) : 0; }, array_values($dailyAvgOverall));

    echo json_encode([
        'filters' => [ 'from' => $from, 'to' => $to ],
        'totals' => [ 'feedback' => $total ],
        'averages' => [ 'cleanliness' => $avgClean, 'staff' => $avgStaff, 'security' => $avgSec ],
        'distribution' => [ 'overall' => $distOverall, 'cleanliness' => $distClean, 'staff' => $distStaff, 'security' => $distSec ],
        'daily' => [ 'labels' => $dailyLabels, 'count' => $dailySeriesCount, 'avg_overall' => $dailySeriesAvg ],
        'comments' => $recentComments
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed']);
}
