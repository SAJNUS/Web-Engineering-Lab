<?php
require __DIR__.'/config.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('DELETE FROM biodata WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user']['id']]);
header('Location: index.php?deleted=1');
