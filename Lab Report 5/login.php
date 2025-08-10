<?php require __DIR__.'/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
  $stmt->execute([$email]);
  $user = $stmt->fetch();
  if ($user && password_verify($pass, $user['password_hash'])) {
    $_SESSION['user'] = ['id'=>$user['id'], 'name'=>$user['name'], 'email'=>$user['email']];
    header('Location: index.php');
    exit;
  } else {
    $error = 'Invalid credentials';
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"><title>Login</title>
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
  <h2>Login</h2>
  <?php if (isset($_GET['registered'])): ?><p style="color:green">Registered! Please login.</p><?php endif; ?>
  <?php if (!empty($error)): ?><p style="color:red"><?=h($error)?></p><?php endif; ?>
  <form method="post">
    <label>Email *</label>
    <input type="email" name="email" required>
    <label>Password *</label>
    <input type="password" name="password" required>
    <div class="buttons"><button type="submit">Login</button></div>
    <p>No account? <a href="register.php">Sign Up</a></p>
  </form>
</div>
</body>
</html>
