<?php
require_once 'config/database.php';

// Initialize database silently
initializeDatabase();

// Check if user is logged in
requireLogin();

$conn = getDBConnection();
$userId = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Handle form submission (Create/Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = sanitizeInput($_POST['fullname']);
    $fatherName = sanitizeInput($_POST['father']);
    $motherName = sanitizeInput($_POST['mother']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'] ?? '';
    $address = sanitizeInput($_POST['address']);
    $phone = sanitizeInput($_POST['phone']);
    $email = sanitizeInput($_POST['email']);
    $linkedin = sanitizeInput($_POST['linkedin']);
    $github = sanitizeInput($_POST['github']);
    $education = sanitizeInput($_POST['education']);
    $skills = sanitizeInput($_POST['skills']);
    $languages = sanitizeInput($_POST['languages']);
    $maritalStatus = isset($_POST['marital']) ? implode(', ', $_POST['marital']) : '';
    $hobbies = isset($_POST['hobbies']) ? implode(', ', $_POST['hobbies']) : '';
    $bloodGroup = $_POST['blood'];
    $website = sanitizeInput($_POST['website']);
    
    // Handle file upload
    $profilePicture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES["profile_picture"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
                $profilePicture = $targetFile;
            }
        }
    }
    
    // Check if biodata already exists for this user
    $stmt = $conn->prepare("SELECT id FROM biodata WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing biodata
        $biodataId = $result->fetch_assoc()['id'];
        
        if (!empty($profilePicture)) {
            $stmt = $conn->prepare("UPDATE biodata SET full_name=?, father_name=?, mother_name=?, date_of_birth=?, gender=?, address=?, phone=?, email=?, linkedin=?, github=?, education=?, skills=?, languages=?, marital_status=?, hobbies=?, blood_group=?, website=?, profile_picture=? WHERE user_id=?");
            $stmt->bind_param("ssssssssssssssssssi", $fullName, $fatherName, $motherName, $dob, $gender, $address, $phone, $email, $linkedin, $github, $education, $skills, $languages, $maritalStatus, $hobbies, $bloodGroup, $website, $profilePicture, $userId);
        } else {
            $stmt = $conn->prepare("UPDATE biodata SET full_name=?, father_name=?, mother_name=?, date_of_birth=?, gender=?, address=?, phone=?, email=?, linkedin=?, github=?, education=?, skills=?, languages=?, marital_status=?, hobbies=?, blood_group=?, website=? WHERE user_id=?");
            $stmt->bind_param("sssssssssssssssssi", $fullName, $fatherName, $motherName, $dob, $gender, $address, $phone, $email, $linkedin, $github, $education, $skills, $languages, $maritalStatus, $hobbies, $bloodGroup, $website, $userId);
        }
        
        if ($stmt->execute()) {
            $message = 'Biodata updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error updating biodata: ' . $conn->error;
            $messageType = 'error';
        }
    } else {
        // Insert new biodata
        $stmt = $conn->prepare("INSERT INTO biodata (user_id, full_name, father_name, mother_name, date_of_birth, gender, address, phone, email, linkedin, github, education, skills, languages, marital_status, hobbies, blood_group, website, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssssssssssssss", $userId, $fullName, $fatherName, $motherName, $dob, $gender, $address, $phone, $email, $linkedin, $github, $education, $skills, $languages, $maritalStatus, $hobbies, $bloodGroup, $website, $profilePicture);
        
        if ($stmt->execute()) {
            $message = 'Biodata saved successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error saving biodata: ' . $conn->error;
            $messageType = 'error';
        }
    }
    
    $stmt->close();
}

// Handle delete operation
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $stmt = $conn->prepare("DELETE FROM biodata WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        $message = 'Biodata deleted successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error deleting biodata: ' . $conn->error;
        $messageType = 'error';
    }
    
    $stmt->close();
}

