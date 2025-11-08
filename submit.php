<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

require 'db_config.php';

$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
$rc = isset($_POST['rating_cleanliness']) ? (int)$_POST['rating_cleanliness'] : null;
$rs = isset($_POST['rating_staff']) ? (int)$_POST['rating_staff'] : null;
$rsec = isset($_POST['rating_security']) ? (int)$_POST['rating_security'] : null;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : null;

$valid = function ($r) { return $r !== null && $r >= 1 && $r <= 5; };
if (!$valid($rc) || !$valid($rs) || !$valid($rsec)) {
    http_response_code(400);
    echo 'Invalid ratings. Please select a value from 1 to 5.';
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO feedback (name, email, phone, rating_cleanliness, rating_staff, rating_security, comment) VALUES (:name, :email, :phone, :rc, :rs, :rsec, :comment)');
    $stmt->bindValue(':name', $name !== '' ? $name : null, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email !== '' ? $email : null, PDO::PARAM_STR);
    $stmt->bindValue(':phone', $phone !== '' ? $phone : null, PDO::PARAM_STR);
    $stmt->bindValue(':rc', $rc, PDO::PARAM_INT);
    $stmt->bindValue(':rs', $rs, PDO::PARAM_INT);
    $stmt->bindValue(':rsec', $rsec, PDO::PARAM_INT);
    $stmt->bindValue(':comment', $comment !== '' ? $comment : null, PDO::PARAM_STR);
    $stmt->execute();
    header('Location: thanks.php');
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Failed to submit feedback.';
    exit;
}
