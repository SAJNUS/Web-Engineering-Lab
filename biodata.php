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
        <label>Profile Picture <span class="required-asterisk">*</span></label><br> 
        <div class="avatar"> 
          <?php if ($biodata && !empty($biodata['profile_picture'])): ?>
            <img src="<?php echo htmlspecialchars($biodata['profile_picture']); ?>" alt="Profile Picture">
          <?php else: ?>
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="default profile">
          <?php endif; ?>
        </div> 
        <input type="file" name="profile_picture" accept="image/*"> 
      </div>       
 
      <label>Full Name <span class="required-asterisk">*</span></label> 
      <input type="text" name="fullname" placeholder="Enter your full name" required value="<?php echo $biodata ? htmlspecialchars($biodata['full_name']) : ''; ?>"> 
 
      <label>Father's Name <span class="required-asterisk">*</span></label> 
      <input type="text" name="father" placeholder="Enter your father's name" value="<?php echo $biodata ? htmlspecialchars($biodata['father_name']) : ''; ?>"> 
 
      <label>Mother's Name <span class="required-asterisk">*</span></label> 
      <input type="text" name="mother" placeholder="Enter your mother's name" value="<?php echo $biodata ? htmlspecialchars($biodata['mother_name']) : ''; ?>"> 
 
      <label>Date of Birth <span class="required-asterisk">*</span></label> 
      <input type="date" name="dob" value="<?php echo $biodata ? $biodata['date_of_birth'] : ''; ?>"> 
 
      <label>Gender <span class="required-asterisk">*</span></label> 
      <div class="radio-group"> 
        <label><input type="radio" name="gender" value="Male" <?php echo ($biodata && $biodata['gender'] == 'Male') ? 'checked' : ''; ?>> Male</label> 
        <label><input type="radio" name="gender" value="Female" <?php echo ($biodata && $biodata['gender'] == 'Female') ? 'checked' : ''; ?>> Female</label> 
        <label><input type="radio" name="gender" value="Other" <?php echo ($biodata && $biodata['gender'] == 'Other') ? 'checked' : ''; ?>> Other</label> 
      </div> 
 
      <label>Address <span class="required-asterisk">*</span></label> 
      <textarea name="address" placeholder="Enter your address"><?php echo $biodata ? htmlspecialchars($biodata['address']) : ''; ?></textarea> 
 
      <label>Phone Number <span class="required-asterisk">*</span></label> 
      <input type="tel" name="phone" placeholder="Enter your phone number" value="<?php echo $biodata ? htmlspecialchars($biodata['phone']) : ''; ?>"> 
 
      <label>Email <span class="required-asterisk">*</span></label> 
      <input type="email" name="email" placeholder="Enter your email address" value="<?php echo $biodata ? htmlspecialchars($biodata['email']) : ''; ?>"> 
 
      <label>LinkedIn</label> 
      <input type="url" name="linkedin" placeholder="Enter your LinkedIn profile URL" value="<?php echo $biodata ? htmlspecialchars($biodata['linkedin']) : ''; ?>"> 
 
      <label>GitHub</label> 
      <input type="url" name="github" placeholder="Enter your GitHub profile URL" value="<?php echo $biodata ? htmlspecialchars($biodata['github']) : ''; ?>"> 
 
      <label>Education <span class="required-asterisk">*</span></label> 
      <textarea name="education" placeholder="Enter your educational qualifications"><?php echo $biodata ? htmlspecialchars($biodata['education']) : ''; ?></textarea> 
 
      <label>Skills <span class="required-asterisk">*</span></label> 
      <textarea name="skills" placeholder="Enter your skills"><?php echo $biodata ? htmlspecialchars($biodata['skills']) : ''; ?></textarea> 
 
      <label>Languages Known <span class="required-asterisk">*</span></label> 
      <input type="text" name="languages" placeholder="Enter languages you know" value="<?php echo $biodata ? htmlspecialchars($biodata['languages']) : ''; ?>"> 
 
      <label>Marital Status <span class="required-asterisk">*</span></label> 
      <div class="checkbox-group"> 
        <?php
        $maritalStatuses = $biodata ? explode(', ', $biodata['marital_status']) : [];
        ?>
        <label><input type="checkbox" name="marital[]" value="Married" <?php echo in_array('Married', $maritalStatuses) ? 'checked' : ''; ?>> Married</label> 
        <label><input type="checkbox" name="marital[]" value="Unmarried" <?php echo in_array('Unmarried', $maritalStatuses) ? 'checked' : ''; ?>> Unmarried</label> 
      </div> 
 
      <label>Hobbies <span class="required-asterisk">*</span></label> 
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
 
      <label>Blood Group <span class="required-asterisk">*</span></label> 
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

  <script>
    // Add IDs to form elements for validation
    document.querySelector('input[name="profile_picture"]').id = 'profilePic';
    document.querySelector('input[name="fullname"]').id = 'fullname';
    document.querySelector('input[name="father"]').id = 'father';
    document.querySelector('input[name="mother"]').id = 'mother';
    document.querySelector('input[name="dob"]').id = 'dob';
    document.querySelector('textarea[name="address"]').id = 'address';
    document.querySelector('input[name="phone"]').id = 'phone';
    document.querySelector('input[name="email"]').id = 'email';
    document.querySelector('textarea[name="education"]').id = 'education';
    document.querySelector('textarea[name="skills"]').id = 'skills';
    document.querySelector('input[name="languages"]').id = 'languages';
    document.querySelector('select[name="blood"]').id = 'blood';

    // Add error divs after each required field
    function addErrorDiv(fieldId, errorId) {
      const field = document.getElementById(fieldId);
      if (field && !document.getElementById(errorId)) {
        const errorDiv = document.createElement('div');
        errorDiv.id = errorId;
        errorDiv.className = 'error-message';
        errorDiv.style.display = 'none';
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
      }
    }

    // Add error divs for all required fields
    addErrorDiv('profilePic', 'profilePic-error');
    addErrorDiv('fullname', 'fullname-error');
    addErrorDiv('father', 'father-error');
    addErrorDiv('mother', 'mother-error');
    addErrorDiv('dob', 'dob-error');
    addErrorDiv('address', 'address-error');
    addErrorDiv('phone', 'phone-error');
    addErrorDiv('email', 'email-error');
    addErrorDiv('education', 'education-error');
    addErrorDiv('skills', 'skills-error');
    addErrorDiv('languages', 'languages-error');
    addErrorDiv('blood', 'blood-error');

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
      let hasErrors = false;
      
      // Clear previous errors
      clearAllErrors();
      
      // Validate Profile Picture (only required if no existing picture)
      const profilePic = document.getElementById('profilePic');
      const existingPicture = document.querySelector('.avatar img');
      const hasExistingPicture = existingPicture && !existingPicture.src.includes('3135715.png'); // Check if not default image
      
      if (!hasExistingPicture && (!profilePic.files || profilePic.files.length === 0)) {
        showError('profilePic', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Full Name
      const fullname = document.getElementById('fullname');
      if (!fullname.value.trim()) {
        showError('fullname', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Father's Name
      const father = document.getElementById('father');
      if (!father.value.trim()) {
        showError('father', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Mother's Name
      const mother = document.getElementById('mother');
      if (!mother.value.trim()) {
        showError('mother', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Date of Birth
      const dob = document.getElementById('dob');
      if (!dob.value) {
        showError('dob', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Gender
      const genderSelected = document.querySelector('input[name="gender"]:checked');
      if (!genderSelected) {
        showGenderError('Please select a gender');
        hasErrors = true;
      }
      
      // Validate Address
      const address = document.getElementById('address');
      if (!address.value.trim()) {
        showError('address', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Phone Number
      const phone = document.getElementById('phone');
      if (!phone.value.trim()) {
        showError('phone', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Email
      const email = document.getElementById('email');
      if (!email.value.trim()) {
        showError('email', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Education
      const education = document.getElementById('education');
      if (!education.value.trim()) {
        showError('education', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Skills
      const skills = document.getElementById('skills');
      if (!skills.value.trim()) {
        showError('skills', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Languages
      const languages = document.getElementById('languages');
      if (!languages.value.trim()) {
        showError('languages', 'This field is required');
        hasErrors = true;
      }
      
      // Validate Marital Status
      const maritalSelected = document.querySelector('input[name="marital[]"]:checked');
      if (!maritalSelected) {
        showMaritalError('Please select marital status');
        hasErrors = true;
      }
      
      // Validate Hobbies
      const hobbiesSelected = document.querySelector('input[name="hobbies[]"]:checked');
      if (!hobbiesSelected) {
        showHobbiesError('Please select at least one hobby');
        hasErrors = true;
      }
      
      // Validate Blood Group
      const blood = document.getElementById('blood');
      if (!blood.value) {
        showError('blood', 'This field is required');
        hasErrors = true;
      }
      
      if (hasErrors) {
        e.preventDefault(); // Prevent form submission
      }
    });
    
    function showError(fieldId, message) {
      const field = document.getElementById(fieldId);
      const errorDiv = document.getElementById(fieldId + '-error');
      
      if (field) {
        field.style.border = '2px solid #dc3545';
      }
      
      if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
      }
    }
    
    function showGenderError(message) {
      const genderGroup = document.querySelector('.radio-group');
      if (genderGroup) {
        genderGroup.style.border = '2px solid #dc3545';
        genderGroup.style.padding = '8px';
        genderGroup.style.borderRadius = '6px';
        
        // Add error message after gender group
        let errorDiv = document.getElementById('gender-error');
        if (!errorDiv) {
          errorDiv = document.createElement('div');
          errorDiv.id = 'gender-error';
          errorDiv.className = 'error-message';
          genderGroup.parentNode.insertBefore(errorDiv, genderGroup.nextSibling);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
      }
    }
    
    function showMaritalError(message) {
      const maritalGroups = document.querySelectorAll('.checkbox-group');
      const maritalGroup = maritalGroups[0]; // First checkbox group is marital status
      if (maritalGroup) {
        maritalGroup.style.border = '2px solid #dc3545';
        maritalGroup.style.padding = '8px';
        maritalGroup.style.borderRadius = '6px';
        
        let errorDiv = document.getElementById('marital-error');
        if (!errorDiv) {
          errorDiv = document.createElement('div');
          errorDiv.id = 'marital-error';
          errorDiv.className = 'error-message';
          maritalGroup.parentNode.insertBefore(errorDiv, maritalGroup.nextSibling);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
      }
    }
    
    function showHobbiesError(message) {
      const hobbiesGroups = document.querySelectorAll('.checkbox-group');
      const hobbiesGroup = hobbiesGroups[1]; // Second checkbox group is hobbies
      if (hobbiesGroup) {
        hobbiesGroup.style.border = '2px solid #dc3545';
        hobbiesGroup.style.padding = '8px';
        hobbiesGroup.style.borderRadius = '6px';
        
        let errorDiv = document.getElementById('hobbies-error');
        if (!errorDiv) {
          errorDiv = document.createElement('div');
          errorDiv.id = 'hobbies-error';
          errorDiv.className = 'error-message';
          hobbiesGroup.parentNode.insertBefore(errorDiv, hobbiesGroup.nextSibling);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
      }
    }
    
    function clearAllErrors() {
      // Clear error messages
      const errorMessages = document.querySelectorAll('.error-message');
      errorMessages.forEach(error => {
        error.style.display = 'none';
        error.textContent = '';
      });
      
      // Reset field borders
      const inputs = document.querySelectorAll('input, textarea, select');
      inputs.forEach(input => {
        input.style.border = '';
      });
      
      // Reset group borders
      const groups = document.querySelectorAll('.radio-group, .checkbox-group');
      groups.forEach(group => {
        group.style.border = '';
        group.style.padding = '';
      });
    }
    
    // Clear errors when user starts typing/selecting
    document.querySelectorAll('input, textarea, select').forEach(field => {
      field.addEventListener('input', function() {
        this.style.border = '';
        const errorDiv = document.getElementById(this.id + '-error');
        if (errorDiv) {
          errorDiv.style.display = 'none';
        }
      });
    });
    
    // Clear gender error when selected
    document.querySelectorAll('input[name="gender"]').forEach(radio => {
      radio.addEventListener('change', function() {
        const genderGroup = document.querySelector('.radio-group');
        const genderError = document.getElementById('gender-error');
        if (genderGroup) {
          genderGroup.style.border = '';
          genderGroup.style.padding = '';
        }
        if (genderError) genderError.style.display = 'none';
      });
    });
    
    // Clear marital error when selected
    document.querySelectorAll('input[name="marital[]"]').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const maritalGroups = document.querySelectorAll('.checkbox-group');
        const maritalGroup = maritalGroups[0];
        const maritalError = document.getElementById('marital-error');
        if (maritalGroup) {
          maritalGroup.style.border = '';
          maritalGroup.style.padding = '';
        }
        if (maritalError) maritalError.style.display = 'none';
      });
    });
    
    // Clear hobbies error when selected
    document.querySelectorAll('input[name="hobbies[]"]').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const hobbiesGroups = document.querySelectorAll('.checkbox-group');
        const hobbiesGroup = hobbiesGroups[1];
        const hobbiesError = document.getElementById('hobbies-error');
        if (hobbiesGroup) {
          hobbiesGroup.style.border = '';
          hobbiesGroup.style.padding = '';
        }
        if (hobbiesError) hobbiesError.style.display = 'none';
      });
    });
  </script>
</body> 
</html>
