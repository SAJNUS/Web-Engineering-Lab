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
$action = $_GET['action'] ?? 'list';

// Handle CRUD operations
switch($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = createBiodata($conn, $userId, $_POST, $_FILES);
            $message = $result['message'];
            $messageType = $result['type'];
            if ($result['type'] == 'success') {
                header('Location: biodata_crud.php?action=list');
                exit();
            }
        }
        break;
        
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = updateBiodata($conn, $userId, $_POST, $_FILES);
            $message = $result['message'];
            $messageType = $result['type'];
            if ($result['type'] == 'success') {
                header('Location: biodata_crud.php?action=list');
                exit();
            }
        }
        break;
        
    case 'delete':
        $result = deleteBiodata($conn, $userId);
        $message = $result['message'];
        $messageType = $result['type'];
        header('Location: biodata_crud.php?action=list');
        exit();
        break;
}

// Get biodata for current user
$biodata = getBiodata($conn, $userId);

// CRUD Functions
function createBiodata($conn, $userId, $postData, $files) {
    // Check if biodata already exists
    $stmt = $conn->prepare("SELECT id FROM biodata WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['message' => 'Biodata already exists for this user. Use update instead.', 'type' => 'error'];
    }
    
    $profilePicture = handleFileUpload($files);
    
    $stmt = $conn->prepare("INSERT INTO biodata (user_id, full_name, father_name, mother_name, date_of_birth, gender, address, phone, email, linkedin, github, education, skills, languages, marital_status, hobbies, blood_group, website, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $fullName = sanitizeInput($postData['fullname']);
    $fatherName = sanitizeInput($postData['father']);
    $motherName = sanitizeInput($postData['mother']);
    $dob = $postData['dob'];
    $gender = $postData['gender'] ?? '';
    $address = sanitizeInput($postData['address']);
    $phone = sanitizeInput($postData['phone']);
    $email = sanitizeInput($postData['email']);
    $linkedin = sanitizeInput($postData['linkedin']);
    $github = sanitizeInput($postData['github']);
    $education = sanitizeInput($postData['education']);
    $skills = sanitizeInput($postData['skills']);
    $languages = sanitizeInput($postData['languages']);
    $maritalStatus = isset($postData['marital']) ? implode(', ', $postData['marital']) : '';
    $hobbies = isset($postData['hobbies']) ? implode(', ', $postData['hobbies']) : '';
    $bloodGroup = $postData['blood'];
    $website = sanitizeInput($postData['website']);
    
    $stmt->bind_param("issssssssssssssssss", $userId, $fullName, $fatherName, $motherName, $dob, $gender, $address, $phone, $email, $linkedin, $github, $education, $skills, $languages, $maritalStatus, $hobbies, $bloodGroup, $website, $profilePicture);
    
    if ($stmt->execute()) {
        return ['message' => 'Biodata created successfully!', 'type' => 'success'];
    } else {
        return ['message' => 'Error creating biodata: ' . $conn->error, 'type' => 'error'];
    }
}

function updateBiodata($conn, $userId, $postData, $files) {
    $profilePicture = handleFileUpload($files);
    
    if (!empty($profilePicture)) {
        $stmt = $conn->prepare("UPDATE biodata SET full_name=?, father_name=?, mother_name=?, date_of_birth=?, gender=?, address=?, phone=?, email=?, linkedin=?, github=?, education=?, skills=?, languages=?, marital_status=?, hobbies=?, blood_group=?, website=?, profile_picture=? WHERE user_id=?");
        
        $fullName = sanitizeInput($postData['fullname']);
        $fatherName = sanitizeInput($postData['father']);
        $motherName = sanitizeInput($postData['mother']);
        $dob = $postData['dob'];
        $gender = $postData['gender'] ?? '';
        $address = sanitizeInput($postData['address']);
        $phone = sanitizeInput($postData['phone']);
        $email = sanitizeInput($postData['email']);
        $linkedin = sanitizeInput($postData['linkedin']);
        $github = sanitizeInput($postData['github']);
        $education = sanitizeInput($postData['education']);
        $skills = sanitizeInput($postData['skills']);
        $languages = sanitizeInput($postData['languages']);
        $maritalStatus = isset($postData['marital']) ? implode(', ', $postData['marital']) : '';
        $hobbies = isset($postData['hobbies']) ? implode(', ', $postData['hobbies']) : '';
        $bloodGroup = $postData['blood'];
        $website = sanitizeInput($postData['website']);
        
        $stmt->bind_param("ssssssssssssssssssi", $fullName, $fatherName, $motherName, $dob, $gender, $address, $phone, $email, $linkedin, $github, $education, $skills, $languages, $maritalStatus, $hobbies, $bloodGroup, $website, $profilePicture, $userId);
    } else {
        $stmt = $conn->prepare("UPDATE biodata SET full_name=?, father_name=?, mother_name=?, date_of_birth=?, gender=?, address=?, phone=?, email=?, linkedin=?, github=?, education=?, skills=?, languages=?, marital_status=?, hobbies=?, blood_group=?, website=? WHERE user_id=?");
        
        $fullName = sanitizeInput($postData['fullname']);
        $fatherName = sanitizeInput($postData['father']);
        $motherName = sanitizeInput($postData['mother']);
        $dob = $postData['dob'];
        $gender = $postData['gender'] ?? '';
        $address = sanitizeInput($postData['address']);
        $phone = sanitizeInput($postData['phone']);
        $email = sanitizeInput($postData['email']);
        $linkedin = sanitizeInput($postData['linkedin']);
        $github = sanitizeInput($postData['github']);
        $education = sanitizeInput($postData['education']);
        $skills = sanitizeInput($postData['skills']);
        $languages = sanitizeInput($postData['languages']);
        $maritalStatus = isset($postData['marital']) ? implode(', ', $postData['marital']) : '';
        $hobbies = isset($postData['hobbies']) ? implode(', ', $postData['hobbies']) : '';
        $bloodGroup = $postData['blood'];
        $website = sanitizeInput($postData['website']);
        
        $stmt->bind_param("sssssssssssssssssi", $fullName, $fatherName, $motherName, $dob, $gender, $address, $phone, $email, $linkedin, $github, $education, $skills, $languages, $maritalStatus, $hobbies, $bloodGroup, $website, $userId);
    }
    
    if ($stmt->execute()) {
        return ['message' => 'Biodata updated successfully!', 'type' => 'success'];
    } else {
        return ['message' => 'Error updating biodata: ' . $conn->error, 'type' => 'error'];
    }
}

function deleteBiodata($conn, $userId) {
    $stmt = $conn->prepare("DELETE FROM biodata WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        return ['message' => 'Biodata deleted successfully!', 'type' => 'success'];
    } else {
        return ['message' => 'Error deleting biodata: ' . $conn->error, 'type' => 'error'];
    }
}

function getBiodata($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM biodata WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

function handleFileUpload($files) {
    $profilePicture = '';
    
    if (isset($files['profile_picture']) && $files['profile_picture']['error'] == 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($files["profile_picture"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is actual image
        $check = getimagesize($files["profile_picture"]["tmp_name"]);
        if ($check !== false) {
            // Allow certain file formats
            if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                if (move_uploaded_file($files["profile_picture"]["tmp_name"], $targetFile)) {
                    $profilePicture = $targetFile;
                }
            }
        }
    }
    
    return $profilePicture;
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata CRUD Management</title>
    <link rel="stylesheet" href="css/biodata.css">
    <style>
        .crud-nav {
            background: #f4f4f4;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .crud-nav a {
            display: inline-block;
            padding: 8px 15px;
            margin: 0 5px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }
        .crud-nav a:hover {
            background: #0056b3;
        }
        .crud-nav a.active {
            background: #28a745;
        }
        .biodata-display {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .biodata-field {
            margin: 10px 0;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .biodata-field strong {
            display: inline-block;
            width: 150px;
            color: #333;
        }
        .profile-img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 50%;
        }
        .action-buttons {
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        .btn-primary { background: #007bff; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        .btn-secondary { background: #6c757d; }
        .btn:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>BIODATA CRUD MANAGEMENT</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="crud-nav">
            <a href="?action=list" class="<?php echo $action == 'list' ? 'active' : ''; ?>">View Biodata</a>
            <?php if ($biodata): ?>
                <a href="?action=edit" class="<?php echo $action == 'edit' ? 'active' : ''; ?>">Edit Biodata</a>
            <?php else: ?>
                <a href="?action=new" class="<?php echo $action == 'new' ? 'active' : ''; ?>">Create Biodata</a>
            <?php endif; ?>
            <a href="biodata.html">Back to Original Form</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($action == 'list'): ?>
            <div class="biodata-display">
                <h3>Current Biodata</h3>
                <?php if ($biodata): ?>
                    <?php if (!empty($biodata['profile_picture'])): ?>
                        <div class="biodata-field">
                            <strong>Profile Picture:</strong><br>
                            <img src="<?php echo htmlspecialchars($biodata['profile_picture']); ?>" alt="Profile Picture" class="profile-img">
                        </div>
                    <?php endif; ?>
                    
                    <div class="biodata-field">
                        <strong>Full Name:</strong> <?php echo htmlspecialchars($biodata['full_name']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Father's Name:</strong> <?php echo htmlspecialchars($biodata['father_name']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Mother's Name:</strong> <?php echo htmlspecialchars($biodata['mother_name']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Date of Birth:</strong> <?php echo htmlspecialchars($biodata['date_of_birth']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Gender:</strong> <?php echo htmlspecialchars($biodata['gender']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Address:</strong> <?php echo htmlspecialchars($biodata['address']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Phone:</strong> <?php echo htmlspecialchars($biodata['phone']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Email:</strong> <?php echo htmlspecialchars($biodata['email']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>LinkedIn:</strong> <?php echo htmlspecialchars($biodata['linkedin']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>GitHub:</strong> <?php echo htmlspecialchars($biodata['github']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Education:</strong> <?php echo htmlspecialchars($biodata['education']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Skills:</strong> <?php echo htmlspecialchars($biodata['skills']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Languages:</strong> <?php echo htmlspecialchars($biodata['languages']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Marital Status:</strong> <?php echo htmlspecialchars($biodata['marital_status']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Hobbies:</strong> <?php echo htmlspecialchars($biodata['hobbies']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Blood Group:</strong> <?php echo htmlspecialchars($biodata['blood_group']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Website:</strong> <?php echo htmlspecialchars($biodata['website']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Created:</strong> <?php echo htmlspecialchars($biodata['created_at']); ?>
                    </div>
                    <div class="biodata-field">
                        <strong>Last Updated:</strong> <?php echo htmlspecialchars($biodata['updated_at']); ?>
                    </div>

                    <div class="action-buttons">
                        <a href="?action=edit" class="btn btn-primary">Edit Biodata</a>
                        <a href="?action=delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your biodata?')">Delete Biodata</a>
                    </div>
                <?php else: ?>
                    <p>No biodata found. <a href="?action=new" class="btn btn-success">Create New Biodata</a></p>
                <?php endif; ?>
            </div>

        <?php elseif ($action == 'new' || $action == 'edit'): ?>
            <div class="form-container">
                <h3><?php echo $action == 'new' ? 'Create New Biodata' : 'Edit Biodata'; ?></h3>
                <form method="POST" action="?action=<?php echo $action == 'new' ? 'create' : 'update'; ?>" enctype="multipart/form-data">
                    <!-- Include the biodata form fields here -->
                    <div class="form-group">
                        <label>Full Name:</label>
                        <input type="text" name="fullname" value="<?php echo $biodata ? htmlspecialchars($biodata['full_name']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Father's Name:</label>
                        <input type="text" name="father" value="<?php echo $biodata ? htmlspecialchars($biodata['father_name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Mother's Name:</label>
                        <input type="text" name="mother" value="<?php echo $biodata ? htmlspecialchars($biodata['mother_name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Date of Birth:</label>
                        <input type="date" name="dob" value="<?php echo $biodata ? htmlspecialchars($biodata['date_of_birth']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Gender:</label>
                        <select name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo ($biodata && $biodata['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($biodata && $biodata['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($biodata && $biodata['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Address:</label>
                        <textarea name="address"><?php echo $biodata ? htmlspecialchars($biodata['address']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="tel" name="phone" value="<?php echo $biodata ? htmlspecialchars($biodata['phone']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo $biodata ? htmlspecialchars($biodata['email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>LinkedIn:</label>
                        <input type="url" name="linkedin" value="<?php echo $biodata ? htmlspecialchars($biodata['linkedin']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>GitHub:</label>
                        <input type="url" name="github" value="<?php echo $biodata ? htmlspecialchars($biodata['github']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Education:</label>
                        <textarea name="education"><?php echo $biodata ? htmlspecialchars($biodata['education']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Skills:</label>
                        <textarea name="skills"><?php echo $biodata ? htmlspecialchars($biodata['skills']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Languages:</label>
                        <input type="text" name="languages" value="<?php echo $biodata ? htmlspecialchars($biodata['languages']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Marital Status:</label>
                        <?php $maritalStatuses = $biodata ? explode(', ', $biodata['marital_status']) : []; ?>
                        <label><input type="checkbox" name="marital[]" value="Single" <?php echo in_array('Single', $maritalStatuses) ? 'checked' : ''; ?>> Single</label>
                        <label><input type="checkbox" name="marital[]" value="Married" <?php echo in_array('Married', $maritalStatuses) ? 'checked' : ''; ?>> Married</label>
                        <label><input type="checkbox" name="marital[]" value="Divorced" <?php echo in_array('Divorced', $maritalStatuses) ? 'checked' : ''; ?>> Divorced</label>
                    </div>

                    <div class="form-group">
                        <label>Hobbies:</label>
                        <?php $hobbies = $biodata ? explode(', ', $biodata['hobbies']) : []; ?>
                        <label><input type="checkbox" name="hobbies[]" value="Reading" <?php echo in_array('Reading', $hobbies) ? 'checked' : ''; ?>> Reading</label>
                        <label><input type="checkbox" name="hobbies[]" value="Traveling" <?php echo in_array('Traveling', $hobbies) ? 'checked' : ''; ?>> Traveling</label>
                        <label><input type="checkbox" name="hobbies[]" value="Sports" <?php echo in_array('Sports', $hobbies) ? 'checked' : ''; ?>> Sports</label>
                        <label><input type="checkbox" name="hobbies[]" value="Music" <?php echo in_array('Music', $hobbies) ? 'checked' : ''; ?>> Music</label>
                    </div>

                    <div class="form-group">
                        <label>Blood Group:</label>
                        <select name="blood">
                            <option value="">Select Blood Group</option>
                            <?php
                            $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                            foreach ($bloodGroups as $group) {
                                $selected = ($biodata && $biodata['blood_group'] == $group) ? 'selected' : '';
                                echo "<option value='$group' $selected>$group</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Website:</label>
                        <input type="url" name="website" value="<?php echo $biodata ? htmlspecialchars($biodata['website']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Profile Picture:</label>
                        <input type="file" name="profile_picture" accept="image/*">
                        <?php if ($biodata && !empty($biodata['profile_picture'])): ?>
                            <br><small>Current: <img src="<?php echo htmlspecialchars($biodata['profile_picture']); ?>" alt="Current Profile" style="max-width: 50px; max-height: 50px;"></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <?php echo $action == 'new' ? 'Create Biodata' : 'Update Biodata'; ?>
                        </button>
                        <a href="?action=list" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
