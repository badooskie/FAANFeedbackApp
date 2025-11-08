<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('HTTP/1.0 401 Unauthorized');
    echo 'Unauthorized';
    exit;
}

require 'db_config.php';

$from = isset($_GET['from']) ? $_GET['from'] : null;
$to = isset($_GET['to']) ? $_GET['to'] : null;

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

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=feedback_export.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['id','rating_cleanliness','rating_staff','rating_security','comment','timestamp']);
foreach ($rows as $r) {
    fputcsv($out, [$r['id'],$r['rating_cleanliness'],$r['rating_staff'],$r['rating_security'],$r['comment'],$r['timestamp']]);
}
fclose($out);
exit;