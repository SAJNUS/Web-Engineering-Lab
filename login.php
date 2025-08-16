<?php
require_once 'config/database.php';

// Initialize database silently
initializeDatabase();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $message = 'Please enter both username and password.';
        $messageType = 'error';
    } else {
        $conn = getDBConnection();
        
        // Check user credentials
        $stmt = $conn->prepare("SELECT id, username, password, first_name, last_name FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (verifyPassword($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                $message = 'Login successful! Redirecting to biodata form...';
                $messageType = 'success';
                
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'biodata.php';
                    }, 2000);
                </script>";
            } else {
                $message = 'Invalid username or password.';
                $messageType = 'error';
            }
        } else {
            $message = 'Invalid username or password.';
            $messageType = 'error';
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
    <title>Login - Biodata Access</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Icon" class="user-icon">
                <h2>Access Biodata Form</h2>
                <p>Please login to continue</p>
            </div>
            
            <form method="POST" class="login-form">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="input-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username or email" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" id="remember">
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit" class="login-btn">Login</button>
            </form>
            
            <div class="login-footer">
                <p>Don't have an account? <a href="signup.php">Sign up</a></p>
                <p><a href="portfolio.html" class="back-link">← Back to Portfolio</a></p>
            </div>
        </div>
    </div>
</body>
</html>
