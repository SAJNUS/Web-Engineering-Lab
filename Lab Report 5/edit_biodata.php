<?php
require __DIR__.'/config.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM biodata WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user']['id']]);
$bd = $stmt->fetch();
if (!$bd) { http_response_code(404); echo "Not found"; exit; }

$hobbiesArr = array_filter(array_map('trim', explode(',', $bd['hobbies'] ?? '')));
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"><title>Edit Biodata</title>
  <link rel="stylesheet" href="4.2 biodata.css">
</head>
<body>
<div class="container">
  <h2>Edit Biodata</h2>
  <form action="update_biodata.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$bd['id'] ?>">
    <?php if ($bd['photo_path']): ?>
      <div class="profile-pic">
        <div class="avatar"><img src="<?=h($bd['photo_path'])?>" alt="current"></div>
      </div>
    <?php endif; ?>
    <label>Change Photo (optional)</label>
    <input type="file" name="photo" accept="image/*">

    <label>Full Name *</label>
    <input type="text" name="fullname" value="<?=h($bd['fullname'])?>" required>

    <label>Father's Name</label>
    <input type="text" name="father" value="<?=h($bd['father'])?>">

    <label>Mother's Name</label>
    <input type="text" name="mother" value="<?=h($bd['mother'])?>">

    <label>Date of Birth</label>
    <input type="date" name="dob" value="<?=h($bd['dob'])?>">

    <label>Gender</label>
    <div class="radio-group">
      <?php foreach (['Male','Female','Other'] as $g): ?>
        <label><input type="radio" name="gender" value="<?=$g?>" <?= $bd['gender']===$g?'checked':''; ?>> <?=$g?></label>
      <?php endforeach; ?>
    </div>

    <label>Address</label>
    <textarea name="address"><?=h($bd['address'])?></textarea>

    <label>Phone Number</label>
    <input type="tel" name="phone" value="<?=h($bd['phone'])?>">

    <label>Email</label>
    <input type="email" name="email" value="<?=h($bd['email'])?>">

    <label>LinkedIn</label>
    <input type="url" name="linkedin" value="<?=h($bd['linkedin'])?>">

    <label>GitHub</label>
    <input type="url" name="github" value="<?=h($bd['github'])?>">

    <label>Education</label>
    <textarea name="education"><?=h($bd['education'])?></textarea>

    <label>Skills</label>
    <textarea name="skills"><?=h($bd['skills'])?></textarea>

    <label>Languages Known</label>
    <input type="text" name="languages" value="<?=h($bd['languages'])?>">

    <label>Marital Status</label>
    <div class="radio-group">
      <?php foreach (['Married','Unmarried'] as $m): ?>
        <label><input type="radio" name="marital" value="<?=$m?>" <?= $bd['marital_status']===$m?'checked':''; ?>> <?=$m?></label>
      <?php endforeach; ?>
    </div>

    <label>Hobbies</label>
    <div class="checkbox-group">
      <?php
        $hlist = ['Reading','Writing','Traveling','Gaming','Cooking','Music','Sports','Drawing','Photography','Blogging'];
        foreach ($hlist as $h) {
          $chk = in_array($h, $hobbiesArr) ? 'checked' : '';
          echo '<label><input type="checkbox" name="hobbies[]" value="'.h($h).'" '.$chk.'> '.h($h).'</label>';
        }
      ?>
    </div>

    <label>Blood Group</label>
    <select name="blood">
      <option value="">Select Blood Group</option>
      <?php foreach (['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $b): ?>
        <option <?= $bd['blood']===$b?'selected':''; ?>><?=h($b)?></option>
      <?php endforeach; ?>
    </select>

    <label>Personal Website</label>
    <input type="url" name="website" value="<?=h($bd['website'])?>">

    <div class="buttons">
      <button type="submit">Update</button>
      <a href="index.php"><button class="reset" type="button">Cancel</button></a>
    </div>
  </form>
</div>
</body>
</html>
