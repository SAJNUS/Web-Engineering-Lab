<?php require __DIR__.'/config.php'; require_login(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Biodata</title>
  <link rel="stylesheet" href="4.2 biodata.css">
  <style>
    .avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>BIODATA</h2>

    <form action="save_biodata.php" method="post" enctype="multipart/form-data">
      <div class="profile-pic">
        <label>Profile Picture</label><br>
        <div class="avatar">
          <img id="preview" src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="profile preview">
        </div>
        <input type="file" name="photo" accept="image/*">
      </div>

      <label>Full Name *</label>
      <input type="text" name="fullname" placeholder="Enter your full name" required>

      <label>Father's Name</label>
      <input type="text" name="father" placeholder="Enter your father's name">

      <label>Mother's Name</label>
      <input type="text" name="mother" placeholder="Enter your mother's name">

      <label>Date of Birth</label>
      <input type="date" name="dob">

      <label>Gender</label>
      <div class="radio-group">
        <label><input type="radio" name="gender" value="Male"> Male</label>
        <label><input type="radio" name="gender" value="Female"> Female</label>
        <label><input type="radio" name="gender" value="Other"> Other</label>
      </div>

      <label>Address</label>
      <textarea name="address" placeholder="Enter your address"></textarea>

      <label>Phone Number</label>
      <input type="tel" name="phone" placeholder="Enter your phone number">

      <label>Email</label>
      <input type="email" name="email" placeholder="Enter your email address">

      <label>LinkedIn</label>
      <input type="url" name="linkedin" placeholder="Enter your LinkedIn profile URL">

      <label>GitHub</label>
      <input type="url" name="github" placeholder="Enter your GitHub profile URL">

      <label>Education</label>
      <textarea name="education" placeholder="Enter your educational qualifications"></textarea>

      <label>Skills</label>
      <textarea name="skills" placeholder="Enter your skills"></textarea>

      <label>Languages Known</label>
      <input type="text" name="languages" placeholder="Enter languages you know">

      <label>Marital Status</label>
      <div class="radio-group">
        <label><input type="radio" name="marital" value="Married"> Married</label>
        <label><input type="radio" name="marital" value="Unmarried"> Unmarried</label>
      </div>

      <label>Hobbies</label>
      <div class="checkbox-group">
        <?php
          $hlist = ['Reading','Writing','Traveling','Gaming','Cooking','Music','Sports','Drawing','Photography','Blogging'];
          foreach ($hlist as $h) {
            echo '<label><input type="checkbox" name="hobbies[]" value="'.h($h).'"> '.h($h).'</label>';
          }
        ?>
      </div>

      <label>Blood Group</label>
      <select name="blood">
        <option value="">Select Blood Group</option>
        <?php foreach (['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $b): ?>
          <option><?=h($b)?></option>
        <?php endforeach; ?>
      </select>

      <label>Personal Website</label>
      <input type="url" name="website" placeholder="Enter your website URL">

      <div class="buttons">
        <button type="submit">Submit Bio Data</button>
        <button type="reset" class="reset">Reset Form</button>
      </div>
    </form>
  </div>

  <script>
    document.querySelector('input[name="photo"]').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        document.getElementById('preview').src = URL.createObjectURL(file);
      }
    });
  </script>
</body>
</html>