// Fetch existing biodata
$biodata = null;
$stmt = $conn->prepare("SELECT * FROM biodata WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $biodata = $result->fetch_assoc();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html> 
<html lang="en"> 
<head> 
  <meta charset="UTF-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <title>Biodata</title> 
  <link rel="stylesheet" href="css/biodata.css"> 
</head> 
<body> 
  <div class="container"> 
    <div class="header">
      <div></div> <!-- Empty div for spacing -->
      <div class="user-info">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</span>
        <a href="logout.php" class="logout-btn">Logout</a>
      </div>
    </div>
    
    <h2>BIODATA</h2>
    
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="post" enctype="multipart/form-data"> 
      <div class="profile-pic"> 
        <label>Profile Picture</label><br> 
        <div class="avatar"> 
          <?php if ($biodata && !empty($biodata['profile_picture'])): ?>
            <img src="<?php echo htmlspecialchars($biodata['profile_picture']); ?>" alt="Profile Picture">
          <?php else: ?>
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="default profile">
          <?php endif; ?>
        </div> 
        <input type="file" name="profile_picture" accept="image/*"> 
      </div>       
 
      <label>Full Name *</label> 
      <input type="text" name="fullname" placeholder="Enter your full name" required value="<?php echo $biodata ? htmlspecialchars($biodata['full_name']) : ''; ?>"> 
 
      <label>Father's Name</label> 
      <input type="text" name="father" placeholder="Enter your father's name" value="<?php echo $biodata ? htmlspecialchars($biodata['father_name']) : ''; ?>"> 
 
      <label>Mother's Name</label> 
      <input type="text" name="mother" placeholder="Enter your mother's name" value="<?php echo $biodata ? htmlspecialchars($biodata['mother_name']) : ''; ?>"> 
 
      <label>Date of Birth</label> 
      <input type="date" name="dob" value="<?php echo $biodata ? $biodata['date_of_birth'] : ''; ?>"> 
 
      <label>Gender</label> 
      <div class="radio-group"> 
        <label><input type="radio" name="gender" value="Male" <?php echo ($biodata && $biodata['gender'] == 'Male') ? 'checked' : ''; ?>> Male</label> 
        <label><input type="radio" name="gender" value="Female" <?php echo ($biodata && $biodata['gender'] == 'Female') ? 'checked' : ''; ?>> Female</label> 
        <label><input type="radio" name="gender" value="Other" <?php echo ($biodata && $biodata['gender'] == 'Other') ? 'checked' : ''; ?>> Other</label> 
      </div> 
 
      <label>Address</label> 
      <textarea name="address" placeholder="Enter your address"><?php echo $biodata ? htmlspecialchars($biodata['address']) : ''; ?></textarea> 
 
      <label>Phone Number</label> 
      <input type="tel" name="phone" placeholder="Enter your phone number" value="<?php echo $biodata ? htmlspecialchars($biodata['phone']) : ''; ?>"> 
 
      <label>Email</label> 
      <input type="email" name="email" placeholder="Enter your email address" value="<?php echo $biodata ? htmlspecialchars($biodata['email']) : ''; ?>"> 
 
      <label>LinkedIn</label> 
      <input type="url" name="linkedin" placeholder="Enter your LinkedIn profile URL" value="<?php echo $biodata ? htmlspecialchars($biodata['linkedin']) : ''; ?>"> 
 
      <label>GitHub</label> 
      <input type="url" name="github" placeholder="Enter your GitHub profile URL" value="<?php echo $biodata ? htmlspecialchars($biodata['github']) : ''; ?>"> 
 
      <label>Education</label> 
      <textarea name="education" placeholder="Enter your educational qualifications"><?php echo $biodata ? htmlspecialchars($biodata['education']) : ''; ?></textarea> 
 
      <label>Skills</label> 
      <textarea name="skills" placeholder="Enter your skills"><?php echo $biodata ? htmlspecialchars($biodata['skills']) : ''; ?></textarea> 
 
      <label>Languages Known</label> 
      <input type="text" name="languages" placeholder="Enter languages you know" value="<?php echo $biodata ? htmlspecialchars($biodata['languages']) : ''; ?>"> 
 
      <label>Marital Status</label> 
      <div class="checkbox-group"> 
        <?php
        $maritalStatuses = $biodata ? explode(', ', $biodata['marital_status']) : [];
        ?>
        <label><input type="checkbox" name="marital[]" value="Married" <?php echo in_array('Married', $maritalStatuses) ? 'checked' : ''; ?>> Married</label> 
        <label><input type="checkbox" name="marital[]" value="Unmarried" <?php echo in_array('Unmarried', $maritalStatuses) ? 'checked' : ''; ?>> Unmarried</label> 
      </div> 
 
      <label>Hobbies</label> 
      <div class="checkbox-group"> 
        <?php
        $userHobbies = $biodata ? explode(', ', $biodata['hobbies']) : [];
        $hobbies = ['Reading', 'Writing', 'Traveling', 'Gaming', 'Cooking', 'Music', 'Sports', 'Drawing', 'Photography', 'Blogging'];
        foreach ($hobbies as $hobby) {
            $checked = in_array($hobby, $userHobbies) ? 'checked' : '';
            echo "<label><input type=\"checkbox\" name=\"hobbies[]\" value=\"$hobby\" $checked> $hobby</label>";
        }
        ?>
      </div> 
 
      <label>Blood Group</label> 
      <select name="blood"> 
        <option value="">Select Blood Group</option> 
        <?php
        $bloodGroups = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];
        foreach ($bloodGroups as $group) {
            $selected = ($biodata && $biodata['blood_group'] == $group) ? 'selected' : '';
            echo "<option value=\"$group\" $selected>$group</option>";
        }
        ?>
      </select> 
 
      <label>Personal Website</label> 
      <input type="url" name="website" placeholder="Enter your website URL" value="<?php echo $biodata ? htmlspecialchars($biodata['website']) : ''; ?>"> 
 
      <div class="buttons"> 
        <button type="submit"><?php echo $biodata ? 'Update' : 'Submit Bio Data'; ?></button> 
        <?php if ($biodata): ?>
          <button type="button" class="delete" onclick="if(confirm('Are you sure you want to delete your biodata?')) { window.location.href='?action=delete'; }">Delete</button>
        <?php endif; ?>
        <button type="reset" class="reset">Reset</button> 
      </div> 
    </form> 
  </div> 
</body> 
</html>
