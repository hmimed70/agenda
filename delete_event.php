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

$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT e.*, a.user_id as agenda_owner_id
    FROM events e
    JOIN agendas a ON e.agenda_id = a.id
    WHERE e.id = :event_id
    AND a.user_id = :user_id
");
$stmt->execute([
    'event_id' => $event_id,
    'user_id' => $user_id
]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header('Location: index.php');
    exit;
}

$agenda_id = $event['agenda_id'];

$stmt = $pdo->prepare("DELETE FROM events WHERE id = :event_id");
$stmt->execute(['event_id' => $event_id]);

header('Location: view_agenda.php?id=' . $agenda_id);
exit;
