<?php require __DIR__.'/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';

  if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($pass) >= 6) {
    $stmt = db()->prepare('INSERT INTO users (name,email,password_hash) VALUES (?,?,?)');
    try {
      $stmt->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
      header('Location: login.php?registered=1');
      exit;
    } catch (PDOException $e) {
      $error = (str_contains($e->getMessage(), 'Duplicate')) ? 'Email already registered' : 'Error';
    }
  } else {
    $error = 'Fill all fields correctly (password ≥ 6 chars)';
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"><title>Sign Up</title>
  <link rel="stylesheet" href="4.2 biodata.css">
  <style>
    input[type="password"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  outline: none;
  box-sizing: border-box;
  font-size: 16px;
}

input[type="password"]:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}

  </style>
</head>
<body>
<div class="container">
  <h2>Create Account</h2>
  <?php if (!empty($error)): ?><p style="color:red"><?=h($error)?></p><?php endif; ?>
  <form method="post">
    <label>Name *</label>
    <input type="text" name="name" required>
    <label>Email *</label>
    <input type="email" name="email" required>
    <label>Password *</label>
    <input type="password" name="password" required>
    <div class="buttons"><button type="submit">Sign Up</button></div>
    <p>Already have an account? <a href="login.php">Login</a></p>
  </form>
</div>
</body>
</html>
