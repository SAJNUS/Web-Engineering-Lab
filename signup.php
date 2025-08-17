<?php
require_once 'config/database.php';

// Initialize database silently
initializeDatabase();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = sanitizeInput($_POST['firstName']);
    $lastName = sanitizeInput($_POST['lastName']);
    $email = sanitizeInput($_POST['email']);
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $phone = sanitizeInput($_POST['phone']);
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
    } elseif (!validatePassword($password)) {
        $message = 'Password must contain at least 8 characters including uppercase, lowercase, number, and special character.';
        $messageType = 'error';
    } else {
        $conn = getDBConnection();
        
        // Check if email or username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = 'Email or username already exists.';
            $messageType = 'error';
        } else {
            // Insert new user
            $hashedPassword = hashPassword($password);
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, password, phone) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $firstName, $lastName, $email, $username, $hashedPassword, $phone);
            
            if ($stmt->execute()) {
                $message = 'Account created successfully! Redirecting to login page...';
                $messageType = 'success';
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                </script>";
            } else {
                $message = 'Error creating account. Please try again.';
                $messageType = 'error';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Create Account</title>
    <link rel="stylesheet" href="css/signup.css">
</head>
<body>
    <div class="signup-container">
        <div class="signup-box">
            <div class="signup-header">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Icon" class="user-icon">
                <h2>Create Account</h2>
                <p>Join us to access biodata form</p>
            </div>
            
            <form method="POST" class="signup-form">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="input-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>">
                    </div>
                    
                    <div class="input-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>">
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                    <div class="password-requirements">
                        <p class="requirements-title">Password must contain:</p>
                        <ul class="requirements-list">
                            <li id="uppercase" class="requirement">At least one uppercase letter (A-Z)</li>
                            <li id="lowercase" class="requirement">At least one lowercase letter (a-z)</li>
                            <li id="number" class="requirement">At least one number (0-9)</li>
                            <li id="special" class="requirement">At least one special character (!@#$%^&*)</li>
                            <li id="length" class="requirement">Minimum 8 characters long</li>
                        </ul>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                </div>
                
                <div class="input-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
                
                <div class="terms-agreement">
                    <label class="checkbox-label">
                        <input type="checkbox" id="terms" required>
                        <span class="checkmark"></span>
                        I agree to the <a href="#" class="terms-link">Terms and Conditions</a> and <a href="#" class="terms-link">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="signup-btn">Create Account</button>
            </form>
            
            <div class="signup-footer">
                <p>Already have an account? <a href="login.php" class="login-link">Sign in</a></p>
                <p><a href="portfolio.html" class="back-link">← Back to Portfolio</a></p>
            </div>
        </div>
    </div>

    <script>
        // Password validation function
        function validatePassword(password) {
            const criteria = {
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password),
                length: password.length >= 8
            };
            
            return {
                isValid: Object.values(criteria).every(Boolean),
                criteria: criteria
            };
        }

        // Real-time password validation
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const validation = validatePassword(password);
            
            // Update visual indicators
            const requirements = document.querySelectorAll('.requirement');
            requirements.forEach(req => {
                const criteriaName = req.id;
                if (validation.criteria[criteriaName]) {
                    req.classList.add('valid');
                    req.classList.remove('invalid');
                } else if (password.length > 0) {
                    req.classList.add('invalid');
                    req.classList.remove('valid');
                } else {
                    req.classList.remove('valid', 'invalid');
                }
            });
        });

        // Real-time password confirmation validation
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#e1e5e9';
            }
        });
    </script>
</body>
</html>
