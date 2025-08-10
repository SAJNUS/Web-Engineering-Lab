<?php
require __DIR__.'/config.php';
require_login();
$userId = $_SESSION['user']['id'];

$stmt = db()->prepare('SELECT * FROM biodata WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$userId]);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"><title>My Biodata</title>
  <link rel="stylesheet" href="4.2 biodata.css">
</head>
<body>
<div class="container">
  <h2>Hello, <?=h($_SESSION['user']['name'])?> 👋</h2>
  <?php if (isset($_GET['created'])): ?><p style="color:green">Created!</p><?php endif; ?>
  <?php if (isset($_GET['updated'])): ?><p style="color:green">Updated!</p><?php endif; ?>
  <?php if (isset($_GET['deleted'])): ?><p style="color:green">Deleted!</p><?php endif; ?>

  <div class="buttons">
    <a href="biodata_form.php"><button>Add New Biodata</button></a>
    <a href="logout.php"><button class="reset">Logout</button></a>
  </div>

  <?php if (!$rows): ?>
    <p>No biodata yet. Create one!</p>
  <?php else: ?>
    <?php foreach ($rows as $r): ?>
      <div style="border:1px solid #ddd;border-radius:8px;padding:12px;margin:12px 0;">
        <div style="display:flex;gap:12px;align-items:center;">
          <?php if ($r['photo_path']): ?>
            <img src="<?=h($r['photo_path'])?>" alt="photo" style="width:70px;height:70px;border-radius:50%;object-fit:cover;">
          <?php endif; ?>
          <div>
            <strong><?=h($r['fullname'])?></strong><br>
            <small><?=h($r['email'])?> · <?=h($r['phone'])?></small>
          </div>
        </div>
        <div class="buttons">
          <a href="edit_biodata.php?id=<?= (int)$r['id'] ?>"><button>Edit</button></a>
          <a href="delete_biodata.php?id=<?= (int)$r['id'] ?>" onclick="return confirm('Delete this record?')"><button class="reset">Delete</button></a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
</body>
</html>
