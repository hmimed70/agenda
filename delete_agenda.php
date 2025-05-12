<?php
session_start();
require_once 'db/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$agenda_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM agendas WHERE id = :agenda_id AND user_id = :user_id");
$stmt->execute([
    'agenda_id' => $agenda_id,
    'user_id' => $user_id
]);
$agenda = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agenda) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM agenda_shares WHERE agenda_id = :agenda_id");
$stmt->execute(['agenda_id' => $agenda_id]);

$stmt = $pdo->prepare("DELETE FROM events WHERE agenda_id = :agenda_id");
$stmt->execute(['agenda_id' => $agenda_id]);

$stmt = $pdo->prepare("DELETE FROM agendas WHERE id = :agenda_id");
$stmt->execute(['agenda_id' => $agenda_id]);

header('Location: index.php');
exit;
