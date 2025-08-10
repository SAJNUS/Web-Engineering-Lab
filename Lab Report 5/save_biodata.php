<?php
require __DIR__.'/config.php';
require_login();

try {
  $userId = $_SESSION['user']['id'];
  $photoPath = save_upload($_FILES['photo'] ?? null, $userId);

  $hobbies = $_POST['hobbies'] ?? [];
  if (!is_array($hobbies)) $hobbies = [$hobbies];
  $hobbiesStr = implode(', ', array_map('trim', $hobbies));

  $sql = "INSERT INTO biodata
    (user_id, fullname, father, mother, dob, gender, address, phone, email, linkedin, github,
     education, skills, languages, marital_status, hobbies, blood, website, photo_path)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
  $stmt = db()->prepare($sql);
  $stmt->execute([
    $userId,
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
    $photoPath
  ]);

  header('Location: index.php?created=1');
} catch (Throwable $e) {
  http_response_code(400);
  echo "Error: " . h($e->getMessage());
}
