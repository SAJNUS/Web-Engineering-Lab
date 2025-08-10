<?php
require __DIR__.'/config.php';
require_login();

try {
  $id = (int)($_POST['id'] ?? 0);
  $userId = $_SESSION['user']['id'];

  // Ensure owner
  $chk = db()->prepare('SELECT * FROM biodata WHERE id=? AND user_id=?');
  $chk->execute([$id, $userId]);
  $cur = $chk->fetch();
  if (!$cur) throw new RuntimeException('Record not found');

  $newPhoto = save_upload($_FILES['photo'] ?? null, $userId);
  $photoPath = $newPhoto ?: $cur['photo_path'];

  $hobbies = $_POST['hobbies'] ?? [];
  if (!is_array($hobbies)) $hobbies = [$hobbies];
  $hobbiesStr = implode(', ', array_map('trim', $hobbies));

  $sql = "UPDATE biodata SET
    fullname=?, father=?, mother=?, dob=?, gender=?, address=?, phone=?, email=?, linkedin=?, github=?,
    education=?, skills=?, languages=?, marital_status=?, hobbies=?, blood=?, website=?, photo_path=?
    WHERE id=? AND user_id=?";
  $stmt = db()->prepare($sql);
  $stmt->execute([
    trim($_POST['fullname'] ?? ''),
    trim($_POST['father'] ?? ''),
    trim($_POST['mother'] ?? ''),
    $_POST['dob'] ?: null,
    $_POST['gender'] ?? null,
    trim($_POST['address'] ?? ''),
    trim($_POST['phone'] ?? ''),
    trim($_POST['email'] ?? ''),
    trim($_POST['linkedin'] ?? ''),
    trim($_POST['github'] ?? ''),
    trim($_POST['education'] ?? ''),
    trim($_POST['skills'] ?? ''),
    trim($_POST['languages'] ?? ''),
    $_POST['marital'] ?? null,
    $hobbiesStr,
    $_POST['blood'] ?? null,
    trim($_POST['website'] ?? ''),
    $photoPath,
    $id, $userId
  ]);

  header('Location: index.php?updated=1');
} catch (Throwable $e) {
  http_response_code(400);
  echo "Error: " . h($e->getMessage());
}
